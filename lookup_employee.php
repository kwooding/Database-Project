<?php
require_once __DIR__ . '/config.php';
$employee = null;
$error    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ssn_raw = $_POST['ssn'] ?? '';
    // strip everything except digits so users can paste with dashes/spaces
    $ssn     = preg_replace('/[^0-9]/', '', $ssn_raw);

    if (strlen($ssn) !== 9) {
        $error = 'Please enter exactly 9 digits (no dashes or spaces).';
    } else {
        $stmt = $mysqli->prepare('SELECT SSN, Fullname, Phone, Startdate, Branchid FROM EMPLOYEE WHERE SSN = ?');
        $stmt->bind_param('s', $ssn);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows) {
            $employee = $res->fetch_assoc();
        } else {
            $error = 'No employee found with that SSN.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Lookup Employee</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width:600px;">
    <h1 class="mb-4">Lookup Employee by SSN</h1>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="mb-4">
      <div class="mb-3">
        <label for="ssn" class="form-label">SSN (9 digits)</label>
        <input type="text" class="form-control" id="ssn" name="ssn"
               pattern="\d{9}" maxlength="11" required
               value="<?= htmlspecialchars($_POST['ssn'] ?? '') ?>"
               placeholder="e.g. 123456789">
      </div>
      <button class="btn btn-primary" type="submit">Search</button>
      <a href="index.php" class="btn btn-link">Back to Home</a>
    </form>

    <?php if ($employee): ?>
      <h2>Employee Details</h2>
      <table class="table table-striped">
        <tr><th>SSN</th><td><?= htmlspecialchars($employee['SSN']) ?></td></tr>
        <tr><th>Name</th><td><?= htmlspecialchars($employee['Fullname']) ?></td></tr>
        <tr><th>Phone</th><td><?= htmlspecialchars($employee['Phone']) ?></td></tr>
        <tr><th>Start Date</th><td><?= htmlspecialchars($employee['Startdate']) ?></td></tr>
        <tr><th>Branch ID</th><td><?= htmlspecialchars($employee['Branchid']) ?></td></tr>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
