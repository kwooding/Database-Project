<?php
require_once __DIR__ . '/config.php';

/* Simple KPI queries — only the first one changed */
$metrics = [
  'branches'  => $mysqli->query('SELECT COUNT(*) c FROM branchname')->fetch_assoc()['c'],
  'employees' => $mysqli->query('SELECT COUNT(*) c FROM EMPLOYEE')->fetch_assoc()['c'],
  'customers' => $mysqli->query('SELECT COUNT(*) c FROM CUSTOMER')->fetch_assoc()['c'],
  'accounts'  => $mysqli->query('SELECT COUNT(*) c FROM BANKACCOUNT')->fetch_assoc()['c'],
  'balance'   => $mysqli->query('SELECT FORMAT(SUM(BALANCE),2) s FROM BANKACCOUNT')->fetch_assoc()['s'],
  'loans'     => $mysqli->query('SELECT COUNT(*) c, FORMAT(SUM(Amount),2) s FROM LOANACCOUNT')->fetch_assoc(),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard Stats</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>body{background:#f6f8fb}</style>
</head>
<body class="p-4">

  <h1 class="mb-4">Bank Dashboard</h1>

  <div class="row g-3">
    <div class="col-sm-6 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3><?= $metrics['branches'] ?></h3>
          <p class="mb-0">Branches</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3><?= $metrics['employees'] ?></h3>
          <p class="mb-0">Employees</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3><?= $metrics['customers'] ?></h3>
          <p class="mb-0">Customers</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3><?= $metrics['accounts'] ?></h3>
          <p class="mb-0">Accounts</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3>$<?= $metrics['balance'] ?></h3>
          <p class="mb-0">Total Balance</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3><?= $metrics['loans']['c'] ?></h3>
          <p class="mb-1">Loans</p>
          <small class="text-muted">$<?= $metrics['loans']['s'] ?> outstanding</small>
        </div>
      </div>
    </div>
  </div>

  <p class="mt-4"><a href="index.php">« Back to Home</a></p>
</body>
</html>
