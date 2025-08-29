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

        // ---- Submit CSV File Form AJAX ----
        const form = container.querySelector('form.csv-preview-import-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                const action = form.action;

                try {
                    const response = await fetch(action, {
                        method: 'POST',
                        body: formData,
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
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

        // ---- Submit Encoding Select Changes via AJAX ----
        const select = container.querySelector('.encoding-select');
        if (select) {
            select.addEventListener('change', async (e) => {
                e.preventDefault();

                const selectedEncoding = e.target.value;

                try {
                    const response = await fetch('/csv-preview-submit', { // ou form.action si tu veux
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            encoding: selectedEncoding,
                            action_type: 'encoding_change' // permet de différencier du submit du form
                        })
                    });

                    const html = await response.text();
                    const content = container.querySelector('.csv-preview-convert-popup-content');
                    content.innerHTML = html;

                    initCsvPopupJs(container);

                } catch (err) {
                    console.error('Erreur CSV encoding change :', err);
                }
            });
        }

        // ---- Submit Export Form (Download CSV) ----
        const exportForm = container.querySelector('form.csv-preview-export-form');
        if (exportForm) {
            exportForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(exportForm);
                const action = exportForm.action;

                try {
                    const response = await fetch(action, {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        // Convertir le body en JSON
                        const data = await response.json();

                        // Afficher l'erreur dans le DOM
                        const errorLine = container.querySelector('.csv-convert-download-error');
                        errorLine.innerHTML = data.error || 'Unknown error';

                        // Rejeter la Promise pour signaler l'erreur
                        return;
                    }

                    // Récupérer le contenu CSV comme blob
                    const blob = await response.blob();

                    // Créer un lien temporaire pour forcer le téléchargement
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let filename = 'converted.csv'; // fallback

                    if (contentDisposition) {
                        const match = contentDisposition.match(/filename="?([^"]+)"?/);
                        if (match && match[1]) {
                            filename = match[1];
                        }
                    }

                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();

                    // Nettoyage
                    a.remove();
                    window.URL.revokeObjectURL(url);

                    // Fermer la popup
                    container.style.display = 'none';

                } catch (err) {
                    console.error('Erreur CSV export :', err);
                }
            });
        }
    }
});
