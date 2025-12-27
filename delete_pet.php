<?php
/**
 * Delete Pet
 */
require_once 'includes/db.php';

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

try {
    // Fetch pet data to get QR code path and name
    $stmt = $pdo->prepare("SELECT name, qr_code_path FROM pets WHERE id = :id");
    $stmt->execute([':id' => $petId]);
    $pet = $stmt->fetch();
    
    if (!$pet) {
        $_SESSION['error'] = "Pet not found.";
        header("Location: index.php");
        exit;
    }
    
    // Delete QR code file if it exists
    if ($pet['qr_code_path'] && file_exists($pet['qr_code_path'])) {
        unlink($pet['qr_code_path']);
    }
    
    // Delete pet from database
    $stmt = $pdo->prepare("DELETE FROM pets WHERE id = :id");
    $stmt->execute([':id' => $petId]);
    
    $_SESSION['success'] = "Pet '{$pet['name']}' has been deleted successfully.";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting pet: " . $e->getMessage();
}

header("Location: index.php");
exit;
?>
