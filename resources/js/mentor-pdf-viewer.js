import * as pdfjsLib from 'pdfjs-dist';
import pdfWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker;

document.addEventListener('livewire:initialized', () => {
    let pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.0,
        canvas = null,
        ctx = null;
        
    let currentPreviewUrl = null;

    if (canvas) {
        ctx = canvas.getContext('2d');
    }

    const renderPage = (num) => {
        pageRendering = true;
        
        pdfDoc.getPage(num).then((page) => {
            const viewport = page.getViewport({scale: scale});
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            const renderTask = page.render(renderContext);

            renderTask.promise.then(() => {
                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });

        document.getElementById('pdf-page-num').textContent = num;
    };

    const queueRenderPage = (num) => {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    };

    const onPrevPage = () => {
        if (pageNum <= 1) {
            return;
        }
        pageNum--;
        queueRenderPage(pageNum);
    };

    const onNextPage = () => {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        queueRenderPage(pageNum);
    };

    const onZoomIn = () => {
        scale += 0.2;
        queueRenderPage(pageNum);
    };

    const onZoomOut = () => {
        if (scale <= 0.4) return;
        scale -= 0.2;
        queueRenderPage(pageNum);
    };
    
    const onFitWidth = () => {
        if(!pdfDoc) return;
        pdfDoc.getPage(pageNum).then((page) => {
            const viewport = page.getViewport({scale: 1.0});
            const container = document.getElementById('pdf-canvas-container');
            const newScale = (container.clientWidth - 40) / viewport.width;
            scale = newScale;
            queueRenderPage(pageNum);
        });
    }

    // Use event delegation to handle Livewire DOM re-renders
    document.addEventListener('click', (e) => {
        if (e.target.closest('#pdf-prev')) { e.preventDefault(); onPrevPage(); }
        else if (e.target.closest('#pdf-next')) { e.preventDefault(); onNextPage(); }
        else if (e.target.closest('#pdf-zoom-in')) { e.preventDefault(); onZoomIn(); }
        else if (e.target.closest('#pdf-zoom-out')) { e.preventDefault(); onZoomOut(); }
        else if (e.target.closest('#pdf-fit')) { e.preventDefault(); onFitWidth(); }
        else if (e.target.closest('#pdf-retry-btn')) { 
            e.preventDefault(); 
            if (currentPreviewUrl) loadPdf(currentPreviewUrl); 
        }
    });

    const loadPdf = (url) => {
        if (!url) return;
        currentPreviewUrl = url;
        
        canvas = document.getElementById('pdf-render-canvas');
        if (canvas) {
            // Always get new context since canvas might be recreated by Livewire
            ctx = canvas.getContext('2d');
        }
        
        const loadingDiv = document.getElementById('pdf-loading');
        const errorDiv = document.getElementById('pdf-error');
        const toolbar = document.getElementById('pdf-toolbar');
        const errorText = document.getElementById('pdf-error-text');
        
        loadingDiv.classList.remove('hidden');
        errorDiv.classList.add('hidden');
        toolbar.classList.add('hidden');
        
        // reset canvas
        if (ctx) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        const loadingTask = pdfjsLib.getDocument({
            url: url,
            withCredentials: true
        });

        loadingTask.promise.then((pdfDocument_) => {
            pdfDoc = pdfDocument_;
            document.getElementById('pdf-page-count').textContent = pdfDoc.numPages;
            
            loadingDiv.classList.add('hidden');
            toolbar.classList.remove('hidden');
            
            pageNum = 1;
            // auto fit
            onFitWidth();
        }).catch((reason) => {
            loadingDiv.classList.add('hidden');
            errorDiv.classList.remove('hidden');
            
            if (reason.status === 404) {
                errorText.textContent = "File dokumen penilaian mentor tidak ditemukan.";
            } else if (reason.status === 403) {
                errorText.textContent = "Anda tidak memiliki izin melihat dokumen ini.";
            } else {
                errorText.textContent = "Gagal memuat pratinjau dokumen.";
            }
            console.error('Error loading PDF:', reason);
        });
    };



    Livewire.on('open-mentor-pdf-viewer', (data) => {
        // data contains previewUrl and downloadUrl
        const previewUrl = Array.isArray(data) ? data[0].previewUrl : data.previewUrl;
        const downloadUrl = Array.isArray(data) ? data[0].downloadUrl : data.downloadUrl;
        
        const newTabBtn = document.getElementById('pdf-new-tab-btn');
        const downloadBtn = document.getElementById('pdf-download-btn');
        
        if (newTabBtn) {
            newTabBtn.href = previewUrl;
            newTabBtn.classList.remove('hidden');
        }
        
        if (downloadBtn) {
            downloadBtn.href = downloadUrl;
            downloadBtn.classList.remove('hidden');
        }
        
        // A little delay to ensure modal transitions are mostly done for width calculations
        setTimeout(() => {
            loadPdf(previewUrl);
        }, 150);
    });
});
