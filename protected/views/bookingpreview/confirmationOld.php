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
    .center {
      text-align: center;
    }
    .bold {
      font-weight: bold;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5px;
    }
    td {
      padding: 5px;
      vertical-align: top;
    }
    .underline {
      text-decoration: underline;
       }
    .signature-section {
      display: flex;
      justify-content: space-between;
      margin-top: 60px;
    }
    .signature-box {
      text-align: center;
      width: 40%;
    }
  </style>
</head>
<body>

  <h2 class="center bold underline">CONFIRMATION LETTER</h2>

  <table>
    <tr>
      <td class="bold">Code No.</td>
      <td class="underline">GB-<?php echo @$booking->id?></td>
      <td class="bold">Date:</td>
      <td class="underline"><?php echo @$booking->createdOn?></td>
    </tr>
    <tr>
      <td class="bold">Plot No.</td>
      <td class="underline"><?php echo @$booking->plot->plot_number?></td>
      <td class="bold">Block No.</td>
      <td class="underline"><?php echo @$booking->plot->block_number?></td>
    </tr>
    <tr>
      <td class="bold">Category</td>
      <td class="underline"><?php echo @$booking->plot->category->name?></td>
      <td class="bold">Size:</td>
      <td class="underline"><?php echo @$booking->plot->size->size?></td>
    </tr>
  </table>

  <h3 class="center">The Particulars in our record are as under</h3>

  <table>
    <tr>
      <td class="bold">Allottee's Name</td>
      <td class="underline"><?php echo @$booking->customer->name?></td>
    </tr>
    <tr>
      <td class="bold">S/o.W/o.D/o.</td>
      <td class="underline"><?php echo @$booking->customer->father_husband_name?></td>
    </tr>
    <tr>
      <td class="bold">CNIC No.</td>
      <td class="underline"><?php echo @$booking->customer->cnic?></td>
    </tr>
    <tr>
      <td class="bold">Address</td>
      <td class="underline">
        <?php echo @$booking->customer->address?>
      </td>
    </tr>
    <tr>
      <td class="bold">Cell No.</td>
      <td class="underline"><?php echo @$booking->customer->mobile?></td>
      <td class="bold">Res. No.</td>
      <td class="underline"><?php echo @$booking->customer->phone?></td>
      <td class="bold">Off. No.</td>
      <td class="underline"><?php echo @$booking->customer->office?></td>
    </tr>
  </table>

  <p class="center" style="margin-top: 30px;">
    We are pleased to confirm you the above described plot in our project
  </p>

  <h2 class="center">"GULSHAN-E-BHOLARI"</h2>

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