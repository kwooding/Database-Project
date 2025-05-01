<?php
/********************************************************************
 * Customer Lookup by SSN
 * --------------------------------------------------------------
 * – Requires  config.php  for the $mysqli connection
 * – Accepts 9-digit SSN (dashes/spaces allowed when typing)
 * – Shows Customer info + optional Personal Banker name
 *******************************************************************/
require_once __DIR__ . '/config.php';

$customer = null;
$error    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Grab raw input and keep only digits
    $ssn_raw = $_POST['ssn'] ?? '';
    $ssn     = preg_replace('/[^0-9]/', '', $ssn_raw);

    if (strlen($ssn) !== 9) {
        $error = 'Please enter exactly 9 digits (no dashes or spaces).';
    } else {
        /* Prepared statement to prevent SQL-injection */
        $stmt = $mysqli->prepare(
            'SELECT C.SSN,
                    C.Fullname,
                    C.Address,
                    C.PersonalbankerSSN,
                    E.Fullname   AS BankerName
               FROM CUSTOMER C
          LEFT JOIN EMPLOYEE E
                 ON E.SSN = C.PersonalbankerSSN
              WHERE C.SSN = ?'
        );
        $stmt->bind_param('s', $ssn);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows) {
            $customer = $res->fetch_assoc();
        } else {
            $error = 'No customer found with that SSN.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Lookup Customer</title>
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">

  <h1 class="mb-4">Customer Lookup</h1>

  <form method="post" class="row g-3 mb-4" autocomplete="off">
    <div class="col-auto">
      <input type="text"
             name="ssn"
             class="form-control"
             maxlength="11"
             placeholder="Enter SSN (digits only)"
             value="<?= htmlspecialchars($_POST['ssn'] ?? '') ?>"
             required>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Search</button>
    </div>
  </form>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($customer): ?>
    <table class="table table-bordered w-auto">
      <tr><th>SSN</th><td><?= htmlspecialchars($customer['SSN']) ?></td></tr>
      <tr><th>Name</th><td><?= htmlspecialchars($customer['Fullname']) ?></td></tr>
      <tr><th>Address</th><td><?= htmlspecialchars($customer['Address']) ?></td></tr>
      <?php if ($customer['PersonalbankerSSN']): ?>
        <tr><th>Personal Banker SSN</th>
            <td><?= htmlspecialchars($customer['PersonalbankerSSN']) ?></td></tr>
        <tr><th>Banker Name</th>
            <td><?= htmlspecialchars($customer['BankerName'] ?? '—') ?></td></tr>
      <?php endif; ?>
    </table>
  <?php endif; ?>

  <p class="mt-4"><a href="index.php">« Back to Home</a></p>
</body>
</html>
