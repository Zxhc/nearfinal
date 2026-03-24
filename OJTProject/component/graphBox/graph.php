<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$m = $_SESSION['selected_month'] ?? (int)date('n');
$y = $_SESSION['selected_year'] ?? (int)date('Y');
$view = $_GET['view'] ?? 'weekly';


if ($view === 'weekly') {
    $whereClause = "WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)";
} else {
    $whereClause = "WHERE MONTH(date) = $m AND YEAR(date) = $y";
};

$chartSql = "SELECT 
                item, 
                description, 
                DATE(date) as log_date, 
                DAYNAME(date) as day_name, 
                COUNT(*) as usage_count, 
                WEEKDAY(date) as day_index
             FROM history 
             $whereClause
             GROUP BY item, description, log_date, day_name, day_index 
             ORDER BY log_date ASC";

$chartResult = $conn->query($chartSql);
$allChartData = [];

if ($chartResult) {
    while ($row = $chartResult->fetch_assoc()) {
        $itemName = $row['item'];
        
        if (!isset($allChartData[$itemName])) {
            $allChartData[$itemName] = [
                'desc' => $row['description'] ?? 'No description available.',
                'stats' => ($view === 'weekly') ? array_fill(0, 7, 0) : []
            ];
        }

        if ($view === 'weekly') {
            $allChartData[$itemName]['stats'][$row['day_index']] = (int)$row['usage_count'];
        } else {
            $allChartData[$itemName]['stats'][$row['log_date']] = (int)$row['usage_count'];
        }
    }
}
?>

<script>
    const allChartData = <?php echo json_encode($allChartData); ?>;
    const currentView = "<?php echo $view; ?>";
</script>

<div class="box-content box-4" id="box-graph">
    <div class="content-container" id="graph-content" style="height: 100%;">
        
        <div class="graph-header" style="display: flex; justify-content: space-between; align-items: center; padding: 10px;">
            <h2 style="margin: 0; font-size: 1.2rem;">Usage per Item (<?= date('F', mktime(0, 0, 0, $m, 10)) ?> <?= $y ?>)</h2>
            <div class="chart-toggle">
                <a href="?view=weekly&month=<?= $m ?>&year=<?= $y ?>" class="<?= ($view === 'weekly') ? 'active' : '' ?>">Weekly</a>
                <a href="?view=monthly&month=<?= $m ?>&year=<?= $y ?>" class="<?= ($view === 'monthly') ? 'active' : '' ?>">Monthly</a>
            </div>
        </div>

        <div id="chartsWrapper" style="padding: 15px;">
            <?php if (empty($allChartData)): ?>
                <div class="no-data-msg" style="text-align: center; padding: 40px; width: 100%;">
                    <p style="color: gray;">No usage history found for <?= date('F Y', mktime(0, 0, 0, $m, 10)) ?>.</p>
                </div>
          
            <?php endif; ?>
        </div>
    </div>
</div>