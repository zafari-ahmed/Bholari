<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirmation Letter</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      margin-top:50%!important;
      margin-left:10%;
      margin-right:5%;
    }
    h1 {
      text-align: center;
      text-decoration: underline;
    }
    .row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .section-title {
      margin-top: 30px;
      font-weight: bold;
    }
    .content-box {
      border-bottom: 1px solid black;
      display: inline-block;
      min-width: 150px;
      padding: 0 5px;
    }
    .center-text {
      text-align: center;
      margin-top: 40px;
      font-weight: bold;
    }
    .signature-section {
      display: flex;
      justify-content: space-between;
      margin-top: 80px;
    }
    .signature-box {
      text-align: center;
      width: 40%;
    }
    
    .record-line {
    display: flex;
    align-items: center;
    margin: 15px 0;
    font-size: 14px;
  }

  .record-line .label {
    flex: 0 0 120px; /* fixed width for all labels */
    font-weight: bold;
  }

  .record-line .value {
    flex: 1; /* take up all remaining width */
    border-bottom: 1px solid #000;
    /*text-align: center;*/
    padding: 2px 0;
  }
  .record-line .valueAddress {
      border-bottom: 1px solid #000;
      /*text-align: center;*/
      padding: 2px 4px;
      flex:1;
      word-wrap: break-word;   /* breaks long text */
      white-space: normal;     /* allows wrapping */
    }
  </style>
</head>
<body>

  <h1>CONFIRMATION LETTER</h1>

  <div class="row">
    <div>File No. <span class="content-box" style="text-align:center">GB-<?php echo @$booking->id?></span></div>
    <div>Date: <span class="content-box" style="text-align:center"><?php echo date('d-m-Y',strtotime(@$booking->createdOn))?></span></div>
  </div>

    <div class="row" style="display:inline-flex;width:100%">
        <div style="width:30%">Plot No. <span style="margin-left: 5%; font-weight: bold; border-bottom: 1px solid #000; padding-left: 5%; padding-right: 5%;"><?php echo @$booking->plot->plot_number?></span></div>
        <div style="width:40%">Block No. <span style="margin-left: 5%; font-weight: bold; border-bottom: 1px solid #000; padding-left: 5%; padding-right: 5%;"><?php echo @$booking->plot->block_number?></span></div>
        <div style="width:50%">Category <span style="margin-left: 5%; font-weight: bold; border-bottom: 1px solid #000; padding-left: 5%; padding-right: 5%;"><?php echo @$booking->plot->category->name?></span></div>
        <div style="width:35%">Size: <span style="margin-left: 5%; font-weight: bold; border-bottom: 1px solid #000; padding-left: 5%; padding-right: 5%;"><?php echo @$booking->plot->size->size?></span></div>
    </div>
  
  <p class="section-title" style="text-align:center">The Particulars in our record are as under</p>

  <!--<div>-->
  <!--<p style="display:flex"><strong>Allottee's Name</strong>: <span class="content-box" style="margin-left: 10% !important; padding-left: 5%; display: block !important;width:60%"><?php echo @$booking->customer->name?></span></p>-->
  <!--<p style="display:flex"><strong>S/o.W/o.D/o.</strong>: <span class="content-box" style="margin-left: 10% !important; padding-left: 5%; display: block !important;width:60%"><?php echo @$booking->customer->father_husband_name?></span></p>-->
  <!--<p style="display:flex"><strong>CNIC No.</strong>: <span class="content-box" style="margin-left: 10% !important; padding-left: 5%; display: block !important;width:60%"><?php echo @$booking->customer->cnic?></span></p>-->
  <!--<p style="display:flex"><strong>Address</strong>: <span class="content-box" style="margin-left: 10% !important; padding-left: 5%; display: block !important;width:60%"><?php echo @$booking->customer->address?></span><br></p>-->
  <!--  </div>-->
  
  
  <div>
  <div class="record-line">
    <div class="label">Allottee's Name</div>
    <div class="value"><?php echo @$booking->customer->name?></div>
  </div>

  <div class="record-line">
    <div class="label">S/o.W/o.D/o.</div>
    <div class="value"><?php echo @$booking->customer->father_husband_name?></div>
  </div>

  <div class="record-line">
    <div class="label">CNIC No.</div>
    <div class="value"><?php echo @$booking->customer->cnic?></div>
  </div>


<?php 
$address = trim(@$booking->customer->address);

// if longer than 50 chars, break on nearest space
if (strlen($address) > 50) {
    $firstLine = wordwrap($address, 50, "\n", false); 
    $lines = explode("\n", $firstLine);
} else {
    $lines = [$address];
}
?>
  <div class="record-line">
    <div class="label">Address</div>
    <div class="valueAddress"><?php echo $lines[0]; ?></div>
  </div>
    <?php for ($i = 1; $i < count($lines); $i++): ?>
    <div class="record-line">
      <div class="label"></div>
      <div class="value"><?php echo $lines[$i]; ?></div>
    </div>
    <?php endfor; ?>
</div>
  <!--<div class="row">-->
  <!--  <div><strong>Cell No.</strong> <span class="content-box" style="text-align:center"><?php echo @$booking->customer->mobile?></span></div>-->
  <!--  <div><strong>Res. No.</strong> <span class="content-box" style="text-align:center"><?php echo @$booking->customer->phone?></span></div>-->
  <!--  <div><strong>Off. No.</strong> <span class="content-box" style="text-align:center"><?php echo @$booking->customer->office?></span></div>-->
  <!--</div>-->
  
  <div class="row" style="display:inline-flex;width:100%">
        <div style="width:40%"><strong>Cell No.</strong> <span style="margin-left: 5%; border-bottom: 1px solid #000; padding-left: 5%; padding-right: 5%;"><?php echo @$booking->customer->mobile?></span></div>
        <div style="width:40%"><strong>Res. No.</strong> <span style="margin-left: 5%; border-bottom: 1px solid #000; padding-left: 5%; padding-right: 5%;"><?php echo @$booking->customer->phone?></span></div>
        <div style="width:40%"><strong>Off. No.</strong> <span style="margin-left: 5%; border-bottom: 1px solid #000; padding-left: 5%; padding-right: 5%;"><?php echo @$booking->customer->office?></span></div>
    </div>

  <p class="center-text">
    We are pleased to confirm you the above described plot in our project
  </p>
  <!--<h2 style="text-align:center;">"GULSHAN-E-BHOLARI"</h2>-->

  <div class="signature-section">
    <div class="signature-box">
     <hr>
      <p>Signature of<br>Allottee</p>
    </div>
    <div class="signature-box">
      <hr>
      <p>Authorized<br>Signature</p>
    </div>
  </div>

</body>
</html>