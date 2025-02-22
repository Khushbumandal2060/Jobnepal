<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/top.css" rel="stylesheet">
    <style>
        .company-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .company-card:hover {
            transform: translateY(-5px);
        }
        .company-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .revenue-badge {
            background: #e9ecef;
            padding: 0.35em 0.65em;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            position: sticky;
            top: 20px;
        }
        @media (max-width: 768px) {
            .company-img {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header2.php'; ?>

    <div class="container-fluid my-4">
        <?php include '../includes/tophiring.php'; ?>
    </div>

    <main class="container my-5">
        <div class="row g-4">
            <!-- Filters Column -->
            <div class="col-lg-3">
                <div class="filter-section">
                    <h5 class="mb-4">All Filters</h5>
                    
                    <div class="filter-group mb-4">
                        <h6>Company Type</h6>
                        <?php
                        $companyTypes = [
                            'Corporate' => 4052,
                            'Foreign MRC' => 1450,
                            'Indian MRC' => 512,
                            'Sterling' => 554
                        ];
                        
                        foreach ($companyTypes as $type => $count) {
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' id='type-$type'>
                                    <label class='form-check-label small' for='type-$type'>$type ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="filter-group mb-4">
                        <h6>Location</h6>
                        <?php
                        $locations = [
                            'Bengaluru' => 3174,
                            'Delhi / NCG' => 3168,
                            'Mumbai' => 2766,
                            'Hyderabad' => 2204
                        ];
                        
                        foreach ($locations as $location => $count) {
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' id='loc-$location'>
                                    <label class='form-check-label small' for='loc-$location'>$location ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="filter-group mb-4">
                        <h6>Industry</h6>
                        <?php
                        $industries = [
                            'IT/Networking' => 3174,
                            'Marketing' => 3168,
                            'Automotive' => 2766,
                            'Engineering' => 2204
                        ];
                        
                        foreach ($industries as $industry => $count) {
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' id='ind-$industry'>
                                    <label class='form-check-label small' for='ind-$industry'>$industry ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Company Listings -->
            <div class="col-lg-9">
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php
                    $companies = [
                        [
                            'name' => 'Jayem Automotive',
                            'image' => 'https://placehold.co/150x150',
                            'location' => 'New York, USA',
                            'industry' => 'Automotive',
                            'highlights' => ['2.7% revenue growth', '500+ employees'],
                            'revenue' => '2.7% revenue growth'
                        ],
                        [
                            'name' => 'Jayem Engineering',
                            'image' => 'https://placehold.co/150x150',
                            'location' => 'San Francisco, USA',
                            'industry' => 'Engineering & Construction',
                            'highlights' => ['3.5% revenue growth', '48% market share'],
                            'revenue' => '3.5% revenue growth'
                        ],
                        [
                            'name' => 'Buddy Study',
                            'image' => 'https://placehold.co/150x150',
                            'location' => 'London, UK',
                            'industry' => 'Education',
                            'highlights' => ['27 min response time', 'E-learning platform'],
                            'revenue' => '27 min response time'
                        ],
                        [
                            'name' => 'Sterling Solutions',
                            'image' => 'https://placehold.co/150x150',
                            'location' => 'Berlin, Germany',
                            'industry' => 'IT Services',
                            'highlights' => ['5K+ employees', 'Global network'],
                            'revenue' => '48% market share'
                        ]
                    ];

                    foreach ($companies as $company) {
                        echo '
                        <div class="col">
                            <div class="card company-card h-100 shadow-sm">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="'.$company['image'].'" class="img-fluid rounded-start company-img" alt="'.$company['name'].'">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">'.$company['name'].'</h5>
                                            <div class="d-flex gap-2 mb-3">
                                                <span class="revenue-badge">'.$company['revenue'].'</span>
                                            </div>
                                            <p class="card-text small mb-1">
                                                <strong>Location:</strong> '.$company['location'].'<br>
                                                <strong>Industry:</strong> '.$company['industry'].'
                                            </p>
                                            <ul class="list-unstyled small text-muted mb-3">
                                                '.implode('', array_map(function($h) { return "<li>$h</li>"; }, $company['highlights'])).'
                                            </ul>
                                            <a href="#" class="btn btn-primary btn-sm">View Jobs</a>
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