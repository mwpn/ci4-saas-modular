<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fa;
        }

        .main-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px 0;
        }

        .page-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            border: none;
            padding: 20px;
        }

        .card-title {
            margin: 0;
            font-weight: 600;
        }

        .info-item {
            padding: 15px 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .info-value {
            color: #6c757d;
        }

        .badge {
            font-size: 0.75rem;
            padding: 6px 12px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="main-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title"><?= $title ?></h1>
                        <div class="btn-group">
                            <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                            <a href="<?= base_url('users/edit/' . $user->id) ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit User
                            </a>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">User Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-item">
                                        <div class="info-label">ID</div>
                                        <div class="info-value"><?= $user->id ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Full Name</div>
                                        <div class="info-value"><?= esc($user->name) ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Email Address</div>
                                        <div class="info-value"><?= esc($user->email) ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Phone Number</div>
                                        <div class="info-value"><?= esc($user->phone ?? 'Not provided') ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Status</div>
                                        <div class="info-value">
                                            <?php
                                            $statusClass = match ($user->status) {
                                                'active' => 'bg-success',
                                                'inactive' => 'bg-secondary',
                                                'suspended' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            $statusText = ucfirst($user->status);
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Created At</div>
                                        <div class="info-value"><?= date('d M Y H:i', strtotime($user->created_at)) ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Updated At</div>
                                        <div class="info-value"><?= date('d M Y H:i', strtotime($user->updated_at)) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>

</html>