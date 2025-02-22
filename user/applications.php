<!-- applications.php -->
<section class="applications-list">
    <h2 class="section-title">My Applications</h2>
    <div class="job-list">
        <?php foreach ($applications as $application): ?>
        <div class="job-card">
            <div>
                <h3><?= htmlspecialchars($application['job_title']) ?></h3>
                <p><?= htmlspecialchars($application['company']) ?></p>
                <p>Status: <?= htmlspecialchars($application['status']) ?></p>
            </div>
            <span class="application-date"><?= htmlspecialchars($application['date']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>