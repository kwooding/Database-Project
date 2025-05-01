<?php
require_once __DIR__ . '/config.php';
$db_ok = isset($mysqli) && $mysqli->ping();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>TermProject │ Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      background:#6e6e6e url('PATH_TO_EMBLEM_IMAGE') no-repeat center center fixed;
      background-size:320px;color:#000
    }
    .navbar-dark{background:#343434;border-bottom:1px solid #444}
    .card-main{
      background:#fff;border:1px solid #d0d0d0;
      box-shadow:0 2px 4px rgba(0,0,0,.08);
      transition:transform .2s;width:100%;max-width:260px;min-height:260px
    }
    .card-main:hover{transform:translateY(-6px)}
    .emoji{font-size:48px}
  </style>
</head>
<body>

<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Term Project</span>
    <?php if(!$db_ok): ?>
      <span class="badge bg-danger">DB offline</span>
    <?php endif; ?>
  </div>
</nav>

<div class="container py-5 text-center">
  <h1 class="text-light mb-5">Welcome!</h1>

  <div class="row g-4 justify-content-center">

    <div class="col-auto">
      <a href="branches.php">
        <div class="card card-main d-flex flex-column align-items-center justify-content-center">
          <span class="emoji">🏢</span>
          <h5>View All Branches</h5>
        </div>
      </a>
    </div>

    <div class="col-auto">
      <a href="lookup_employee.php">
        <div class="card card-main d-flex flex-column align-items-center justify-content-center">
          <span class="emoji">🔍</span>
          <h5>Lookup Records</h5>
        </div>
      </a>
    </div>

    <div class="col-auto">
      <a href="lookup_customer.php">
        <div class="card card-main d-flex flex-column align-items-center justify-content-center">
          <span class="emoji">🧑‍💼</span>
          <h5>Lookup Customer</h5>
        </div>
      </a>
    </div>

    <!-- NEW CARD: Branch overview -->
    <div class="col-auto">
      <a href="branch_overview.php">
        <div class="card card-main d-flex flex-column align-items-center justify-content-center">
          <span class="emoji">👥</span>
          <h5>Branch Overview</h5>
        </div>
      </a>
    </div>

    <!-- NEW CARD: Customer accounts -->
    <div class="col-auto">
      <a href="customer_accounts.php">
        <div class="card card-main d-flex flex-column align-items-center justify-content-center">
          <span class="emoji">💼</span>
          <h5>Customer Accounts</h5>
        </div>
      </a>
    </div>

    <div class="col-auto">
      <a href="queries.php">
        <div class="card card-main d-flex flex-column align-items-center justify-content-center">
          <span class="emoji">📑</span>
          <h5>Phase III Queries</h5>
        </div>
      </a>
    </div>

    <div class="col-auto">
      <a href="add_customer.php">
        <div class="card card-main d-flex flex-column align-items-center justify-content-center">
          <span class="emoji">➕</span>
          <h5>Add Customer / Account</h5>
        </div>
      </a>
    </div>

    <div class="col-auto">
      <a href="dashboard.php">
        <div class="card card-main d-flex flex-column align-items-center justify-content-center">
          <span class="emoji">📊</span>
          <h5>Dashboard Stats</h5>
        </div>
      </a>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
