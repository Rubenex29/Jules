        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('sidebarCollapse').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('active');
                // Toggle sidebar display
                var sidebar = document.getElementById('sidebar');
                if (sidebar.style.marginLeft === '-250px') {
                   sidebar.style.marginLeft = '0';
                } else {
                   sidebar.style.marginLeft = '-250px';
                }
            });
        });
    </script>
</body>
</html>
