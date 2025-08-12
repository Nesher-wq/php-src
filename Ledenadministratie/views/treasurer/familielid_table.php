<?php
use models\Familielid;
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

// Haal berekende contributies op uit session
$berekende_contributies = $_SESSION['berekende_contributies'] ?? [];
$geselecteerd_boekjaar = $_SESSION['geselecteerd_boekjaar'] ?? null;

// Organiseer contributies per familielid
$contributies_per_lid = [];
foreach ($berekende_contributies as $contributie) {
    $contributies_per_lid[$contributie['familielid_id']][] = $contributie;
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
            <th>Stalling</th>
            <?php if (!empty($berekende_contributies)): ?>
                <th>Contributie Type</th>
                <th>Bedrag (€)</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($familieleden as $familielid): ?>
            <?php
            $lid_contributies = $contributies_per_lid[$familielid['id']] ?? [];
            $aantal_rijen = max(1, count($lid_contributies));
            ?>
            <?php for ($i = 0; $i < $aantal_rijen; $i++): ?>
                <tr>
                    <?php if ($i === 0): // Alleen bij eerste rij de familielid gegevens tonen ?>
                        <td rowspan="<?php echo $aantal_rijen; ?>"><?php echo htmlspecialchars($familielid['naam']); ?></td>
                        <td rowspan="<?php echo $aantal_rijen; ?>"><?php echo htmlspecialchars(date('d-m-Y', strtotime($familielid['geboortedatum']))); ?></td>
                        <td rowspan="<?php echo $aantal_rijen; ?>"><?php echo htmlspecialchars($familielid['soort_familielid']); ?></td>
                        <td rowspan="<?php echo $aantal_rijen; ?>"><?php echo htmlspecialchars($soortlidModel->getOmschrijvingById($familielid['soort_lid_id'])); ?></td>
                        <td rowspan="<?php echo $aantal_rijen; ?>"><?php echo htmlspecialchars($familielid['stalling'] ?? 0); ?></td>
                    <?php endif; ?>
                    
                    <?php if (!empty($berekende_contributies)): ?>
                        <?php if (isset($lid_contributies[$i])): ?>
                            <td><?php echo htmlspecialchars(ucfirst($lid_contributies[$i]['contributie_type'])); ?></td>
                            <td>€ <?php echo number_format($lid_contributies[$i]['bedrag'], 2, ',', '.'); ?></td>
                        <?php else: ?>
                            <td>-</td>
                            <td>-</td>
                        <?php endif; ?>
                    <?php endif; ?>
                </tr>
            <?php endfor; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (!empty($berekende_contributies)): ?>
    <div class="contributie-totaal" style="margin-top: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 5px;">
        <?php
        $totaal_lidmaatschap = 0;
        $totaal_stalling = 0;
        $totaal_algemeen = 0;
        
        foreach ($berekende_contributies as $contributie) {
            if ($contributie['contributie_type'] === 'lidmaatschap') {
                $totaal_lidmaatschap += $contributie['bedrag'];
            } elseif ($contributie['contributie_type'] === 'stalling') {
                $totaal_stalling += $contributie['bedrag'];
            }
            $totaal_algemeen += $contributie['bedrag'];
        }
        ?>
        <h4>Totaal Overzicht voor <?php echo htmlspecialchars($geselecteerd_boekjaar); ?></h4>
        <p><strong>Totaal Lidmaatschap:</strong> € <?php echo number_format($totaal_lidmaatschap, 2, ',', '.'); ?></p>
        <p><strong>Totaal Stalling:</strong> € <?php echo number_format($totaal_stalling, 2, ',', '.'); ?></p>
        <p><strong>Totaal Algemeen:</strong> € <?php echo number_format($totaal_algemeen, 2, ',', '.'); ?></p>
    </div>
<?php endif; ?>
