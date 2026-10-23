<?php
// ----- CONFIG -----
$numRows = 7; // change if you want more/less rows by default


// decode saved JSON if available
$data = [];
$cop = 0;
if (!empty($booking->payment_schedule_software_json)) {
    $decoded = json_decode($booking->payment_schedule_software_json, true);
    $cop = @$decoded['cop'];
    if (isset($decoded['rows']) && is_array($decoded['rows'])) {
        $data = $decoded['rows'];
    }
}


$heading1Options = [
  'Empty Box','Booking','Allocation','Confirmation','Monthly Installment',
  'Half Yearly','Yearly','Demarcation','Possession','2nd Last Payment','Last Payment'
];
$heading2Options = [
  'Empty Box','1st Payment','2nd Payment','3rd Payment',
  'Monthly Installment','Half Yearly','Yearly','2nd Last Payment','Last Payment'
];

// Helper: HTML-escape
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Payment Schedule</title>
<style>
  body { font-family: Arial, sans-serif; padding: 24px; }
  table { border-collapse: collapse; width: 100%; max-width: 900px; }
  th, td { border: 1px solid #000; padding: 10px; text-align: center; }
  th { background: #f2f2f2; }
  select, input[type="text"] {
    width: 95%; padding: 6px; border: 1px solid #555; text-align: center;
  }
  .submit-row { margin-top: 16px; }
  pre { background: #f7f7f7; padding: 12px; border: 1px solid #ddd; overflow: auto; }
</style>
</head>
<body>

<h2>Payment Schedule (Software)</h2>

<form method="post">
    <input type="hidden" name="booking_id" value="<?php echo @$booking->id?>"/>
  <table>
    <tr>
      <th>Heading 1</th>
      <th>Heading 2</th>
      <th>Value</th>
    </tr>

    <?php
    // Existing posted data (for sticky fill)
    $postedRows = $_POST['rows'] ?? [];

    for ($i = 1; $i <= $numRows; $i++):
        $rowKey = "row{$i}";
        $selH1  = $data[$rowKey]['heading1'] ?? '';
        $selH2  = $data[$rowKey]['heading2'] ?? '';
        $val    = $data[$rowKey]['value']    ?? '';
    ?>
    <tr>
      <td>
        <select name="rows[<?= $rowKey ?>][heading1]" class="form-control" required>
          <?php foreach ($heading1Options as $opt): ?>
            <option value="<?= h($opt) ?>" <?= ($opt === $selH1 ? 'selected' : '') ?>>
              <?= h($opt) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </td>
      <td>
        <select name="rows[<?= $rowKey ?>][heading2]" class="form-control" required>
          <?php foreach ($heading2Options as $opt): ?>
            <option value="<?= h($opt) ?>" <?= ($opt === $selH2 ? 'selected' : '') ?>>
              <?= h($opt) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </td>
      <td>
        <input type="text" name="rows[<?= $rowKey ?>][value]" value="<?= h($val) ?>" required>
      </td>
    </tr>
    <?php endfor; ?>
  </table>
  <br/>
    <div style="width:20%">
        <label>Cost of Plot</label>
        <input type="text" name="cost_of_plot" value="<?php echo @$cop ?? 0?>" required>
    </div>
  <div class="submit-row">
    <button type="submit">Submit</button>
  </div>
</form>


</body>
</html>
