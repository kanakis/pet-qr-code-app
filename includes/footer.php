    </main>
    
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">
                <i class="fas fa-paw"></i> Pet QR Code App &copy; <?php echo date('Y'); ?>
            </p>
            <small class="text-muted">Manage your pets and generate QR codes easily</small>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo isset($baseUrl) ? $baseUrl : BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>
