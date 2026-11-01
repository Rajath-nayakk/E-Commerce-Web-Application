<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Error | NovaTech Gear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #0f172a; color: #f8fafc; font-family: system-ui, -apple-system, sans-serif; }
        .error-card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="error-card p-4 p-md-5 shadow-lg">
                    <div class="d-flex align-items-center gap-3 mb-4 text-danger">
                        <i class="bi bi-database-fill-x display-4"></i>
                        <div>
                            <h2 class="h3 fw-bold m-0 text-white">Database Connection Failed</h2>
                            <span class="small text-danger fw-semibold">MySQL Server Unreachable</span>
                        </div>
                    </div>

                    <div class="alert alert-dark bg-black border-secondary text-warning p-3 rounded-3 mb-4 font-monospace small">
                        <strong>Error Details:</strong><br>
                        <?= htmlspecialchars($errorMessage ?? 'SQLSTATE[HY000] [2002] Connection refused', ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <h5 class="fw-bold text-white mb-3">Troubleshooting Checklist:</h5>
                    <div class="list-group list-group-flush mb-4 small">
                        <div class="list-group-item bg-transparent text-light border-secondary px-0">
                            <strong>1. Start MySQL:</strong> If using XAMPP/WAMP, start the <strong>MySQL</strong> module from the control panel.
                        </div>
                        <div class="list-group-item bg-transparent text-light border-secondary px-0">
                            <strong>2. Check Database:</strong> Ensure <code><?= htmlspecialchars($dbName ?? 'ecommerce_db', ENT_QUOTES, 'UTF-8') ?></code> exists. Run <code>php database/migrate.php</code> in terminal to auto-create and seed tables.
                        </div>
                        <div class="list-group-item bg-transparent text-light border-secondary px-0">
                            <strong>3. Verify .env:</strong> Check <code>DB_HOST=<?= htmlspecialchars($dbHost ?? '127.0.0.1', ENT_QUOTES, 'UTF-8') ?></code>, <code>DB_PORT=<?= htmlspecialchars((string)($dbPort ?? 3306), ENT_QUOTES, 'UTF-8') ?></code>, and <code>DB_USER</code> in your <code>.env</code> file.
                        </div>
                        <div class="list-group-item bg-transparent text-light border-secondary px-0">
                            <strong>4. Explicit SQLite Mode:</strong> If you explicitly wish to use SQLite for standalone testing without MySQL, set <code>DB_DRIVER=sqlite</code> in <code>.env</code>.
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="/" class="btn btn-primary px-4 rounded-pill">
                            <i class="bi bi-arrow-clockwise me-1"></i>Retry Connection
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
