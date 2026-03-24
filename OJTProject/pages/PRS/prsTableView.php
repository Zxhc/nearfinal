<?php
$cols = ['pr_id', 'ref_number', 'pr_date', 'status','all_materials', 'all_descriptions', 'company',  'total_amount', 'remarks'];
?>

<table class="inventory-table" id="inventoryTable">
    <thead>
        <tr>
            <?php foreach($cols as $col): ?>
                <?php if (strtolower($col) == 'id' || strtolower($col) == 'pr_id') continue; ?> 
                <th>
                    <?php 
                        if($col == 'all_materials') echo "Materials List";
                        elseif($col == 'ref_number') echo "Reference No.";
                        elseif($col == 'pr_date') echo "Date Requested";
                        elseif($col == 'status') echo "Status"; 
                        else echo ucwords(str_replace('_', ' ', $col));
                    ?>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
    <?php 
        $row_status_class = getStatusClass($row['status'] ?? ''); 
    ?>
   <tr data-id="<?= $row['pr_id'] ?>" data-ref="<?= $row['ref_number'] ?>" class="<?= $row_status_class ?>">
                    <?php foreach ($cols as $col): ?>
                        <?php if (strtolower($col) == 'id' || strtolower($col) == 'pr_id') continue; ?> 
                        
                        <td data-field="<?= $col ?>">
                            <?php 
                                if ($col == 'status') {
                                    $current_status = $row['status'] ?? 'On Process/Admin';
                                    $status_options = [
                                                    'On Process', 
                                                    'Follow Up', 
                                                    'Hold', 
                                                    'Production Office', 
                                                    'Received', 
                                                    'Ready for Reporting',
                                                    'Cancelled'
                                                     ];
                                    $status_class = getStatusClass($current_status); 
                                    
                                    echo '<form method="POST" style="margin:0;">';
                                    echo '<input type="hidden" name="ref_number" value="' . htmlspecialchars($row['ref_number']) . '">';
                                    echo '<input type="hidden" name="update_status_manual" value="1">';
                                    echo '<select name="new_status" class="manual-status-select ' . $status_class . '" onchange="this.form.submit()">';
                                    foreach ($status_options as $opt) {
                                        $selected = ($current_status == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                    echo '</select>';
                                    echo '</form>';
                                }

                                // ---  CURRENCY FORMATTING ---
                                elseif ($col == 'total_amount') {
                                    echo "₱" . number_format($row[$col], 2);
                                } 
                                
                                // --- MATERIALS LIST DROPDOWN ---
                                elseif ($col == 'all_materials') {
                                    if (!empty($row[$col])) {
                                        $items = explode(', ', $row[$col]);
                                        $count = count($items);
                                        echo '<details style="cursor: pointer; outline: none;">';
                                        echo '<summary style="color: #072d7a; font-weight: bold; list-style: none; font-size: 0.85rem;">';
                                        echo '▼ View ' . $count . ' Items';
                                        echo '</summary>';
                                        echo '<div style="margin-top: 5px; padding: 8px; background: #f8fafc; border-radius: 4px; max-height: 120px; overflow-y: auto; font-size: 0.8rem; border-left: 3px solid #072d7a; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">';
                                        foreach ($items as $index => $item) {
                                            echo '<div style="padding: 4px 0; border-bottom: 1px solid #e2e8f0;">';
                                            echo '<strong>' . ($index + 1) . '.</strong> ' . htmlspecialchars($item);
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        echo '</details>';
                                    } else {
                                        echo '<span style="color:gray; font-style: italic;">None</span>';
                                    }
                                } 
                                // --- DESCRIPTION LIST DROPDOWN ---
                                    elseif ($col == 'all_descriptions') {
                                        if (!empty($row[$col])) {
                                            $desc_list = explode(', ', $row[$col]);
                                            echo '<details style="cursor: pointer; outline: none;">';
                                            echo '<summary style="color: #072d7a; font-weight: bold; list-style: none; font-size: 0.85rem;">';
                                            echo '▼ View ' . $count . ' description';
                                            echo '</summary>';
                                           echo '<div style="margin-top: 5px; padding: 8px; background: #f8fafc; border-radius: 4px; max-height: 120px; overflow-y: auto; font-size: 0.8rem; border-left: 3px solid #072d7a; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">';
                                            foreach ($desc_list as $d) {
                                                echo '<div style="border-bottom: 1px dashed #f1f5f9; padding: 2px 0;">• ' . htmlspecialchars($d) . '</div>';
                                            }
                                            echo '</div>';
                                            echo '</details>';
                                        } else {
                                            echo '<span style="color:#cbd5e1;">-</span>';
                                        }
                                    }

                                // --- SMART REMARKS ---    
                                elseif ($col == 'remarks') {
                                    $remarkText = $row[$col] ?? '';
                                    $limit = 30;
                                    if (strlen($remarkText) > $limit) {
                                        $shortText = substr($remarkText, 0, $limit) . "...";
                                        echo '<details style="cursor: pointer; outline: none;">';
                                        echo '<summary style="list-style: none; color: #555;">';
                                        echo htmlspecialchars($shortText) . ' <span style="color:#072d7a; font-size: 0.75rem;">[More]</span>';
                                        echo '</summary>';
                                        echo '<div style="margin-top: 5px; padding: 10px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; font-size: 0.85rem; color: #92400e; max-width: 200px; word-wrap: break-word;">';
                                        echo htmlspecialchars($remarkText);
                                        echo '</div>';
                                        echo '</details>';
                                    } else {
                                        echo htmlspecialchars($remarkText ?: '-');
                                    }
                                }
                                
                                // --- DEFAULT DISPLAY ---
                                else {
                                    echo htmlspecialchars($row[$col] ?? '');
                                }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?= count($cols) ?>" style="text-align:center; padding: 60px; color: #999;">
                    <span class="material-symbols-outlined" style="font-size: 48px; display: block; margin-bottom: 15px;">history</span>
                    No records found for this period.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>