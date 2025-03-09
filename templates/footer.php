<!-- Footer -->
<footer class="footer mt-auto py-3 bg-dark text-light">
    <div class="container text-center">
        <span class="text-muted">© 2025 Finance Master | Built with <i class="fas fa-heart text-danger"></i> by Carlos Moronari</span>
    </div>
</footer>

<!-- Bootstrap JS and other scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    // Theme toggle logic
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const isLight = !document.body.classList.contains('light-theme'); // Toggle logic
            const newTheme = isLight ? 'light' : 'dark';
            document.body.classList.toggle('light-theme', isLight); // Apply toggle immediately
            themeToggle.querySelector('i').className = `fas ${isLight ? 'fa-sun' : 'fa-moon'}`; // Update icon
            fetch('update_theme.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'theme=' + newTheme
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('Theme updated to:', data.theme);
                } else {
                    console.error('Theme update failed:', data.message);
                    // Revert if server fails
                    document.body.classList.toggle('light-theme', !isLight);
                    themeToggle.querySelector('i').className = `fas ${!isLight ? 'fa-sun' : 'fa-moon'}`;
                }
            })
            .catch(error => {
                console.error('Error updating theme:', error);
                // Revert on error
                document.body.classList.toggle('light-theme', !isLight);
                themeToggle.querySelector('i').className = `fas ${!isLight ? 'fa-sun' : 'fa-moon'}`;
            });
        });
    }

    // Chart rendering (for analytics page only)
    <?php if (isset($analytics)): ?>
    const expenseCtx = document.getElementById('expenseChart');
    if (expenseCtx) {
        new Chart(expenseCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($analytics['expenses'], 'name')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($analytics['expenses'], 'total')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'top' } } }
        });
    }

    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($analytics['monthly'], 'month')); ?>,
                datasets: [
                    { label: 'Income', data: <?php echo json_encode(array_column($analytics['monthly'], 'income')); ?>, backgroundColor: '#22c55e' },
                    { label: 'Expenses', data: <?php echo json_encode(array_column($analytics['monthly'], 'expense')); ?>, backgroundColor: '#ef4444' }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }
    <?php endif; ?>
</script>
</body>
</html>