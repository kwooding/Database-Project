<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require_once __DIR__ . '/config.php';

/* ─── Pull every account a customer holds, with type-specific details ─── */
$sql = $mysqli->query("
   SELECT c.SSN,
          c.Fullname,
          b.Accountnumber,
          FORMAT(b.BALANCE,2) AS Balance,
          CASE
              WHEN s.Accountnumber IS NOT NULL  THEN 'Savings'
              WHEN ch.Accountnumber IS NOT NULL THEN 'Checking'
              WHEN mm.Accountnumber IS NOT NULL THEN 'Money-Market'
              ELSE '—'
          END AS AcctType,
          COALESCE(s.Interestrate, mm.Varinterestrate) AS Rate,
          ch.Overdraft AS Overdraft
     FROM customer        c
     JOIN openaccount     o  ON o.CustomerSSN = c.SSN
     JOIN bankaccount     b  ON b.Accountnumber = o.Accountnumber
LEFT JOIN savings        s  ON s.Accountnumber  = b.Accountnumber
LEFT JOIN checking       ch ON ch.Accountnumber = b.Accountnumber
LEFT JOIN moneymarket    mm ON mm.Accountnumber = b.Accountnumber
 ORDER BY c.Fullname, b.Accountnumber
");

/* simple helper */
function redact_ssn(string $ssn): string {
    // keep last 4 digits
    $tail = substr($ssn, -4);
    return '***-**-' . $tail;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Accounts</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    table{border-collapse:collapse}
    td,th{border:1px solid #ccc;padding:6px}
  </style>
</head>
<body class="container py-4">
  <h1 class="mb-4">Accounts Held by Customers</h1>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Customer</th><th>SSN</th>
        <th>Account #</th><th>Type</th><th>Balance</th>
        <th>Rate&nbsp;%</th><th>Overdraft</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($r = $sql->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($r['Fullname']) ?></td>
          <td><?= redact_ssn($r['SSN'])            ?></td>
          <td><?= $r['Accountnumber']               ?></td>
          <td><?= $r['AcctType']                    ?></td>
          <td>$<?= $r['Balance']                    ?></td>
          <td><?= $r['Rate']      !== null ? $r['Rate']      : '—' ?></td>
          <td><?= $r['Overdraft'] !== null ? $r['Overdraft'] : '—' ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <p><a href="index.php">« Back to Home</a></p>
</body>
</html>
