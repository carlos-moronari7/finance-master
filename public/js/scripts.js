document.getElementById('themeToggle').addEventListener('click', function() {
    document.body.classList.toggle('light-mode');
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-adjust');
    icon.classList.toggle('fa-sun');
    
    // Optional: Persist theme choice (requires server-side update)
    fetch('index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'theme=' + (document.body.classList.contains('light-mode') ? 'light' : 'dark')
    });
});