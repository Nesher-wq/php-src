<?php
/**
 * Migratie script om stalling kolom toe te voegen aan familielid tabel
 * Voer dit script uit via de browser: /Ledenadministratie/migrate_stalling.php
 */

require_once __DIR__ . '/config/connection.php';

use config\Connection;

try {
    $conn = new Connection();
    $pdo = $conn->getConnection();
    
    echo "<h2>Stalling Kolom Migratie</h2>";
    echo "<pre>";
    
    // Controleer of stalling kolom al bestaat
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as kolom_bestaat
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'familielid' 
        AND COLUMN_NAME = 'stalling'
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['kolom_bestaat'] > 0) {
        echo "✅ Stalling kolom bestaat al in de familielid tabel.\n";
        echo "Geen actie nodig.\n";
    } else {
        echo "❌ Stalling kolom bestaat nog niet.\n";
        echo "Toevoegen van stalling kolom...\n";
        
        // Voeg stalling kolom toe
        $alterStmt = $pdo->prepare("ALTER TABLE familielid ADD COLUMN stalling INT NOT NULL DEFAULT 0");
        $alterStmt->execute();
        
        echo "✅ Stalling kolom succesvol toegevoegd!\n";
    }
    
    // Verificatie: toon huidige tabel structuur voor familielid
    echo "\n--- Huidige familielid tabel structuur ---\n";
    $stmt = $pdo->prepare("DESCRIBE familielid");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        $nullable = $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $column['Default'] !== null ? "DEFAULT '{$column['Default']}'" : '';
        echo sprintf("%-20s %-15s %-10s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $nullable, 
            $default
        );
    }
    
    // Test: haal een familielid op om te zien of stalling kolom werkt
    echo "\n--- Test familielid record ---\n";
    $stmt = $pdo->prepare("SELECT id, naam, stalling FROM familielid LIMIT 1");
    $stmt->execute();
    $testRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testRecord) {
        echo "Test record gevonden:\n";
        echo "ID: {$testRecord['id']}\n";
        echo "Naam: {$testRecord['naam']}\n";
        echo "Stalling: {$testRecord['stalling']}\n";
    } else {
        echo "Geen familielid records gevonden voor test.\n";
    }
    
    echo "\n✅ Migratie voltooid!\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2>❌ Fout tijdens migratie</h2>";
    echo "<pre>";
    echo "Error: " . $e->getMessage() . "\n";
    echo "In bestand: " . $e->getFile() . "\n";
    echo "Op regel: " . $e->getLine() . "\n";
    echo "</pre>";
    
    echo "<h3>Handmatige oplossing:</h3>";
    echo "<p>Voer de volgende SQL query handmatig uit in je database:</p>";
    echo "<code>ALTER TABLE familielid ADD COLUMN stalling INT NOT NULL DEFAULT 0;</code>";
}
?>
