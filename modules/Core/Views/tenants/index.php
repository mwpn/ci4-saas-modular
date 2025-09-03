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
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table {
            font-size: 0.9rem;
        }

        .badge {
            font-size: 0.75rem;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0"><?= $title ?></h2>
                    <a href="/tenants/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Tenant
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?= $stats['total'] ?></h5>
                                <p class="card-text text-muted">Total Tenants</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-success"><?= $stats['active'] ?></h5>
                                <p class="card-text text-muted">Aktif</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-warning"><?= $stats['inactive'] ?></h5>
                                <p class="card-text text-muted">Tidak Aktif</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-danger"><?= $stats['suspended'] ?></h5>
                                <p class="card-text text-muted">Ditangguhkan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tenants Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daftar Tenants</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th>Domain</th>
                                        <th>Status</th>
                                        <th>Dibuat</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tenants)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada tenant
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($tenants as $tenant): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-medium"><?= esc($tenant->name) ?></div>
                                                </td>
                                                <td>
                                                    <code><?= esc($tenant->slug) ?></code>
                                                </td>
                                                <td>
                                                    <?= $tenant->domain ? esc($tenant->domain) : '-' ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = match ($tenant->status) {
                                                        'active' => 'success',
                                                        'inactive' => 'warning',
                                                        'suspended' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>">
                                                        <?= ucfirst($tenant->status) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($tenant->created_at)) ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="/tenants/show/<?= $tenant->id ?>" class="btn btn-outline-primary btn-sm">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="/tenants/edit/<?= $tenant->id ?>" class="btn btn-outline-secondary btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <?php if ($tenant->status === 'active'): ?>
                                                            <a href="/tenants/deactivate/<?= $tenant->id ?>" class="btn btn-outline-warning btn-sm" onclick="return confirm('Nonaktifkan tenant?')">
                                                                <i class="bi bi-pause"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="/tenants/activate/<?= $tenant->id ?>" class="btn btn-outline-success btn-sm" onclick="return confirm('Aktifkan tenant?')">
                                                                <i class="bi bi-play"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="/tenants/delete/<?= $tenant->id ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus tenant?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>