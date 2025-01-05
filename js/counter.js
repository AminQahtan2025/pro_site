    let visitorCount = parseInt(localStorage.getItem('visitorCount') || 0);
    visitorCount++;
    localStorage.setItem('visitorCount', visitorCount);
    function animateCounter(element, start, end, duration) {
        let startTime = null;
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = value;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        }
        window.requestAnimationFrame(step);
    }
    const counterElement = document.getElementById('visitor-count');
    animateCounter(counterElement, 0, visitorCount, 2000); 