<?php
require_once __DIR__ . '/../models/Familie.php';
require_once __DIR__ . '/../includes/utils.php';

use models\Familie;

class FamilieController {
    private $familieModel;
    
    public function __construct($pdo) {
        $this->familieModel = new Familie($pdo);
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $action = $_POST['action'] ?? '';
        switch ($action) {
            case 'add_family':
                if (isset($_POST['add_familie'])) {
                    return $this->handleAddFamilie();
                }
                break;
            case 'edit_family':
                if (isset($_POST['edit_familie'])) {
                    $edit_familie_id = $_POST['edit_familie_id'] ?? null;
                    if ($edit_familie_id) {
                        $familie = $this->getFamilieById($edit_familie_id);
                        return [
                            'success' => true,
                            'familie' => $familie
                        ];
                    }
                }
                if (isset($_POST['update_familie'])) {
                    return $this->handleUpdateFamilie();
                }
                break;
            case 'delete_family':
                if (isset($_POST['delete_familie'])) {
                    return $this->handleDeleteFamilie();
                }
                break;
        }

        return ['success' => false];
    }

    private function handleAddFamilie() {
        $result = $this->familieModel->create(
            $_POST['familie_naam'],
            $_POST['familie_straat'],
            $_POST['familie_huisnummer'],
            $_POST['familie_postcode'],
            $_POST['familie_woonplaats']
        );
        
        return [
            'success' => $result,
            'message' => $result ? "Familie succesvol toegevoegd." : "Fout bij het toevoegen van de familie."
        ];
    }

    private function handleUpdateFamilie() {
        $result = $this->familieModel->update(
            $_POST['familie_id'],
            $_POST['familie_naam'],
            $_POST['familie_straat'],
            $_POST['familie_huisnummer'],
            $_POST['familie_postcode'],
            $_POST['familie_woonplaats']
        );
        
        return [
            'success' => $result,
            'message' => $result ? "Familie succesvol bijgewerkt." : "Fout bij het bijwerken van de familie."
        ];
    }

    private function handleDeleteFamilie() {
        $familieId = $_POST['delete_familie_id'] ?? $_POST['familie_id'] ?? null;
        
        if (!$familieId) {
            return [
                'success' => false,
                'message' => "Geen familie ID opgegeven voor verwijdering."
            ];
        }
        
        $result = $this->familieModel->delete($familieId);
        
        return [
            'success' => $result,
            'message' => $result ? "Familie succesvol verwijderd." : "Fout bij het verwijderen van de familie."
        ];
    }

    public function getFamilieById($id) {
        try {
            return $this->familieModel->getFamilieById($id);
        } catch (PDOException $e) {
            writeLog("Error in getFamilieById: " . $e->getMessage());
            return null;
        }
    }
}
