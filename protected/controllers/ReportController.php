<?php

class ReportController extends Controller
{
	public function actionIndex()
	{
		$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
		$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
		$this->render('index',$data);
	}


	public function actionReport4()
	{
		$data['paymentmodes'] = PaymentModes::model()->findAll('amount = 0');
		$this->render('index4',$data);
	}

	public function actionSearch()
	{

		$development = 0;
		$data['development'] = $development;
		$criteria = new CDbCriteria();
		
		$criteria->addCondition('t.createdOn >= :startDate AND t.createdOn <= :endDate');
		$criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59');
		$criteria->order = "t.createdOn ASC";
		if($_POST['transaction_type'] == 'other'){
			$type = $_POST['transaction_type'];
			$criteria->addCondition("t.transaction_type != 'cash'");
		}
		if($_POST['transaction_type'] == 'cash'){
			$type = $_POST['transaction_type'];
			$criteria->addCondition("t.transaction_type = 'cash'");
		}
// 		else if($_POST['transaction_type'] != 'all'){
// 			$type = $_POST['transaction_type'];
// 			$criteria->addCondition("t.transaction_type LIKE '%$type%'");
// 		}
		
		if(!empty($_POST['mode'])){
			if($_POST['mode'] != 'all'){
				$sql = "SELECT GROUP_CONCAT(id) as id FROM payment_schedule_payment_modes WHERE mode = '".$_POST['mode']."' LIMIT 1";
				$data['paymentmodeID'] = Yii::app()->db->createCommand($sql)->queryRow();
				$criteria->addInCondition('plot_payment_mode_id',explode(',', $data['paymentmodeID']['id']));
			}
		}
		
		$criteria->addCondition('t.status = 1');
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$trans = CustomerPlotTransactions::model()->findAll($criteria);

		//extra transacrion
		$criteriaE = new CDbCriteria();
		$criteriaE->addCondition('t.createdOn >= :startDate AND t.createdOn <= :endDate');
		if($_POST['transaction_type'] == 'other'){
			$type = $_POST['transaction_type'];
			$criteriaE->addCondition("t.transaction_type != 'cash'");
		}
		if($_POST['transaction_type'] == 'cash'){
			$type = $_POST['transaction_type'];
			$criteriaE->addCondition("t.transaction_type = 'cash'");
		}
// 		else if($_POST['transaction_type'] != 'all'){
// 			$type = $_POST['transaction_type'];
// 			$criteriaE->addCondition("t.transaction_type LIKE '%$type%'");
// 		}

		if(!empty($_POST['mode'])){
			if($_POST['mode'] != 'all'){
				$criteriaE->addCondition("t.plot_payment_mode = '".$_POST['mode']."'");
			}
		}

		$criteriaE->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59');
		$criteriaE->order = "date(t.createdOn) ASC";
		$criteriaE->addCondition('t.status = 1');
		$extraTrans = CustomerPlotExtraTransactions::model()->findAll($criteriaE);	

		$data['model'] = array_merge($trans,$extraTrans);
		array_multisort( array_column($data['model'], "transaction_number"), SORT_ASC, $data['model'] );
		

		$data['modelCount'] = 0;

		//Normal Transaction
		$criteria->select = array('COUNT(DISTINCT t.plot_id) as total');
		$data['modelCountNormal'] = CustomerPlotTransactions::model()->with('plot')->find($criteria);

		//Extra Transaction
		$criteriaE->select = array('COUNT(DISTINCT t.plot_id) as total');
		$data['modelCountExtra'] = CustomerPlotExtraTransactions::model()->with('plot')->find($criteriaE);
		//}
		$data['modelCount'] = @$data['modelCountNormal']->total + @$data['modelCountExtra']->total;

		$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
		$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
		
		if(isset($_POST['submit']) && $_POST['submit'] == 'csv'){
            set_time_limit(0);
            ini_set('memory_limit','512M');
            set_time_limit(0);
        
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=transactions_report_'.date('Ymd_His').'.csv');
        
            $output = fopen('php://output', 'w');
        
            // CSV Header
            fputcsv($output, array(
                'Plot #',
                'Client Name',
                'Transaction #',
                'Transaction Type',
                'Payment Mode',
                'Amount',
                'Created On',
                'Created By'
            ));
        
            foreach($data['model'] as $row){
        
                $plot = @$row->plot->plot->block_number.'-'.
                        @$row->plot->plot->plot_type.'-'.
                        @$row->plot->plot->plot_number;
        
                $transactionNumber = ($this->startsWith($row->transaction_number, '#'))
                    ? $row->transaction_number
                    : '#'.ltrim($row->transaction_number,'0');
        
                // payment mode logic same as view
                if(isset($row->plot_payment_mode_id)){
                    $mode = ucfirst(@$row->plotPaymentMode->mode);
                } else {
                    $mode = ucfirst(@$row->plot_payment_mode);
                }
        
                fputcsv($output, array(
                    $plot,
                    @$row->plot->customer->name,
                    $transactionNumber,
                    @$row->transaction_type,
                    $mode,
                    $row->amount,
                    date('d M Y',strtotime($row->createdOn)),
                    $row->createdBy
                ));
            }
        
            fclose($output);
            Yii::app()->end();
        }
		$this->render('index',$data);
	}


	public function actionReportdev()
	{
		$data['transactions'] = [];
		$data['paymentmodes'] = PaymentModes::model()->findAll('amount = 0');
		$this->render('indexDev',$data);
	}

	public function actionSearchdev()
	{

		$modes = $_POST['mode'];
		if($modes == 'All'){
			$modes = ['development','penalty'];
		} else{
			$modes = [$_POST['mode']];
		}
		$result['transactions'] = [];
		foreach($modes as $m){
			$criteria = new CDbCriteria();
			$criteria->addBetweenCondition('t.createdOn', @$_POST['start_date'].' 00:00:00', @$_POST['end_date'].' 23:59:59');
			$criteria->order = "t.createdOn ASC";
			if($_POST['transaction_type'] != 'all'){
				$type = $_POST['transaction_type'];
				$criteria->addCondition("t.transaction_type LIKE '%$type%'");
			}
			$criteria->addCondition('plot_payment_mode = "'.$m.'"');		
			//$criteria->addCondition('plot.status = 1');
			$models = CustomerPlotExtraTransactions::model()->with('plot')->findAll($criteria);	
			$total = 0;
			foreach($models as $index=>$data){
				$result['transactions'][$m][$index]['id'] = $data->plot->id;
				$result['transactions'][$m][$index]['plot'] = $data->plot->plot->block_number.' / '.@$data->plot->plot->plot_number;
                $result['transactions'][$m][$index]['customer_name'] = $data->plot->customer->name;
                $result['transactions'][$m][$index]['transaction_number'] = ($this->startsWith($data->transaction_number, '#'))?$data->transaction_number:'#'.$data->transaction_number;
                $result['transactions'][$m][$index]['transaction_type'] = @$data->transaction_type;
                $result['transactions'][$m][$index]['plot_payment_mode'] = $data->plot_payment_mode;
                $result['transactions'][$m][$index]['amount'] = 'Rs. '.number_format($data->amount);
                $total = $total + $data->amount;
                $result['transactions'][$m][$index]['createdOn'] = date('d M,Y',strtotime($data->createdOn));
                $result['transactions'][$m][$index]['createdBy'] = $data->createdBy;
			}
			$result['transactions'][$m]['total'] = $total;
		}
		
		$data = $result;
		$this->render('indexDev',$data);
	}

	public function actionSearch4()
	{

		$result = array();
		foreach($_POST['mode'] as $m){
			$paymentmode = PaymentModes::model()->findByPk($m);
			$criteria = new CDbCriteria();
			$criteria->select = 'SUM(t.amount) as amount,COUNT(t.id) as id,t.transaction_type,COUNT(distinct(t.plot_id)) as plot_id';
			$criteria->addCondition('t.createdOn >= :startDate AND t.createdOn <= :endDate');
			$criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59');
			$criteria->order = "t.createdOn ASC";
			$criteria->group = "t.transaction_type";
			if($_POST['transaction_type'] != 'all'){
				$type = $_POST['transaction_type'];
				$criteria->addCondition("t.transaction_type LIKE '%$type%'");
			}
			$criteria->addCondition("t.plot_payment_mode_id IN ($m)");
			$criteria->addCondition('plot.status = 1');
			$model = CustomerPlotTransactions::model()->with('plot')->findAll($criteria);
			
			$result[$paymentmode->mode] = $paymentmode->attributes;
			foreach($model as $mm){
				$result[$paymentmode->mode]['data'][] = $mm->attributes;	
			}
			
		}
		//echo '<pre>';print_r($result);exit;
		$data['result'] = $result;
		$data['paymentmodes'] = PaymentModes::model()->findAll('amount = 0');
		$this->render('index4',$data);
	}

	public function actionReport2()
	{	
		$data['accounts'] = Accounts::model()->findAll('is_installment = 0 AND is_visible = 1');
		$this->render('index2',$data);
	}

	public function actionReport6()
	{	
		$this->render('index6');
	}

	public function actionSearch6()
	{
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('t.createdOn', @$_POST['start_date'].' 00:00:00', @$_POST['end_date'].' 23:59:59');
		$criteria->addCondition('plot.status = 1');
		$criteria->addCondition("mainPlot.phase_id = $phaseId");

		$criteria->order = "t.createdOn ASC";
		$data['model'] = CustomerPlotTransactions::model()->with(['plot','plot.plot'=>array('alias' => 'mainPlot')])->findAll($criteria);
		$data['accounts'] = Accounts::model()->findAll('is_visible = 1');
		$data['partners'] = Accounts::model()->findAll('is_installment = 0 AND is_visible = 1');

		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('t.createdOn', @$_POST['start_date'].' 00:00:00', @$_POST['end_date'].' 23:59:59');
		$criteria->order = "t.createdOn ASC";
		$data['cancelled'] = CustomerPlotCancelled::model()->findAll($criteria);

		//development
		$modes = ['development','penalty'];
		$result['transactions'] = [];
		foreach($modes as $m){
			$criteria = new CDbCriteria();
			$criteria->addBetweenCondition('t.createdOn', @$_POST['start_date'].' 00:00:00', @$_POST['end_date'].' 23:59:59');
			$criteria->order = "t.createdOn ASC";
			$criteria->addCondition('plot_payment_mode = "'.$m.'"');		
			//$criteria->addCondition('plot.status = 1');
			$models = CustomerPlotExtraTransactions::model()->with('plot')->findAll($criteria);	
			$total = 0;
			foreach($models as $index=>$datas){
				$result['transactions'][$m][$index]['id'] = $datas->plot->id;
				$result['transactions'][$m][$index]['plot'] = $datas->plot->plot->block_number.' / '.@$datas->plot->plot->plot_number;
                $result['transactions'][$m][$index]['customer_name'] = $datas->plot->customer->name;
                $result['transactions'][$m][$index]['transaction_number'] = ($this->startsWith($datas->transaction_number, '#'))?$datas->transaction_number:'#'.$datas->transaction_number;
                $result['transactions'][$m][$index]['transaction_type'] = @$datas->transaction_type;
                $result['transactions'][$m][$index]['plot_payment_mode'] = $datas->plot_payment_mode;
                $result['transactions'][$m][$index]['amount'] = 'Rs. '.number_format($datas->amount);
                $total = $total + $datas->amount;
                $result['transactions'][$m][$index]['createdOn'] = date('d M,Y',strtotime($datas->createdOn));
                $result['transactions'][$m][$index]['createdBy'] = $datas->createdBy;
			}
			$result['transactions'][$m]['total'] = $total;
		}
		
		$data['transactions'] = $result;
		$criteria = new CDbCriteria();
		$criteria->addCondition('createdOn >= :startDate AND createdOn <= :endDate');
		if(!empty($_POST['type'])){
			$criteria->addCondition('expense_type = :mode_id');
			$criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59',':mode_id' =>$_POST['type']);			
		} else{
			$criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59');			
		}
		
		$data['expenses'] = Expenses::model()->findAll($criteria);
		$this->render('index6',$data);
	}

	public function actionReport3()
	{	
		$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
		$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
		$data['agents'] = Agents::model()->findAll();
		$this->render('index3',$data);
	}
	
	public function actionCallingreport()
	{	
		//$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
		//$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
		$sql2 = "SELECT id,block_number FROM `plots` GROUP BY block_number;";
		$data['modes'] = Yii::app()->db->createCommand($sql2)->queryAll();
		$data['agents'] = Agents::model()->findAll();
		//echo '<pre>';print_r($data);exit;
		$this->render('index4',$data);
	}

	public function actionBlockreport()
	{	
		//$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
		//$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
		$sql2 = "SELECT id,block_number FROM `plots` GROUP BY block_number;";
		$data['modes'] = Yii::app()->db->createCommand($sql2)->queryAll();
		$data['agents'] = Agents::model()->findAll();
		//echo '<pre>';print_r($data);exit;
		$this->render('index5',$data);
	}
	
	public function actionDealerReport()
	{	
		$data['agents'] = Agents::model()->findAll();
		$this->render('dealer_report',$data);
	}
	
	public function actionMonthlyinstallmentreport(){
	    $data['agents'] = Agents::model()->findAll();
		$this->render('monthly_installment_report',$data);
	}
	
	public function actionMonthlyinstallsearch(){
// 	    print_r($_POST);exit;
//         $sql2 = "SELECT cp.id AS customer_booking_id FROM customer_plots cp WHERE cp.status = 1  AND EXISTS (      SELECT 1      FROM customer_plot_transactions t      WHERE t.plot_id = cp.id        AND t.plot_payment_mode_id = 4        AND t.amount < 50000  );";
// 		$data['model'] = Yii::app()->db->createCommand($sql2)->queryAll();

        $agent = (int)$_POST['agent'];
        $mode  = (int)$_POST['mode'];
        
        $sql2 = "
            SELECT cp.id AS customer_booking_id
            FROM customer_plots cp
            WHERE cp.status = 1
              AND cp.agent_id = {$agent}";
        if($agent == 0){
            $sql2 = "
            SELECT cp.id AS customer_booking_id
            FROM customer_plots cp
            WHERE cp.status = 1";
        }
        if ($mode == 1) {
            $sql2 .= "
                AND EXISTS (
                    SELECT 1
                    FROM customer_plot_transactions t
                    WHERE t.plot_id = cp.id
                      AND t.plot_payment_mode_id = 4
                      AND t.amount < 50000
                )
            ";
        } else {
            $sql2 .= "
                AND NOT EXISTS (
                    SELECT 1
                    FROM customer_plot_transactions t
                    WHERE t.plot_id = cp.id
                      AND t.plot_payment_mode_id = 4
                )
            ";
        }
        
        $data['model'] = Yii::app()->db->createCommand($sql2)->queryAll();
		$data['agents'] = Agents::model()->findAll();
		$data['mode'] = $mode;
		$this->render('monthly_installment_report',$data);    
	}


	public function actionSearch2()
	{
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('t.createdOn', @$_POST['start_date'].' 00:00:00', @$_POST['end_date'].' 23:59:59');
		$criteria->addCondition('plot.status = 1');
		$criteria->addCondition("mainPlot.phase_id = $phaseId");

		$criteria->order = "t.createdOn ASC";
		$data['model'] = CustomerPlotTransactions::model()->with(['plot','plot.plot'=>array('alias' => 'mainPlot')])->findAll($criteria);
		//$data['accounts'] = Accounts::model()->findAll('is_installment = 0');
		$data['accounts'] = Accounts::model()->findAll('is_visible = 1');
		$data['partners'] = Accounts::model()->findAll('is_installment = 0 AND is_visible = 1');

		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('t.createdOn', @$_POST['start_date'].' 00:00:00', @$_POST['end_date'].' 23:59:59');
		$criteria->order = "t.createdOn ASC";
		$data['cancelled'] = CustomerPlotCancelled::model()->findAll($criteria);
		//echo '<pre>';print_r($criteria);
		$this->render('index2',$data);
	}


	// public function plotDiscount($id,$number_format = true){
	// 	$plot = Plots::model()->findByPk($id);
	// 	$totalDiscount = 0;
		
	// 	$total = $this->plotTotal($id,false);
	// 	$totalDiscount = $this->Percentage($total,$plot->discount,0);
	// 	//$total = $total - $totalDiscount;
	// 	if($number_format){
	// 		return number_format($totalDiscount);	
	// 	} else{
	// 		return $totalDiscount;
	// 	}
		
	// }


	// public function plotTotal($id,$number_format = true,$is_total = true){
	// 	$plot = Plots::model()->findByPk($id);
	// 	$total = 0;
	// 	if($plot){
	// 		if($is_total){
	// 		$total += $plot->total;	
	// 		}
			
	// 		if($plot->is_road_facing == 1){
	// 			$total += $this->Percentage($plot->total,$plot->is_road_facing_amount,0);
	// 		}
	// 		if($plot->is_park_facing == 1){
	// 			$total += $this->Percentage($plot->total,$plot->is_park_facing_amount,0);	
	// 		}
	// 		if($plot->is_corner == 1){
	// 			$total += $this->Percentage($plot->total,$plot->is_corner_amount,0);
	// 		}
	// 		if($plot->is_west_open == 1){
	// 			$total += $this->Percentage($plot->total,$plot->is_west_open_amount,0);
	// 		}	
	// 	}
		
	// 	if($number_format){
	// 		return number_format($total);	
	// 	} else{
	// 		return $total;
	// 	}
		
	// }

// 	public function plotTotal($id,$number_format = true,$is_total = true,$withside = true){
// 		$plot = Plots::model()->findByPk($id);
// 		$plotTotal = 0;
// 		if($is_total){
// 			$plotTotal += @$plot->total;	
// 		}

// 		if($plot->is_corner == 1){
//             $cornerCharger = $this->Percentage($plotTotal,$plot->is_corner_amount,0);
//             $plotTotal = $plotTotal + $cornerCharger;
//         }

//         if($plot->is_park_facing == 1){
//             $parkFacing = $this->Percentage($plotTotal,$plot->is_park_facing_amount,0);
//             $plotTotal = $plotTotal + $parkFacing;
//         }

//         if($plot->is_west_open == 1){
//             $westOpen = $this->Percentage($plotTotal,$plot->is_west_open_amount,0);
//             $plotTotal = $plotTotal + $westOpen;
//         }

//         if($number_format){
// 			return number_format($plotTotal);	
// 		} else{
// 			return $plotTotal;
// 		}
		
// 	}
	
	
	
	public function plotTotal($id,$number_format = true,$is_total = true,$withside = true){
		$plot = Plots::model()->findByPk($id);
		$plotTotal = 0;
		if($is_total){
			$plotTotal += @$plot->total;	
		}
	
        
        if($plot->is_road_facing == 1){
            $roadCharger = $this->Percentage($plot->total,$plot->is_road_facing_amount,0);
            $plotTotal = $plotTotal + $roadCharger;
        }
        
        if($plot->is_corner == 1){
            $cornerCharger = $this->Percentage($plot->total,$plot->is_corner_amount,0);
            $plotTotal = $plotTotal + $cornerCharger;
        }

        if($plot->is_park_facing == 1){
            $parkFacing = $this->Percentage($plot->total,$plot->is_park_facing_amount,0);
            $plotTotal = $plotTotal + $parkFacing;
        }

        if($plot->is_west_open == 1){
            $westOpen = $this->Percentage($plot->total,$plot->is_west_open_amount,0);
            $plotTotal = $plotTotal + $westOpen;
        }
        
        if($plot->discount){
			$plotTotal = $plotTotal - $plot->discount;
		}

        if($number_format){
			return number_format($plotTotal);	
		} else{
			return $plotTotal;
		}
		
	}

	public function plotDiscount($id,$number_format = true){

		$plot = Plots::model()->findByPk($id);
		$totalDiscount = 0;
		if($plot->discount){
			$totalDiscount = $plot->discount;
		}
		$total = $this->plotTotal($id,false,true,false);
		$totalDiscount = $total - $totalDiscount;
		if($number_format){
			return number_format($totalDiscount);	
		} else{
			return $totalDiscount;
		}
		
	}

	public function actionSearch3()
	{
	    if($_POST['submitBtn'] == 'report'){
	        $criteria = new CDbCriteria();
    		$criteria->addCondition('t.createdOn >= :startDate AND t.createdOn <= :endDate AND plot.agent_id = :agent');
            $criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59',':agent'=>$_POST['agent']);	
            if(!empty($_POST['mode'])){
    			if($_POST['mode'] != 'all'){
    				$sql = "SELECT GROUP_CONCAT(id) as id FROM payment_schedule_payment_modes WHERE mode = '".$_POST['mode']."' LIMIT 1";
    				$data['paymentmodeID'] = Yii::app()->db->createCommand($sql)->queryRow();
    				$criteria->addInCondition('plot_payment_mode_id',explode(',', $data['paymentmodeID']['id']));
    			}
    		}
    		$criteria->order = "t.createdOn ASC";
    		$data['model'] = CustomerPlotTransactions::model()->with('plot')->findAll($criteria);
    		$data['agents'] = Agents::model()->findAll();
    		
    		//$data['monthBooking'] = 
    		$data['agentSelected'] = Agents::model()->findByPk($_POST['agent']);
    		$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
    		$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
    		$this->render('index3',$data);    
	    } 
	    
	    if($_POST['submitBtn'] == 'csv'){
	        $criteria = new CDbCriteria();
    		$criteria->addCondition('t.createdOn >= :startDate AND t.createdOn <= :endDate AND plot.agent_id = :agent');
            $criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59',':agent'=>$_POST['agent']);	
            if(!empty($_POST['mode'])){
    			if($_POST['mode'] != 'all'){
    				$sql = "SELECT GROUP_CONCAT(id) as id FROM payment_schedule_payment_modes WHERE mode = '".$_POST['mode']."' LIMIT 1";
    				$data['paymentmodeID'] = Yii::app()->db->createCommand($sql)->queryRow();
    				$criteria->addInCondition('plot_payment_mode_id',explode(',', $data['paymentmodeID']['id']));
    			}
    		}
    		$criteria->order = "t.createdOn ASC";
    		$data['model'] = CustomerPlotTransactions::model()->with('plot')->findAll($criteria);
    		$data['agents'] = Agents::model()->findAll();
    		
    		//$data['monthBooking'] = 
    		$data['agentSelected'] = $agent = Agents::model()->findByPk($_POST['agent']);
    		$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
    		$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
	        set_time_limit(0);
            
            header('Content-Type: text/csv; charset=utf-8');
            $fileNameCsv = $agent->name.'-dealer_transactions_'.date('Ymd_His').'.csv';
            header('Content-Disposition: attachment; filename='.$fileNameCsv);
        
            $output = fopen('php://output', 'w');
        
            // CSV Header
            fputcsv($output, array(
                'File #',
                'Plot #',
                'Client Name',
                'Transaction #',
                'Payment Mode',
                'Amount',
                'Created On'
            ));
        
            foreach($data['model'] as $row){
        
                $plot = @$row->plot->plot->block_number.' / '.@$row->plot->plot->plot_number;
        
                $transactionNumber = ($this->startsWith($row->transaction_number, '#'))
                    ? $row->transaction_number
                    : '#'.$row->transaction_number;
        
                fputcsv($output, array(
                    $row->plot->id,
                    $plot,
                    @$row->plot->customer->name,
                    $transactionNumber,
                    @$row->plotPaymentMode->mode,
                    $row->amount,
                    date('d M Y',strtotime($row->createdOn))
                ));
            }
        
            fclose($output);
            Yii::app()->end();
	    }
	    if($_POST['submitBtn'] == 'booking'){
	        $startDate = $_POST['start_date'].' 00:00:00';
	        $endDate = $_POST['end_date'].' 23:59:59';
	        $criteria = new CDbCriteria();
    		$criteria->addCondition('t.createdOn >= :startDate AND t.createdOn <= :endDate AND plot.agent_id = :agent');
            $criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59',':agent'=>$_POST['agent']);	
            if(!empty($_POST['mode'])){
    			if($_POST['mode'] != 'all'){
    				$sql = "SELECT GROUP_CONCAT(id) as id FROM payment_schedule_payment_modes WHERE mode = '".$_POST['mode']."' LIMIT 1";
    				$data['paymentmodeID'] = Yii::app()->db->createCommand($sql)->queryRow();
    				$criteria->addInCondition('plot_payment_mode_id',explode(',', $data['paymentmodeID']['id']));
    			}
    		}
    		// only booking transaction
            $criteria->addCondition('t.plot_payment_mode_id = 1');
        
            $criteria->addCondition("
                t.createdOn = (
                    SELECT MIN(t2.createdOn)
                    FROM customer_plot_transactions t2
                    WHERE t2.plot_id = t.plot_id
                    AND t2.plot_payment_mode_id = 1
                )
            ");
            
            $criteria->addBetweenCondition('t.createdOn', $startDate, $endDate);
    		$criteria->order = "t.createdOn ASC";
    		$data['model'] = CustomerPlotTransactions::model()->with('plot')->findAll($criteria);
    		$data['agents'] = Agents::model()->findAll();
    		
    		//$data['monthBooking'] = 
    		$data['agentSelected'] = Agents::model()->findByPk($_POST['agent']);
    		$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
    		$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
    		$this->render('index3',$data);      
            
	    }
	    
	}
	
// 	public function actionCallingsearch()
// 	{
// 	    $criteria = new CDbCriteria();

//         if (!empty($_POST['agent'])) {
//             $criteria->addInCondition('t.agent_id', $_POST['agent']);
//         }
        
//         if (!empty($_POST['mode'])) {
//           $criteria->compare('plot.block_number', $_POST['mode']);
//         }
        
//         $criteria->addCondition('t.status = 1');
//         $data['bookings'] = CustomerPlots::model()->with('plot', 'customer')->findAll($criteria);
// 		$data['agents'] = Agents::model()->findAll();
// 		$data['agentSelected'] = Agents::model()->findByPk($_POST['agent']);
// 		$sql2 = "SELECT id,block_number FROM `plots` GROUP BY block_number;";
// 		$data['modes'] = Yii::app()->db->createCommand($sql2)->queryAll();
// 	    //echo '<pre>';print_r($data);exit;
// 		$this->render('index4',$data);
// 	}

    public function actionCallingsearch()
    {
        $criteria = new CDbCriteria();
        if (!empty($_POST['agent']) && is_array($_POST['agent']) && !in_array('all', $_POST['agent'])) {
            $criteria->addInCondition('t.agent_id', $_POST['agent']);
        } else {
            // Get all agent IDs using listData
            $agentIds = array_keys(CHtml::listData(Agents::model()->findAll(), 'id', 'id'));
            
            if (!empty($agentIds)) {
                $criteria->addInCondition('t.agent_id', $agentIds);
            }
        }
    
        if (!empty($_POST['mode'])) {
            $criteria->compare('plot.block_number', $_POST['mode']);
        }
        $criteria->order = 't.createdOn ASC';
        $criteria->addCondition('t.status = 1');
        //$criteria->addCondition('t.status = 1 AND t.id = 1047');
        //$criteria->addCondition('t.status = 1 AND t.id IN (5302)');
    
        $data['bookings'] = CustomerPlots::model()
            ->with('plot','customer','agent')
            ->findAll($criteria);
    
        $data['agents'] = Agents::model()->findAll();
        $data['agentSelected'] = Agents::model()->findByPk($_POST['agent']);
    
        $sql2 = "SELECT id,block_number FROM plots GROUP BY block_number";
        $data['modes'] = Yii::app()->db->createCommand($sql2)->queryAll();
    
    
        /* ================= CSV EXPORT ================= */
    
        if(isset($_POST['submit']) && $_POST['submit'] == 'csv'){
    
            set_time_limit(0);
    
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=calling_report_'.date('Ymd_His').'.csv');
    
            $output = fopen('php://output', 'w');
    
            // CSV Header
            fputcsv($output, array(
                'Sr.#',
                'Date',
                'File #',
                'Name',
                'Contact #',
                'SQ.YDS.',
                'Block',
                'Plot',
                'Total AMT',
                'Paid AMT',
                'Rem. AMT',
                'Last AMT',
                'Dealer',
                'Remarks'
            ));
    
            foreach($data['bookings'] as $ind => $row){
    
                $plotBaseTotal = $this->finalViewBookingTotal($row->plot->id);
                
                $paid = intval(@$row->customerPlotTransactionSum) +
                        intval(@$row->customerPlotExtraTransactionSum);
    
                $remaining = @$plotBaseTotal['final_total'] - $paid;
    
                $lastAmt = @$row->transactionSumsByNumber.' / '.@$row->customerPlotTransactionslastCalling->createdOn;
    
                fputcsv($output, array(
                    $ind + 1,
                    date('d-m-Y',strtotime($row->createdOn)),
                    'HR-'.$row->id,
                    $row->customer->name,
                    $row->customer->mobile,
                    intval($row->plot->size->size),
                    $row->plot->block_number,
                    $row->plot->plot_number,
                    @$plotBaseTotal['final_total'],
                    $paid,
                    $remaining,
                    $lastAmt,
                    $this->getInitials($row->agent->name),
                    ''
                ));
            }
    
            fclose($output);
            Yii::app()->end();
        }
    
        /* ================= NORMAL VIEW ================= */
    
        $this->render('index4',$data);
    }

    public function actionBlocksearch()
    {
        $criteria = new CDbCriteria();
        if (!empty($_POST['agent']) && is_array($_POST['agent']) && !in_array('all', $_POST['agent'])) {
            $criteria->addInCondition('t.agent_id', $_POST['agent']);
        } else {
            // Get all agent IDs using listData
            $agentIds = array_keys(CHtml::listData(Agents::model()->findAll(), 'id', 'id'));
            
            if (!empty($agentIds)) {
                $criteria->addInCondition('t.agent_id', $agentIds);
            }
        }
    
        if (!empty($_POST['mode'])) {
            $criteria->compare('plot.block_number', $_POST['mode']);
        }
        $criteria->order = 't.createdOn ASC';
        $criteria->addCondition('t.status = 1');
        
        $data['bookings'] = CustomerPlots::model()
            ->with('plot','customer','agent')
            ->findAll($criteria);
    
        $data['agents'] = Agents::model()->findAll();
        $data['agentSelected'] = Agents::model()->findByPk($_POST['agent']);
    
        $sql2 = "SELECT id,block_number FROM plots GROUP BY block_number";
        $data['modes'] = Yii::app()->db->createCommand($sql2)->queryAll();
    
    
        /* ================= CSV EXPORT ================= */
    
        if(isset($_POST['submit']) && $_POST['submit'] == 'csv'){
    
            set_time_limit(0);
    
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=calling_report_'.date('Ymd_His').'.csv');
    
            $output = fopen('php://output', 'w');
    
            // CSV Header
            fputcsv($output, array(
                'Sr.#',
                'Date',
                'File #',
                'Name',
                'Contact #',
                'SQ.YDS.',
                'Block',
                'Plot',
                'Total AMT',
                'Paid AMT',
                'Rem. AMT',
                'Last AMT',
                'Dealer',
                'Remarks'
            ));
    
            foreach($data['bookings'] as $ind => $row){
    
                $plotBaseTotal = $this->finalViewBookingTotal($row->plot->id);
                
                $paid = intval(@$row->customerPlotTransactionSum) +
                        intval(@$row->customerPlotExtraTransactionSum);
    
                $remaining = @$plotBaseTotal['final_total'] - $paid;
    
                $lastAmt = @$row->transactionSumsByNumber.' / '.@$row->customerPlotTransactionslastCalling->createdOn;
    
                fputcsv($output, array(
                    $ind + 1,
                    date('d-m-Y',strtotime($row->createdOn)),
                    'HR-'.$row->id,
                    $row->customer->name,
                    $row->customer->mobile,
                    intval($row->plot->size->size),
                    $row->plot->block_number,
                    $row->plot->plot_number,
                    @$plotBaseTotal['final_total'],
                    $paid,
                    $remaining,
                    $lastAmt,
                    $this->getInitials($row->agent->name),
                    ''
                ));
            }
    
            fclose($output);
            Yii::app()->end();
        }
    
        /* ================= NORMAL VIEW ================= */
    
        $this->render('index5',$data);
    }
    
    function getInitials($name, $length = 6) {
        $name = trim($name);
        return strtoupper(substr($name, 0, $length));
    }
	
	public function actionDealerreportsearch()
	{
	    $criteria = new CDbCriteria();
		$criteria->addCondition('t.createdOn >= :startDate AND t.createdOn <= :endDate AND plot.agent_id = :agent');
        $criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59',':agent'=>$_POST['agent']);	
        //if(!empty($_POST['mode'])){
			//if($_POST['mode'] != 'all'){
				$sql = "SELECT GROUP_CONCAT(id) as id FROM payment_schedule_payment_modes WHERE mode IN ('Booking','Allocation','Confirmation') LIMIT 1";
				$data['paymentmodeID'] = Yii::app()->db->createCommand($sql)->queryRow();
				$criteria->addInCondition('plot_payment_mode_id',explode(',', $data['paymentmodeID']['id']));
			//}
		//}
		$criteria->order = "t.createdOn ASC";
		$data['model'] = CustomerPlotTransactions::model()->with('plot')->findAll($criteria);
		$data['agents'] = Agents::model()->findAll();
		$data['agentSelected'] = Agents::model()->findByPk($_POST['agent']);
		$data['start_date'] = $_POST['start_date'];
		$data['end_date'] = $_POST['end_date'];
		$sql = "SELECT id,mode FROM `payment_schedule_payment_modes` GROUP BY mode";
		$data['paymentmodes'] = Yii::app()->db->createCommand($sql)->queryAll();
	    if(isset($_POST['form'])){
	        $this->render('dealer_report',$data);
	    } else{
	        $agentName = $data['agentSelected']->name;
	        $headers = [
                'File #',
                'Plot #',
                'Client Name',
                'Transaction #',
                'Payment Mode',
                'Amount',
                'Commission',
                'Created On',
            ];
          		
          	$fileName = $agentName.'-Transactions-' . date('Y-m-d-H-i') . '.csv';
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
        
            $fp = fopen('php://output', 'w');
            
            /*// CSV Header
            fputcsv($fp, [
                'File #',
                'Block #',
                'Plot #',
                'Client Name',
                'Transaction #',
                'Payment Mode',
                'Amount',
                'Commission',
                'Created On',
            ]);
        
            foreach ($data['model'] as $data) {
                $amount = (float) $data->amount;
                $commission = $this->Percentage($amount, $data->plot->agent_percentage, 0);
        
                fputcsv($fp, [
                    @$data->plot->id,
                    @$data->plot->plot->block_number,
                    @$data->plot->plot->plot_number,
                    @$data->plot->customer->name,
                    ($this->startsWith($data->transaction_number, '#'))
                        ? $data->transaction_number
                        : '#' . $data->transaction_number,
                    @$data->plotPaymentMode->mode,
                    number_format($amount, 2, '.', ''),
                    number_format($commission, 2, '.', ''),
                    $data->createdOn,
                ]);
            }*/
            
            // CSV Header
            fputcsv($fp, [
                'File #',
                'Block #',
                'Plot #',
                'Category',
                'Size',
                'Road Facing',
            	'Road Facing %',
            	'Corner',
            	'Corner %',
            	'Park Facing',
            	'Park Facing %',
            	'West Open',
            	'West Open %',
            	'total',
            	'discount',
                'Client Name',
            	'Father Husband Name',
            	'Gender',
            	'Occupation',
            	'Dob',
            	'CNIC',
            	'Address',
            	'Phone',
            	'Office',
            	'Mobile',
            	'Email',
            	'Nominee Name',
            	'Nominee Relation',
            	'Nominee CNIC',
            	'Transaction #',
                'Payment Mode',
                'Amount',
            	'Reference Number',
            	'Transaction Type',
            	'Bank',
            	'Branch',
            	'Comment',
            	'Reason',
                'Commission',
                'Created On',
            ]);
            
            foreach ($data['model'] as $data) {
                $amount = (float) $data->amount;
                $commission = $this->Percentage($amount, $data->plot->agent_percentage, 0);
                
                fputcsv($fp, [
                    @$data->plot->id,
                    @$data->plot->plot->block_number,
                    @$data->plot->plot->plot_number,
                    @$data->plot->plot->category->name,
                    @$data->plot->plot->size->size,
                    @$data->plot->plot->is_road_facing,
            		@$data->plot->plot->is_road_facing_amount,
            		@$data->plot->plot->is_corner,
            		@$data->plot->plot->is_corner_amount,
            		@$data->plot->plot->is_park_facing,
            		@$data->plot->plot->is_park_facing_amount,
            		@$data->plot->plot->is_west_open,
            		@$data->plot->plot->is_west_open_amount,
            		@$data->plot->plot->total,
            		@$data->plot->plot->discount,
                    @$data->plot->customer->name,
            		@$data->plot->customer->father_husband_name,
            		@$data->plot->customer->gender,
            		@$data->plot->customer->occupation,
            		@$data->plot->customer->dob,
            		@$data->plot->customer->cnic,
            		@$data->plot->customer->address,
            		@$data->plot->customer->phone,
            		@$data->plot->customer->office,
            		@$data->plot->customer->mobile,
            		@$data->plot->customer->email,
            		@$data->plot->customer->nominee_name,
            		@$data->plot->customer->nominee_relation,
            		@$data->plot->customer->nominee_cnic,
                    ($this->startsWith($data->transaction_number, '#'))
                        ? $data->transaction_number
                        : '#' . $data->transaction_number,
                    @$data->plotPaymentMode->mode,
                    number_format($amount, 2, '.', ''),
                    @$data->reference_number,
            		@$data->transaction_type,
            		@$data->bank,
            		@$data->branch,
            		@$data->comment,
            		@$data->reason,
                    number_format($commission, 2, '.', ''),
                    $data->createdOn,
                ]);
            }
            fclose($fp);
            Yii::app()->end();
    //         $count = 1;
    //         foreach($plots as $ind=>$plot):
    // 	        $list[$count][] = $ind+1;
    // 	        $list[$count][] = @$plot->block_number;
    	        
    // 			$count++;
    //         endforeach;
    
    //         $fileName = 'Available-Plots-'.date('Y-m-d-H-i').'.csv';
    // 		$fp = fopen($fileName, 'w');
    //       	foreach ($list as $fields) {
    //           fputcsv($fp, $fields);
    //       	}
    
    
    
    //       	$filename = getcwd().'/'.$fileName;
    //       	$fileName = 'CommisionReport-' . date('Y-m-d') . '.csv';
    //       	header("Content-type: text/csv");
    // 		header("Content-disposition: attachment; filename = $fileName");
    // 		readfile($filename);
	    }
	}
	

	public function Percentage($total,$percentage,$type=1){
		if($type == 1){
			return number_format((@$percentage / 100) * @$total);	
		} else{
			return ((@$percentage / 100) * @$total);
		}
		
	}

	public function DealerPercentage($agent,$total,$percentage,$type=1,$mode=''){
		if($mode->is_distribute == 1){
			if($type == 1){
				$total = (($total/100))/10; 
				//return number_format((((@$percentage / 100) * @$total)/100)*$agent->percentage_value);	
				return number_format((@$total*$agent->percentage_value));	
			} else{
				$total = (($total/100))/10; 
				//return (((@$percentage / 100) * @$total)/100)*$agent->percentage_value;
				return (@$total*$agent->percentage_value);
			}	
		} else{
			return 0;
		}
		
		
	}

	public function actionViewPdf(){
		$labor = Labours::model()->findByPk($id);
		$pdf = Yii::createComponent('application.extensions.tcpdf.ETcPdf', 
                        'L', 'cm', 'A4', true, 'UTF-8');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor("ENGRO");
        $pdf->SetTitle("Applicant Training Report");
        $pdf->SetSubject("Applicant Training Report");
        $pdf->SetKeywords("Applicant Training,Report,");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont("times", "N", 9);


        $html='<style>.table-pdf td{text-align:center;font-weight:normal;vertical-align:middle;font-style:normal;padding:10px}</style>';
        //generate report data
        
        $html .= '<table class="table-pdf" border="1">';
        $html .= '<tr><td colspan="9" style="text-align:center">'.$labor->full_name.' Training Report</td></tr>';
        
        $html .= '<tr><td colspan="9"></td></tr>';
        $html .= '<tr><td >ID</td><td>Name</td><td>Type</td><td>Batch No.</td><td>Start Date</td><td>End Date</td><td>Status</td><td>Score</td><td>Result</td></tr>';
        if($labor->traings){
        	foreach($labor->traings as $d){ 
              //foreach($requisition->clientCompanyRequisitionDetails as $d){
                $html .= '<tr><td >'.$d->training->id.'</td><td>'.$d->training->institute_name.'</td><td>'.$d->training->training_type.'</td><td>'.$d->training->batch_no.'</td><td>'.$d->training->start_date.'</td><td>'.$d->training->end_date.'</td><td>'.(($d->training->status==0)?'Open':'End').'</td><td>'.$d->score.'</td><td>'.$d->result.'</td></tr>'; 
            }
        }

        $html .= '</table>';
        // output the HTML content
        $pdf->writeHTML($html, true, true, true, true, '');
        $pdf->Output("result.pdf", "I");
	}

	/*public function actionCancelled()
	{	
		//$data['cancelled'] = CustomerPlotCancelled::model()->findAll(array('group'=>'booking_id'));
		//$data['cancelled'] = CustomerPlotCancelled::model()->findAll();
		$data['cancelled'] = CustomerPlotCancelled::model()->findAll([
            'order' => 'createdOn DESC'
        ]);
		$this->render('cancelled',$data);
	}*/
	public function actionCancelled()
    {
        $criteria = new CDbCriteria();
    
        $startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $endDate   = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
    
        if ($startDate != '') {
            $criteria->addCondition('createdOn >= :start_date');
            $criteria->params[':start_date'] = $startDate . ' 00:00:00';
        }
    
        if ($endDate != '') {
            $criteria->addCondition('createdOn <= :end_date');
            $criteria->params[':end_date'] = $endDate . ' 23:59:59';
        }
    
        $criteria->order = 'createdOn DESC';
    
        $data['cancelled'] = CustomerPlotCancelled::model()->findAll($criteria);
    
        $this->render('cancelled', $data);
    }
	
	public function actionCancelledcsv()
	{	
		//$cancelled = CustomerPlotCancelled::model()->findAll();
		$criteria = new CDbCriteria();
    
        $startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $endDate   = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
    
        if ($startDate != '') {
            $criteria->addCondition('createdOn >= :start_date');
            $criteria->params[':start_date'] = $startDate . ' 00:00:00';
        }
    
        if ($endDate != '') {
            $criteria->addCondition('createdOn <= :end_date');
            $criteria->params[':end_date'] = $endDate . ' 23:59:59';
        }
    
        $criteria->order = 'createdOn DESC';
    
        $cancelled = CustomerPlotCancelled::model()->findAll($criteria);
        
		set_time_limit(0);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=cancelled_bookings_'.date('Ymd_His').'.csv');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, array(
            'Booking ID',
            'Customer Name',
            'Customer Number',
            'Dealer',
            'Block',
            'Plot',
            'Paid Amount',
            'Deduct Amount',
            'Cancelled Date',
            'Reason'
        ));

        foreach($cancelled as $row){

            fputcsv($output, array(
                $this->getBookingRegNo($row->booking->id),
                @$row->booking->customer->name,
                @$row->booking->customer->mobile,
                @$row->booking->agent->name,
                @$row->booking->plot->block_number,
                @$row->booking->plot->plot_type.' - '.$row->booking->plot->plot_number,
                intval(@$row->booking->customerPlotTransactionSum),
                intval(@$row->amount),
                date('d M Y',strtotime(@$row->createdOn)),
                @$row->reason
            ));
        }

        fclose($output);
        Yii::app()->end();
	}

	public function actionBlocked()
	{	
		//$data['cancelled'] = CustomerPlotCancelled::model()->findAll(array('group'=>'booking_id'));
		$data['cancelled'] = CustomerPlots::model()->findAll('blocked=1');
		$this->render('blocked',$data);
	}



	public function actionTransfered()
	{	
		//$data['cancelled'] = CustomerPlotCancelled::model()->findAll(array('group'=>'booking_id'));
		$data['transfered'] = CustomerPlotTransfers::model()->findAll();

		$this->render('transfered',$data);
	}



	public function actionMonthlyreport(){
		$data['paymentmodes'] = PaymentModes::model()->findAll('amount = 0');
		$this->render('monthlyreport',$data);
	}

	public function actionMonthlyreportsearch(){

		$month = date("m",strtotime($_POST['start_date']));
		$year = date("Y",strtotime($_POST['start_date']));
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		
		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('t.createdOn',"'01-$month-$year'", "'31-$month-$year'");
		$data['model'] = CustomerPlotTransactions::model()->findAll($criteria);

		print_r($data);
	}


	public function actionExportavailableplot(){
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$plots = Plots::model()->findAll("status = 0 AND phase_id = $phaseId");
		$list = array (
	        array('S.No','Block #','Plot Type','Plot #','Details','Sq. Yds.','Category','SCHEDULE','Dealer','Sub Dealer','Discount','Commission','Date','Commision Recived','Payment','Number','Corner','West Open','Park Facing'),
	    );
      		
      	
        $count = 1;
        foreach($plots as $ind=>$plot):
	        $list[$count][] = $ind+1;
	        $list[$count][] = @$plot->block_number;
	        $list[$count][] = @$plot->plot_type;
	        $list[$count][] = @$plot->plot_number;
	        $list[$count][] = @$plot->plot_type.'-'.$plot->plot_number.'/'.@$plot->block_number;
            $list[$count][] = @$plot->size->size;
            $list[$count][] = @$plot->category->name;
            $list[$count][] = @($plot->agentReserve)?@$plot->agentReserve[0]->paymentSchedule->name:'';
            $list[$count][] = @($plot->agentReserve)?@$plot->agentReserve[0]->agent->name:'';
			$list[$count][] = @($plot->agentReserve)?@$plot->agentReserve[0]->agent->agentParent->name:'';
			$list[$count][] = @$plot->discount;
			$list[$count][] = @($plot->agentReserve)?@$plot->agentReserve[0]->commission:'0';
			$list[$count][] = '';
			$list[$count][] = '';
			$list[$count][] = '';
			$list[$count][] = '';
			$list[$count][] = @($plot->is_corner)?'1':'0';;
			$list[$count][] = @($plot->is_west_open)?'1':'0';;
			$list[$count][] = @($plot->is_park_facing)?'1':'0';;
			$count++;
        endforeach;

        $fileName = 'Available-Plots-'.date('Y-m-d-H-i').'.csv';
		$fp = fopen($fileName, 'w');
      	foreach ($list as $fields) {
          fputcsv($fp, $fields);
      	}



      	$filename = getcwd().'/'.$fileName;
      	header("Content-type: text/csv");
		header("Content-disposition: attachment; filename = $fileName");
		readfile($filename);
	}
	
	
	public function actionCommissionreport(){
	   /* $userModel = Yii::app()->session->get('userModel');
		$criteria = new CDbCriteria();
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$criteria->addCondition("phase_id = $phaseId");
		$criteria->addCondition("expense_type IN (3,7)");
		$criteria->order = "createdOn DESC, booking_id ASC";
		$criteria->limit = 50;
		$data['expenses'] = $expenses = Expenses::model()->findAll($criteria);
		$this->render('comm-report',$data);*/
		
		$userModel = Yii::app()->session->get('userModel');
        $phaseId = $userModel['phase_id'];
        
        // Get 50 booking IDs
        $criteria = new CDbCriteria();
        $criteria->select = 'booking_id';
        $criteria->distinct = true;
        $criteria->addCondition("phase_id = $phaseId");
        $criteria->addCondition("expense_type IN (3,7)");
        $criteria->addCondition("booking_id IS NOT NULL");
        $criteria->addCondition("booking_id != ''");
        $criteria->order = "booking_id ASC";
        $criteria->limit = 200;
        
        $bookingModels = Expenses::model()->findAll($criteria);
        
        $bookingIds = array();
        
        foreach ($bookingModels as $booking) {
            $bookingIds[] = $booking->booking_id;
        }
        
        // Get all records for those 50 bookings
        // PLUS records where booking_id is empty/null
        $criteria = new CDbCriteria();
        $criteria->addCondition("phase_id = $phaseId");
        $criteria->addCondition("expense_type IN (7)");
        
        $bookingCondition = new CDbCriteria();
        
        if (!empty($bookingIds)) {
            $bookingCondition->addInCondition('booking_id', $bookingIds);
        }
        
        $bookingCondition->addCondition("booking_id IS NULL", 'OR');
        $bookingCondition->addCondition("booking_id = ''", 'OR');
        
        $criteria->mergeWith($bookingCondition);
        
        $criteria->order = "booking_id ASC, createdOn DESC";
        
        $data['expenses'] = Expenses::model()->findAll($criteria);
        
        $this->render('comm-report', $data);
	}



	
	

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}