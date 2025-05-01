<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/config.php';

/* ── Phase III Queries ────────────────────────────────────────────────── */

/* 1) Employees per branch */
$q1 = $mysqli->query("
    SELECT Branchid, COUNT(*) AS num_emp
      FROM EMPLOYEE
     GROUP BY Branchid
") or die('Query1 Error: '.htmlspecialchars($mysqli->error));

/* 2) Branches with >1 employee */
$q2 = $mysqli->query("
    SELECT Branchid, COUNT(*) AS num_emp
      FROM EMPLOYEE
     GROUP BY Branchid
    HAVING COUNT(*) > 1
") or die('Query2 Error: '.htmlspecialchars($mysqli->error));

/* 3) Branch with the most assets — updated for split tables */
$q3 = $mysqli->query("
    SELECT bn.Branchid, bn.Branchname, ba.Assets
      FROM branchname   AS bn
      JOIN branchassets AS ba ON ba.Branchid = bn.Branchid
     WHERE ba.Assets = (SELECT MAX(Assets) FROM branchassets)
") or die('Query3 Error: '.htmlspecialchars($mysqli->error));

/* 4) Customer with the largest individual account balance */
$q4 = $mysqli->query("
    SELECT C.SSN, C.Fullname
      FROM CUSTOMER C
     WHERE C.SSN IN (
           SELECT O.CustomerSSN
             FROM OPENACCOUNT O
             JOIN BANKACCOUNT B ON B.Accountnumber = O.Accountnumber
            WHERE B.BALANCE = (SELECT MAX(BALANCE) FROM BANKACCOUNT)
     )
") or die('Query4 Error: '.htmlspecialchars($mysqli->error));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Phase III Queries</title>
  <style>table{border-collapse:collapse}td,th{border:1px solid #ccc;padding:4px}</style>
</head>
<body>
  <h1>Phase III Queries</h1>

  <h2>1) Employees per branch</h2>
  <table>
    <tr><th>Branch ID</th><th># Employees</th></tr>
    <?php while ($r = $q1->fetch_assoc()): ?>
      <tr><td><?= $r['Branchid'] ?></td><td><?= $r['num_emp'] ?></td></tr>
    <?php endwhile; ?>
  </table>

  <h2>2) Branches with more than one employee</h2>
  <table>
    <tr><th>Branch ID</th><th># Employees</th></tr>
    <?php while ($r = $q2->fetch_assoc()): ?>
      <tr><td><?= $r['Branchid'] ?></td><td><?= $r['num_emp'] ?></td></tr>
    <?php endwhile; ?>
  </table>

  <h2>3) Branch with the most assets</h2>
  <table>
    <tr><th>ID</th><th>Name</th><th>Assets</th></tr>
    <?php while ($r = $q3->fetch_assoc()): ?>
      <tr>
        <td><?= $r['Branchid']   ?></td>
        <td><?= $r['Branchname'] ?></td>
        <td><?= $r['Assets']     ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h2>4) Customer holding the single largest account balance</h2>
  <table>
    <tr><th>SSN</th><th>Name</th></tr>
    <?php while ($r = $q4->fetch_assoc()): ?>
      <tr><td><?= $r['SSN'] ?></td><td><?= $r['Fullname'] ?></td></tr>
    <?php endwhile; ?>
  </table>

  <p><a href="index.php">« Back to Home</a></p>
</body>
</html>
