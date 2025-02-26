<?php
// manage_companies.php
session_start();
include '../auth/config.php';

// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /jobnepal/auth/login.php");
    exit;
}


// Fetch all companies
try {
    $sql = "SELECT
                c.id,
                c.name,
                c.company_website,
                c.company_description,
                u.email,
                u.created_at,
                c.logo,
                (SELECT COUNT(*) FROM jobs j WHERE j.company_id = c.id) AS job_count
            FROM companies c
            INNER JOIN users u ON c.user_id = u.id
            WHERE u.role = 'company'
            ORDER BY u.created_at DESC";

    $stmt = $pdo->query($sql);
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error fetching companies: ' . $e->getMessage()];
    error_log("Error fetching companies: " . $e->getMessage());
    $companies = [];
}

?>

<style>
    .company-card {
        width: 400px; /* Fixed width */
        border: 1px solid #ddd;
        border-radius: 10px;
        margin: 10px;
        padding: 20px;
        text-align: left;
        background-color: #f8f9fa;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
        display: flex; /* Use flexbox for layout */
        flex-direction: column; /* Stack items vertically */
        position: relative;
    }

    .company-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .company-card .company-logo {
        max-width: 80px;
        max-height: 80px;
        margin-bottom: 15px;
        border-radius: 8px;
        align-self: center; /* Center the logo */
    }

    .company-card h3 {
        color: #343a40;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .company-card p {
        color: #6c757d;
        margin-bottom: 8px;
    }

    .company-card strong {
        color: #495057;
    }

    .company-card .description {
        margin-top: 15px;
        font-style: italic;
        color: #777;
    }

    .company-card .delete-button {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 15px; /* Add spacing */
        align-self: flex-start;
    }

    .company-card .delete-button:hover {
        background-color: #c82333;
    }

    .message {
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
    }

    .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div style="width: 80%; margin: 20px; text-align: left;">
    <h1>Manage Companies</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?= htmlspecialchars($_SESSION['message']['type']) ?>">
            <?= htmlspecialchars($_SESSION['message']['text']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($companies)): ?>
        <p>No companies found.</p>
    <?php else: ?>
        <div style="display: flex; flex-wrap: wrap;">
            <?php foreach ($companies as $company): ?>
                <div class="company-card">

                    <?php if (!empty($company['logo'])): ?>
                        <img src="<?= htmlspecialchars($company['logo']) ?>" alt="Company Logo" class="company-logo">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($company['name']) ?></h3>

                    <p>
                        <strong>Email:</strong> <?= htmlspecialchars($company['email']) ?><br>
                        <strong>Website:</strong> <a href="<?= htmlspecialchars($company['company_website']) ?>" target="_blank"><?= htmlspecialchars($company['company_website']) ?></a><br>
                        <strong>Joined:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($company['created_at']))) ?><br>
                        <strong>Jobs Posted:</strong> <?= htmlspecialchars($company['job_count']) ?>
                    </p>

                    <?php if (!empty($company['company_description'])): ?>
                        <p class="description">
                            <strong>Description:</strong> <?= htmlspecialchars($company['company_description']) ?>
                        </p>
                    <?php endif; ?>

                    <form method="post" action="delete_company.php">
                        <input type="hidden" name="delete_company" value="<?= htmlspecialchars($company['id']) ?>">
                        <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this company and all associated jobs?')">
                            Delete Company
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>