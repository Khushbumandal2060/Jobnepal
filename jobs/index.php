<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/top.css" rel="stylesheet">
    <style>
        .job-card {
            transition: transform 0.2s;
            border-left: 4px solid transparent;
        }
        .job-card:hover {
            transform: translateY(-3px);
            border-left-color: #0d6efd;
        }
        .urgent-badge {
            background: #dc3545;
            color: white;
        }
        .featured-badge {
            background: #ffc107;
            color: black;
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 8px;
            position: sticky;
            top: 20px;
        }
        .company-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .salary-range {
            color: #28a745;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include '../includes/header2.php'; ?>

    <main class="container my-5">
        <div class="row g-4">
            <!-- Filters Column -->
            <div class="col-lg-3">
                <div class="filter-section p-3">
                    <h5 class="mb-3">Filter Jobs</h5>

                    <div class="mb-4">
                        <h6>Job Type</h6>
                        <?php
                        $jobTypes = [
                            'Full-time' => 2345,
                            'Part-time' => 876,
                            'Contract' => 543,
                            'Remote' => 1567
                        ];
                        
                        foreach ($jobTypes as $type => $count) {
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' id='type-$type'>
                                    <label class='form-check-label small' for='type-$type'>$type ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="mb-4">
                        <h6>Experience Level</h6>
                        <?php
                        $experienceLevels = [
                            'Entry Level' => 945,
                            'Mid Level' => 1567,
                            'Senior Level' => 723,
                            'Executive' => 189
                        ];
                        
                        foreach ($experienceLevels as $level => $count) {
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' id='level-$level'>
                                    <label class='form-check-label small' for='level-$level'>$level ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="mb-4">
                        <h6>Salary Range</h6>
                        <?php
                        $salaryRanges = [
                            '$30k - $50k' => 845,
                            '$50k - $80k' => 1345,
                            '$80k - $120k' => 967,
                            '$120k+' => 345
                        ];
                        
                        foreach ($salaryRanges as $range => $count) {
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' id='range-$range'>
                                    <label class='form-check-label small' for='range-$range'>$range ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Job Listings -->
            <div class="col-lg-9">
                <div class="row row-cols-1 g-4">
                    <?php
                    $jobs = [
                        [
                            'title' => 'Senior Software Engineer',
                            'company' => 'Tech Innovators Inc',
                            'location' => 'New York, NY',
                            'type' => 'Full-time',
                            'salary' => '$120,000 - $150,000',
                            'experience' => '5+ years',
                            'logo' => 'https://placehold.co/100x100',
                            'urgent' => true,
                            'posted' => '2d ago',
                            'description' => 'Lead development of next-gen cloud platforms. Requires expertise in AWS and microservices architecture.'
                        ],
                        [
                            'title' => 'Marketing Manager',
                            'company' => 'Digital Solutions Co',
                            'location' => 'Remote',
                            'type' => 'Contract',
                            'salary' => '$80,000 - $100,000',
                            'experience' => '3-5 years',
                            'logo' => 'https://placehold.co/100x100',
                            'featured' => true,
                            'posted' => '1d ago',
                            'description' => 'Drive digital marketing strategies for global clients. SEO/SEM experience required.'
                        ],
                        [
                            'title' => 'Data Analyst',
                            'company' => 'InfoTech Systems',
                            'location' => 'Chicago, IL',
                            'type' => 'Full-time',
                            'salary' => '$65,000 - $85,000',
                            'experience' => '2+ years',
                            'logo' => 'https://placehold.co/100x100',
                            'posted' => '3d ago',
                            'description' => 'Analyze business metrics and create data visualization reports. SQL/Python proficiency required.'
                        ]
                    ];

                    foreach ($jobs as $job) {
                        echo '
                        <div class="col">
                            <div class="card job-card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex gap-3">
                                        <img src="'.$job['logo'].'" class="company-logo" alt="'.$job['company'].' logo">
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="mb-1">'.$job['title'].'</h5>
                                                    <p class="mb-1 text-muted">'.$job['company'].' • '.$job['location'].'</p>
                                                </div>
                                                <div class="text-end">
                                                    '.((isset($job['urgent'])) ? '<span class="badge urgent-badge me-1">Urgent</span>' : '').'
                                                    '.((isset($job['featured'])) ? '<span class="badge featured-badge">Featured</span>' : '').'
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <span class="badge bg-primary">'.$job['type'].'</span>
                                                <span class="badge bg-secondary">'.$job['experience'].' Experience</span>
                                                <span class="salary-range">'.$job['salary'].'</span>
                                            </div>
                                            
                                            <p class="text-muted mb-3">'.$job['description'].'</p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Posted '.$job['posted'].'</small>
                                                <div class="btn-group">
                                                    <a href="#" class="btn btn-sm btn-outline-primary">Save Job</a>
                                                    <a href="#" class="btn btn-sm btn-primary">Apply Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>