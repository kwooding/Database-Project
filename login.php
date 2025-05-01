<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ssn = $_POST['ssn'] ?? '';
    $stmt = $mysqli->prepare("
        SELECT Fullname, Phone, Startdate, Branchid
          FROM EMPLOYEE
         WHERE SSN = ?
    ");
    $stmt->bind_param("s", $ssn);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $emp = $res->fetch_assoc();
        $message = "Found: {$emp['Fullname']} (Phone: {$emp['Phone']}, Started: {$emp['Startdate']}, Branch: {$emp['Branchid']})";
    } else {
        $message = "No employee with SSN “" . htmlspecialchars($ssn) . "” found.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Employee Lookup</title></head>
<body>
  <h1>Lookup Employee by SSN</h1>
  <form method="post">
    <label>SSN: <input type="text" name="ssn" maxlength="9" required></label>
    <button type="submit">Search</button>
  </form>
  <?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <p><a href="index.php">« Back to Home</a></p>
</body>
</html>
