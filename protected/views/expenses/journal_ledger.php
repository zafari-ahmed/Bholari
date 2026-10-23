<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Journal Entry</title>

<style>
  @page {
    size: A4;
    margin: 15mm;
  }

  body {
    font-family: Arial, sans-serif;
    font-size: 12px;
    margin: 0;
    padding: 0;
    background: #fff;
  }

  .page {
    width: 210mm;
    min-height: 297mm;
    padding: 10mm;
    box-sizing: border-box;
  }

  /* HEADER */
  .header {
    margin-bottom: 10px;
  }

  .title {
    font-size: 18px;
    font-weight: bold;
  }

  .meta {
    margin-top: 5px;
    font-size: 12px;
  }

  .line {
    border-bottom: 1px solid #000;
    margin-top: 5px;
  }

  /* TABLE */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  th, td {
    border: 1px solid #000;
    padding: 6px;
    text-align: left;
  }

  th {
    background: #f2f2f2;
    font-weight: bold;
  }

  .text-right {
    text-align: right;
  }

  .no-border td {
    border: none;
  }

  /* TOTAL */
  .total-row td {
    font-weight: bold;
  }

  /* FOOTER */
  .footer {
    margin-top: 40px;
  }

  .signature {
    margin-top: 60px;
    display: flex;
    justify-content: space-between;
  }

  .sign-box {
    width: 30%;
    text-align: center;
  }

  .sign-line {
    border-top: 1px solid #000;
    margin-top: 40px;
    padding-top: 5px;
  }

  /* PRINT */
  @media print {
    body {
      margin: 0;
    }
  }
</style>
</head>

<body>

<div class="page">
<div style="overflow:hidden;">
	<div style="width:10%;float:left;position: relative;left: -5%;">
		<img src="<?php echo Yii::app()->baseUrl?>/images/GB1-B.png" style="max-width: 160%;margin-top: 15px;margin-left: 30px;">
	</div>
	<div style="width:70%;float:left;text-align: center;position:relative;left:15%">
        <img src="<?php echo Yii::app()->baseUrl?>/images/GB-B.png" style="    max-width: 100%;margin-top: 5px;margin-left: -115px;">
	</div>
	<div style="width:18%;float:left;position: relative;right: -22%;margin-top:1%">
		<img src="<?php echo Yii::app()->baseUrl?>/images/SS-B.png" style="    max-width: 70%;margin-top: 5px;margin-left: -115px;">
	</div>
</div>
  <!-- HEADER -->
  <div class="header">
    <div class="title">Journal Entry</div>

    <div class="meta">
      Reference: <?php echo $this->getExpenseRegNo($expense->id,'expense')?><br>
      Date: <?php echo date('d-M-Y',strtotime(@$expense->createdOn))?>
    </div>

    <div class="line"></div>
  </div>

  <!-- TABLE -->
  <table>
    <thead>
      <tr>
        <th style="width:5%">#</th>
        <th>Description</th>
        <th style="width:20%">Debit</th>
        <th style="width:20%">Credit</th>
      </tr>
    </thead>

    <tbody>
      <!-- LOOP START -->
      <!-- Example row -->
      <tr>
        <td>1</td>
        <td><?php echo ucfirst($this->expenseType(@$expense->expense_type))?> - <?php echo @ucfirst($expense->payment_mode)?></td>
        <td class="text-right"><?php echo number_format(@$expense->amount)?></td>
        <td class="text-right">-</td>
      </tr>

      <tr>
        <td>2</td>
        <td><?php echo @$expense->account->name?></td>
        <td class="text-right">-</td>
        <td class="text-right"><?php echo number_format(@$expense->amount)?></td>
      </tr>
      <!-- LOOP END -->

      <!-- TOTAL -->
      <tr class="total-row">
        <td colspan="2">Total</td>
        <td class="text-right"><?php echo number_format(@$expense->amount)?></td>
        <td class="text-right"><?php echo number_format(@$expense->amount)?></td>
      </tr>
    </tbody>
  </table>

  <!-- DESCRIPTION / NOTES -->
  <div class="footer">
    <strong>Description:</strong>
    <p><?php echo @$expense->description?></p>
  </div>

  <!-- SIGNATURE -->
  <div class="signature">
    <div class="sign-box">
      <div class="sign-line">Prepared By</div>
    </div>

    <div class="sign-box">
      <div class="sign-line">Checked By</div>
    </div>

    <div class="sign-box">
      <div class="sign-line">Approved By</div>
    </div>
  </div>

</div>

</body>
</html>