/**
 * PDF and Print Handler for PHP Desktop
 * This script ensures PDFs and print dialogs work correctly in desktop environment
 */

// Override window.open to handle PDFs in same window
(function() {
    const originalOpen = window.open;
    
    window.open = function(url, target, features) {
        // Check if URL is a PDF or print-related
        if (url && (url.includes('.pdf') || url.includes('invoice') || url.includes('export-pdf'))) {
            // For PDFs, open in same window with ability to go back
            if (target === '_blank') {
                // Create iframe overlay for PDF viewing
                showPDFViewer(url);
                return null;
            }
        }
        
        // For other cases, use original behavior
        return originalOpen.call(window, url, target, features);
    };
    
    // PDF Viewer Overlay
    function showPDFViewer(url) {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.id = 'pdf-viewer-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 99999;
            display: flex;
            flex-direction: column;
        `;
        
        // Create header with controls
        const header = document.createElement('div');
        header.style.cssText = `
            background: linear-gradient(135deg, #1e293b, #334155);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        `;
        
        const title = document.createElement('div');
        title.style.cssText = `
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        `;
        title.innerHTML = `
            <i class="bi bi-file-pdf text-red-400"></i>
            <span>PDF Viewer</span>
        `;
        
        const controls = document.createElement('div');
        controls.style.cssText = `
            display: flex;
            gap: 0.75rem;
        `;
        
        // Print button
        const printBtn = document.createElement('button');
        printBtn.innerHTML = '<i class="bi bi-printer"></i> Print';
        printBtn.style.cssText = `
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        `;
        printBtn.onmouseover = () => printBtn.style.transform = 'translateY(-2px)';
        printBtn.onmouseout = () => printBtn.style.transform = 'translateY(0)';
        printBtn.onclick = () => {
            const iframe = document.getElementById('pdf-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.print();
            }
        };
        
        // Download button
        const downloadBtn = document.createElement('button');
        downloadBtn.innerHTML = '<i class="bi bi-download"></i> Download';
        downloadBtn.style.cssText = printBtn.style.cssText;
        downloadBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        downloadBtn.onclick = () => {
            const link = document.createElement('a');
            link.href = url;
            link.download = 'document.pdf';
            link.click();
        };
        
        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '<i class="bi bi-x-lg"></i> Close';
        closeBtn.style.cssText = printBtn.style.cssText;
        closeBtn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
        closeBtn.onclick = () => {
            document.body.removeChild(overlay);
        };
        
        controls.appendChild(printBtn);
        controls.appendChild(downloadBtn);
        controls.appendChild(closeBtn);
        
        header.appendChild(title);
        header.appendChild(controls);
        
        // Create iframe for PDF
        const iframe = document.createElement('iframe');
        iframe.id = 'pdf-iframe';
        iframe.src = url;
        iframe.style.cssText = `
            flex: 1;
            border: none;
            background: white;
        `;
        
        // Assemble overlay
        overlay.appendChild(header);
        overlay.appendChild(iframe);
        
        // Add to body
        document.body.appendChild(overlay);
        
        // Close on Escape key
        const escapeHandler = (e) => {
            if (e.key === 'Escape' && document.getElementById('pdf-viewer-overlay')) {
                document.body.removeChild(overlay);
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);
    }
    
    // Handle all PDF links automatically
    document.addEventListener('click', function(e) {
        let target = e.target;
        
        // Find the actual link if clicking on child element
        while (target && target.tagName !== 'A') {
            target = target.parentElement;
        }
        
        if (target && target.tagName === 'A') {
            const href = target.getAttribute('href');
            const targetAttr = target.getAttribute('target');
            
            // Check if it's a PDF or invoice link
            if (href && (
                href.includes('.pdf') || 
                href.includes('invoice') || 
                href.includes('export-pdf') ||
                href.includes('/reports/')
            )) {
                e.preventDefault();
                
                // Use absolute URL
                let url = href;
                if (!url.startsWith('http')) {
                    url = window.location.origin + (href.startsWith('/') ? href : '/' + href);
                }
                
                showPDFViewer(url);
            }
        }
    });
    
    // Enhanced print function
    window.printDocument = function() {
        window.print();
    };
    
    console.log('✅ PDF Desktop Handler initialized');
})();
