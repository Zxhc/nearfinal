<div class="history-header">
    <h2 style="display: flex; align-items: center; gap: 10px; margin: 0;">
        <?php if ($pendingCount > 0): ?>
            <span class="material-symbols-outlined bell-shake">notifications_active</span>
                <?php else: ?>
                    <span class="material-symbols-outlined" style="color: #22c55e;">notifications</span>
                <?php endif; ?>
                    
                <span class = "mobile-header-view">Inventory Alerts</span>

                <?php if ($pendingCount > 0): ?>
                    <span class="status-badge pending-badge"><?= $pendingCount ?> PENDING</span>
                <?php else: ?>
                    <span class="status-badge clear-badge">HEALTHY</span>
                <?php endif; ?>
                </h2>
                    <button type="button" class="excel-btn hide-on-mobile" onclick="openPRModal()" 
        <?= ($pendingCount > 0) ? '' : 'disabled' ?>>
        <span class="material-symbols-outlined">check_circle</span> 
        <span>Resolve Selected</span>
    </button>
</div>
            