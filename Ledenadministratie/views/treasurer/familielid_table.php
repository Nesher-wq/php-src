<?php
require_once __DIR__ . '/../../controllers/FamilielidController.php';
require_once __DIR__ . '/../../../Ledenadministratie_config/connection.php';
require_once __DIR__ . '/../../models/Soortlid.php';

// Initialize database connection and controllers
$conn = new config\Connection();
$pdo = $conn->getConnection();
$familielidController = new \FamilielidController($pdo);
$soortlidModel = new models\Soortlid();

// Fetch all familieleden first
$familieleden_lijst = $familielidController->getAllFamilieleden();

// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Haal berekende contributies op uit session
$berekende_contributies = array();
if (isset($_SESSION['berekende_contributies'])) {
    $berekende_contributies = $_SESSION['berekende_contributies'];
}

$geselecteerd_boekjaar = null;
if (isset($_SESSION['geselecteerd_boekjaar'])) {
    $geselecteerd_boekjaar = $_SESSION['geselecteerd_boekjaar'];
}
?>

<h3>Familieleden Overzicht</h3>
<?php if ($geselecteerd_boekjaar): ?>
    <p><strong>Contributies voor boekjaar: <?php echo htmlspecialchars($geselecteerd_boekjaar); ?></strong></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Naam</th>
            <th>Geboortedatum</th>
            <th>Soort familielid</th>
            <th>Soort lid</th>
            <th><?php 
                $contributieHeaderText = '-';
                if (!empty($berekende_contributies)) {
                    $contributieHeaderText = 'Contributie Type';
                }
                echo $contributieHeaderText; 
            ?></th>
            <th><?php 
                $bedragHeaderText = '-';
                if (!empty($berekende_contributies)) {
                    $bedragHeaderText = 'Bedrag (€)';
                }
                echo $bedragHeaderText; 
            ?></th>
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
            $contributieType = '';
            if (isset($contributie['contributie_type'])) {
                $contributieType = $contributie['contributie_type'];
            }
            
            $contributieBedrag = 0;
            if (isset($contributie['bedrag'])) {
                $contributieBedrag = $contributie['bedrag'];
            }
            
            if ($contributieType === 'Basis') {
                $totaal_basis = $totaal_basis + $contributieBedrag;
            }
            if ($contributieType === 'Stalling') {
                $totaal_stalling = $totaal_stalling + $contributieBedrag;
            }
            $totaal_algemeen = $totaal_algemeen + $contributieBedrag;
        }
        ?>
        <h4>Totaal Overzicht voor <?php echo htmlspecialchars($geselecteerd_boekjaar); ?></h4>
        <p><strong>Totaal Basis Contributie:</strong> € <?php echo number_format($totaal_basis, 2, ',', '.'); ?></p>
        <p><strong>Totaal Stalling:</strong> € <?php echo number_format($totaal_stalling, 2, ',', '.'); ?></p>
        <p><strong>Totaal Algemeen:</strong> € <?php echo number_format($totaal_algemeen, 2, ',', '.'); ?></p>
    </div>
<?php endif; ?>

