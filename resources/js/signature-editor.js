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
    initialSetupDone = false;
    zoomFactor = 1.0;
    updateZoomUI();
    
    // Apply saved coordinates if any
    if (detail.page) {
        document.getElementById('wire_previewPage').value = detail.page;
    }
    if (detail.x) document.getElementById('wire_previewX').value = detail.x;
    if (detail.y) document.getElementById('wire_previewY').value = detail.y;
    if (detail.width) document.getElementById('wire_previewWidth').value = detail.width;
    if (detail.height) document.getElementById('wire_previewHeight').value = detail.height;
    
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
    } catch (err) {
        console.error("Error loading PDF: ", err);
        pdfError.classList.remove('hidden');
        pdfErrorMessage.textContent = 'Gagal memuat PDF: ' + err.message;
    }
});

function initializeEditorElements() {
    canvas = document.getElementById('pdfCanvas');
    if(canvas) ctx = canvas.getContext('2d');
    signatureBox = document.getElementById('signatureBox');
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
    if(!signatureBox) return;
    
    let w_page = document.getElementById('wire_previewPage');
    let w_x = document.getElementById('wire_previewX');
    let w_y = document.getElementById('wire_previewY');
    let w_w = document.getElementById('wire_previewWidth');
    let w_h = document.getElementById('wire_previewHeight');

    if(!w_page) return;
    
    let cssX = parseFloat(signatureBox.getAttribute('data-x')) || 0;
    let cssY = parseFloat(signatureBox.getAttribute('data-y')) || 0;
    let cssW = parseFloat(signatureBox.style.width) || 150;
    let cssH = parseFloat(signatureBox.style.height) || 75;
    
    let ptX = (cssX / scale);
    let ptY = (cssY / scale);
    let ptW = (cssW / scale);
    let ptH = (cssH / scale);
    
    let mmX = (ptX * 25.4 / 72).toFixed(2);
    let mmY = (ptY * 25.4 / 72).toFixed(2);
    let mmW = (ptW * 25.4 / 72).toFixed(2);
    let mmH = (ptH * 25.4 / 72).toFixed(2);
    
    w_page.value = pageNum;
    w_x.value = mmX;
    w_y.value = mmY;
    w_w.value = mmW;
    w_h.value = mmH;
    
    w_page.dispatchEvent(new Event('input', { bubbles: true }));
    w_x.dispatchEvent(new Event('input', { bubbles: true }));
    w_y.dispatchEvent(new Event('input', { bubbles: true }));
    w_w.dispatchEvent(new Event('input', { bubbles: true }));
    w_h.dispatchEvent(new Event('input', { bubbles: true }));
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
            
            if(signatureBox) {
                signatureBox.style.display = 'flex';
                
                let w_x = document.getElementById('wire_previewX');
                let w_y = document.getElementById('wire_previewY');
                let w_w = document.getElementById('wire_previewWidth');
                let w_h = document.getElementById('wire_previewHeight');
                
                if (w_x && w_x.value > 0) {
                    let ptX = (parseFloat(w_x.value) * 72 / 25.4);
                    let ptY = (parseFloat(w_y.value) * 72 / 25.4);
                    let ptW = (parseFloat(w_w.value) * 72 / 25.4);
                    let ptH = (parseFloat(w_h.value) * 72 / 25.4);
                    
                    let pxX = ptX * scale;
                    let pxY = ptY * scale;
                    let pxW = ptW * scale;
                    let pxH = ptH * scale;
                    
                    signatureBox.style.width = pxW + 'px';
                    signatureBox.style.height = pxH + 'px';
                    
                    signatureBox.style.transform = `translate(${pxX}px, ${pxY}px)`;
                    signatureBox.setAttribute('data-x', pxX);
                    signatureBox.setAttribute('data-y', pxY);
                } else {
                    let defaultX = 20;
                    let defaultY = 20;
                    signatureBox.style.transform = `translate(${defaultX}px, ${defaultY}px)`;
                    signatureBox.setAttribute('data-x', defaultX);
                    signatureBox.setAttribute('data-y', defaultY);
                    updateLivewireData();
                }
                
                setupInteractJs();
            }
        });
    });

    let pageNumEl = document.getElementById('pageNum');
    if (pageNumEl) pageNumEl.textContent = num;
    
    let prevBtn = document.getElementById('prevPage');
    let nextBtn = document.getElementById('nextPage');
    if (prevBtn) prevBtn.disabled = num <= 1;
    if (nextBtn) nextBtn.disabled = num >= pdfDoc.numPages;
}

function queueRenderPage(num) {
    if (pageRendering) {
        pageNumPending = num;
    } else {
        renderPage(num);
    }
}

function onPrevPage() {
    if (pageNum <= 1 || !pdfDoc) return;
    pageNum--;
    queueRenderPage(pageNum);
    updateLivewireData();
}

function onNextPage() {
    if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
    pageNum++;
    queueRenderPage(pageNum);
    updateLivewireData();
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

function setupInteractJs() {
    if(initialSetupDone) return;
    
    interact('#signatureBox')
        .draggable({
            modifiers: [
                interact.modifiers.restrictRect({
                    restriction: 'parent',
                    endOnly: false
                })
            ],
            listeners: {
                move(event) {
                    var target = event.target;
                    var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                    var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);
                },
                end(event) {
                    updateLivewireData();
                }
            }
        })
        .resizable({
            edges: { left: true, right: true, bottom: true, top: true },
            modifiers: [
                interact.modifiers.restrictEdges({
                    outer: 'parent'
                }),
                interact.modifiers.restrictSize({
                    min: { width: 50, height: 25 }
                })
            ],
            listeners: {
                move: function (event) {
                    let { x, y } = event.target.dataset;

                    x = (parseFloat(x) || 0) + event.deltaRect.left;
                    y = (parseFloat(y) || 0) + event.deltaRect.top;

                    Object.assign(event.target.style, {
                        width: `${event.rect.width}px`,
                        height: `${event.rect.height}px`,
                        transform: `translate(${x}px, ${y}px)`
                    });

                    Object.assign(event.target.dataset, { x, y });
                },
                end(event) {
                    updateLivewireData();
                }
            }
        });
        
    initialSetupDone = true;
}

// Reset state when modal is closed
window.addEventListener('preview-modal-closed', () => {
    currentRequestId = null;
    if (initialSetupDone) {
        interact('#signatureBox').unset();
        initialSetupDone = false;
    }
});
