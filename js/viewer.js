function goBack() {
    window.history.back();
}
document.getElementById('fullscreen-btn').addEventListener('click', function () {
    const pdfViewer = document.getElementById('pdf-viewer');
    if (pdfViewer.requestFullscreen) {
        pdfViewer.requestFullscreen();
    } else if (pdfViewer.webkitRequestFullscreen) {
        pdfViewer.webkitRequestFullscreen();
    } else if (pdfViewer.msRequestFullscreen) { 
        pdfViewer.msRequestFullscreen();
    }
});
document.getElementById('fullscreen-btn')?.addEventListener('click', () => {
    const viewer = document.getElementById('pdf-viewer');
    if (viewer.requestFullscreen) viewer.requestFullscreen();
});