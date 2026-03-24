<?php
include "../../include/config.php"; 

$ids_param = isset($_GET['ids']) ? $_GET['ids'] : '';
$inventory_data = [];

if (!empty($ids_param)) {
    $clean_ids = implode(',', array_map('intval', explode(',', $ids_param)));
    $sql = "SELECT id, item, description, item_uuid FROM inventory WHERE id IN ($clean_ids) ORDER BY id DESC";
} else {
    $sql = "SELECT id, item, description, item_uuid FROM inventory ORDER BY id DESC";
}

$result = $conn->query($sql);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $inventory_data[] = [
            'id' => $row['id'], 
            'item' => $row['item'], 
            'desc' => $row['description'],
            'uuid' => $row['item_uuid'] 
        ];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>QR Generator</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { font-family: sans-serif; text-align: center; background: #f4f4f4; padding: 20px; }
        .grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; }
        .qr-card { 
            background: #fff; border: 1px solid #ddd; padding: 15px; 
            border-radius: 8px; width: 180px; display: flex; 
            flex-direction: column; align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            page-break-inside: avoid; 
        }
        .name-label { margin-top: 10px; font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .desc-label { font-size: 11px; color: #777; margin-top: 2px; }
        .uuid-label { font-size: 9px; color: #ccc; margin-top: 5px; } /* Optional: Para sa debugging */
        @media print { .no-print { display: none; } body { background: #fff; padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print">
        <h1>Inventory QR Codes</h1>
        <p>This QR uses <strong>UUID</strong> for monthly persistence.</p>
        <button onclick="window.print()" style="padding: 10px 20px; margin-bottom: 20px; cursor:pointer; background: #28a745; color: white; border: none; border-radius: 5px;">Print Selected</button>
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor:pointer;">Back to Inventory</button>
    </div>
    
    <div id="qr-container" class="grid"></div>

    <script>
        const data = <?= json_encode($inventory_data) ?>;
        
        data.forEach(item => {
            if (!item.uuid) {
                console.error("Missing UUID for item: " + item.item);
                return;
            }

            const card = document.createElement('div');
            card.className = 'qr-card';
            
            const qrDiv = document.createElement('div');
            qrDiv.id = "qr-" + item.id;

            card.innerHTML = `
                <div class="name-label">${item.item}</div>
                <div class="desc-label">${item.desc}</div>
                <div class="uuid-label">${item.uuid}</div>
            `;
            
            card.prepend(qrDiv);
            document.getElementById('qr-container').appendChild(card);
            new QRCode(qrDiv, {
                text: "http://192.168.10.204/user_ims/index.php?item_uuid=" + item.uuid,
                width: 150, 
                height: 150,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>