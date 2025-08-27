document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.getElementById('csv-preview-convert-open-popup');

    if (openBtn) {
        openBtn.addEventListener('click', async () => {
            const url = openBtn.dataset.url;

            const response = await fetch(url);
            const html = await response.text();

            let container = document.getElementById('csv-preview-convert--popup');
            if (!container) {
                container = document.createElement('div');
                container.id = 'csv-preview-convert--popup';
                document.body.appendChild(container);
            }

            container.innerHTML = html;
            container.style.display = 'flex';

            initCsvPopupJs(container);
        });
    }

    function initCsvPopupJs(container) {
        const closeBtn = container.querySelector('#csv-preview-convert-close-popup');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                container.style.display = 'none';
            });
        }

        const fileInput = container.querySelector('.form-input-hidden');
        const fileNameSpan = container.querySelector('.file-name');
        const browseBtn = container.querySelector('.btn-browse');

        if (browseBtn && fileInput && fileNameSpan) {
            browseBtn.addEventListener('click', () => fileInput.click());

            if (fileInput.files.length > 0) {
                fileNameSpan.textContent = fileInput.files[0].name;
            }

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    fileNameSpan.textContent = fileInput.files[0].name;
                } else {
                    fileNameSpan.textContent = 'Aucun fichier sélectionné';
                }
            });
        }

        // ---- Submit AJAX ----
        const form = container.querySelector('form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                const action = form.action;

                try {
                    const response = await fetch(action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const html = await response.text();

                    const content = container.querySelector('.csv-preview-convert-popup-content');
                    content.innerHTML = html;

                    initCsvPopupJs(container);

                } catch (err) {
                    console.error('Erreur CSV submit :', err);
                }
            });
        }
    }
});
