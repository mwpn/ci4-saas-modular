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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tenant-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .tenant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .tenant-card.selected {
            border: 2px solid #667eea;
            background: #f8f9ff;
        }

        .tenant-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 600;
            margin: 0 auto 15px;
        }

        .tenant-name {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .tenant-domain {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .tenant-status {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 800px;
            width: 100%;
            margin: 20px;
        }

        .page-title {
            text-align: center;
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-subtitle {
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 40px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <h1 class="page-title">Pilih Tenant</h1>
        <p class="page-subtitle">Silakan pilih tenant yang ingin Anda akses</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($tenants)): ?>
            <div class="text-center py-5">
                <div class="tenant-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h4 class="text-muted">Tidak ada tenant yang tersedia</h4>
                <p class="text-muted">Silakan hubungi administrator untuk membuat tenant baru.</p>
            </div>
        <?php else: ?>
            <form id="tenantForm" method="POST" action="<?= base_url('onboarding/set-tenant') ?>">
                <div class="row g-4">
                    <?php foreach ($tenants as $tenant): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="tenant-card card h-100 p-4" onclick="selectTenant(<?= $tenant->id ?>)">
                                <div class="card-body text-center">
                                    <div class="tenant-icon">
                                        <?= strtoupper(substr($tenant->name, 0, 2)) ?>
                                    </div>
                                    <h5 class="tenant-name"><?= esc($tenant->name) ?></h5>
                                    <p class="tenant-domain"><?= esc($tenant->domain) ?></p>
                                    <span class="tenant-status status-active">Aktif</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="tenant_id" id="selectedTenantId">

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" id="selectBtn" disabled>
                        Pilih Tenant
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedTenantId = null;

        function selectTenant(tenantId) {
            // Remove previous selection
            document.querySelectorAll('.tenant-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selection to clicked card
            event.currentTarget.classList.add('selected');

            // Set selected tenant
            selectedTenantId = tenantId;
            document.getElementById('selectedTenantId').value = tenantId;
            document.getElementById('selectBtn').disabled = false;
        }
    </script>
</body>

</html>