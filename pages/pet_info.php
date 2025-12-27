<?php
/**
 * Pet Information Display Page
 * This page is accessed via QR code and displays pet details
 */
require_once '../includes/db.php';

// Get database connection
$pdo = getDbConnection();

if (!$pdo) {
    die("Database connection failed. Please check your configuration.");
}

// Get token from query string
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Invalid access. Please scan a valid QR code.");
}

// Fetch pet data using the unique token
try {
    $stmt = $pdo->prepare("SELECT * FROM pets WHERE unique_token = :token");
    $stmt->execute([':token' => $token]);
    $pet = $stmt->fetch();
    
    if (!$pet) {
        die("Pet not found. The QR code may be invalid or the pet may have been removed.");
    }
} catch (PDOException $e) {
    die("Error fetching pet information: " . $e->getMessage());
}

$pageTitle = htmlspecialchars($pet['name']) . ' - Pet Information';
$cssPath = '../assets/css/style.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $cssPath; ?>">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .pet-info-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .pet-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .pet-header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        
        .pet-body {
            padding: 30px;
        }
        
        .info-row {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #212529;
        }
        
        .icon-wrapper {
            display: inline-block;
            width: 30px;
            text-align: center;
            margin-right: 10px;
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .pet-header h1 {
                font-size: 1.8rem;
            }
            
            .pet-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="pet-info-card">
                    <div class="pet-header">
                        <i class="fas fa-paw fa-3x mb-3"></i>
                        <h1><?php echo htmlspecialchars($pet['name']); ?></h1>
                        <?php if ($pet['breed']): ?>
                        <p class="mb-0 fs-5"><?php echo htmlspecialchars($pet['breed']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pet-body">
                        <?php if ($pet['age']): ?>
                        <div class="info-row">
                            <div class="info-label">
                                <span class="icon-wrapper"><i class="fas fa-birthday-cake"></i></span>
                                Age
                            </div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($pet['age']); ?> <?php echo $pet['age'] == 1 ? 'year' : 'years'; ?> old
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($pet['owner_name']): ?>
                        <div class="info-row">
                            <div class="info-label">
                                <span class="icon-wrapper"><i class="fas fa-user"></i></span>
                                Owner
                            </div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($pet['owner_name']); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($pet['owner_contact']): ?>
                        <div class="info-row">
                            <div class="info-label">
                                <span class="icon-wrapper"><i class="fas fa-phone"></i></span>
                                Contact Information
                            </div>
                            <div class="info-value">
                                <?php 
                                $contact = htmlspecialchars($pet['owner_contact']);
                                // Check if it's an email
                                if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                                    echo '<a href="mailto:' . $contact . '">' . $contact . '</a>';
                                } 
                                // Check if it looks like a phone number
                                else if (preg_match('/^[\d\s\-\+\(\)]+$/', $contact)) {
                                    echo '<a href="tel:' . preg_replace('/[^\d\+]/', '', $contact) . '">' . $contact . '</a>';
                                } 
                                else {
                                    echo $contact;
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($pet['medical_history']): ?>
                        <div class="info-row">
                            <div class="info-label">
                                <span class="icon-wrapper"><i class="fas fa-notes-medical"></i></span>
                                Medical History
                            </div>
                            <div class="info-value">
                                <?php echo nl2br(htmlspecialchars($pet['medical_history'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="info-row">
                            <div class="info-label">
                                <span class="icon-wrapper"><i class="fas fa-calendar"></i></span>
                                Record Created
                            </div>
                            <div class="info-value">
                                <?php echo date('F d, Y', strtotime($pet['created_at'])); ?>
                            </div>
                        </div>
                        
                        <?php if ($pet['updated_at'] && $pet['updated_at'] != $pet['created_at']): ?>
                        <div class="info-row">
                            <div class="info-label">
                                <span class="icon-wrapper"><i class="fas fa-clock"></i></span>
                                Last Updated
                            </div>
                            <div class="info-value">
                                <?php echo date('F d, Y', strtotime($pet['updated_at'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i>
                            <strong>Important:</strong> If you found this pet, please contact the owner using the information provided above.
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-white">
                        <i class="fas fa-shield-alt"></i> This information is protected and accessed securely via QR code
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
