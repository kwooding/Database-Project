<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require_once __DIR__ . '/config.php';

/* ── employees per branch ───────────────────────────────────────────── */
$sqlEmp = $mysqli->query("
    SELECT bn.Branchid,
           bn.Branchname,
           COUNT(e.SSN)          AS emp_cnt,
           GROUP_CONCAT(e.Fullname SEPARATOR ', ') AS emp_list
      FROM branchname bn
 LEFT JOIN employee    e  ON e.Branchid = bn.Branchid
  GROUP BY bn.Branchid
  ORDER BY bn.Branchid
");

/* ── customers per branch (via their personal banker’s branch) ─────── */
$sqlCust = $mysqli->query("
    SELECT bn.Branchid,
           COUNT(c.SSN)          AS cust_cnt,
           GROUP_CONCAT(c.Fullname SEPARATOR ', ') AS cust_list
      FROM branchname bn
 LEFT JOIN employee        pb ON pb.Branchid = bn.Branchid        -- personal banker
 LEFT JOIN customer         c ON c.PersonalbankerSSN = pb.SSN
  GROUP BY bn.Branchid
  ORDER BY bn.Branchid
");

/* combine into one array keyed by Branchid */
$data = [];
while ($r = $sqlEmp->fetch_assoc()) {
    $data[$r['Branchid']] = [
        'name'     => $r['Branchname'],
        'emp_cnt'  => $r['emp_cnt'],
        'emp_list' => $r['emp_list'],
        'cust_cnt' => 0,
        'cust_list'=> ''
    ];
}
while ($r = $sqlCust->fetch_assoc()) {
    if (!isset($data[$r['Branchid']])) $data[$r['Branchid']] = ['name'=>'(unknown)'];
    $data[$r['Branchid']]['cust_cnt']  = $r['cust_cnt'];
    $data[$r['Branchid']]['cust_list'] = $r['cust_list'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Branch Overview</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>table{border-collapse:collapse}td,th{border:1px solid #ccc;padding:6px}</style>
</head>
<body class="container py-4">
  <h1 class="mb-4">Employees &amp; Customers per Branch</h1>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>ID</th><th>Name</th>
        <th># Employees</th><th>Employees</th>
        <th># Customers</th><th>Customers</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $bid => $row): ?>
        <tr>
          <td><?= $bid ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= $row['emp_cnt'] ?></td>
          <td><?= htmlspecialchars($row['emp_list'] ?: '—') ?></td>
          <td><?= $row['cust_cnt'] ?></td>
          <td><?= htmlspecialchars($row['cust_list'] ?: '—') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <p><a href="index.php">« Back to Home</a></p>
</body>
</html>
