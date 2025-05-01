<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'config.php';

/* ── NEW QUERY ──────────────────────────────────────────────────────────
   We now pull data from the five-table branch design:
      branchname   (bn)  ← primary key
      branchassets (ba)
      branchlocation (bl)
   ───────────────────────────────────────────────────────────────────── */
$sql = "
  SELECT bn.Branchid ,
         bn.Branchname ,
         ba.Assets ,
         bl.Address
    FROM branchname   AS bn
    JOIN branchassets AS ba ON ba.Branchid = bn.Branchid
    JOIN branchlocation bl ON bl.Branchid = bn.Branchid
  ORDER BY bn.Branchid
";
$result = $mysqli->query($sql);
if (!$result) {
    die('Query Error: ' . htmlspecialchars($mysqli->error));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Branches</title>
</head>
<body>
  <h1>All Branches</h1>
  <table border="1" cellpadding="5">
    <tr><th>ID</th><th>Name</th><th>Assets</th><th>Address</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['Branchid'])   ?></td>
        <td><?= htmlspecialchars($row['Branchname']) ?></td>
        <td><?= htmlspecialchars($row['Assets'])     ?></td>
        <td><?= htmlspecialchars($row['Address'])    ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <p><a href="index.php">« Back to Home</a></p>
</body>
</html>
