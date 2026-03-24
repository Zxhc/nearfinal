<div class="month-filter-container">
            <div class="month-tabs">
                <?php
                $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                foreach ($months as $index => $name): 
                    $mVal = $index + 1;
                    $activeClass = ($mVal == $selectedMonth) ? 'active' : '';
                ?>
        <a href="?month=<?= $mVal ?>&year=<?= $selectedYear ?>" class="month-tab <?= $activeClass ?>"><?= $name ?></a>
        <?php endforeach; ?>
    </div>
</div>