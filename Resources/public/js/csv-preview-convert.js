document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.getElementById('csv-preview-convert-open-popup');
    const closeBtn = document.getElementById('csv-preview-convert-close-popup');
    const popup = document.getElementById('csv-preview-convert--popup');

    if (openBtn && popup) {
        openBtn.addEventListener('click', () => {
            popup.style.display = 'flex';
        });
    }

    if (closeBtn && popup) {
        closeBtn.addEventListener('click', () => {
            popup.style.display = 'none';
        });
    }
});