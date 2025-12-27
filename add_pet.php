<?php
/**
 * Add New Pet Form
 */
require_once 'includes/db.php';

$pageTitle = 'Add New Pet';
$errors = [];
$formData = [];

// Get database connection
$pdo = getDbConnection();

if (!$pdo) {
    die("Database connection failed. Please check your configuration.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $formData['name'] = sanitizeInput($_POST['name'] ?? '');
    $formData['breed'] = sanitizeInput($_POST['breed'] ?? '');
    $formData['age'] = filter_var($_POST['age'] ?? '', FILTER_VALIDATE_INT);
    $formData['owner_name'] = sanitizeInput($_POST['owner_name'] ?? '');
    $formData['owner_contact'] = sanitizeInput($_POST['owner_contact'] ?? '');
    $formData['medical_history'] = sanitizeInput($_POST['medical_history'] ?? '');
    
    // Validation
    if (empty($formData['name'])) {
        $errors[] = "Pet name is required.";
    }
    
    if ($formData['age'] === false || $formData['age'] < 0) {
        $errors[] = "Please enter a valid age.";
        $formData['age'] = '';
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $uniqueToken = generateUniqueToken();
            
            $sql = "INSERT INTO pets (name, breed, age, owner_name, owner_contact, medical_history, unique_token) 
                    VALUES (:name, :breed, :age, :owner_name, :owner_contact, :medical_history, :unique_token)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $formData['name'],
                ':breed' => $formData['breed'],
                ':age' => $formData['age'],
                ':owner_name' => $formData['owner_name'],
                ':owner_contact' => $formData['owner_contact'],
                ':medical_history' => $formData['medical_history'],
                ':unique_token' => $uniqueToken
            ]);
            
            $petId = $pdo->lastInsertId();
            
            // Set success message and redirect
            $_SESSION['success'] = "Pet '{$formData['name']}' has been added successfully!";
            header("Location: generate_qr.php?id=" . $petId);
            exit;
            
        } catch (PDOException $e) {
            $errors[] = "Error adding pet: " . $e->getMessage();
        }
    }
}

$cssPath = '/assets/css/style.css';
include 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <h1 class="mb-4">
            <i class="fas fa-plus-circle"></i> Add New Pet
        </h1>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" action="add_pet.php" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Pet Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" 
                               required>
                        <div class="invalid-feedback">Please enter the pet's name.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="breed" class="form-label">Breed</label>
                        <input type="text" class="form-control" id="breed" name="breed" 
                               value="<?php echo htmlspecialchars($formData['breed'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="age" class="form-label">Age (years)</label>
                        <input type="number" class="form-control" id="age" name="age" 
                               value="<?php echo htmlspecialchars($formData['age'] ?? ''); ?>" 
                               min="0" max="50">
                    </div>
                    
                    <div class="mb-3">
                        <label for="owner_name" class="form-label">Owner Name</label>
                        <input type="text" class="form-control" id="owner_name" name="owner_name" 
                               value="<?php echo htmlspecialchars($formData['owner_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="owner_contact" class="form-label">Owner Contact</label>
                        <input type="text" class="form-control" id="owner_contact" name="owner_contact" 
                               value="<?php echo htmlspecialchars($formData['owner_contact'] ?? ''); ?>"
                               placeholder="Phone or email">
                    </div>
                    
                    <div class="mb-3">
                        <label for="medical_history" class="form-label">Medical History</label>
                        <textarea class="form-control" id="medical_history" name="medical_history" 
                                  rows="4"><?php echo htmlspecialchars($formData['medical_history'] ?? ''); ?></textarea>
                        <div class="form-text">Include vaccinations, allergies, medications, etc.</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Add Pet
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
