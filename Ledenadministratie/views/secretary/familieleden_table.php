<h3>Familieleden</h3>
<table>
    <thead>
        <tr>
            <th>Naam</th>
            <th>Geboortedatum</th>
            <th>Soort</th>
            <th>Stalling</th>
            <th>Acties</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Initialize validation flags to safely check data structure
        // These flags help us verify the data exists and is in the expected format
        // before attempting to display familieleden information
        $editFamilieExists = false;      // Check if $edit_familie variable exists
        $editFamilieIsArray = false;     // Check if $edit_familie is an array (not null/string/etc)
        $familieLedenExist = false;      // Check if 'familieleden' key exists in the array
        $familieLedenIsArray = false;    // Check if 'familieleden' value is actually an array
        
        // Step 1: Check if the main $edit_familie variable exists
        // This variable is set when a family is being edited and contains family data
        if (isset($edit_familie)) {
            $editFamilieExists = true;
            
            // Step 2: Verify that $edit_familie is an array (contains structured data)
            // We need this to be an array so we can safely access array keys
            if (is_array($edit_familie)) {
                $editFamilieIsArray = true;
                
                // Step 3: Check if the 'familieleden' key exists in the familie array
                // This key should contain all family members for the selected family
                if (isset($edit_familie['familieleden'])) {
                    $familieLedenExist = true;
                    
                    // Step 4: Verify that 'familieleden' is an array of family members
                    // We need this to be an array so we can loop through each familielid
                    if (is_array($edit_familie['familieleden'])) {
                        $familieLedenIsArray = true;
                    }
                }
            }
        }
        
        // Final validation: Only show familieleden table if ALL conditions are met
        // This prevents errors when trying to loop through invalid or missing data
        $shouldShowFamilieleden = false;
        if ($editFamilieExists && $editFamilieIsArray && $familieLedenExist && $familieLedenIsArray) {
            $shouldShowFamilieleden = true;
        }
        
        if ($shouldShowFamilieleden): 
        ?>
            <?php foreach ($edit_familie['familieleden'] as $familielid): ?>
                <tr>
                    <td><?php 
                        $familielidNaam = '';
                        if (isset($familielid['naam'])) {
                            $familielidNaam = $familielid['naam'];
                        }
                        echo htmlspecialchars($familielidNaam); 
                    ?></td>
                    <td><?php 
                        $familielidGeboortedatum = '';
                        if (isset($familielid['geboortedatum'])) {
                            $familielidGeboortedatum = $familielid['geboortedatum'];
                        }
                        echo htmlspecialchars($familielidGeboortedatum); 
                    ?></td>
                    <td><?php 
                        $familielidSoort = '';
                        if (isset($familielid['soort_familielid'])) {
                            $familielidSoort = $familielid['soort_familielid'];
                        }
                        echo htmlspecialchars($familielidSoort); 
                    ?></td>
                    <td><?php 
                        $familielidStalling = '';
                        if (isset($familielid['stalling'])) {
                            $familielidStalling = $familielid['stalling'];
                        }
                        echo htmlspecialchars($familielidStalling); 
                    ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="edit_familielid">
                            <input type="hidden" name="edit_familielid_id" value="<?php 
                                $familielidIdForEdit = '';
                                if (isset($familielid['id'])) {
                                    $familielidIdForEdit = $familielid['id'];
                                }
                                echo $familielidIdForEdit; 
                            ?>">
                            <input type="hidden" name="edit_familie_id" value="<?php 
                                $familieIdForEdit = '';
                                if (isset($edit_familie['id'])) {
                                    $familieIdForEdit = $edit_familie['id'];
                                }
                                echo $familieIdForEdit; 
                            ?>">
                            <button type="submit" name="edit_familielid" class="action-btn edit-btn">Bewerken</button>
                        </form>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Weet u zeker dat u dit familielid wilt verwijderen?');">
                            <input type="hidden" name="action" value="delete_familielid">
                            <input type="hidden" name="delete_familielid_id" value="<?php 
                                $familielidIdForDelete = '';
                                if (isset($familielid['id'])) {
                                    $familielidIdForDelete = $familielid['id'];
                                }
                                echo $familielidIdForDelete; 
                            ?>">
                            <input type="hidden" name="familie_id" value="<?php 
                                $familieIdForDelete = '';
                                if (isset($edit_familie['id'])) {
                                    $familieIdForDelete = $edit_familie['id'];
                                }
                                echo $familieIdForDelete; 
                            ?>">
                            <button type="submit" name="delete_familielid" class="action-btn delete-btn">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align: center;">Geen familieleden gevonden</td></tr>
        <?php endif; ?>
    </tbody>
</table>
