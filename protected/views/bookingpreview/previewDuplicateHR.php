<!DOCTYPE html>
<html lang="en">
<head>

 <meta charset="UTF-8">
  <title>Plot Application Form - GB - <?php echo @$booking->id?></title>
  <style>
    body {
      /*font-family: Arial, sans-serif;*/
      font-family: calibri;
      margin: 40px;
      line-height: 1.3;
      margin-left:10%;
      margin-top:10%;
      transform: rotate(-1.2deg);
        transform-origin: top center;
    }
    h2 {
      /*text-align: center;*/
      
    }
    .form-section {
      margin-bottom: 10px;
    }
    .form-section label {
      font-weight: bold;
      display: inline-block;
      width: 220px;
      vertical-align: top;
    }
    .form-section span {
      display: inline-block;
      min-width: 60%;
      border-bottom: 1px solid #000;
    }
    .double {
      display: inline-flex;
      width: 100%;
      /*gap: 40px;*/
    }
    .signature {
      margin-top: 20px;
    }
    .signature div {
      display: inline-block;
      width: 100%;
      text-align: center;
    }
    .declaration {
      margin-top: 25px;
      text-align: justify;
      font-size: 14px
    }
    .text-center {
      text-align: center;
    }
    hr {
      margin: 30px 0;
    }
   .uploader{
     float: right;
     text-align: right;
        
     }
  imagePreview {
      display: none;
      max-width: 100px;
      border: 1px solid #ccc;
      padding: 5px;
      margin-top: 10px;
    }

  </style>

</head>
<body>
    <div style="position: relative;top: 100px;">
<h2><span style="margin-left:25%;border-bottom:1px solid;font-weight:bold;padding-left:10px;padding-right:10px;font-size:2rem">APPLICATION FORM</span></h2>
  <h2><span style="margin-left:35%;border-bottom:1px solid;font-weight:bold;padding-left:10px;padding-right:10px">GB-<?php echo @$booking->id?></span></h2>

</div>
<?php $img = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSIxyT0DAa5_kwzb-e-bpTvAXIyW0OispA76Q&s';

if($booking->agent_cnic){
    $img = Yii::app()->baseUrl.'/uploads/booking/'.$booking->agent_cnic;
}
?>
<img id="imagePreview" src="<?php echo $img?>" alt="Image Preview" style=" max-width: 100px; border: 1px solid #ccc; padding: 5px;float: right;margin-right: 9%;">


<!-- <div class="uploader">

    <label for="photoUpload">Upload Your Photo:</label><br>
<input type="file" id="photoUpload" name="photoUpload" accept="image/*" onchange="previewImage(event)"><br><br>

</div> -->
<!--<h2>Application Form</h2>-->
<div><?php $link = 'https://google.com';?><img  id="imagePreview" style=" max-width: 100px; border: 1px solid #ccc; padding: 5px;margin-right: 9%;" src="https://quickchart.io/qr?text=<?php echo $link?>&choe=UTF-8" class="qrcode"/></div>
  <!--<p >Dear Sirrr,<br>-->
  <!--I, the undersigned, request you to kindly register my name for booking a plot in your project <strong>"GULSHAN-E-BHOLARI"</strong> on prescribed schedule of payment. My particulars are as under:</p>-->
 



  <div class="double " style="margin-top: 5%;margin-bottom:2%">
    <div style="display: flex;width: 30%;">
        <label>Plot No.:</label> 
        <span style="font-weight:600;border-bottom: 1px solid;padding-left: 5%;padding-right: 5%;"><?php echo @$booking->plot->plot_number?></span>
    </div>
    <div style="display: flex;width: 30%;">
        <label>Block No.:</label> 
        <span style="font-weight:600;border-bottom: 1px solid;padding-left: 5%;padding-right: 5%;"><?php echo @$booking->plot->block_number?></span>
    </div>
    <div style="display: flex;width: 30%;">
        <label>Category:</label> 
        <span style="font-weight:600;border-bottom: 1px solid;padding-left: 5%;padding-right: 5%;"><?php echo @$booking->plot->category->name?></span>
    </div>
    <div style="display: flex;width: 30%;">
        <label>Size:</label> 
        <span style="font-weight:600;border-bottom: 1px solid;padding-left: 5%;padding-right: 5%;"><?php echo @$booking->plot->size->size?></span>
    </div>
  </div>

  <div class="form-section"><label>Name/Mr/Mrs:</label> <span><?php echo @$booking->customer->name?></span></div>
  <div class="form-section"><label>Father's / Husband's Name:</label> <span><?php echo @$booking->customer->father_husband_name?></span></div>
  <div class="form-section"><label>Occupation: </label> <span><?php echo @$booking->customer->occupation?></span></div>
  <!--<div class="form-section" style="width:100%;display: flex
;">-->
    <!--<div style="width:100%;display: flex">-->
    <!--    <label>Occupation:</label> -->
    <!--    <span><?php //echo @$booking->customer->occupation?></span>-->
    <!--</div>-->
    <!--<div style="margin-left:2%;width:70%;display: flex">-->
    <!--    <label>Date of Birth:</label> -->
    <!--    <span><?php //echo @$booking->customer->dob?></span>-->
    <!--</div>-->
    <!--<div>-->
    <!--    <label>Date of Birth:</label> -->
    <!--    <span><?php //echo @$booking->customer->dob?></span>-->
    <!--</div>-->
  <!--</div>-->
  <div class="form-section"><label>CNIC No. / Date Of Birth:</label> <span><?php echo @$booking->customer->cnic?> / <?php echo @$booking->customer->dob?></span></div>
  <div class="form-section"><label>Postal / Residential Address:</label>
    <span style="width:40%;word-wrap: break-word;"><?php echo @$booking->customer->address?></span>
  </div>

  <div class="form-section"><label>Mobile / Residential Number:</label> 
    <span><?php echo @$booking->customer->mobile?> / <?php echo @$booking->customer->phone?></span></div>
  <!--<div class="form-section"><label>Residential Number:</label> <span><?php echo @$booking->customer->phone?></span></div>-->
  <div class="form-section"><label>Email:</label> <span><?php echo @$booking->customer->email ?? '-'?></span></div>

  <div class="form-section"><label>Nominee's Name/Relation:</label> <span><?php echo @$booking->customer->nominee_name?> / <?php echo @$booking->customer->nominee_relation?></span></div>
  <!--<div class="form-section"><label>Relation:</label> <span><?php //echo @$booking->customer->nominee_relation?></span></div>-->
  <div class="form-section"><label>Nominee CNIC No.:</label> <span><?php echo @$booking->customer->nominee_cnic?></span></div>
  <div class="form-section"><label>Booked By:</label> <span><?php echo @$booking->agent->name?></span></div>

  <div class="declaration" style="">
    It is hereby declared that I have read all the terms and conditions of booking/allocation in the project, printed in this application form and I accepted the same. It is further declared that I shall abide by all the existing terms and conditions (1 to 18) and those, which may be prescribed by your firm/company for the purchase of a plot in this project from time to time.
    Moreover, I shall also share the burden of amount incurred due to any controllable/uncontrollable conditions on development expenditures i.e. Natural calamities, abrupt market price hike/inflation beyond normal circumstances, and as per current market situation in shape of development charges, booking/allocation fees as prescribed by company/firm as per situation.
  </div><br/><br/><br/>

  <div>Yours faithfully,</div><br/><br/>

  <div class="signature">
    <div style="margin-left: 10%;">
      ____________________________________________________<br>
      <b>Applicant Signature & Thumb &nbsp;&nbsp;&nbsp;&nbsp;Booking Date: <?php echo date('d-m-Y',strtotime($booking->createdOn))?></b>
    </div>
    <div> 
        
    </div>
  </div>
  <script>
    window.print();
</script>
</body>
</html>