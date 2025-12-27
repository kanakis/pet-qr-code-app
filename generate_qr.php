<?php
/**
 * Generate QR Code for Pet
 */
require_once 'includes/db.php';
require_once 'vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

$pageTitle = 'Generate QR Code';

// Get database connection
$pdo = getDbConnection();

if (!$pdo) {
    die("Database connection failed. Please check your configuration.");
}

// Get pet ID from query string
$petId = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

if (!$petId) {
    $_SESSION['error'] = "Invalid pet ID.";
    header("Location: index.php");
    exit;
}

// Fetch pet data
try {
    $stmt = $pdo->prepare("SELECT * FROM pets WHERE id = :id");
    $stmt->execute([':id' => $petId]);
    $pet = $stmt->fetch();
    
    if (!$pet) {
        $_SESSION['error'] = "Pet not found.";
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching pet: " . $e->getMessage());
}

// Generate QR code
$qrGenerated = false;
$qrCodePath = '';

try {
    // Create the URL that the QR code will link to
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host;
    $petInfoUrl = $baseUrl . '/pages/pet_info.php?token=' . $pet['unique_token'];
    
    // Create QR code directory if it doesn't exist
    $qrDir = __DIR__ . '/qr_codes';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0755, true);
    }
    
    // Generate unique filename for QR code
    $qrFilename = 'pet_' . $pet['id'] . '_' . time() . '.png';
    $qrCodePath = '/qr_codes/' . $qrFilename;
    $qrFullPath = __DIR__ . $qrCodePath;
    
    // Delete old QR code if exists
    if ($pet['qr_code_path'] && file_exists(__DIR__ . $pet['qr_code_path'])) {
        unlink(__DIR__ . $pet['qr_code_path']);
    }
    
    // Build and save QR code
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($petInfoUrl)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(400)
        ->margin(10)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->build();
    
    $result->saveToFile($qrFullPath);
    
    // Update database with QR code path
    $stmt = $pdo->prepare("UPDATE pets SET qr_code_path = :qr_code_path WHERE id = :id");
    $stmt->execute([
        ':qr_code_path' => $qrCodePath,
        ':id' => $petId
    ]);
    
    $qrGenerated = true;
    $_SESSION['success'] = "QR Code generated successfully for '{$pet['name']}'!";
    
} catch (Exception $e) {
    $_SESSION['error'] = "Error generating QR code: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

$cssPath = '/assets/css/style.css';
include 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <h1 class="mb-4">
            <i class="fas fa-qrcode"></i> QR Code Generated!
        </h1>
        
        <?php if ($qrGenerated): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> QR Code has been generated successfully for <strong><?php echo htmlspecialchars($pet['name']); ?></strong>!
        </div>
        
        <div class="card">
            <div class="card-body text-center">
                <h3 class="card-title"><?php echo htmlspecialchars($pet['name']); ?></h3>
                <p class="text-muted"><?php echo htmlspecialchars($pet['breed'] ?? 'Unknown breed'); ?></p>
                
                <div class="qr-code-container my-4">
                    <img src="<?php echo htmlspecialchars($qrCodePath); ?>" 
                         alt="QR Code for <?php echo htmlspecialchars($pet['name']); ?>"
                         class="img-fluid">
                </div>
                
                <p class="lead">Scan this QR code to view pet information</p>
                
                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <a href="pages/pet_info.php?token=<?php echo htmlspecialchars($pet['unique_token']); ?>" 
                       class="btn btn-info" target="_blank">
                        <i class="fas fa-eye"></i> Preview Pet Info Page
                    </a>
                    <button class="btn btn-primary btn-print-qr" 
                            data-qr-image="<?php echo htmlspecialchars($qrCodePath); ?>">
                        <i class="fas fa-print"></i> Print QR Code
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
