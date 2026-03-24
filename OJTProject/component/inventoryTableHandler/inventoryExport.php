<?php
include_once __DIR__ . '/../../include/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- FILTER LOGIC ---
$selectedMonth = isset($_SESSION['selected_month']) ? (int)$_SESSION['selected_month'] : (int)date('n');
$selectedYear = isset($_SESSION['selected_year']) ? (int)$_SESSION['selected_year'] : (int)date('Y');

$monthName = date("F", mktime(0, 0, 0, $selectedMonth, 1));
$filename = "Inventory_Report_{$monthName}_{$selectedYear}.xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

echo "<?xml version=\"1.0\"?>\n";
echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#333333"/>
  </Style>

  <Style ss:ID="MainTitle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Bold="1" ss:Size="18" ss:Color="#1A365D"/>
  </Style>

  <Style ss:ID="Subtitle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="11" ss:Color="#4A5568"/>
  </Style>

  <Style ss:ID="Header">
   <Font ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#2D3748" ss:Pattern="Solid"/> 
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>

  <Style ss:ID="CellData">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
   </Borders>
  </Style>

  <Style ss:ID="CellDataAlt">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Interior ss:Color="#F7FAFC" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
   </Borders>
  </Style>

  <Style ss:ID="Currency">
   <NumberFormat ss:Format="&quot;₱&quot;#,##0.00"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
   </Borders>
  </Style>

  <Style ss:ID="TotalLabel">
   <Font ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#2B6CB0" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>

  <Style ss:ID="TotalAmount">
   <Font ss:Bold="1" ss:Size="11" ss:Color="#1A365D"/>
   <Interior ss:Color="#EBF8FF" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <NumberFormat ss:Format="&quot;₱&quot;#,##0.00"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#1A365D"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>

  <Style ss:ID="SignatureLine">
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Alignment ss:Horizontal="Center" ss:Vertical="Top"/>
   <Font ss:Bold="1"/>
  </Style>
 </Styles>

 <Worksheet ss:Name="Inventory Report">
  <Table>
   <Column ss:Width="200"/> 
   <Column ss:Width="300"/> 
   <Column ss:Width="80"/>  
   <Column ss:Width="60"/>  
   <Column ss:Width="100"/> 
   <Column ss:Width="120"/> 

   <Row ss:Height="40">
    <Cell ss:MergeAcross="5" ss:StyleID="MainTitle">
        <Data ss:Type="String">HEPC JIG INVENTORY MANAGEMENT SYSTEM</Data>
    </Cell>
   </Row>
   <Row ss:Height="25">
    <Cell ss:MergeAcross="5" ss:StyleID="Subtitle">
        <Data ss:Type="String">OFFICIAL INVENTORY REPORT | <?= strtoupper($monthName) ?> <?= $selectedYear ?></Data>
    </Cell>
   </Row>

   <Row ss:Index="4" ss:Height="30">
    <Cell ss:StyleID="Header"><Data ss:Type="String">ITEM NAME</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">DESCRIPTION</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">CABINET</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">QTY</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">UNIT PRICE</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">TOTAL VALUE</Data></Cell>
   </Row>

   <?php
   $query = "SELECT item, description, cabinet, quantity, price 
             FROM inventory 
             WHERE MONTH(date_created) = $selectedMonth 
             AND YEAR(date_created) = $selectedYear 
             ORDER BY item ASC";
             
   $result = $conn->query($query);
   $grandTotal = 0;
   $rowCount = 0;

   if ($result && $result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           $total_value = (float)$row['quantity'] * (float)$row['price'];
           $grandTotal += $total_value;
           $rowCount++;
           
           $rowStyle = ($rowCount % 2 == 0) ? "CellDataAlt" : "CellData";

           echo "<Row ss:Height=\"25\">";
           echo "<Cell ss:StyleID=\"$rowStyle\"><Data ss:Type=\"String\">" . htmlspecialchars($row['item']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$rowStyle\"><Data ss:Type=\"String\">" . htmlspecialchars($row['description']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$rowStyle\"><Data ss:Type=\"String\">" . htmlspecialchars($row['cabinet']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$rowStyle\"><Data ss:Type=\"Number\">" . $row['quantity'] . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"Currency\"><Data ss:Type=\"Number\">" . $row['price'] . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"Currency\"><Data ss:Type=\"Number\">$total_value</Data></Cell>";
           echo "</Row>";
       }
   }
   ?>

   <Row ss:Height="35" ss:Index="<?= $rowCount + 6 ?>">
    <Cell ss:Index="5" ss:StyleID="TotalLabel"><Data ss:Type="String">GRAND TOTAL: </Data></Cell>
    <Cell ss:StyleID="TotalAmount"><Data ss:Type="Number"><?= $grandTotal ?></Data></Cell>
   </Row>
   
 

   <Row ss:Height="20">
    <Cell ss:MergeAcross="5"><Data ss:Type="String">Date Exported: <?= date('F d, Y | h:i A') ?></Data></Cell>
   </Row>

   <Row ss:Height="25" ss:Index="<?= $rowCount + 11 ?>">
    <Cell ss:StyleID="SignatureLine"><Data ss:Type="String">Prepared By: </Data></Cell>
    <Cell ss:Index="5" ss:MergeAcross="1" ss:StyleID="SignatureLine"><Data ss:Type="String">Verified / Approved By</Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>
<?php exit(); ?>