<?php
/**
 * Homepage - List all pets
 */
require_once 'includes/db.php';

$pageTitle = 'Pet QR Code App - Home';

// Get database connection
$pdo = getDbConnection();

// Handle connection errors
if (!$pdo) {
    die("Database connection failed. Please check your configuration.");
}

// Fetch all pets
$stmt = $pdo->query("SELECT * FROM pets ORDER BY created_at DESC");
$pets = $stmt->fetchAll();

// Check for success message
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : null;
unset($_SESSION['success']);

$cssPath = '/assets/css/style.css';
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-4">
            <i class="fas fa-paw"></i> My Pets
        </h1>
        <p class="lead">Manage your pets and generate QR codes</p>
    </div>
</div>

<?php if ($successMessage): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-12">
        <a href="add_pet.php" class="btn btn-primary btn-lg">
            <i class="fas fa-plus-circle"></i> Add New Pet
        </a>
    </div>
</div>

<?php if (empty($pets)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> No pets found. Click "Add New Pet" to get started!
</div>
<?php else: ?>
<div class="row">
    <?php foreach ($pets as $pet): ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card pet-card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-dog"></i> <?php echo htmlspecialchars($pet['name']); ?>
                </h5>
                <p class="card-text">
                    <strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed'] ?? 'N/A'); ?><br>
                    <strong>Age:</strong> <?php echo htmlspecialchars($pet['age'] ?? 'N/A'); ?> years<br>
                    <strong>Owner:</strong> <?php echo htmlspecialchars($pet['owner_name'] ?? 'N/A'); ?><br>
                    <strong>Contact:</strong> <?php echo htmlspecialchars($pet['owner_contact'] ?? 'N/A'); ?>
                </p>
                
                <?php if ($pet['qr_code_path'] && file_exists($pet['qr_code_path'])): ?>
                <div class="qr-code-container mb-3">
                    <img src="<?php echo htmlspecialchars($pet['qr_code_path']); ?>" 
                         alt="QR Code for <?php echo htmlspecialchars($pet['name']); ?>"
                         class="img-fluid"
                         style="max-width: 200px;">
                </div>
                <?php endif; ?>
                
                <div class="d-grid gap-2">
                    <a href="pages/pet_info.php?token=<?php echo htmlspecialchars($pet['unique_token']); ?>" 
                       class="btn btn-info btn-sm" target="_blank">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" 
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <?php if (!$pet['qr_code_path'] || !file_exists($pet['qr_code_path'])): ?>
                    <a href="generate_qr.php?id=<?php echo $pet['id']; ?>" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-qrcode"></i> Generate QR Code
                    </a>
                    <?php else: ?>
                    <a href="generate_qr.php?id=<?php echo $pet['id']; ?>" 
                       class="btn btn-secondary btn-sm">
                        <i class="fas fa-sync"></i> Regenerate QR Code
                    </a>
                    <button class="btn btn-outline-primary btn-sm btn-print-qr" 
                            data-qr-image="<?php echo htmlspecialchars($pet['qr_code_path']); ?>">
                        <i class="fas fa-print"></i> Print QR Code
                    </button>
                    <?php endif; ?>
                    <a href="delete_pet.php?id=<?php echo $pet['id']; ?>" 
                       class="btn btn-danger btn-sm btn-delete">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
            <div class="card-footer text-muted">
                <small>Added: <?php echo date('M d, Y', strtotime($pet['created_at'])); ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
