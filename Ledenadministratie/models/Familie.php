<?php
/**
 * Familie.php - Family Data Model and Operations
 * 
 * This model represents a family unit in the membership administration system.
 * A family ('familie' in Dutch) represents a group of people who share a common
 * household and are managed together for membership and billing purposes.
 * 
 * Database Structure:
 * - Stores family contact information (name, address details)
 * - Links to family members (familieleden) through foreign key relationships
 * - Enforces data integrity by preventing deletion of families with active members
 * 
 * Business Rules:
 * - Each family must have a unique name and address combination
 * - Families cannot be deleted if they have existing family members
 * - Address information is required for contribution billing
 * 
 * Properties:
 * - id: Unique family identifier (primary key)
 * - naam: Family surname/name
 * - straat: Street name
 * - huisnummer: House number
 * - postcode: Postal code
 * - woonplaats: City/town name
 * - familieleden: Array of family members (populated when needed)
 */

namespace models;
use models\Familielid; // Family member model (for future relationship implementation)
use PDO;
use PDOException;

// Security: Prevent direct access to this model file
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ../index.php');
    exit;
}

// Utility Functions: Include logging and helper functions
require_once __DIR__ . '/../includes/utils.php';

/**
 * Familie Class - Family Data Model
 * 
 * Handles all database operations related to families including
 * CRUD operations, validation, and business rule enforcement.
 */
class Familie {
    // Family Properties: Basic information about the family
    public $id;              // Unique identifier (auto-generated)
    public $naam;            // Family surname
    public $straat;          // Street name
    public $huisnummer;      // House number
    public $postcode;        // Postal code
    public $woonplaats;      // City/town
    public $familieleden = []; // Array of family members (loaded when needed)

    // Database Connection: Private PDO instance for database operations
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function voegFamilielidToe(Familielid $lid) {
        $this->familieleden[] = $lid;
    }

    public static function getAllFamilieleden(array $families): array {
        $allFamilieleden = [];

        foreach ($families as $familie) {
            if ($familie instanceof self) {
                $allFamilieleden = array_merge($allFamilieleden, $familie->familieleden);
            }
        }

        return $allFamilieleden;
    }

    public function create($naam, $straat, $huisnummer, $postcode, $woonplaats) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO familie (naam, straat, huisnummer, postcode, woonplaats) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$naam, $straat, $huisnummer, $postcode, $woonplaats]);
        } catch (PDOException $e) {
            writeLog("Familie Model Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $naam, $straat, $huisnummer, $postcode, $woonplaats) {
        try {
            $stmt = $this->pdo->prepare("UPDATE familie SET naam = ?, straat = ?, huisnummer = ?, postcode = ?, woonplaats = ? WHERE id = ?");
            return $stmt->execute([$naam, $straat, $huisnummer, $postcode, $woonplaats, $id]);
        } catch (PDOException $e) {
            writeLog("Familie Model Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            // First check if there are any family members
            $checkStmt = $this->pdo->prepare("SELECT COUNT(*) FROM familielid WHERE familie_id = ?");
            $checkStmt->execute([$id]);
            $memberCount = $checkStmt->fetchColumn();
            
            if ($memberCount > 0) {
                writeLog("Cannot delete family with ID $id: family still has $memberCount member(s)");
                return [
                    'success' => false,
                    'error_type' => 'has_members',
                    'member_count' => $memberCount,
                    'message' => "Kan familie niet verwijderen: deze familie heeft nog $memberCount familielid(en). Verwijder eerst alle familieleden."
                ];
            }
            
            // Delete the family
            $stmt = $this->pdo->prepare("DELETE FROM familie WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Familie succesvol verwijderd.'
                ];
            } else {
                return [
                    'success' => false,
                    'error_type' => 'database_error',
                    'message' => 'Fout bij het verwijderen van de familie uit de database.'
                ];
            }
        } catch (PDOException $e) {
            writeLog("Familie Model Error in delete: " . $e->getMessage());
            return [
                'success' => false,
                'error_type' => 'database_exception',
                'message' => 'Database fout bij het verwijderen van de familie.'
            ];
        }
    }

    public function getFamilieById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM familie WHERE id = ?");
            $stmt->execute([$id]);
            $familie = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($familie) {
                // Initialize empty familieleden array
                $familie['familieleden'] = [];
                
                // Get family members if they exist
                $stmt = $this->pdo->prepare("SELECT * FROM familielid WHERE familie_id = ?");
                $stmt->execute([$id]);
                $familieleden = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                if ($familieleden) {
                    $familie['familieleden'] = $familieleden;
                }
            }
            
            return $familie;
        } catch (\PDOException $e) {
            writeLog("Error in Familie model getFamilieById: " . $e->getMessage());
            return null;
        }
    }
}
?>