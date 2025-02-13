const sidebar = document.getElementById('sidebar');
const topbar = document.getElementById('topbar');
const content = document.getElementById('content');
const toggleSidebar = document.getElementById('toggleSidebar');

toggleSidebar.addEventListener('click', () => {
    sidebar.classList.toggle('closed');
    topbar.classList.toggle('shifted');
    content.classList.toggle('shifted');
});