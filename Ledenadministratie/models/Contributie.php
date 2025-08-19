<?php
namespace models;

class Contributie {
    private static $pdo;
    private static $stallingBedrag = 50.00; // Example amount for stalling

    public static function setPDO($pdo) {
        self::$pdo = $pdo;
    }

    private static function validatePDO() {
        if (self::$pdo === null) {
            throw new \RuntimeException('Database connection not initialized. Call setPDO() first.');
        }
    }

    public static function createContributies($boekjaar) {
        try {
            self::validatePDO();
            
            // Ensure session is started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Clear previous session data first
            unset($_SESSION['berekende_contributies']);
            unset($_SESSION['geselecteerd_boekjaar']);
            
            $stmt = self::$pdo->query("SELECT * FROM familielid");
            $familieleden = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Debug logging
            error_log("Number of familieleden found: " . count($familieleden));
            error_log("Calculating for boekjaar: " . $boekjaar);
            
            $berekende_contributies = [];
            $success = true;

            foreach ($familieleden as $familielid) {
                // Add base contribution for everyone
                $basisBedrag = self::getBasisContributie($familielid['soort_familielid'], $familielid['geboortedatum'], $boekjaar);
                $berekende_contributies[] = [
                    'familielid_id' => $familielid['id'],
                    'contributie_type' => 'Basis',
                    'bedrag' => $basisBedrag,
                    'naam' => $familielid['naam'],
                    'geboortedatum' => $familielid['geboortedatum'],
                    'soort_familielid' => $familielid['soort_familielid'],
                    'soort_lid_id' => $familielid['soort_lid_id']
                ];

                // Add stalling contribution if they have extra stallingen
                if ($familielid['stalling'] > 1) {
                    $extraStallingen = $familielid['stalling'] - 1;
                    $stallingBedrag = $extraStallingen * self::$stallingBedrag;
                    $berekende_contributies[] = [
                        'familielid_id' => $familielid['id'],
                        'contributie_type' => 'Stalling',
                        'bedrag' => $stallingBedrag,
                        'naam' => $familielid['naam'],
                        'geboortedatum' => $familielid['geboortedatum'],
                        'soort_familielid' => $familielid['soort_familielid'],
                        'soort_lid_id' => $familielid['soort_lid_id']
                    ];
                }

                // Store base contribution in database
                $success &= self::storeContributie($familielid['id'], $boekjaar, $basisBedrag, 0);
                
                // Store extra stalling contribution as a separate record if applicable
                if ($familielid['stalling'] > 1) {
                    $success &= self::storeContributie($familielid['id'], $boekjaar, $stallingBedrag, $stallingBedrag);
                }
            }

            if ($success) {
                $_SESSION['berekende_contributies'] = $berekende_contributies;
                $_SESSION['geselecteerd_boekjaar'] = $boekjaar;
                
                // Debug logging
                error_log("Storing in session - Number of contributies: " . count($berekende_contributies));
                error_log("Session ID: " . session_id());
                error_log("New boekjaar set to: " . $boekjaar);
            }

            return $success;
        } catch (\Exception $e) {
            error_log("Error in createContributies: " . $e->getMessage());
            return false;
        }
    }

    private static function storeContributie($familielidId, $boekjaar, $bedrag, $stallingBedrag) {
        try {
            // First delete existing contributions for this member and year to avoid duplicates
            $deleteStmt = self::$pdo->prepare("DELETE FROM contributie WHERE familielid_id = ? AND boekjaar = ?");
            $deleteStmt->execute([$familielidId, $boekjaar]);
            
            $stmt = self::$pdo->prepare("INSERT INTO contributie (familielid_id, boekjaar, bedrag, stalling_aantal, basis_bedrag, stalling_bedrag) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            
            $isStalling = $stallingBedrag > 0;
            return $stmt->execute([
                $familielidId,
                $boekjaar,
                $bedrag,
                ($isStalling ? 1 : 0),
                ($isStalling ? 0 : $bedrag),  // if it's a stalling record, base amount is 0
                $stallingBedrag
            ]);
        } catch (\Exception $e) {
            error_log("Error in storeContributie: " . $e->getMessage());
            return false;
        }
    }

    private static function getBasisContributie($soortFamilielid, $geboortedatum = null, $boekjaar = null) {
        // Get boekjaar-specific contribution rates from database if available
        if ($boekjaar) {
            try {
                $stmt = self::$pdo->prepare("SELECT basiscontributie FROM boekjaar WHERE jaar = ?");
                $stmt->execute([$boekjaar]);
                $boekjaarData = $stmt->fetch(\PDO::FETCH_ASSOC);
                $basisTarief = $boekjaarData ? $boekjaarData['basiscontributie'] : 100.00;
            } catch (\Exception $e) {
                $basisTarief = 100.00; // fallback
            }
        } else {
            $basisTarief = 100.00;
        }
        
        // If we have birth date, calculate age-based contribution
        if ($geboortedatum) {
            $birthDate = new \DateTime($geboortedatum);
            $referenceDate = $boekjaar ? new \DateTime($boekjaar . '-01-01') : new \DateTime();
            $age = $referenceDate->diff($birthDate)->y;
            
            // Age-based contribution rules as percentage of base rate
            if ($age < 8) {
                return $basisTarief * 0.25; // 25% for Jeugd
            } elseif ($age <= 12) {
                return $basisTarief * 0.40; // 40% for Aspirant
            } elseif ($age <= 17) {
                return $basisTarief * 0.60; // 60% for Junior
            } elseif ($age <= 50) {
                return $basisTarief; // 100% for Senior
            } else {
                return $basisTarief * 0.75; // 75% for Oudere
            }
        }
        
        // Fallback to family member type based contribution
        switch($soortFamilielid) {
            case 'vader':
            case 'moeder':
            case 'Volwassene':
                return $basisTarief;
            case 'zoon':
            case 'dochter':
            case 'Kind':
                return $basisTarief * 0.50;
            case 'Senior':
                return $basisTarief * 0.75;
            default:
                return $basisTarief;
        }
    }

    public static function calculateContributiesWithoutSaving($year) {
        try {
            self::validatePDO();
            
            // Fixed SQL query to match your actual database schema
            $sql = "SELECT f.id as familielid_id, f.naam, f.geboortedatum, 
                          f.soort_familielid, f.soort_lid_id, f.stalling
                   FROM familielid f";
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute();
            $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $contributieData = [];
            foreach ($members as $member) {
                $amount = self::calculateIndividualContributie($member, $year);
                $contributieData[] = [
                    'familielid_id' => $member['familielid_id'],
                    'naam' => $member['naam'],
                    'geboortedatum' => $member['geboortedatum'],
                    'soort_familielid' => $member['soort_familielid'],
                    'soort_lid_id' => $member['soort_lid_id'],
                    'jaar' => $year,
                    'bedrag' => $amount
                ];
            }
            
            return $contributieData;
        } catch (\PDOException $e) {
            error_log("Error calculating contributions: " . $e->getMessage());
            return false;
        }
    }
    
    private static function calculateIndividualContributie($member, $year = null) {
        $birthDate = new \DateTime($member['geboortedatum']);
        $referenceDate = $year ? new \DateTime($year . '-01-01') : new \DateTime();
        $age = $referenceDate->diff($birthDate)->y;
        
        // Get year-specific base rate
        $basisTarief = 100.00; // default
        if ($year) {
            try {
                $stmt = self::$pdo->prepare("SELECT basiscontributie FROM boekjaar WHERE jaar = ?");
                $stmt->execute([$year]);
                $boekjaarData = $stmt->fetch(\PDO::FETCH_ASSOC);
                $basisTarief = $boekjaarData ? $boekjaarData['basiscontributie'] : 100.00;
            } catch (\Exception $e) {
                $basisTarief = 100.00;
            }
        }
        
        // Age-based contribution rules
        if ($age < 8) {
            return $basisTarief * 0.25; // Jeugd
        } elseif ($age <= 12) {
            return $basisTarief * 0.40; // Aspirant
        } elseif ($age <= 17) {
            return $basisTarief * 0.60; // Junior
        } elseif ($age <= 50) {
            return $basisTarief; // Senior
        } else {
            return $basisTarief * 0.75; // Oudere
        }
    }
}