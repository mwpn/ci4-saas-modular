<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['total_users'] ?? 0 ?></div>
                <div class="stats-label">Total Users</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['active_users'] ?? 0 ?></div>
                <div class="stats-label">Active Users</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['total_tenants'] ?? 0 ?></div>
                <div class="stats-label">Total Tenants</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-number"><?= $stats['revenue'] ?? 0 ?></div>
                <div class="stats-label">Revenue</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activity -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_activities)): ?>
                                <?php foreach ($recent_activities as $activity): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                    <?= strtoupper(substr($activity['user_name'], 0, 1)) ?>
                                                </div>
                                                <?= $activity['user_name'] ?>
                                            </div>
                                        </td>
                                        <td><?= $activity['action'] ?></td>
                                        <td><?= date('M j, Y H:i', strtotime($activity['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $activity['status'] === 'success' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($activity['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent activity</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Add New User
                    </a>
                    <a href="<?= base_url('tenants/create') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-building-add me-2"></i>Add New Tenant
                    </a>
                    <a href="<?= base_url('settings') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-gear me-2"></i>System Settings
                    </a>
                    <a href="<?= base_url('auth/profile') ?>" class="btn btn-outline-info">
                        <i class="bi bi-person me-2"></i>My Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- System Status -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">System Status</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-success">Online</div>
                            <small class="text-muted">Server Status</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-info">99.9%</div>
                            <small class="text-muted">Uptime</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant Info -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tenant Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-primary"><?= TenantContext::getTenantName() ?? 'N/A' ?></div>
                            <small class="text-muted">Current Tenant</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-warning"><?= date('M Y') ?></div>
                            <small class="text-muted">Billing Period</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>