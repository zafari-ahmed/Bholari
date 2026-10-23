<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function checkSession() {
        if (!isset(Yii::app()->session['userModel'])) {
            $previousUrl=Yii::app()->request->urlReferrer;
            Yii::app()->session['urlReferer']=$previousUrl;
            //echo "<body onLoad='artificialbody()'></body>";
            //return false;
            //exit();
            Yii::app()->user->setFlash('error','Please Login the get back to the previous page');
            $this->redirect(Yii::app()->params['AppUrl']);
            //$this->redirect(Yii::app()->request->urlReferrer);
        }
	}

	public function sendSMS($number,$mesage){
		$msg = 'https://bsms-portal.convexinteractive.com/api_sms/api.php?key=f546245e7023cc890fc7566f4a62b774&receiver='.$number.'&sender=SHINESTAR&msgdata='.rawurlencode($mesage);
		//Try using %0A in the URL, just like you've used %20 instead of the space character.
		// use key 'http' even if you send the request to https://...
		$options = array(
		  'http' => array(
		    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		    'method'  => 'GET',
		  ),
		);
		//echo $mesage.'</br>';
		//$context  = @stream_context_create($options);
		//$result = @file_get_contents($msg, false, $context);
		//var_dump($result); exit;
	}


	public function startsWith($haystack, $needle)
	{
	     $length = strlen($needle);
	     return (substr($haystack, 0, $length) === $needle);
	}

	public function removeWith($haystack, $needle)
	{
	     $length = strlen($needle);
	     return (substr($haystack, 0, $length) === $needle);
	}


	public function Percentage($total,$percentage,$view = 1){
		if($view == 1){
			return number_format((@$percentage / 100) * @$total);
		} else{
			return (@$percentage / 100) * @$total;
		}
	}

	// public function getBookingRegNo($id){
	// 	$booking = CustomerPlots::model()->findByPk($id);
	// 	$bookingPaymentCount = CustomerPlots::model()->count('id < :id AND is_special = :special',array(':id'=>$id,':special'=>$booking->is_special));
	// 	//echo '<pre>';print_r($bookingPaymentCount+1);exit;
	// 	$paymentSchedule = PaymentSchedules::model()->find(array('order'=>'id desc'));
	// 	if($booking->special){
	// 		return $booking->special->name.'/'.(sprintf('%03d',$bookingPaymentCount+1)).'/KC';
	// 	} else{
	// 		return $paymentSchedule->name.'/'.(sprintf('%03d',$bookingPaymentCount+1)).'/KC';
	// 	}	
	// }
	public function getBookingRegNo($id,$isShowOld=false,$lockID = 400){
		$booking = CustomerPlots::model()->findByPk($id);
		/*$plotDetail = [];
		if($booking){
			$plotDetail = CustomerPlots::model()->findAll('plot_id = :plot AND status != 0',array(':plot'=>$booking->plot_id),array('order'=>'id desc'));	
		}
		$bookingPaymentCount = CustomerPlots::model()->count('id < :id AND is_special = :special',array(':id'=>$id,':special'=>$booking->is_special));	
		
		//echo '<pre>';print_r($bookingPaymentCount);exit;
		
		$paymentSchedule = PaymentSchedules::model()->find(array('order'=>'id desc'));
		if($isShowOld){
				if($booking->special){
					return $booking->special->name.'/'.(sprintf('%03d',$bookingPaymentCount+1)).'/KC';
				} else{
					return $paymentSchedule->name.'/'.(sprintf('%03d',$bookingPaymentCount+1)).'/KC';
				}
		} else{
			if(count($plotDetail) > 1){
				$bookingFirst = CustomerPlots::model()->findByPk($plotDetail[0]->id);
				$bookingPaymentCount = CustomerPlots::model()->count('id < :id AND is_special = :special',array(':id'=>$plotDetail[0]->id,':special'=>$bookingFirst->is_special));
				if($bookingFirst->special){
					return $bookingFirst->special->name.'/'.(sprintf('%03d',$bookingPaymentCount+1)).'/KC';
				} else{
					return $paymentSchedule->name.'/'.(sprintf('%03d',$bookingPaymentCount+1)).'/KC';
				}
			} else{
				if($booking->special){
					return $booking->special->name.'/'.(sprintf('%03d',$bookingPaymentCount+1)).'/KC';
				} else{
					return $paymentSchedule->name.'/'.(sprintf('%03d',$bookingPaymentCount+1)).'/KC';
				}
			}
		}*/
		return 'GB-'.$booking->id;
		
	}

	public function getWarningLetterNo($id){
		//Regno/ST-I/WL-I
		$booking = CustomerPlots::model()->findByPk($id);
		$wmCount = CustomerPlotsWarningLetters::model()->count('booking_id=:id',array(':id'=>$id));
		$modes = [
			1=>'I',
			2=>'II',
			3=>'III',
			4=>'IV',
			5=>'V',
			6=>'VI',
			7=>'VII',
			8=>'VIII',
			9=>'IX',
			10=>'X',
			11=>'XI',
			12=>'XII'
		];
		if($booking && $booking->plot->customerPlotTransfers){
			return $this->getBookingRegNo($id).'/ST-'.($booking->plot->customerPlotTransfersCount).'/WL-'.$modes[$wmCount+1];
		} else{
			return $this->getBookingRegNo($id).'/WL-'.$modes[$wmCount+1];
		}
	}

	public function getExpenseRegNo($id, $type='expense'){
		$expensePaymentCount = $pettyCashPaymentCount = 0;
		if($type=='expense'){
			$expense = Expenses::model()->findByPk($id);
			$pettyCashPaymentCount = PettyCashExpenses::model()->count('createdOn < :id AND expense_type = :type',array(':id'=>$expense->createdOn,':type'=>$expense->expense_type));	
			$expensePaymentCount = Expenses::model()->count('id < :id AND expense_type = :type',array(':id'=>$id,':type'=>$expense->expense_type));	
		}

		if($type=='pettyCash'){
			$expense = PettyCashExpenses::model()->findByPk($id);
			$expensePaymentCount = Expenses::model()->count('createdOn < :id AND expense_type = :type',array(':id'=>$expense->createdOn,':type'=>$expense->expense_type));	
			$pettyCashPaymentCount = PettyCashExpenses::model()->count('id < :id AND expense_type = :type',array(':id'=>$id,':type'=>$expense->expense_type));	
			
		}
		
		$totalCount = $expensePaymentCount + $pettyCashPaymentCount;
		
		
		$modes = [
			1=>'I',
			2=>'II',
			3=>'III',
			4=>'IV',
			5=>'V',
			6=>'VI',
			7=>'VII',
			8=>'VIII',
			9=>'IX',
			10=>'X',
			11=>'XI',
			12=>'XII',
			13=>'XIII',
			14=>'XIV',
			15=>'XV',
			16=>'XVI',
			17=>'XVII',
		];
		//return $pettyCashPaymentCount;
		//return 'EXP/'.$modes[$expense->expense_type].'-'.(sprintf('%03d',$totalCount+1)).'/GB';
		
		return 'EXP/'.(sprintf('%03d',$totalCount+1)).'/GB';
		
	}


	public function documentTypes(){
		$types = ['CNIC-Front','CNIC-Back','NADRA Verification Form','Thumb Image','Nominee CNIC-Front','Nominee CNIC-Back'];
		return $types;
	}

	public function paymentScheduleModes(){
		$types = ['Booking','Confirmation','Allocation','Monthly','Yearly','Possession'];
		return $types;
	}

	public function PlotUpdatedTotal($id){
		$plot = Plots::model()->findByPk($id);
		$plotTotal = $plot->total;
        
        if($plot->is_corner == 1){
            $cornerCharger = $this->Percentage($plotTotal,$plot->is_corner_amount,0);
            $plotTotal = $plotTotal + $cornerCharger;
        }

        if($plot->is_park_facing == 1){
            $parkFacing = $this->Percentage($plotTotal,$plot->is_park_facing_amount,0);
            $plotTotal = $plotTotal + $parkFacing;
        }

        if($plot->is_west_open == 1){
            $westOpen = $this->Percentage($plotTotal,$plot->is_west_open_amount,0);
            $plotTotal = $plotTotal + $westOpen;
        }

        $plotTotal = $plotTotal-$plot->discount;
        return 'PKR '.number_format($plotTotal);
	}

	public function getPaymentScheduleTotal($type,$id){
		$paySch = PaymentSchedulePaymentModes::model()->findAll('payment_schedule_id = :id AND plot_type = :type',array(
			':id'=> $id,
			':type' =>strtolower($type)
		));
		$total = 0;
		if($paySch){
			foreach($paySch as $a){
				$total = $total + $a->amount;
			}
		}
		return $total;
	}

	public function plotTotalCancel($id,$number_format = true,$is_total = true){
		$plot = Plots::model()->findByPk($id);
		$total = 0;
		if($is_total){
			$total += $plot->total;	
		}
		
		/*if($plot->is_road_facing == 1){
			$total += $this->Percentage($plot->total,$plot->is_road_facing_amount,0);
		}
		if($plot->is_park_facing == 1){
			$total += $this->Percentage($plot->total,$plot->is_park_facing_amount,0);	
		}
		if($plot->is_corner == 1){
			$total += $this->Percentage($plot->total,$plot->is_corner_amount,0);
		}
		if($plot->is_west_open == 1){
			$total += $this->Percentage($plot->total,$plot->is_west_open_amount,0);
		}*/
		if($number_format){
			return number_format($total);	
		} else{
			return $total;
		}
		
	}




	public function Getplotamountsafter3steps($booking){
		$booking = CustomerPlots::model()->findByPk($booking->id);
		return $booking->customerPlotTransactionSumWithout3;

	}

	public function expenseType($id,$onlyModes = false){
// 		$modes = [
// 			1=>'Survayour',
// 			2=>'Site',
// 			3=>'Sewrage',
// 			4=>'Road',
// 			5=>'Marketing',
// 			6=>'Boundry',
// 			7=>'Gardening',
// 			8=>'Office Setup',
// 			9=>'Office Running',
// 			10=>'Agent Commission',
// 			11=>'Donation',
// 			12=>'Qadir Personal',
// 			13=>'Assets',
// 			14=>'Loan',
// 			15=>'Petty Cash Load',
// 			16=>'Petty Cash Expense',
// 			17=>'Zakat',
// 		];
		
		$modes = [
            1  => 'LAND PAYMENT',
            2  => 'INVESTMENT RETURN',
            3  => 'OFFICE EXPENSE',
            4  => 'SALARY',
            5  => 'TOWN PLANNING',
            6  => 'OTHERS',
            7  => 'COMMISSION',
            8  => 'LOAN',
            9  => 'SITE DEVELOPMENT EXPENSE',
            10 => 'CHARITY',
            11 => 'REFUND',
            12 => 'INVESTMENT PROFIT RETURN',
        ];

	
		if($onlyModes){
			return $modes;
		} else{
			return $modes[$id];	
		}
		
	}

	public function expenseTypeReverse($id){
		$modes = [
            1  => 'LAND PAYMENT',
            2  => 'INVESTMENT RETURN',
            3  => 'OFFICE EXPENSE',
            4  => 'SALARY',
            5  => 'TOWN PLANNING',
            6  => 'OTHERS',
            7  => 'COMMISSION',
            8  => 'LOAN',
            9  => 'SITE DEVELOPMENT EXPENSE',
            10 => 'CHARITY',
            11 => 'REFUND',
            12 => 'INVESTMENT PROFIT RETURN',
        ];
        
        
        $modes = array_flip($modes);

		if(array_key_exists($id, $modes)){
			return $modes[$id];	
		} else{
			return -1;
		}
			
		
	}

	public function discountedPlotCostOfLand($id){
		$plot = Plots::model()->findByPk($id);
		if($plot->discount){
			return $plot->total-$plot->discount;	
		} else{
			return $plot->total;
		}
	}

	public function discountedPlotCostOfLandAndExtra($id){
		$plot = Plots::model()->findByPk($id);
		$costOfLand = $plotTotal = 0;
		if($plot->discount){
			$costOfLand = $plot->total-$plot->discount;	
		} else{
			$costOfLand = $plot->total;
		}
		$plotTotal = $costOfLand + $this->plotExtra($plot->id,false,true,true);
		return $plotTotal;
	}

	public function numberToRoman($num)  
	{ 
	    // Be sure to convert the given parameter into an integer
	    $n = intval($num);
	    $result = ''; 
	 
	    // Declare a lookup array that we will use to traverse the number: 
	    $lookup = array(
	        'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 
	        'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 
	        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
	    ); 
	 
	    foreach ($lookup as $roman => $value)  
	    {
	        // Look for number of matches
	        $matches = intval($n / $value); 
	 
	        // Concatenate characters
	        $result .= str_repeat($roman, $matches); 
	 
	        // Substract that from the number 
	        $n = $n % $value; 
	    } 

	    return $result; 
	} 
	
	
	public function finalViewBookingTotal($id)
    {
        $plot = Plots::model()->findByPk($id);
        if (!$plot) {
            return false;
        }
    
        // Base price after discount
        $baseTotal = $plot->total - $plot->discount;
    
        $extras = [];
        $extraTotal = 0;
    
        // Corner
        if ($plot->is_corner == 1) {
            $amount = $this->Percentage($plot->total, $plot->is_corner_amount, false);
            $extras[] = [
                'title' => 'Corner',
                'percent' => $plot->is_corner_amount,
                'amount' => $amount
            ];
            $extraTotal += $amount;
        }
    
        // Road Facing
        if ($plot->is_road_facing == 1) {
            $amount = $this->Percentage($plot->total, $plot->is_road_facing_amount, false);
            $extras[] = [
                'title' => 'Road Facing',
                'percent' => $plot->is_road_facing_amount,
                'amount' => $amount
            ];
            $extraTotal += $amount;
        }
    
        // Park Facing
        if ($plot->is_park_facing == 1) {
            $amount = $this->Percentage($plot->total, $plot->is_park_facing_amount, false);
            $extras[] = [
                'title' => 'Park Facing',
                'percent' => $plot->is_park_facing_amount,
                'amount' => $amount
            ];
            $extraTotal += $amount;
        }
    
        // West Open
        if ($plot->is_west_open == 1) {
            $amount = $this->Percentage($plot->total, $plot->is_west_open_amount, false);
            $extras[] = [
                'title' => 'West Open',
                'percent' => $plot->is_west_open_amount,
                'amount' => $amount
            ];
            $extraTotal += $amount;
        }
    
        return [
            'base_total' => $baseTotal,
            'extras' => $extras,
            'extra_total' => $extraTotal,
            'final_total' => $baseTotal + $extraTotal
        ];
    }
    
    /*public function getPaymentScheduleArray($plotId)
    {
        $plot = CustomerPlots::model()->findByPk($plotId);
    
        if (!$plot || empty($plot->payment_schedule_json)) {
            return [];
        }
    
        $data = json_decode($plot->payment_schedule_json, true);
    
        if (!isset($data['rows'])) {
            return [];
        }
    
        $rows = array_values($data['rows']); // reset index for next-row access
        $result = [];
    
        for ($i = 0; $i < count($rows); $i++) {
    
            $row = $rows[$i];
    
            $heading1 = strtolower(trim($row['heading1']));
            $heading2 = strtolower(trim($row['heading2']));
    
            // Skip pure empty rows (handled later if needed)
            if ($heading1 === 'empty box' && $heading2 === 'empty box') {
                continue;
            }
    
            // Normalize key
            $key = str_replace([' ', '-'], '_', $heading1);
    
            $result[$key] = [
                'heading1' => $row['heading1'],
                'heading2' => $row['heading2'],
                'total'    => $row['value'],
            ];
            
            
             $stFunc = self::paymentModeId($key);
             
             $result[$key]['rec'] = @$plot->$stFunc;
             $result[$key]['lastPaymentDate'] = @$plot->customerPlotTransactionslastCalling->createdOn;
    
            // ✅ Handle installment types
            if (in_array($key, ['monthly_installment', 'yearly', 'half_yearly','2nd_last_payment','last_payment'])) {
    
                $value = $row['value'];
    
                // Try full format first: 50000 X 36 = 1800000
                preg_match('/(\d+)\s*[\*xX]\s*(\d+)\s*=\s*(\d+)?/', $value, $matches);
    
                $amount   = isset($matches[1]) ? (int)$matches[1] : 0;
                $duration = isset($matches[2]) ? (int)$matches[2] : 0;
                $total    = isset($matches[3]) ? (int)$matches[3] : 0;
    
                // ✅ If total missing → take from next row (Empty Box)
                if ($total === 0 && isset($rows[$i + 1])) {
                    $nextRow = $rows[$i + 1];
    
                    if (
                        strtolower(trim($nextRow['heading1'])) === 'empty box' &&
                        is_numeric($nextRow['value'])
                    ) {
                        $total = (int)$nextRow['value'];
                    }
                }
    
                $result[$key]['amount']   = $amount;
                $result[$key]['duration'] = $duration;
                $result[$key]['total']    = $total;
            }
            
            if (in_array($key, ['booking','allocation','confirmation'])) {
    
                $value = $row['value'];
    
                // Try full format first: 50000 X 36 = 1800000
                preg_match('/(\d+)\s*[\*xX]\s*(\d+)\s*=\s*(\d+)?/', $value, $matches);
                if(!empty($matches)){
                    $amount   = isset($matches[1]) ? (int)$matches[1] : 0;
                    $duration = isset($matches[2]) ? (int)$matches[2] : 0;
                    $total    = isset($matches[3]) ? (int)$matches[3] : 0;
        
                    $result[$key]['amount']   = $amount;
                    $result[$key]['duration'] = $duration;
                    $result[$key]['total']    = $total;    
                }
                
            }
        }
    
        // Total COP
        if (isset($data['cop'])) {
            $result['total'] = (int)$data['cop'];
        }
        
        
        $dues = [];
        $monthStartDate = $plot->monthly_start_date;
        $lastPaymentDate = @$plot->customerPlotTransactionslastCalling->createdOn;
        
        if (!empty($monthStartDate)) {
        
            $start = new DateTime($monthStartDate);
            $now   = new DateTime();
        
            // Total months from start till now
            $totalMonths = ($start->diff($now)->y * 12) + $start->diff($now)->m + 1;
        
            // Months since last payment
            $dueMonths = $totalMonths;
        
            if (!empty($lastPaymentDate)) {
                $last = new DateTime($lastPaymentDate);
                $dueMonths = ($last->diff($now)->y * 12) + $last->diff($now)->m;
            }
        
            $order = ['booking', 'allocation', 'confirmation'];
            $monthCounter = 1;
        
            // ✅ Fixed stages
            foreach ($order as $key) {
        
                if (!isset($result[$key])) continue;
                if ($monthCounter > $totalMonths) break;
        
                $total = (int)$result[$key]['total'];
                $rec   = (int)$result[$key]['rec'];
        
                $remaining = $total - $rec;
        
                if ($remaining > 0) {
                    $dues[] = [
                        'stage' => $key,
                        'due'   => $remaining
                    ];
                }
        
                $monthCounter++;
            }
        
            // ✅ Monthly installment
            if (isset($result['monthly_installment'])) {
        
                $monthlyAmount = (int)$result['monthly_installment']['amount'];
                $monthlyRec    = (int)$result['monthly_installment']['rec'];
        
                $monthsForInstallment = $totalMonths - count($order);
        
                if ($monthsForInstallment > 0) {
        
                    $expected = $monthsForInstallment * $monthlyAmount;
                    $remaining = $expected - $monthlyRec;
        
                    if ($remaining > 0) {
                        $dues[] = [
                            'stage' => 'monthly_installment',
                            'months_due' => $monthsForInstallment,
                            'per_month' => $monthlyAmount,
                            'due' => $remaining
                        ];
                    }
                }
            }
            
            // // ✅ Monthly installment
            // if (isset($result['yearly'])) {
        
            //     $monthlyAmount = (int)$result['yearly']['amount'];
            //     $monthlyRec    = (int)$result['yearly']['rec'];
        
            //     $monthsForInstallment = $totalMonths - count($order);
        
            //     if ($monthsForInstallment > 0) {
        
            //         $expected = $monthsForInstallment * $monthlyAmount;
            //         $remaining = $expected - $monthlyRec;
        
            //         if ($remaining > 0) {
            //             $dues[] = [
            //                 'stage' => 'yearly',
            //                 'months_due' => $monthsForInstallment,
            //                 'per_month' => $monthlyAmount,
            //                 'due' => $remaining
            //             ];
            //         }
            //     }
            // }
        
            $result['dues'] = [
                'due_months' => $dueMonths,
                'items' => $dues
            ];
        }
        
        $summary = [];

        if (!empty($result['dues']['items'])) {
            foreach ($result['dues']['items'] as $item) {
        
                $stage = $item['stage'];
                $due   = $item['due'];
        
                $summary[$stage] = $due;
            }
        }
        
        $result['dues']['summary'] = $summary;


        return $result;
    }*/
    
    public function getPaymentScheduleArray($plotId)
    {
        $plot = CustomerPlots::model()->findByPk($plotId);
    
        if (!$plot || empty($plot->payment_schedule_json)) {
            return [];
        }
    
        $data = json_decode($plot->payment_schedule_json, true);
    
        if (!isset($data['rows'])) {
            return [];
        }
    
        $rows = array_values($data['rows']); // reset index for next-row access
        $result = [];
    
        for ($i = 0; $i < count($rows); $i++) {
    
            $row = $rows[$i];
    
            $heading1 = strtolower(trim($row['heading1']));
            $heading2 = strtolower(trim($row['heading2']));
    
            // Skip pure empty rows (handled later if needed)
            if ($heading1 === 'empty box' && $heading2 === 'empty box') {
                continue;
            }
    
            // Normalize key
            $key = str_replace([' ', '-'], '_', $heading1);
    
            $result[$key] = [
                'heading1' => $row['heading1'],
                'heading2' => $row['heading2'],
                'total'    => $row['value'],
            ];
            
             $stFunc = self::paymentModeId($key);
             $result[$key]['rec'] = 0;
             $result[$key]['lastPaymentDate'] = '';
             if(!empty($stFunc)){
             
                $result[$key]['rec'] = @$plot->$stFunc;
                $result[$key]['lastPaymentDate'] = @$plot->customerPlotTransactionslastCalling->createdOn;
             }
             
    
            // ✅ Handle installment types
            if (in_array($key, ['monthly_installment', 'yearly', 'half_yearly','2nd_last_payment','last_payment'])) {
    
                $value = $row['value'];
    
                // Try full format first: 50000 X 36 = 1800000
                preg_match('/(\d+)\s*[\*xX]\s*(\d+)\s*=\s*(\d+)?/', $value, $matches);
    
                $amount   = isset($matches[1]) ? (int)$matches[1] : 0;
                $duration = isset($matches[2]) ? (int)$matches[2] : 0;
                $total    = isset($matches[3]) ? (int)$matches[3] : 0;
    
                // ✅ If total missing → take from next row (Empty Box)
                if ($total === 0 && isset($rows[$i + 1])) {
                    $nextRow = $rows[$i + 1];
    
                    if (
                        strtolower(trim($nextRow['heading1'])) === 'empty box' &&
                        is_numeric($nextRow['value'])
                    ) {
                        $total = (int)$nextRow['value'];
                    }
                }
    
                $result[$key]['amount']   = $amount;
                $result[$key]['duration'] = $duration;
                $result[$key]['total']    = $total;
            }
            
            if (in_array($key, ['booking','allocation','confirmation'])) {
    
                $value = $row['value'];
    
                // Try full format first: 50000 X 36 = 1800000
                preg_match('/(\d+)\s*[\*xX]\s*(\d+)\s*=\s*(\d+)?/', $value, $matches);
                if(!empty($matches)){
                    $amount   = isset($matches[1]) ? (int)$matches[1] : 0;
                    $duration = isset($matches[2]) ? (int)$matches[2] : 0;
                    $total    = isset($matches[3]) ? (int)$matches[3] : 0;
        
                    $result[$key]['amount']   = $amount;
                    $result[$key]['duration'] = $duration;
                    $result[$key]['total']    = $total;    
                }
                
            }
        }
    
        // Total COP
        if (isset($data['cop'])) {
            $result['total'] = (int)$data['cop'];
        }
        
        
        // ================= DUES CALCULATION =================
        $dues = [];
        $summary = [];
        
        $monthStartDate = date('Y-m-01',strtotime($plot->monthly_start_date));
        $lastPaymentDate = @$plot->customerPlotTransactionslastCalling->createdOn;
        
        if (!empty($monthStartDate)) {
        
            $start = new DateTime($monthStartDate);
            $now   = new DateTime();
        
            // Total months from start till now
            $totalMonths = ($start->diff($now)->y * 12) + $start->diff($now)->m + 1;
        
            // Months since last payment
            $dueMonths = $totalMonths;
        
            if (!empty($lastPaymentDate)) {
                $last = new DateTime($lastPaymentDate);
                $dueMonths = ($last->diff($now)->y * 12) + $last->diff($now)->m;
            }
        
            // 👉 Proper stage order
            $order = [
                'booking',
                'allocation',
                'confirmation',
                'yearly',
                'half_yearly',
                'monthly_installment',
                '2nd_last_payment',
                'last_payment'
            ];
        
            foreach ($order as $key) {
        
                if (!isset($result[$key])) continue;
        
                $total = (int)$result[$key]['total'];
                $rec   = (int)$result[$key]['rec'];
        
                // ✅ Skip if fully paid
                if ($rec >= $total) continue;
        
                $remaining = $total - $rec;
        
                // ✅ Get per-month / per-cycle amount
                if (isset($result[$key]['amount']) && $result[$key]['amount'] > 0) {
                    $amount = (int)$result[$key]['amount'];
                } else {
                    // fallback (important for booking etc.)
                    $amount = $remaining;
                }
        
                // ✅ Calculate based on due months
                $calculatedDue = $dueMonths * $amount;
        
                // ✅ Cap to remaining
                $finalDue = min($calculatedDue, $remaining);
        
                if ($finalDue > 0) {
                    $dues[] = [
                        'stage' => $key,
                        'due'   => $finalDue
                    ];
        
                    $summary[$key] = $finalDue;
                }
            }
        
            $result['dues'] = [
                'due_months' => $dueMonths,
                'items'      => $dues,
                'summary'    => $summary,
                'total_due'  => array_sum($summary)
            ];
        }


        return $result;
    }
    
    
    
    public function paymentModeId($id){
        $modes = [
			'booking'=> 	'customerPlotTransactionSumBooking',
			'allocation'=> 	'customerPlotTransactionSumAllocation',
			'confirmation'=> 	'customerPlotTransactionSumConfirmation',
			'monthly_installment'=> 	'customerPlotTransactionSumMonthly',
			'yearly'=> 	'customerPlotTransactionSumYearly',
			'demarcation'=> 	'customerPlotTransactionSumDemarcation',
			'possession'=> 	'customerPlotTransactionSumPossession',
			'2nd_last_payment'=> 	'customerPlotTransactionSumDemarcation',
			'last_payment'=> 	'customerPlotTransactionSumPossession',
		];

		if(array_key_exists($id, $modes)){
			return $modes[$id];	
		} else{
			return '';
		}
    }
    
    public function getMonthlyDue($startDate, $monthlyAmount)
    {
        if (empty($startDate) || empty($monthlyAmount)) {
            return [
                'months' => 0,
                'due' => 0
            ];
        }
    
        $start = new DateTime($startDate);
        $now   = new DateTime();
    
        // Calculate difference
        $diff = $start->diff($now);
    
        // Convert years + months into total months
        $months = ($diff->y * 12) + $diff->m;
    
        // ✅ Optional: include current month if day passed
        if ($now->format('d') >= $start->format('d')) {
            $months += 1;
        }
    
        $due = $months * $monthlyAmount;
    
        return [
            'months' => $months,
            'monthly_amount' => (int)$monthlyAmount,
            'due' => $due
        ];
    }
    
    /**
	 * Pending dues for a booking (customer_plots.id).
	 * @see CustomerPlots::calculateDues()
	 * @param integer $bookingId
	 * @return array
	 */
	public function calculateBookingDues($bookingId)
	{
		return CustomerPlots::calculateDues($bookingId);
	}

}