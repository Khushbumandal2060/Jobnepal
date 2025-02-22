<!-- profile.php -->
<section class="profile-details">
    <h2 class="section-title">My Profile</h2>
    <div class="profile-info">
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <!-- Add more profile details here -->
    </div>
    <a href="#" class="btn">Edit Profile</a>
</section>