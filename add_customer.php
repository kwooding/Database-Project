<?php
require_once __DIR__ . '/config.php';
$feedback = null;
$types = [
    'SAVINGS'      => 'Savings',
    'CHECKING'     => 'Checking',
    'MONEYMARKET'  => 'Money‑Market'
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pwd = $_POST['admin_pwd'] ?? '';
    if ($pwd !== 'CS331') {
        $feedback = ['type'=>'danger','msg'=>'Incorrect admin password.'];
    } else {
        $ssn      = preg_replace('/[^0-9]/', '', $_POST['ssn'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $addr     = trim($_POST['address'] ?? '');
        $banker   = preg_replace('/[^0-9]/', '', $_POST['banker'] ?? '');
        $acctType = $_POST['acct_type'] ?? '';
        $balance  = floatval($_POST['balance'] ?? 0);
        $extra    = floatval($_POST['extra'] ?? 0);
        // basic validation
        if (strlen($ssn)!==9 || !ctype_digit($ssn)) {
            $feedback=['type'=>'danger','msg'=>'SSN must be exactly 9 digits.'];
        } elseif ($name==='') {
            $feedback=['type'=>'danger','msg'=>'Name cannot be blank.'];
        } elseif ($addr==='') {
            $feedback=['type'=>'danger','msg'=>'Address cannot be blank.'];
        } elseif (!isset($types[$acctType])) {
            $feedback=['type'=>'danger','msg'=>'Invalid account type.'];
        } elseif ($balance<0) {
            $feedback=['type'=>'danger','msg'=>'Balance must be non‑negative.'];
        } elseif ($acctType==='CHECKING' && $extra<0) {
            $feedback=['type'=>'danger','msg'=>'Overdraft must be ≥ 0.'];
        } elseif (($acctType==='SAVINGS' || $acctType==='MONEYMARKET') && $extra<=0) {
            $feedback=['type'=>'danger','msg'=>'Interest rate must be > 0.'];
        } else {
            $mysqli->begin_transaction();
            try {
                // insert customer if new
                $stmt = $mysqli->prepare('SELECT 1 FROM CUSTOMER WHERE SSN=?');
                $stmt->bind_param('s',$ssn);
                $stmt->execute();
                if ($stmt->get_result()->num_rows===0) {
                    $stmt = $mysqli->prepare('INSERT INTO CUSTOMER (SSN,Fullname,Address,PersonalbankerSSN) VALUES (?,?,?,NULLIF(?,""))');
                    $stmt->bind_param('ssss',$ssn,$name,$addr,$banker);
                    $stmt->execute();
                }
                // next account number
                $next = $mysqli->query('SELECT IFNULL(MAX(Accountnumber),1000)+1 AS nxt FROM BANKACCOUNT')->fetch_assoc()['nxt'];
                $stmt = $mysqli->prepare('INSERT INTO BANKACCOUNT (Accountnumber,BALANCE,LastAccessed) VALUES (?,?,CURDATE())');
                $stmt->bind_param('id',$next,$balance);
                $stmt->execute();
                // subtype
                if ($acctType==='CHECKING') {
                    $stmt=$mysqli->prepare('INSERT INTO CHECKING (Accountnumber,Overdraft) VALUES (?,?)');
                } elseif ($acctType==='SAVINGS') {
                    $stmt=$mysqli->prepare('INSERT INTO SAVINGS (Accountnumber,Interestrate) VALUES (?,?)');
                } else {
                    $stmt=$mysqli->prepare('INSERT INTO MONEYMARKET (Accountnumber,Varinterestrate) VALUES (?,?)');
                }
                $stmt->bind_param('id',$next,$extra);
                $stmt->execute();
                // openaccount
                $stmt=$mysqli->prepare('INSERT INTO OPENACCOUNT (CustomerSSN,Accountnumber,Opendate) VALUES (?,?,CURDATE())');
                $stmt->bind_param('si',$ssn,$next);
                $stmt->execute();
                $mysqli->commit();
                $feedback=['type'=>'success','msg'=>'Customer and account created successfully. Account #'.$next];
            } catch (Throwable $e){
                $mysqli->rollback();
                $feedback=['type'=>'danger','msg'=>'Insert failed: '.$e->getMessage()];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Add Customer / Account</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h1 class="mb-4">Add Customer &amp; Account</h1>
    <?php if($feedback): ?>
      <div class="alert alert-<?= $feedback['type'] ?>"><?= htmlspecialchars($feedback['msg']) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3 bg-white p-4 rounded shadow-sm">
      <div class="col-md-3">
        <label class="form-label">Admin&nbsp;Password</label>
        <input name="admin_pwd" type="password" class="form-control" required autocomplete="off">
      </div>
      <div class="col-md-3">
        <label class="form-label">Customer&nbsp;SSN</label>
        <input name="ssn" type="text" pattern="[0-9\-\s]{9,11}" class="form-control" required placeholder="123456789">
      </div>
      <div class="col-md-6">
        <label class="form-label">Full&nbsp;Name</label>
        <input name="name" type="text" class="form-control" required>
      </div>
      <div class="col-md-12">
        <label class="form-label">Address</label>
        <input name="address" type="text" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Personal&nbsp;Banker&nbsp;SSN&nbsp;(optional)</label>
        <input name="banker" type="text" pattern="[0-9\-\s]{0,11}" class="form-control" placeholder="987654321">
      </div>
      <div class="col-md-3">
        <label class="form-label">Account&nbsp;Type</label>
        <select name="acct_type" class="form-select" required>
          <option value="SAVINGS">Savings</option>
          <option value="CHECKING">Checking</option>
          <option value="MONEYMARKET">Money‑Market</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Initial&nbsp;Balance</label>
        <input name="balance" type="number" step="0.01" min="0" value="0" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label" id="extra-label">Interest Rate (%)</label>
        <input name="extra" type="number" step="0.01" min="0" value="1.00" class="form-control" required>
      </div>
      <div class="col-12 text-end">
        <button class="btn btn-primary px-5">Create</button>
        <a href="index.php" class="btn btn-link">Cancel</a>
      </div>
    </form>
  </div>
<script>
// change label "extra" field depending on type
const acctSel = document.querySelector('select[name="acct_type"]');
const extraLbl = document.getElementById('extra-label');
acctSel.addEventListener('change',()=>{
  if(acctSel.value==='CHECKING') extraLbl.textContent='Overdraft Limit';
  else extraLbl.textContent='Interest Rate (%)';
});
</script>
</body>
</html>
