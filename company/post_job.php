<!-- post_job.php -->
<section class="post-job-form">
    <h2 class="section-title">Post a New Job</h2>
    <form action="process_job_posting.php" method="post">
        <div class="form-group">
            <label for="title">Job Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Job Description:</label>
            <textarea id="description" name="description" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
        </div>
        <div class="form-group">
            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary">
        </div>
        <input type="hidden" name="company_id" value="<?= htmlspecialchars($company_id) ?>">
        <button type="submit" class="btn">Post Job</button>
    </form>
</section>