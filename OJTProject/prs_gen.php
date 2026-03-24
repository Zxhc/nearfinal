<?php
require './include/config.php'; 
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ref_number'])) {
    // --- 1. CAPTURE HEADER DATA ---
    $ref_no      = $_POST['ref_number'];
    $company     = $_POST['company'];
    $currency    = $_POST['currency'];
    $grandTotal  = floatval($_POST['total_amount']);
    $remarks     = $_POST['remarks'];
    $pr_date     = $_POST['pr_date'] ?? date('Y-m-d'); 
    
    $uom         = $_POST['uom'] ?? '';
    $rm_fg       = $_POST['rm_fg'] ?? ''; 
    $tor         = $_POST['ToR'] ?? '';   

    $names       = $_POST['item_names'] ?? []; 
    $descs       = $_POST['item_descs'] ?? [];
    $makers      = $_POST['item_makers'] ?? [];
    $qtys        = $_POST['item_qtys'] ?? [];
    $prices      = $_POST['item_prices'] ?? [];
    $ids         = $_POST['acknowledge_ids'] ?? [];

    // --- 2. DATABASE TRANSACTION (STRICTLY PRESERVED) ---
    $conn->begin_transaction();
    try {
        // Save Main Report
        $stmt = $conn->prepare("INSERT INTO pr_reports (ref_number, pr_date, company, currency, total_amount, remarks) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssds", $ref_no, $pr_date, $company, $currency, $grandTotal, $remarks);
        $stmt->execute();

        // Save Items
        $item_stmt = $conn->prepare("INSERT INTO pr_items (pr_ref_number, material_name, description) VALUES (?, ?, ?)");
        foreach ($names as $index => $name) {
            $curr_desc  = $descs[$index] ?? '';
            $item_stmt->bind_param("sss", $ref_no, $name, $curr_desc);
            $item_stmt->execute();
        }

        // Update Inventory Alerts
        if (!empty($ids)) {
            $update_stmt = $conn->prepare("UPDATE inventory SET is_acknowledged = 1 WHERE id = ?");
            foreach ($ids as $id) {
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
            }
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("DB Error: " . $e->getMessage());
    }

    // --- 3. EXCEL GENERATION (THE FIX) ---
    try {
        $spreadsheet = IOFactory::load('PRS_Template.xlsx');
        
        // Gagawa tayo ng 'clean' copy ng template bago lagyan ng data ang unang page
        $cleanTemplate = clone $spreadsheet->getActiveSheet();
        
        $itemsPerPage = 19; 
        $totalItems = count($names);
        $pageCount = ceil($totalItems / $itemsPerPage);

        for ($p = 0; $p < $pageCount; $p++) {
            if ($p == 0) {
                // First Page: Gamitin ang current active sheet
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Page 1");
            } else {
                // Succeeding Pages: I-clone ang MALINIS na template para walang data leakage
                $newSheet = clone $cleanTemplate;
                $newSheet->setTitle("Page " . ($p + 1));
                $spreadsheet->addSheet($newSheet);
                $sheet = $newSheet;
            }

            // --- PAGE NUMBERING (Cell AN4) ---
            $sheet->setCellValue('AN4', "Page " . ($p + 1) . " / " . $pageCount);

            // --- HEADER DATA (Static per page) ---
            $sheet->setCellValue('F6', $company);
            $sheet->setCellValue('D7', $ref_no);
            $sheet->setCellValue('AL7', $pr_date);

            // --- BODY ITEMS LOOP ---
            for ($i = 0; $i < $itemsPerPage; $i++) {
                $idx = ($p * $itemsPerPage) + $i;
                if ($idx >= $totalItems) break;

                $row = 17 + $i;
                
                $sheet->setCellValue('A' . $row, $idx + 1); 
                $sheet->setCellValue('B' . $row, $names[$idx]);   
                $sheet->setCellValue('G' . $row, $descs[$idx] ?? ''); 
                $sheet->setCellValue('O' . $row, $tor);    
                $sheet->setCellValue('R' . $row, $rm_fg);  
                $sheet->setCellValue('V' . $row, $makers[$idx] ?? '');  
                $sheet->setCellValue('Z' . $row, $qtys[$idx]);    
                $sheet->setCellValue('AB' . $row, $uom);   
                $sheet->setCellValue('AE' . $row, $currency); 
                $sheet->setCellValue('AF' . $row, $prices[$idx]); 
                
                $row_total = floatval($qtys[$idx]) * floatval($prices[$idx]);
                $sheet->setCellValue('AI' . $row, $row_total); 
            }

            // --- FOOTER & TOTALS ---
            $sheet->setCellValue('A37', $remarks);
            
            // Grand Total: Lalabas lang sa pinakahuling page
            if ($p == ($pageCount - 1)) {
                $sheet->setCellValue('AL39', $grandTotal); 
            } else {
                // Nilagyan ko ng dash para malinis tingnan habang hindi pa huling page
                $sheet->setCellValue('AL39', "---"); 
            }
        }

        // Output Excel File
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PRS_'.$ref_no.'.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    } catch (Exception $e) {
        die("Excel Error: " . $e->getMessage());
    }
}
?>