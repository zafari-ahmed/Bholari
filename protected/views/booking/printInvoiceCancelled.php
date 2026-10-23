<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style >
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            font-size: 13px;
            font-weight:bold;
        }

        .receipt {
            max-width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            margin-top: -2%;
        }

        .header h2 {
            margin: 0;
        }

        .details,
        .payment {
            width: 100%;
            border-collapse: collapse;
            /*margin-bottom: 20px;*/
        }

        .details td,
        .payment td {
            padding: 8px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .underlined {
            border-bottom: 1px solid black;
            text-align: center;
        }

        @media print {
            .page, .page-break { break-after: page; }
        }
    </style>
</head>
<?php $userModel = Yii::app()->session->get('userModel');?>
<body>
    <div class="receipt">
        <div class="header">
            
            <img src="<?php echo Yii::app()->baseUrl?>/images/GB1-B-resized.png" style="    position: absolute;z-index: 999;width: 35%;margin-left: -15%;margin-top: 25%;opacity: 0.1;">
            <div style="overflow:hidden;">
                <div style="width:10%;float:left;position: relative;left: -5%;">
                    
                    <img src="<?php echo Yii::app()->baseUrl?>/images/SS-B-resized.png" style="max-width: 120%;margin-top: 1px;margin-left: 200%;">
                </div>
                <div style="width:65%;float:left;text-align: center;position:relative;left:15%">
                    <img src="<?php echo Yii::app()->baseUrl?>/images/GB-B-resized.png" style="    max-width: 50%;margin-top: 5px;margin-left: -115px;">
                </div>
                <div style="float:left;margin-top:8%">
                    <!--<img src="<?php //echo Yii::app()->baseUrl?>/images/SS-B.png" style="    max-width: 70%;margin-top: 5px;margin-left: -115px;">-->
                    <span style="padding: 6px;margin: 10px;padding-left: 25px;padding-right: 25px;letter-spacing: 3px;font-size: 13px;">ORIGINAL</span>
                    <br><br>
                    <span style="    padding: 6px;border: 1px solid #000;margin: 10px;background: #0000005c;color: #fff;padding-left: 25px;padding-right: 25px;letter-spacing: 3px;font-size: 13px;">RECEIPT</span>
                    
                </div>
            </div>
        </div>
        <table class="details">
            <tr>
                <td style="width:2%"><strong>Receipt No:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo ltrim(@$transaction[0]->transaction_number,0)?></div></td>

                <td style="width:2%"><strong>File No:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo $this->getBookingRegNo($booking->id)?></div></td>

                <td style="width:1%"><strong>Date:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo date('d-M-Y',strtotime(@$transaction[0]->createdOn))?></div></td>
            </tr>
        </table>
        <table class="details">
            <tr>
                <td style="width:3%"><strong>Mr./Mrs./Ms.</strong></td>
                <td style="width:40%">
                    <div class="underlined" style="font-size: 17px;"><?php echo ucwords(@$transaction[0]->customer->name)?></div>
                </td>
                <td style="width:2%"><strong>Contact:</strong></td>
                <td>
                    <div class="underlined"><?php echo ucwords(@$transaction[0]->customer->mobile)?></div>
                </td>
            </tr>
        </table>
        <table class="details">
            <tr>
                <td style="width:0%"><strong>S/o./D/o./W/o.</strong></td>
                <td style="width:20%">
                    <div class="underlined" style="font-size: 17px;"><?php echo ucwords(@$transaction[0]->customer->father_husband_name)?></div>
                </td>
                <td style="width:4%"><strong>Rec By:</strong></td>
                <td style="width:4%">
                    <div class="underlined" style="margin-left:-15%"><?php echo @$transaction[0]->createdBy?></div>
                </td>
                <td style="width:4%"><strong>Book By:</strong></td>
                <td style="width:4%">
                    <div class="underlined"><?php echo @$transaction[0]->plot->agent->name?></div>
                </td>
            </tr>
        </table>
        <table class="details" style="border: 1px solid #000;margin-bottom: 10px;">
            <tr>
                <td><strong>Block:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->block_number?></div>
                </td>
                <td><strong>Plot No:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->plot_number?></div>
                </td>
                <td><strong>Size:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->size->size?></div>
                </td>
                <td><strong>Category:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->category->name?></div>
                </td>
            </tr>
        </table>
        <table class="payment" border="border-collapse" style="line-height: 7px">
            <tr>
                <td style="text-align: center;"><strong>Payment Description</strong></td>
                <td style="text-align: center;"><strong>Amount</strong></td>
            </tr>
            <?php $is_monthly = $is_yearly = 0;$total = 0; $lastMonthlyy = @$lastMonthly; if($transaction) { foreach($transaction as $index=>$transact): $total = $total + $transact->amount;?>
            <tr>
                <td>
                    <?php if(isset($transact->plot_payment_mode_id)){?> 
                        <?php echo ucfirst(@$transact->plotPaymentMode->mode)?>
                    <?php } else {?>
                        <?php echo ucfirst(@$transact->plot_payment_mode)?>
                    <?php }?>
                </td>
                <td style="text-align: center;"><?php echo number_format(@$transact->amount)?></td>
            </tr>
            <?php endforeach;}?>
        </table>
        
        <table class="details">
            <tr>
                <td colspan="8" style="width:70%">
                    <table class="details" border="border-collapse">
                        <tr>
                            <td>
                                <strong><div class="underlined">Mode of Payment</div></strong><br>
                                <span style="position: absolute;left: 35%;font-size: 15px;"><h2 style="text-align: center;"><?php echo strtoupper(@$transaction[0]->transaction_type)?></h2></span>
                                <div><?php $link = 'https://portal.gulshanebholari.com/ledger/bookingledger/'.$booking->id;?><img  id="imagePreview" style="max-width: 80px;position: absolute;left: 50%;margin-top:-10px" src="https://gulshanebholari.com/qr/test.php?id=<?php echo $booking->id?>" class="qrcode"/></div>
                                <strong>Bank:</strong> <?php echo (@$transaction[0]->transaction_type!='cash')?ucfirst(@$transaction[0]->bank):'N/A'?> <br>
                                <strong>Branch:</strong> <?php echo (@$transaction[0]->transaction_type!='cash')?ucfirst(@$transaction[0]->branch):'N/A'?> <br>
                                <strong>Date:</strong> <?php echo @$transaction[0]->comment?> <br>
                                <strong>Reference No:</strong> <?php echo @$transaction[0]->reference_number?> <br>
                            </td>
                        </tr>
                    </table>
                </td>
                <td colspan="4">
                    <div style="border: 1px solid; padding: 20px; text-align: center;">Total: <?php echo number_format($total)?> PKR</div>
                    <div class="underlined" style="border: 1px solid; text-align: center;height: 60px;">               </div>
                </td>
            </tr>
            <tr>
                <td style="width:20%">
                    <div style="position: relative;margin-top: -10px;font-weight:800">
                        Amount in Words:
                    </div>
                </td>
                <td>
                    <div style="position: relative;margin-top: -10px;"><?php echo ucwords($this->getIndianCurrency($total))?></div>
                </td>
                <td style="position: absolute;right: 30%;font-size:7px"><span>Printed By/Date:</span></td>
                <td style="position:absolute;right: 10%;font-size:7px">
                    <div> <?php echo ucwords($userModel['first_name'].' '.$userModel['last_name'])?> / <?php echo date('Y-m-d H:i:s')?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>
    <br/><br/>
    <div class="receipt" style="margin-top: 20px;">
        <div class="header">
            <img src="<?php echo Yii::app()->baseUrl?>/images/GB1-B-resized.png" style="    position: absolute;z-index: 999;width: 35%;margin-left: -15%;margin-top: 25%;opacity: 0.1;">
            <div style="overflow:hidden;">
                <div style="width:10%;float:left;position: relative;left: -5%;">
                    <img src="<?php echo Yii::app()->baseUrl?>/images/SS-B-resized.png" style="max-width: 120%;margin-top: 1px;margin-left: 200%;">
                </div>
                <div style="width:65%;float:left;text-align: center;position:relative;left:15%">
                    <img src="<?php echo Yii::app()->baseUrl?>/images/GB-B-resized.png" style="    max-width: 50%;margin-top: 5px;margin-left: -115px;">
                </div>
                <div style="float:left;margin-top:8%">
                    <!--<img src="<?php //echo Yii::app()->baseUrl?>/images/SS-B.png" style="    max-width: 70%;margin-top: 5px;margin-left: -115px;">-->
                    <span style="padding: 6px;margin: 10px;padding-left: 25px;padding-right: 25px;letter-spacing: 3px;font-size: 13px;">DUPLICATE</span>
                    <br><br>
                    <span style="    padding: 6px;border: 1px solid #000;margin: 10px;background: #0000005c;color: #fff;padding-left: 25px;padding-right: 25px;letter-spacing: 3px;font-size: 13px;">RECEIPT</span>
                    
                </div>
            </div>
        </div>
        <table class="details">
            <tr>
                <td style="width:2%"><strong>Receipt No:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo ltrim(@$transaction[0]->transaction_number,0)?></div></td>

                <td style="width:2%"><strong>File No:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo $this->getBookingRegNo($booking->id)?></div></td>

                <td style="width:1%"><strong>Date:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo date('d-M-Y',strtotime(@$transaction[0]->createdOn))?></div></td>
            </tr>
        </table>
        <table class="details">
            <tr>
                <td style="width:3%"><strong>Mr./Mrs./Ms.</strong></td>
                <td style="width:40%">
                    <div class="underlined" style="font-size: 17px;"><?php echo ucwords(@$transaction[0]->customer->name)?></div>
                </td>
                <td style="width:2%"><strong>Contact:</strong></td>
                <td>
                    <div class="underlined"><?php echo ucwords(@$transaction[0]->customer->mobile)?></div>
                </td>
            </tr>
        </table>
        <table class="details">
            <tr>
                <td style="width:0%"><strong>S/o./D/o./W/o.</strong></td>
                <td style="width:20%">
                    <div class="underlined" style="font-size: 17px;"><?php echo ucwords(@$transaction[0]->customer->father_husband_name)?></div>
                </td>
                <td style="width:4%"><strong>Rec By:</strong></td>
                <td style="width:4%">
                    <div class="underlined" style="margin-left:-15%"><?php echo @$transaction[0]->createdBy?></div>
                </td>
                <td style="width:4%"><strong>Book By:</strong></td>
                <td style="width:4%">
                    <div class="underlined"><?php echo @$transaction[0]->plot->agent->name?></div>
                </td>
            </tr>
        </table>
        <table class="details" style="border: 1px solid #000;margin-bottom: 10px;">
            <tr>
                <td><strong>Block:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->block_number?></div>
                </td>
                <td><strong>Plot No:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->plot_number?></div>
                </td>
                <td><strong>Size:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->size->size?></div>
                </td>
                <td><strong>Category:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->category->name?></div>
                </td>
            </tr>
        </table>
        <table class="payment" border="border-collapse" style="line-height: 7px">
            <tr>
                <td style="text-align: center;"><strong>Payment Description</strong></td>
                <td style="text-align: center;"><strong>Amount</strong></td>
            </tr>
            <?php $is_monthly = $is_yearly = 0;$total = 0; $lastMonthlyy = @$lastMonthly; if($transaction) { foreach($transaction as $index=>$transact): $total = $total + $transact->amount;?>
            <tr>
                <td>
                    <?php if(isset($transact->plot_payment_mode_id)){?> 
                        <?php echo ucfirst(@$transact->plotPaymentMode->mode)?>
                    <?php } else {?>
                        <?php echo ucfirst(@$transact->plot_payment_mode)?>
                    <?php }?>
                </td>
                <td style="text-align: center;"><?php echo number_format(@$transact->amount)?></td>
            </tr>
            <?php endforeach;}?>
        </table>
        
        <table class="details">
            <tr>
                <td colspan="8" style="width:70%">
                    <table class="details" border="border-collapse">
                        <tr>
                            <td>
                                <strong><div class="underlined">Mode of Payment</div></strong><br>
                                <span style="position: absolute;left: 35%;font-size: 15px;"><h2 style="text-align: center;"><?php echo strtoupper(@$transaction[0]->transaction_type)?></h2></span>
                                <div><?php $link = 'https://portal.gulshanebholari.com/ledger/bookingledger/'.$booking->id;?><img  id="imagePreview" style="max-width: 80px;position: absolute;left: 50%;margin-top:-10px" src="https://gulshanebholari.com/qr/test.php?id=<?php echo $booking->id?>" class="qrcode"/></div>
                                <strong>Bank:</strong> <?php echo (@$transaction[0]->transaction_type!='cash')?ucfirst(@$transaction[0]->bank):'N/A'?> <br>
                                <strong>Branch:</strong> <?php echo (@$transaction[0]->transaction_type!='cash')?ucfirst(@$transaction[0]->branch):'N/A'?> <br>
                                <strong>Date:</strong> <?php echo @$transaction[0]->comment?> <br>
                                <strong>Reference No:</strong> <?php echo @$transaction[0]->reference_number?> <br>
                            </td>
                        </tr>
                    </table>
                </td>
                <td colspan="4">
                    <div style="border: 1px solid; padding: 20px; text-align: center;">Total: <?php echo number_format($total)?> PKR</div>
                    <div class="underlined" style="border: 1px solid; text-align: center;height: 60px;">               </div>
                </td>
            </tr>
            <tr>
                <td style="width:20%">
                    <div style="position: relative;margin-top: -10px;font-weight:800">
                        Amount in Words:
                    </div>
                </td>
                <td>
                    <div style="position: relative;margin-top: -10px;"><?php echo ucwords($this->getIndianCurrency($total))?></div>
                </td>
                <td style="position: absolute;right: 30%;font-size:7px"><span>Printed By/Date:</span></td>
                <td style="position:absolute;right: 10%;font-size:7px">
                    <div> <?php echo ucwords($userModel['first_name'].' '.$userModel['last_name'])?> / <?php echo date('Y-m-d H:i:s')?></div>
                </td>
            </tr>
        </table>
    </div>
    <div class="page-break"></div>
    <br/><br/>
    <div class="receipt" style="margin-top: 20px;">
        <div class="header">
            <img src="<?php echo Yii::app()->baseUrl?>/images/GB1-B-resized.png" style="    position: absolute;z-index: 999;width: 35%;margin-left: -15%;margin-top: 25%;opacity: 0.1;">
            <div style="overflow:hidden;">
                <div style="width:10%;float:left;position: relative;left: -5%;">
                    <img src="<?php echo Yii::app()->baseUrl?>/images/SS-B-resized.png" style="max-width: 120%;margin-top: 1px;margin-left: 200%;">
                </div>
                <div style="width:65%;float:left;text-align: center;position:relative;left:15%">
                    <img src="<?php echo Yii::app()->baseUrl?>/images/GB-B-resized.png" style="    max-width: 50%;margin-top: 5px;margin-left: -115px;">
                </div>
                <div style="float:left;margin-top:8%">
                    <!--<img src="<?php //echo Yii::app()->baseUrl?>/images/SS-B.png" style="    max-width: 70%;margin-top: 5px;margin-left: -115px;">-->
                    <span style="padding: 6px;margin: 10px;padding-left: 25px;padding-right: 25px;letter-spacing: 3px;font-size: 13px;">ACCOUNTS</span>
                    <br><br>
                    <span style="    padding: 6px;border: 1px solid #000;margin: 10px;background: #0000005c;color: #fff;padding-left: 25px;padding-right: 25px;letter-spacing: 3px;font-size: 13px;">RECEIPT</span>
                    
                </div>
            </div>
        </div>
        <table class="details">
            <tr>
                <td style="width:2%"><strong>Receipt No:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo ltrim(@$transaction[0]->transaction_number,0)?></div></td>

                <td style="width:2%"><strong>File No:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo $this->getBookingRegNo($booking->id)?></div></td>

                <td style="width:1%"><strong>Date:</strong></td>
                <td style="width:5%"><div class="underlined" style="margin-left:-15%"><?php echo date('d-M-Y',strtotime(@$transaction[0]->createdOn))?></div></td>
            </tr>
        </table>
        <table class="details">
            <tr>
                <td style="width:3%"><strong>Mr./Mrs./Ms.</strong></td>
                <td style="width:40%">
                    <div class="underlined" style="font-size: 17px;"><?php echo ucwords(@$transaction[0]->customer->name)?></div>
                </td>
                <td style="width:2%"><strong>Contact:</strong></td>
                <td>
                    <div class="underlined"><?php echo ucwords(@$transaction[0]->customer->mobile)?></div>
                </td>
            </tr>
        </table>
        <table class="details">
            <tr>
                <td style="width:0%"><strong>S/o./D/o./W/o.</strong></td>
                <td style="width:20%">
                    <div class="underlined" style="font-size: 17px;"><?php echo ucwords(@$transaction[0]->customer->father_husband_name)?></div>
                </td>
                <td style="width:4%"><strong>Rec By:</strong></td>
                <td style="width:4%">
                    <div class="underlined" style="margin-left:-15%"><?php echo @$transaction[0]->createdBy?></div>
                </td>
                <td style="width:4%"><strong>Book By:</strong></td>
                <td style="width:4%">
                    <div class="underlined"><?php echo @$transaction[0]->plot->agent->name?></div>
                </td>
            </tr>
        </table>
        <table class="details" style="border: 1px solid #000;margin-bottom: 10px;">
            <tr>
                <td><strong>Block:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->block_number?></div>
                </td>
                <td><strong>Plot No:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->plot_number?></div>
                </td>
                <td><strong>Size:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->size->size?></div>
                </td>
                <td><strong>Category:</strong></td>
                <td>
                    <div class=""><?php echo @$booking->plot->category->name?></div>
                </td>
            </tr>
        </table>
        <table class="payment" border="border-collapse" style="line-height: 7px">
            <tr>
                <td style="text-align: center;"><strong>Payment Description</strong></td>
                <td style="text-align: center;"><strong>Amount</strong></td>
            </tr>
            <?php $is_monthly = $is_yearly = 0;$total = 0; $lastMonthlyy = @$lastMonthly; if($transaction) { foreach($transaction as $index=>$transact): $total = $total + $transact->amount;?>
            <tr>
                <td>
                    <?php if(isset($transact->plot_payment_mode_id)){?> 
                        <?php echo ucfirst(@$transact->plotPaymentMode->mode)?>
                    <?php } else {?>
                        <?php echo ucfirst(@$transact->plot_payment_mode)?>
                    <?php }?>
                </td>
                <td style="text-align: center;"><?php echo number_format(@$transact->amount)?></td>
            </tr>
            <?php endforeach;}?>
        </table>
        
        <table class="details">
            <tr>
                <td colspan="8" style="width:70%">
                    <table class="details" border="border-collapse">
                        <tr>
                            <td>
                                <strong><div class="underlined">Mode of Payment</div></strong><br>
                                <span style="position: absolute;left: 35%;font-size: 15px;"><h2 style="text-align: center;"><?php echo strtoupper(@$transaction[0]->transaction_type)?></h2></span>
                                <div><?php $link = 'https://portal.gulshanebholari.com/ledger/bookingledger/'.$booking->id;?><img  id="imagePreview" style="max-width: 80px;position: absolute;left: 50%;margin-top:-10px" src="https://gulshanebholari.com/qr/test.php?id=<?php echo $booking->id?>" class="qrcode"/></div>
                                <strong>Bank:</strong> <?php echo (@$transaction[0]->transaction_type!='cash')?ucfirst(@$transaction[0]->bank):'N/A'?> <br>
                                <strong>Branch:</strong> <?php echo (@$transaction[0]->transaction_type!='cash')?ucfirst(@$transaction[0]->branch):'N/A'?> <br>
                                <strong>Date:</strong> <?php echo @$transaction[0]->comment?> <br>
                                <strong>Reference No:</strong> <?php echo @$transaction[0]->reference_number?> <br>
                            </td>
                        </tr>
                    </table>
                </td>
                <td colspan="4">
                    <div style="border: 1px solid; padding: 20px; text-align: center;">Total: <?php echo number_format($total)?> PKR</div>
                    <div class="underlined" style="border: 1px solid; text-align: center;height: 60px;">               </div>
                </td>
            </tr>
            <tr>
                <td style="width:20%">
                    <div style="position: relative;margin-top: -10px;font-weight:800">
                        Amount in Words:
                    </div>
                </td>
                <td>
                    <div style="position: relative;margin-top: -10px;"><?php echo ucwords($this->getIndianCurrency($total))?></div>
                </td>
                <td style="position: absolute;right: 30%;font-size:7px"><span>Printed By/Date:</span></td>
                <td style="position:absolute;right: 10%;font-size:7px">
                    <div> <?php echo ucwords($userModel['first_name'].' '.$userModel['last_name'])?> / <?php echo date('Y-m-d H:i:s')?></div>
                </td>
            </tr>
        </table>
    </div>
    <script type="text/javascript">
    window.print();
</script>
</body>

</html>