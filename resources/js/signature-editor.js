import * as pdfjsLib from 'pdfjs-dist';
import pdfWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';
import interact from 'interactjs';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker;

let pdfDoc = null;
let pageNum = 1;
let pageRendering = false;
let pageNumPending = null;
let scale = 1.0;
let baseScale = 1.0;
let zoomFactor = 1.0;
let initialSetupDone = false;
let currentRequestId = null;

// DOM Elements
let canvas, ctx, signatureBox, pdfContainerWrapper, pdfStage, pdfError, pdfErrorMessage;

document.addEventListener('open-signature-editor', async (event) => {
    const detail = event.detail[0];
    
    // Check if it's the same modal open
    if (currentRequestId === detail.requestId) {
        return;
    }
    
    currentRequestId = detail.requestId;
    
    // Await next animation frame to ensure DOM is ready
    await new Promise(resolve => requestAnimationFrame(resolve));
    
    initializeEditorElements();
    
    if (!canvas || !pdfStage) {
        console.error("DOM Elements for signature editor not found!");
        return;
    }
    
    // Reset state
    pdfError.classList.add('hidden');
    zoomFactor = 1.0;
    updateZoomUI();
    
    // Apply saved coordinates if any
    if (detail.page) {
        let wp = document.getElementById('wire_previewPage');
        if(wp) wp.value = detail.page;
    }
    
    try {
        const loadingTask = pdfjsLib.getDocument({
            url: detail.previewUrl,
            withCredentials: true
        });
        
        pdfDoc = await loadingTask.promise;
        document.getElementById('pageCount').textContent = pdfDoc.numPages;
        
        let targetPage = parseInt(document.getElementById('wire_previewPage').value) || 1;
        if (targetPage > pdfDoc.numPages) targetPage = pdfDoc.numPages;
        
        pageNum = targetPage;
        renderPage(pageNum);
        
        setupInteractJs();
    } catch (err) {
        console.error("Error loading PDF: ", err);
        pdfError.classList.remove('hidden');
        pdfErrorMessage.textContent = 'Gagal memuat PDF: ' + err.message;
    }
});

function setupInteractJs() {
    if (initialSetupDone) return;
    
    interact('.signature-box').draggable({
        modifiers: [
            interact.modifiers.restrictRect({
                restriction: '#pdfStage',
                endOnly: false
            })
        ],
        listeners: {
            move: function(event) {
                let target = event.target;
                let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                target.style.transform = `translate(${x}px, ${y}px)`;

                target.setAttribute('data-x', x);
                target.setAttribute('data-y', y);
            },
            end: updateLivewireData
        }
    }).resizable({
        edges: { left: true, right: true, top: true, bottom: true },
        modifiers: [
            interact.modifiers.restrictSize({
                min: { width: 50, height: 25 }
            }),
            interact.modifiers.restrictRect({
                restriction: '#pdfStage',
                endOnly: true
            })
        ],
        listeners: {
            move: function (event) {
                let target = event.target;
                let x = (parseFloat(target.getAttribute('data-x')) || 0);
                let y = (parseFloat(target.getAttribute('data-y')) || 0);

                target.style.width = event.rect.width + 'px';
                target.style.height = event.rect.height + 'px';

                x += event.deltaRect.left;
                y += event.deltaRect.top;

                target.style.transform = `translate(${x}px, ${y}px)`;

                target.setAttribute('data-x', x);
                target.setAttribute('data-y', y);
            },
            end: updateLivewireData
        }
    });

    initialSetupDone = true;
}

function initializeEditorElements() {
    canvas = document.getElementById('pdfCanvas');
    if(canvas) ctx = canvas.getContext('2d');
    pdfContainerWrapper = document.getElementById('pdfContainerWrapper');
    pdfStage = document.getElementById('pdfStage');
    pdfError = document.getElementById('pdfError');
    pdfErrorMessage = document.getElementById('pdfErrorMessage');
    
    // Unbind and rebind global event listeners
    document.removeEventListener('click', handleGlobalClicks);
    document.addEventListener('click', handleGlobalClicks);
}

function handleGlobalClicks(e) {
    if(e.target && e.target.id === 'prevPage') onPrevPage();
    if(e.target && e.target.id === 'nextPage') onNextPage();
    if(e.target && e.target.closest('#zoomIn')) onZoomIn();
    if(e.target && e.target.closest('#zoomOut')) onZoomOut();
    if(e.target && e.target.closest('#fitWidth')) onFitWidth();
}

function updateLivewireData() {
    let boxes = document.querySelectorAll('.signature-box');
    if (boxes.length === 0) return;
    
    let w_page = document.getElementById('wire_previewPage');
    if (w_page && w_page.value != pageNum) {
        w_page.value = pageNum;
        w_page.dispatchEvent(new Event('input', { bubbles: true }));
    }
    
    let placementsData = [];
    
    boxes.forEach(box => {
        let boxPage = parseInt(box.getAttribute('data-page'));
        if (boxPage !== pageNum) return;
        
        let index = box.getAttribute('data-index');
        
        let cssX = parseFloat(box.getAttribute('data-x'));
        let cssY = parseFloat(box.getAttribute('data-y'));
        
        // If data-x or data-y is missing, do not overwrite with 0 (it means it hasn't been rendered yet)
        if (isNaN(cssX) || isNaN(cssY)) return;
        
        let cssW = parseFloat(box.style.width) || 150;
        let cssH = parseFloat(box.style.height) || 75;
        
        let ptX = (cssX / scale);
        let ptY = (cssY / scale);
        let ptW = (cssW / scale);
        let ptH = (cssH / scale);
        
        let mmX = (ptX * 25.4 / 72).toFixed(2);
        let mmY = (ptY * 25.4 / 72).toFixed(2);
        let mmW = (ptW * 25.4 / 72).toFixed(2);
        let mmH = (ptH * 25.4 / 72).toFixed(2);
        
        placementsData.push({
            index: index,
            x: mmX,
            y: mmY,
            width: mmW,
            height: mmH
        });
    });
    
    if (window.Livewire && placementsData.length > 0) {
        window.Livewire.dispatch('update-placements', [placementsData]);
    }
}

function calculateScaleToFit(viewport) {
    if(!pdfContainerWrapper) return 1.0;
    let desiredWidth = pdfContainerWrapper.clientWidth - 32; 
    let calcScale = desiredWidth / viewport.width;
    if (calcScale > 2.0) calcScale = 2.0;
    if (calcScale < 0.5) calcScale = 0.5;
    return calcScale;
}

function renderPage(num) {
    if(!pdfDoc) return;
    pageRendering = true;
    
    pdfDoc.getPage(num).then(function(page) {
        if(!canvas) return;
        
        let unscaledViewport = page.getViewport({scale: 1.0});
        
        baseScale = calculateScaleToFit(unscaledViewport);
        scale = baseScale * zoomFactor;
        
        let viewport = page.getViewport({scale: scale});
        
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        
        pdfStage.style.width = viewport.width + 'px';
        pdfStage.style.height = viewport.height + 'px';

        let renderContext = {
            canvasContext: ctx,
            viewport: viewport
        };
        
        let renderTask = page.render(renderContext);
        
        renderTask.promise.then(function() {
            pageRendering = false;
            if (pageNumPending !== null) {
                renderPage(pageNumPending);
                pageNumPending = null;
            }
            
            window.refreshSignatureBoxes();
        });
    });

    let pageNumEl = document.getElementById('pageNum');
    if (pageNumEl) pageNumEl.textContent = num;
    
    let prevBtn = document.getElementById('prevPage');
    let nextBtn = document.getElementById('nextPage');
    if (prevBtn) prevBtn.disabled = num <= 1;
    if (nextBtn) nextBtn.disabled = num >= pdfDoc.numPages;
}

window.refreshSignatureBoxes = function() {
    let boxes = document.querySelectorAll('.signature-box');
    boxes.forEach(box => {
        let boxPage = parseInt(box.getAttribute('data-page'));
        
        if (boxPage === pageNum) {
            box.style.display = 'flex';
            
            let initX = parseFloat(box.getAttribute('data-init-x')) || 20;
            let initY = parseFloat(box.getAttribute('data-init-y')) || 20;
            let initW = parseFloat(box.getAttribute('data-init-w')) || 40;
            let initH = parseFloat(box.getAttribute('data-init-h')) || 20;
            
            let ptX = (initX * 72 / 25.4);
            let ptY = (initY * 72 / 25.4);
            let ptW = (initW * 72 / 25.4);
            let ptH = (initH * 72 / 25.4);
            
            let pxX = ptX * scale;
            let pxY = ptY * scale;
            let pxW = ptW * scale;
            let pxH = ptH * scale;
            
            box.style.width = pxW + 'px';
            box.style.height = pxH + 'px';
            box.style.transform = `translate(${pxX}px, ${pxY}px)`;
            box.setAttribute('data-x', pxX);
            box.setAttribute('data-y', pxY);
        } else {
            box.style.display = 'none';
        }
    });
};

function queueRenderPage(num) {
    if (pageRendering) {
        pageNumPending = num;
    } else {
        renderPage(num);
    }
}

function onPrevPage() {
    if (pageNum <= 1 || !pdfDoc) return;
    updateLivewireData();
    pageNum--;
    
    let w_page = document.getElementById('wire_previewPage');
    if (w_page) {
        w_page.value = pageNum;
        w_page.dispatchEvent(new Event('input', { bubbles: true }));
    }
    
    queueRenderPage(pageNum);
}

function onNextPage() {
    if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
    updateLivewireData();
    pageNum++;
    
    let w_page = document.getElementById('wire_previewPage');
    if (w_page) {
        w_page.value = pageNum;
        w_page.dispatchEvent(new Event('input', { bubbles: true }));
    }
    
    queueRenderPage(pageNum);
}

function updateZoomUI() {
    let zoomLevel = document.getElementById('zoomLevel');
    if(zoomLevel) zoomLevel.textContent = Math.round(zoomFactor * 100) + '%';
}

function onZoomIn() {
    zoomFactor += 0.2;
    if(zoomFactor > 3.0) zoomFactor = 3.0;
    updateZoomUI();
    queueRenderPage(pageNum);
}

function onZoomOut() {
    zoomFactor -= 0.2;
    if(zoomFactor < 0.4) zoomFactor = 0.4;
    updateZoomUI();
    queueRenderPage(pageNum);
}

function onFitWidth() {
    zoomFactor = 1.0;
    updateZoomUI();
    queueRenderPage(pageNum);
}



// Reset state when modal is closed
window.addEventListener('preview-modal-closed', () => {
    currentRequestId = null;
});
