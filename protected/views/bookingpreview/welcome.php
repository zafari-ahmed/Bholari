<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome Letter</title>
  <style>
    body {
      /*font-family: Arial, sans-serif;*/
      font-family: calibri;
      margin: 40px;
      line-height: 1.6;
      margin-left:10%;
    }
    .header, .footer {
      margin-top: 30px;
    }
    .right {
      float: right;
    }
    .subject {
      font-weight: bold;
      margin-top: 30px;
      /*text-decoration: underline;*/
    }
    .details {
      margin-top: 20px;
    font-style: italic;
    margin-left: 20%;
    font-weight:bold;
    }
    .details label {
      display: inline-block;
      width: 150px;
      font-weight: bold;
    }
    .contact {
      margin-top: 30px;
    }
    .footer {
      margin-top: 50px;
    }
  </style>
</head>

<?php $tpp = $booking->plot->total;?>

<?php if($booking->plot->is_corner == 1){?>
<?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_corner_amount,false)?>
<?php } ?>

<?php if($booking->plot->is_road_facing == 1){?>
<?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_road_facing_amount,false)?>
<?php } ?>

<?php if($booking->plot->is_park_facing == 1){?>
 <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_park_facing_amount,false)?>
<?php } ?>



<?php if($booking->plot->is_west_open == 1){?>
<?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_west_open_amount,false)?>
<?php } ?>
<?php $tppF = $tpp?>
<?php if($booking->plot->discount){?>
<?php $tppF = $tpp - $booking->plot->discount?>
<?php }?>
<body>

  <div class="header" >
    <div style="margin-top: 30%;"><strong>To,</strong><span style="float: right;
    margin-right: 5%;"><strong>Date:</strong> <?php echo @date('d-m-Y',strtotime($booking->createdOn))?></span></div>
    <div><strong><?php echo @$booking->customer->name?></strong></div>
    <div><?php echo @$booking->customer->address?></div>
    <div>Contact: <?php echo @$booking->customer->mobile?></div>
    <!--<div class="right"><strong>Date:</strong> <?php //echo @date('d M, Y',strtotime($booking->createdOn))?></div>-->
  </div>

  <div class="subject">SUBJECT: <span style="letter-spacing:2px;margin-left:10%;text-decoration: underline;font-weight:100">WELCOME IN <span style="color:#00008B;font-weight:600">GULSHAN-E-BHOLARI</span></span></div>

  <p style="font-size:15px">
    We are greatly pleased and appreciating to your booking in our project <strong>GULSHAN-E-BHOLARI</strong>,
    the project of <strong>SHINE STAR BUILDER & DEVELOPER</strong> situated at Main Super Highway, Distt. Thatta.
    We hope the move-in process is going smoothly and we welcome you to you settling into your new residence quite nicely.
    Your plot booking particulars are given below:
  </p>

  <div class="details">
    <div><label>Client Name:</label> <?php echo @$booking->customer->name?></div>
    <div><label>CNIC No:</label> <?php echo @$booking->customer->cnic?></div>
    <div><label>File No:</label> GB-<?php echo @$booking->id?></div>
    <div><label>Plot No:</label> <?php echo @$booking->plot->plot_number?></div>
    <div><label>Block No:</label> <?php echo @$booking->plot->block_number?></div>
    <div><label>Plot Size:</label> <?php echo @$booking->plot->size->size?></div>
    <div><label>Category:</label> <?php echo @$booking->plot->category->name?></div>
    <div><label>Cost of Plot:</label> <?php echo 'Rs. '.number_format(@$tppF)?></div>
  </div>

  <div class="contact" style="font-size:15px">
    Should you have any questions or concerns, please feel free to contact us:<br>
    <strong>Cell: 021-34960000</strong>
  </div>

  <div class="footer" style="margin-top:3%">
      
    Best Regards,<br><br>
    ______________________________________<br>
    <strong>SHINE STAR BUILDER & DEVELOPER</strong><br>
    <strong>GULSHAN-E-BHOLARI</strong><br>
  </div>
<script>
    window.print();
</script>
</body>
</html>