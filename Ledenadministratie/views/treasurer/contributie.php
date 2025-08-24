<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../models/Contributie.php';

// Initialize database connection
$conn = new \config\Connection();
$pdo = $conn->getConnection();

// Set PDO instance in Contributie model
models\Contributie::setPDO($pdo);

// Add debug information
if (isset($_GET['debug'])) {
    echo '<pre>';
    echo 'Session contents:<br>';
    print_r($_SESSION);
    echo '</pre>';
}

// Show messages
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

// Store for passing to the table view
$calculated_data = null;
$selected_year = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['boekjaar'])) {
        $boekjaar = $_POST['boekjaar'];
        error_log("Form submitted with boekjaar: " . $boekjaar);
        
        try {
            // Create contributies for the selected year and get the data
            $calculated_data = models\Contributie::calculateContributiesWithoutSaving($boekjaar);
            $selected_year = $boekjaar;
            
            $calculationSuccessful = false;
            $calculationHasData = false;
            
            if ($calculated_data !== false) {
                $calculationSuccessful = true;
                if (!empty($calculated_data)) {
                    $calculationHasData = true;
                }
            }
            
            if ($calculationSuccessful) {
                if ($calculationHasData) {
                    $_SESSION['message'] = "Contributies zijn berekend voor " . htmlspecialchars($boekjaar);
                }
            } else {
                $_SESSION['error'] = "Er is een fout opgetreden bij het berekenen van de contributies.";
            }
        } catch (Exception $e) {
            error_log("Error calculating contributions: " . $e->getMessage());
            $_SESSION['error'] = "Er is een fout opgetreden: " . $e->getMessage();
        }
    }
}
?>

<div class="content-section">
    <h2>Contributie Berekening</h2>
    
    <!-- Form for selecting year -->
    <form method="post" class="form-inline mb-4">
        <div class="form-group mr-2">
            <label for="boekjaar" class="mr-2">Selecteer Boekjaar:</label>
            <input type="number" id="boekjaar" name="boekjaar" class="form-control" 
                   value="<?php echo date('Y'); ?>" min="2020" max="2030" required>
        </div>
        <button type="submit" class="btn btn-primary">Bereken Contributies</button>
    </form>

    <!-- Include the table view -->
    <?php include 'familielid_table.php'; ?>
</div>