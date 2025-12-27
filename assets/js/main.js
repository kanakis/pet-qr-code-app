// Main JavaScript for Pet QR Code App

document.addEventListener('DOMContentLoaded', function() {
    // Confirm delete action
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this pet? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Print QR code functionality
    const printButtons = document.querySelectorAll('.btn-print-qr');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            const qrImage = this.getAttribute('data-qr-image');
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print QR Code</title>');
            printWindow.document.write('<style>body{text-align:center;padding:20px;}img{max-width:100%;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<img src="' + qrImage + '" />');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        });
    });
});
