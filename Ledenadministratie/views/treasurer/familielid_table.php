<?php
use config\Connection;
use models\Soortlid;

require_once __DIR__ . '/../../controllers/FamilielidController.php';
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../models/Soortlid.php';

// Initialize database connection and controllers
$conn = new Connection();
$pdo = $conn->getConnection();
$familielidController = new \FamilielidController($pdo);
$soortlidModel = new Soortlid();

// Fetch all familieleden
$familieleden = $familielidController->getAllFamilieleden();

// Fetch all familieleden first
$familieleden_lijst = $familielidController->getAllFamilieleden();

// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Haal berekende contributies op uit session
$berekende_contributies = $_SESSION['berekende_contributies'] ?? [];
$geselecteerd_boekjaar = $_SESSION['geselecteerd_boekjaar'] ?? null;

// Debug log
error_log('Familielid table - Session data:');
error_log('Session ID: ' . session_id());
error_log('Geselecteerd boekjaar: ' . ($geselecteerd_boekjaar ?? 'niet gezet'));
error_log('Aantal berekende contributies: ' . count($berekende_contributies));
error_log('Contributies data: ' . print_r($berekende_contributies, true));
error_log('Aantal familieleden: ' . count($familieleden_lijst));

// Debug output on page if debug parameter is set
if (isset($_GET['debug'])) {
    echo '<div class="debug-info" style="background: #f5f5f5; padding: 10px; margin: 10px 0; border: 1px solid #ddd;">';
    echo '<h4>Debug Information</h4>';
    echo '<pre>';
    echo "Session ID: " . session_id() . "\n";
    echo "Geselecteerd boekjaar: " . ($geselecteerd_boekjaar ?? 'niet gezet') . "\n";
    echo "Aantal berekende contributies: " . count($berekende_contributies) . "\n";
    echo "Berekende contributies:\n";
    print_r($berekende_contributies);
    echo '</pre>';
    echo '</div>';
}
?>

<h3>Familieleden Overzicht</h3>
<?php if ($geselecteerd_boekjaar): ?>
    <p><strong>Contributies voor boekjaar: <?php echo htmlspecialchars($geselecteerd_boekjaar); ?></strong></p>
<?php endif; ?>

<?php if (isset($_GET['debug'])): ?>
    <div style="background-color: #f8f9fa; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd;">
        <h4>Debug Informatie</h4>
        <pre><?php 
            echo "Sessie ID: " . session_id() . "\n";
            echo "Berekende contributies: " . print_r($berekende_contributies, true) . "\n";
            echo "Geselecteerd boekjaar: " . ($geselecteerd_boekjaar ?? 'niet gezet') . "\n";
            echo "Contributies per lid: " . print_r($berekende_contributies, true);
        ?></pre>
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Naam</th>
            <th>Geboortedatum</th>
            <th>Soort familielid</th>
            <th>Soort lid</th>
            <th><?php echo !empty($berekende_contributies) ? 'Contributie Type' : '-'; ?></th>
            <th><?php echo !empty($berekende_contributies) ? 'Bedrag (€)' : '-'; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($berekende_contributies)): ?>
            <?php foreach ($berekende_contributies as $contributie): ?>
                <tr>
                    <td><?php echo htmlspecialchars($contributie['naam']); ?></td>
                    <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($contributie['geboortedatum']))); ?></td>
                    <td><?php echo htmlspecialchars($contributie['soort_familielid']); ?></td>
                    <td><?php echo htmlspecialchars($soortlidModel->getOmschrijvingById($contributie['soort_lid_id'])); ?></td>
                    <td><?php echo htmlspecialchars($contributie['contributie_type']); ?></td>
                    <td>€ <?php echo number_format($contributie['bedrag'], 2, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($familieleden_lijst as $familielid): ?>
                <tr>
                    <td><?php echo htmlspecialchars($familielid['naam']); ?></td>
                    <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($familielid['geboortedatum']))); ?></td>
                    <td><?php echo htmlspecialchars($familielid['soort_familielid']); ?></td>
                    <td><?php echo htmlspecialchars($soortlidModel->getOmschrijvingById($familielid['soort_lid_id'])); ?></td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($berekende_contributies)): ?>
    <div class="contributie-totaal" style="margin-top: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 5px;">
        <?php
        $totaal_basis = 0;
        $totaal_stalling = 0;
        $totaal_algemeen = 0;
        
        foreach ($berekende_contributies as $contributie) {
            if ($contributie['contributie_type'] === 'Basis') {
                $totaal_basis += $contributie['bedrag'];
            } elseif ($contributie['contributie_type'] === 'Stalling') {
                $totaal_stalling += $contributie['bedrag'];
            }
            $totaal_algemeen += $contributie['bedrag'];
        }
        ?>
        <h4>Totaal Overzicht voor <?php echo htmlspecialchars($geselecteerd_boekjaar); ?></h4>
        <p><strong>Totaal Basis Contributie:</strong> € <?php echo number_format($totaal_basis, 2, ',', '.'); ?></p>
        <p><strong>Totaal Stalling:</strong> € <?php echo number_format($totaal_stalling, 2, ',', '.'); ?></p>
        <p><strong>Totaal Algemeen:</strong> € <?php echo number_format($totaal_algemeen, 2, ',', '.'); ?></p>
    </div>
<?php endif; ?>

