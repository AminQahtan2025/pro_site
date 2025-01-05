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
const urlParams = new URLSearchParams(window.location.search);
const articleNumber = urlParams.get('id');
const articles = {
    "1": {
        title: "Key Graduate Competencies for Thriving in the Future Job Market",
        date: "December 2024",
        pdfPath: "1.pdf"
    },
    "2": {
        title: "The Role of Universities in Fostering Creativity, Innovation, and Entrepreneurship in the Age of Artificial Intelligence",
        date: "December 2024",
        pdfPath: "2.pdf"
    },
    "3": {
        title: "Shaping Your Path to Success",
        date: "December 2024",
        pdfPath: "3.pdf"
    },
    "4": {
        title: "Do You Have the Spirit of Entrepreneurship to Achieve Your Career Dreams?",
        date: "December 2024",
        pdfPath: "4.pdf"
    }
};

if (articles[articleNumber]) {
const article = articles[articleNumber];
document.getElementById('article-header').textContent = article.title;
document.getElementById('article-title').textContent = article.title;
document.getElementById('article-date').textContent = article.date;
document.getElementById('pdf-viewer').src = article.pdfPath;
const otherArticles = document.getElementById('other-articles');
Object.keys(articles).forEach(key => {
if (key !== articleNumber) {
    const li = document.createElement('li');
    li.className = 'list-group-item';
    li.innerHTML = `<a href="?id=${key}" class="text-decoration-none">${articles[key].title}</a>`;
    otherArticles.appendChild(li);
}
});
} else {
document.getElementById('article-header').textContent = "Article Not Found";
document.getElementById('article-title').textContent = "Article Not Found";
document.getElementById('article-date').textContent = "-";
}