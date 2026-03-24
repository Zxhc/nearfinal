<?php

require_once "../../include/config.php"; 

header('Content-Type: application/json');

if (isset($_GET['ref'])) {
    $ref = $conn->real_escape_string($_GET['ref']);
    

    $res = $conn->query("SELECT * FROM pr_reports WHERE ref_number = '$ref'");
    $header = $res->fetch_assoc();
    
    if (!$header) {
        echo json_encode(['success' => false, 'message' => 'PR Reference not found.']);
        exit;
    }

    $items_res = $conn->query("SELECT * FROM pr_items WHERE pr_ref_number = '$ref'");
    $items = [];
    while($row = $items_res->fetch_assoc()) {
        $items[] = $row;
    }
    
    echo json_encode([
        'success' => true, 
        'header' => $header, 
        'items' => $items
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No reference provided.']);
}