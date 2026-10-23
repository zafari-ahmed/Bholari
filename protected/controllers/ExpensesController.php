<?php

class ExpensesController extends Controller
{
    public function actionGetExpenseTypes()
    {
        echo CJSON::encode(Yii::app()->params['expenseTypes']);
        Yii::app()->end();
    }
    
	public function actionAdd()
	{
		$data['accounts'] = Accounts::model()->findAll();
		$data['phases'] = Phases::model()->findAll();
		$data['booking'] = [];
		if(isset($_GET['booking_id'])){
			$data['booking'] = CustomerPlots::model()->findByPk($_GET['booking_id']);	
		}
		$data['expenseTypes'] = Yii::app()->params['expenseTypes'];
		$criteria = new CDbCriteria();
		$criteria->group = "paid_to";
		$data['paid_to_list'] = Expenses::model()->findAll($criteria);
		
		$this->render('add',$data);
	}
	
	public function actionAdddealercommision(){
	    $data['accounts'] = Accounts::model()->findAll();
		$data['phases'] = Phases::model()->findAll();
		$criteria = new CDbCriteria();
		$criteria->group = "paid_to";
		$criteria = new CDbCriteria();
		$criteria->addCondition('t.createdOn >= :startDate AND t.createdOn <= :endDate AND plot.agent_id = :agent');
        $criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59',':agent'=>$_POST['agent']);	
		$sql = "SELECT GROUP_CONCAT(id) as id FROM payment_schedule_payment_modes WHERE mode IN ('Booking','Allocation','Confirmation') LIMIT 1";
		$data['paymentmodeID'] = Yii::app()->db->createCommand($sql)->queryRow();
		$criteria->addInCondition('plot_payment_mode_id',explode(',', $data['paymentmodeID']['id']));
		$criteria->order = "t.createdOn ASC";
		$model = CustomerPlotTransactions::model()->with('plot')->findAll($criteria);
	    $bookingIds = [];$commission = 0;$msg = '';
		if($model){
		    foreach($model as $m):
                $bc = $this->Percentage($m->amount,$m->plot->agent_percentage,0);
                $tm = 'Agent commission('.$m->plot->agent_percentage.'%) against transaction #'.$m->transaction_number.' for amount '.$m->amount."\n";
                array_push($bookingIds,array('bookingId'=>$m->plot->id,'amount'=>$bc,'trans'=>$m->id,'message'=>$tm));
                $msg .= $tm;
                $commission = $commission + $this->Percentage($m->amount,$m->plot->agent_percentage,0);
            endforeach;
		}
		$data['totalCom'] = $commission;
		$data['msg'] = $msg;
		$data['bookingInfo'] = $bookingIds;
		$data['agents'] = Agents::model()->findAll();
		$data['agentSelected'] = Agents::model()->findByPk($_POST['agent']);
		$data['start_date'] = $_POST['start_date'];
		$data['end_date'] = $_POST['end_date'];
		
		$this->render('dealerCom',$data);
	}

	public function actionDelete($status,$id)
	{
	    
// 		if($status==1){
// 			$expense = Expenses::model()->findByPk($id);
// 			$expense->status = 1;
// 			$expense->save(false);
// 			$url  = Yii::app()->baseUrl.'/expenses/expenseinvoice/'.$id;
// 			//Yii::app()->user->setFlash('success','Expense update successfully.');
// 			$this->redirect($url);
// 		}
		//if($status != 1){
			$data['expense'] = Expenses::model()->findByPk($id);
			$data['status'] = 0;
			$this->render('delete',$data);
		//}
	}
	
	public function actionDeletedirect($id)
	{
	    
		$expense = Expenses::model()->findByPk($id);
		if($expense){
		    $expense->delete();
		    Yii::app()->user->setFlash('success','Expense deleted successfully.');
		    $this->redirect(Yii::app()->baseUrl.'/report/commissionreport');    
		}

	}

	public function actionSummary()
	{
		$userModel = Yii::app()->session->get('userModel');
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$sql = "SELECT expense_type,SUM(amount) as amount FROM expenses WHERE STATUS = 1 AND expense_type != 15 AND phase_id = $phaseId GROUP BY expense_type;";
  		$data['result'] = $expenseData = Yii::app()->db->createCommand($sql)->queryAll();
  		$expenseJson = [];
  		foreach($expenseData as $in=>$expense){
  			$expenseJson[$in]['name'] = ucfirst($this->expenseType(@$expense['expense_type']));
  			$expenseJson[$in]['y'] = $expense['amount'];
  		}
  		$data['expenseJson'] = $expenseJson;
  		//echo '<pre>';print_r($data);exit;
		$this->render('indexSummarize',$data);
	}

	public function actionIndex()
	{
		$userModel = Yii::app()->session->get('userModel');
		$criteria = new CDbCriteria();
		if(isset($_GET['development'])){
			$criteria->addCondition("expense_type = 'development'");	
		} else{
			$criteria->addCondition("expense_type != 'development'");
		}

		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$criteria->addCondition("phase_id = $phaseId");
		
		$criteria->order = "createdOn DESC";
		$data['expenses'] = $expenses = Expenses::model()->findAll($criteria);


		$userModel = Yii::app()->session->get('userModel');
		$criteria = new CDbCriteria();
		$criteria->addCondition("phase_id = $phaseId");
		$criteria->order = "createdOn DESC";
		$data['expensesPettyCash'] = $expensesPettyCash = PettyCashExpenses::model()->findAll($criteria);
		$data['expenses'] = array_merge($expenses,$expensesPettyCash);
		array_multisort( array_column($data['expenses'], "createdOn"), SORT_ASC, $data['expenses']);
		$this->render('index-all',$data);
	}

	public function actionSave(){
		if($_POST['description']){
			$expense = new Expenses;
			$expense->attributes = $_POST;
			$expense->account_id = 5;
			$expense->user_id = Yii::app()->session['userModel']['id'];
			$expense->createdOn = $_POST['createdOn'].' '.date('H:i:s');
			$expense->status = 2;
			if(isset($_POST['booking_id'])){
			    $expense->status = 1;
			}
			$expense->reason = NULL;
			$expense->save(false);
			if($_POST['expense_type']==15){
				$pettyCash = new PettyCash;
				$pettyCash->expense_id = $expense->id;
				$pettyCash->amount = $_POST['amount'];
				$pettyCash->description = $_POST['description'];
				$pettyCash->reference_number = $_POST['number'];
				$pettyCash->phase_id = Yii::app()->session->get('userModel')['phase_id'];
				$pettyCash->created_by = Yii::app()->session['userModel']['id'];
				$pettyCash->createdOn = $_POST['createdOn'].' '.date('H:i:s');
				$pettyCash->save();

				//update expense as approved
				$expense->status = 1;
				$expense->save(false);
			}

			$expenseMsg = $expense->description.' of worth '.$expense->amount.' paid to '.$expense->paid_to;
			Yii::app()->user->setFlash('success','Expense add successfully.'.$expenseMsg);
			if(isset($_POST['booking_id'])){
			    $this->redirect(Yii::app()->baseUrl.'/booking/viewbooking/'.$_POST['booking_id']);    
			} else{
			    $this->redirect(Yii::app()->baseUrl.'/expenses/add');
			}
            
		}
	}
	
	
	public function actionSavedealer(){
	    $agentComm = json_decode($_POST['bookingInfo']);
	    if(!empty($agentComm)){
	        foreach($agentComm as $comm){
	            $expense = new Expenses;
                $expense->expense_type = $_POST['expense_type'];
                $expense->account_id = 5;
                $expense->description = $comm->message;
                $expense->amount = $comm->amount;
                $expense->user_id = Yii::app()->session['userModel']['id'];
                $expense->status = 1;
                $expense->reason = NULL;
                $expense->number = $_POST['number'];
                $expense->createdOn = $_POST['createdOn'].' '.date('H:i:s');
                //$expense->phase_id = ;
                $expense->booking_id = $comm->bookingId;
                $expense->payment_mode = $_POST['payment_mode'];
                $expense->paid_to = $_POST['paid_to'];
                $expense->bank = $_POST['bank'];
                $expense->cnic = $_POST['number'];
                $expense->save(false);   
	        }
	    }
		Yii::app()->user->setFlash('success','Commision Record added successfully.');
		$this->redirect(Yii::app()->baseUrl.'/expenses');
	}

	public function actionEdit($id)
	{
		$data['accounts'] = Accounts::model()->findAll();
		$data['phases'] = Phases::model()->findAll();
		$criteria = new CDbCriteria();
		$criteria->group = "paid_to";
		$data['paid_to_list'] = Expenses::model()->findAll($criteria);
		$data['expense'] = expenses::model()->findByPk($id);
		$data['expenseTypes'] = Yii::app()->params['expenseTypes'];
		
		$this->render('edit',$data);
		
	}
	
	public function actionJournal($id)
	{
		
		$data['expense'] = Expenses::model()->findByPk($id);
		$data['expenseTypes'] = Yii::app()->params['expenseTypes'];
		
		$this->renderPartial('journal_ledger',$data);
		
	}


	public function actionUpdate(){
		if($_POST['id']){
			$expense = Expenses::model()->findByPk($_POST['id']);
			$expense->attributes = $_POST;
			$expense->status = @$_POST['status'];
			$expense->reason = @$_POST['reason'];
			$expense->save(false);
			if($_POST['expense_type']==15){
				$pettyCash = PettyCash::model()->find('expense_id = :id',array(':id'=>$expense->id));
				$pettyCash->expense_id = $expense->id;
				$pettyCash->amount = @$_POST['amount'];
				$pettyCash->description = @$_POST['description'];
				$pettyCash->reference_number = @$_POST['number'];
				$pettyCash->phase_id = Yii::app()->session->get('userModel')['phase_id'];
				$pettyCash->created_by = Yii::app()->session['userModel']['id'];
				$pettyCash->createdOn = @$_POST['createdOn'].' '.date('H:i:s');
				$pettyCash->save();
			}
			Yii::app()->user->setFlash('success','Expense update successfully.');
            $this->redirect(Yii::app()->baseUrl.'/expenses');
		}
	}

	public function actionReport()
	{	$criteria = new CDbCriteria();
		$criteria->group = "paid_to";
		$data['paid_to_list'] = Expenses::model()->findAll($criteria);
		$this->render('report2',$data);
	}

	public function actionReportsearch()
	{	
		//echo '<pre>';print_r($_POST);exit;
		$criteria = new CDbCriteria();
		//$criteria->addBetweenCondition('createdOn', @$_POST['start_date'], @$_POST['end_date']);
		$criteria->addCondition('createdOn >= :startDate AND createdOn <= :endDate');
		$criteria->addCondition("expense_type != 15");	
		if($_POST['paid_to']!='All'){
			$criteria->addCondition("paid_to = '{$_POST['paid_to']}'");	
		}

		if($_POST['payment_mode']!='All'){
			$criteria->addCondition("payment_mode = '{$_POST['payment_mode']}'");	
		}
		
		if(!empty($_POST['expense_type'])){
			$criteria->addCondition('expense_type = :mode_id');
			$criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59',':mode_id' =>$_POST['expense_type']);			
		} else{
			$criteria->params = array(':startDate' =>$_POST['start_date'].' 00:00:00',':endDate' =>$_POST['end_date'].' 23:59:59');			
		}
		
		$data['expenses'] = Expenses::model()->findAll($criteria);
		$criteria = new CDbCriteria();
		$criteria->group = "paid_to";
		$data['paid_to_list'] = Expenses::model()->findAll($criteria);
		$this->render('report2',$data);
	}


	public function actionexpenseinvoice($id,$type='expense'){
		$data['link'] = Yii::app()->baseUrl.'/expenses';
		if($type=='expense'){
			$data['expense'] = Expenses::model()->findByPk($id);
		} else{
			$data['expense'] = PettyCashExpenses::model()->findByPk($id);
		}
		$this->renderpartial('printInvoice',$data);
	}


	public function getIndianCurrency(float $number)
	{
	    $decimal = round($number - ($no = floor($number)), 2) * 100;
	    $hundred = null;
	    $digits_length = strlen($no);
	    $i = 0;
	    $str = array();
	    $words = array(0 => '', 1 => 'one', 2 => 'two',
	        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
	        7 => 'seven', 8 => 'eight', 9 => 'nine',
	        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
	        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
	        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
	        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
	        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
	        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
	    $digits = array('', 'hundred','thousand','lac', 'crore');
	    while( $i < $digits_length ) {
	        $divider = ($i == 2) ? 10 : 100;
	        $number = floor($no % $divider);
	        $no = floor($no / $divider);
	        $i += $divider == 10 ? 1 : 2;
	        if ($number) {
	            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
	            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
	            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
	        } else $str[] = null;
	    }
	    $Rupees = implode('', array_reverse($str));
	    $paise = ($decimal) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
	    return ($Rupees ? $Rupees . ' Rupees Only ' : '') . $paise;
	}

	public function actionReportall(){
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$list = array (
	        array('#','H.O.A','Desc','Amount','Mode','Bank','Ref.','Paid To','CNIC/NTN','Cr. By','Date','Status','Reason'),
	    );
      	$criteria = new CDbCriteria();
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$criteria->addCondition("phase_id = $phaseId");
      	//$criteria->addCondition("status = 1");
      	$expenses = Expenses::model()->findAll($criteria);
        $count = 1;
        foreach($expenses as $expense):
        	
	        $list[$count][] = @$this->getExpenseRegNo($expense->id);
	        $list[$count][] = ucfirst($this->expenseType(@$expense->expense_type));
	        $list[$count][] = $expense->description;
	        $list[$count][] = number_format($expense->amount);
	        $list[$count][] = @$expense->payment_mode;
	        $list[$count][] = @$expense->bank;
	        $list[$count][] = '*'.@$expense->number.'*';
	        $list[$count][] = ucfirst(@$expense->paid_to);
	        $list[$count][] = @$expense->cnic;
	        $list[$count][] = @$expense->user->first_name.''.@$expense->user->last_name;
	        $list[$count][] = ($expense->createdOn);
	        if($expense->status==0){
	        	$list[$count][] = 	'Rejected';
	        }
	        if($expense->status==1){
	        	$list[$count][] = 	'Approved';
	        }
	        if($expense->status==2){
	        	$list[$count][] = 	'Pending';
	        }
           	$count++;
        endforeach;
        //echo '<pre>';print_r($list);exit;
        $fName = 'allExpense-'.date('dMY-h:i').'.csv';
		$fp = fopen($fName, 'w');
      	foreach ($list as $fields) {
          fputcsv($fp, $fields);
      	}

      	$filename = getcwd().'/'.$fName;
      	header("Content-type: text/csv");
		header("Content-disposition: attachment; filename = $fName");
		readfile($filename);
	}

	public function actionaccounts(){
		$this->render('report2-account');
	}

	public function actionReportsearchaccount()
	{	
		$startDate  = $data['start_date'] = $_POST['start_date'].' 00:00:00';
		$endDate = $data['end_date'] = $_POST['end_date'].' 23:59:59';
		$days = [];
		$period = new DatePeriod(
		     new DateTime($_POST['start_date']),
		     new DateInterval('P1D'),
		     new DateTime($_POST['end_date'])
		);
		
		//$days[] = $period->start->format('Y-m-d');
		foreach ($period as $key => $value) {
		    $days[] = $value->format('Y-m-d');   
		}
		$days[] = $period->end->format('Y-m-d');

		//Cash Transaction
		$sqlTransactions = "SELECT DATE(createdOn) as `date`,SUM(CASE WHEN transaction_type = 'cash' THEN Amount ELSE 0 END) AS cash_amount ,SUM(CASE WHEN transaction_type != 'cash' THEN Amount ELSE 0 END) AS other_amount FROM customer_plot_transactions WHERE status = 1 AND createdOn >= '{$startDate}' AND createdOn <= '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC";
		
		$transactions = Yii::app()->db->createCommand($sqlTransactions)->queryAll();
        $transactionsArray = [];
        if($transactions){
        	foreach($transactions as $transaction){
        		$transactionsArray[$transaction['date']]['cash_amount'] = $transaction['cash_amount'];
        		$transactionsArray[$transaction['date']]['other_amount'] = $transaction['other_amount'];
        	}
        }

        //Extra Transaction
        $sqlExtraTransactions = "SELECT DATE(createdOn) as `date`,SUM(CASE WHEN transaction_type = 'cash' THEN Amount ELSE 0 END) AS cash_amount ,SUM(CASE WHEN transaction_type != 'cash' THEN Amount ELSE 0 END) AS other_amount FROM customer_plot_extra_transactions WHERE status = 1 AND createdOn >= '{$startDate}' AND createdOn <= '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC;";
        
        $extraTransactions = Yii::app()->db->createCommand($sqlExtraTransactions)->queryAll();
        
        if($extraTransactions){
        	foreach($extraTransactions as $extraTransaction){
        		if(array_key_exists($extraTransaction['date'], $transactionsArray)){
        			$transactionsArray[$extraTransaction['date']]['cash_amount'] += $extraTransaction['cash_amount'];
        			$transactionsArray[$extraTransaction['date']]['other_amount'] += $extraTransaction['other_amount'];	
        		} else{
        			$transactionsArray[$extraTransaction['date']]['cash_amount'] = $extraTransaction['cash_amount'];
        			$transactionsArray[$extraTransaction['date']]['other_amount'] = $extraTransaction['other_amount'];
        		}
        		
        	}
        }
        
        
        //Cash Expense & other
        $sqlExpenses = "SELECT DATE(createdOn) as `date`,SUM(CASE WHEN payment_mode  = 'cash' THEN Amount ELSE 0 END) AS cash_amount ,SUM(CASE WHEN payment_mode  != 'cash' THEN Amount ELSE 0 END) AS other_amount FROM expenses WHERE expense_type != 15 AND status = 1 AND `createdOn` BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC;";
		$expenses = Yii::app()->db->createCommand($sqlExpenses)->queryAll();
		$expenseArray = [];
		if($expenses){
        	foreach($expenses as $expense){
        		$expenseArray[$expense['date']]['cash_amount'] = $expense['cash_amount'];
        		$expenseArray[$expense['date']]['other_amount'] = $expense['other_amount'];
        	}
        }

        //Previous PettyCash & PettyCashExpense
        $sqlPettyCashPrev = "SELECT SUM(amount) AS pettyCash FROM petty_cash WHERE `createdOn` < '{$startDate}'";
        $pettyPettyCashPrev = Yii::app()->db->createCommand($sqlPettyCashPrev)->queryRow();
        
        $sqlPettyCashExpensesPrev = "SELECT SUM(amount) AS pettyCashExpense FROM petty_cash_expenses WHERE `createdOn` < '{$startDate}'";
        $pettyPettyCashExpensesPrev = Yii::app()->db->createCommand($sqlPettyCashExpensesPrev)->queryRow();
        

        //Petty Cash Expense
        $sqlPettyCashExpenses = "SELECT DATE(createdOn) as `date`,SUM(amount) AS cash_amount FROM petty_cash_expenses WHERE status = 1 AND `createdOn` BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC;";
		$pettyCashExpenses = Yii::app()->db->createCommand($sqlPettyCashExpenses)->queryAll();
		$pettyCashExpenseArray = [];
		if($pettyCashExpenses){
        	foreach($pettyCashExpenses as $pettyCashExpense){
        		$pettyCashExpenseArray[$pettyCashExpense['date']]['cash_amount'] = $pettyCashExpense['cash_amount'];
        	}
        }

        //Petty Cash
        $sqlPettyCash = "SELECT DATE(createdOn) as `date`,amount AS cash_amount FROM petty_cash WHERE `createdOn` BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC;";
		$pettyCash = Yii::app()->db->createCommand($sqlPettyCash)->queryAll();
		$pettyCashArray = [];
		if($pettyCash){
        	foreach($pettyCash as $petty){
        		$pettyCashArray[$petty['date']]['cash_amount'] = $petty['cash_amount'];
        	}
        }


		//Final Result
		$result = [];
		$current_value = 0;
		foreach($days as $index=>$day){
				//transactions
				$result[$day]['transaction_cash'] = array_key_exists($day, $transactionsArray)?$transactionsArray[$day]['cash_amount']:0;
				$result[$day]['transaction_other'] = array_key_exists($day, $transactionsArray)?$transactionsArray[$day]['other_amount']:0;

				//Expenses
				$result[$day]['expense_cash'] = array_key_exists($day, $expenseArray)?$expenseArray[$day]['cash_amount']:0;
				$result[$day]['expense_other'] = array_key_exists($day, $expenseArray)?$expenseArray[$day]['other_amount']:0;

				

				//PettyCash
				$result[$day]['pettyCashAmount'] = array_key_exists($day, $pettyCashArray)?$pettyCashArray[$day]['cash_amount']:0;
				$result[$day]['pettyCash'] = array_key_exists($day, $pettyCashArray)?$pettyCashArray[$day]['cash_amount']:0;
				if($index==0){
					$result[$day]['pettyCash'] = $result[$day]['pettyCash'] + ($pettyPettyCashPrev['pettyCash'] - $pettyPettyCashExpensesPrev['pettyCashExpense']);
				}
				if ($result[$day]['pettyCash'] !== 0) {
			        $current_value = $current_value + $result[$day]['pettyCash'];
			    }
			    $result[$day]['pettyCash'] = $current_value;

				//PettyCash Expenses
				$result[$day]['pettyCashExpense'] = array_key_exists($day, $pettyCashExpenseArray)?$pettyCashExpenseArray[$day]['cash_amount']:0;
				
				if ($result[$day]['pettyCashExpense'] !== 0) {
					$current_value = $result[$day]['pettyCash'] - $result[$day]['pettyCashExpense'];
				}
			    $result[$day]['pettyCashBalance'] = $result[$day]['pettyCash'] - $result[$day]['pettyCashExpense'];

		}


		$data['result'] = $result;
		$this->render('report2-account',$data);
	}



	public function actionAccountreportcsv(){
		$startDate  = $data['start'] = $_GET['start'].' 00:00:00';
		$endDate = $data['end'] = $_GET['end'].' 23:59:59';
		$days = [];
		$period = new DatePeriod(
		     new DateTime($_GET['start']),
		     new DateInterval('P1D'),
		     new DateTime($_GET['end'])
		);
		
		//$days[] = $period->start->format('Y-m-d');
		foreach ($period as $key => $value) {
		    $days[] = $value->format('Y-m-d');   
		}
		$days[] = $period->end->format('Y-m-d');

		//Cash Transaction
		$sqlTransactions = "SELECT DATE(createdOn) as `date`,SUM(CASE WHEN transaction_type = 'cash' THEN Amount ELSE 0 END) AS cash_amount ,SUM(CASE WHEN transaction_type != 'cash' THEN Amount ELSE 0 END) AS other_amount FROM customer_plot_transactions WHERE status = 1 AND createdOn >= '{$startDate}' AND createdOn <= '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC";
		
		$transactions = Yii::app()->db->createCommand($sqlTransactions)->queryAll();
        $transactionsArray = [];
        if($transactions){
        	foreach($transactions as $transaction){
        		$transactionsArray[$transaction['date']]['cash_amount'] = $transaction['cash_amount'];
        		$transactionsArray[$transaction['date']]['other_amount'] = $transaction['other_amount'];
        	}
        }

        //Extra Transaction
        $sqlExtraTransactions = "SELECT DATE(createdOn) as `date`,SUM(CASE WHEN transaction_type = 'cash' THEN Amount ELSE 0 END) AS cash_amount ,SUM(CASE WHEN transaction_type != 'cash' THEN Amount ELSE 0 END) AS other_amount FROM customer_plot_extra_transactions WHERE status = 1 AND createdOn >= '{$startDate}' AND createdOn <= '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC;";
        
        $extraTransactions = Yii::app()->db->createCommand($sqlExtraTransactions)->queryAll();
        
        if($extraTransactions){
        	foreach($extraTransactions as $extraTransaction){
        		if(array_key_exists($extraTransaction['date'], $transactionsArray)){
        			$transactionsArray[$extraTransaction['date']]['cash_amount'] += $extraTransaction['cash_amount'];
        			$transactionsArray[$extraTransaction['date']]['other_amount'] += $extraTransaction['other_amount'];	
        		} else{
        			$transactionsArray[$extraTransaction['date']]['cash_amount'] = $extraTransaction['cash_amount'];
        			$transactionsArray[$extraTransaction['date']]['other_amount'] = $extraTransaction['other_amount'];
        		}
        		
        	}
        }
        
        
        //Cash Expense & other
        $sqlExpenses = "SELECT DATE(createdOn) as `date`,SUM(CASE WHEN payment_mode  = 'cash' THEN Amount ELSE 0 END) AS cash_amount ,SUM(CASE WHEN payment_mode  != 'cash' THEN Amount ELSE 0 END) AS other_amount FROM expenses WHERE expense_type != 15 AND status = 1 AND `createdOn` BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC;";
		$expenses = Yii::app()->db->createCommand($sqlExpenses)->queryAll();
		$expenseArray = [];
		if($expenses){
        	foreach($expenses as $expense){
        		$expenseArray[$expense['date']]['cash_amount'] = $expense['cash_amount'];
        		$expenseArray[$expense['date']]['other_amount'] = $expense['other_amount'];
        	}
        }

        //Previous PettyCash & PettyCashExpense
        $sqlPettyCashPrev = "SELECT SUM(amount) AS pettyCash FROM petty_cash WHERE `createdOn` < '{$startDate}'";
        $pettyPettyCashPrev = Yii::app()->db->createCommand($sqlPettyCashPrev)->queryRow();
        
        $sqlPettyCashExpensesPrev = "SELECT SUM(amount) AS pettyCashExpense FROM petty_cash_expenses WHERE `createdOn` < '{$startDate}'";
        $pettyPettyCashExpensesPrev = Yii::app()->db->createCommand($sqlPettyCashExpensesPrev)->queryRow();
        

        //Petty Cash Expense
        $sqlPettyCashExpenses = "SELECT DATE(createdOn) as `date`,SUM(amount) AS cash_amount FROM petty_cash_expenses WHERE status = 1 AND `createdOn` BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC;";
		$pettyCashExpenses = Yii::app()->db->createCommand($sqlPettyCashExpenses)->queryAll();
		$pettyCashExpenseArray = [];
		if($pettyCashExpenses){
        	foreach($pettyCashExpenses as $pettyCashExpense){
        		$pettyCashExpenseArray[$pettyCashExpense['date']]['cash_amount'] = $pettyCashExpense['cash_amount'];
        	}
        }

        //Petty Cash
        $sqlPettyCash = "SELECT DATE(createdOn) as `date`,amount AS cash_amount FROM petty_cash WHERE `createdOn` BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY DATE(createdOn) ORDER BY `createdOn` ASC;";
		$pettyCash = Yii::app()->db->createCommand($sqlPettyCash)->queryAll();
		$pettyCashArray = [];
		if($pettyCash){
        	foreach($pettyCash as $petty){
        		$pettyCashArray[$petty['date']]['cash_amount'] = $petty['cash_amount'];
        	}
        }


		//Final Result
		$result = [];
		$current_value = 0;
		foreach($days as $index=>$day){
			//transactions
			$result[$day]['transaction_cash'] = array_key_exists($day, $transactionsArray)?$transactionsArray[$day]['cash_amount']:0;
			$result[$day]['transaction_other'] = array_key_exists($day, $transactionsArray)?$transactionsArray[$day]['other_amount']:0;

			//Expenses
			$result[$day]['expense_cash'] = array_key_exists($day, $expenseArray)?$expenseArray[$day]['cash_amount']:0;
			$result[$day]['expense_other'] = array_key_exists($day, $expenseArray)?$expenseArray[$day]['other_amount']:0;

			

			//PettyCash
			$result[$day]['pettyCashAmount'] = array_key_exists($day, $pettyCashArray)?$pettyCashArray[$day]['cash_amount']:0;
			$result[$day]['pettyCash'] = array_key_exists($day, $pettyCashArray)?$pettyCashArray[$day]['cash_amount']:0;
			if($index==0){
				$result[$day]['pettyCash'] = $result[$day]['pettyCash'] + ($pettyPettyCashPrev['pettyCash'] - $pettyPettyCashExpensesPrev['pettyCashExpense']);
			}
			if ($result[$day]['pettyCash'] !== 0) {
		        $current_value = $current_value + $result[$day]['pettyCash'];
		    }
		    $result[$day]['pettyCash'] = $current_value;

			//PettyCash Expenses
			$result[$day]['pettyCashExpense'] = array_key_exists($day, $pettyCashExpenseArray)?$pettyCashExpenseArray[$day]['cash_amount']:0;
			
			if ($result[$day]['pettyCashExpense'] !== 0) {
				$current_value = $result[$day]['pettyCash'] - $result[$day]['pettyCashExpense'];
			}
		    $result[$day]['pettyCashBalance'] = $result[$day]['pettyCash'] - $result[$day]['pettyCashExpense'];
		}

		$list = array (
	        array('Date','Transactions(Cash)','Expenses(Cash)','Balance(Cash)','Transactions(Other)','Expenses(Other)','Balance(Other)','Total Transaction','Total Expense','Total Balance')
	    );

	    $count = 1;
        foreach(@$result as $date=>$data):
	        $list[$count][] = $date;
	        $list[$count][] = @$data['transaction_cash'];
	        $list[$count][] = @$data['expense_cash'];
	        $list[$count][] = @$data['transaction_cash']-$data['expense_cash'];

	        $list[$count][] = @$data['transaction_other'];
	        $list[$count][] = @$data['expense_other'];
	        $list[$count][] = @$data['transaction_other']-$data['expense_other'];

	       // $list[$count][] = @$data['pettyCash'].'</br>'.'PettyCash:'.$data['pettyCashAmount'];
	       // $list[$count][] = @$data['pettyCashExpense'];
	       // $list[$count][] = @$data['pettyCashBalance'];

	        $list[$count][] = number_format(@$data['transaction_cash']+@$data['transaction_other']);
	        $list[$count][] = number_format(@$data['expense_cash']+@$data['expense_other']+@$data['pettyCashExpense']);
	        $list[$count][] = number_format((@$data['transaction_cash']-$data['expense_cash'])+(@$data['transaction_other']-$data['expense_other'])+@$data['pettyCashBalance']);
           	$count++;
        endforeach;
        

        $list[$count][] = 'Total';
        $list[$count][] = number_format(array_sum(array_column($result,'transaction_cash')));
        $list[$count][] = number_format(array_sum(array_column($result,'expense_cash')));
        $list[$count][] = number_format(array_sum(array_column($result,'transaction_cash'))-array_sum(array_column($result,'expense_cash')));

        $list[$count][] = number_format(array_sum(array_column($result,'transaction_other')));
        $list[$count][] = number_format(array_sum(array_column($result,'expense_other')));
        $list[$count][] = number_format(array_sum(array_column($result,'transaction_other'))-array_sum(array_column($result,'expense_other')));

        // $list[$count][] = number_format(array_sum(array_column($result,'pettyCash')));
        // $list[$count][] = number_format(array_sum(array_column($result,'pettyCashExpense')));
        // $list[$count][] = number_format(array_sum(array_column($result,'pettyCashBalance')));

        $list[$count][] = number_format(array_sum(array_column($result,'transaction_cash'))+array_sum(array_column($result,'transaction_other')));
        $list[$count][] = number_format(array_sum(array_column($result,'expense_cash'))+array_sum(array_column($result,'expense_other'))+array_sum(array_column($result,'pettyCashExpense')));
        
        //echo '<pre>';print_r($list);exit;
        $fName = 'accounts_report-'.date('dMY-h:i').'.csv';
		$fp = fopen($fName, 'w');
      	foreach ($list as $fields) {
          fputcsv($fp, $fields);
      	}

      	$filename = getcwd().'/'.$fName;
      	header("Content-type: text/csv");
		header("Content-disposition: attachment; filename = $fName");
		readfile($filename);

	}


	public function actionFetchall(){
		$userModel = Yii::app()->session->get('userModel');
		$result_array = [];
		$criteria = new CDbCriteria();
        parse_str($_SERVER['REQUEST_URI'], $result_array);
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		switch (@$result_array['order'][0]['column']) {
			case '0':
				@$result_array['order'][0]['column'] = 't.id';
				break;
			default:
				@$result_array['order'][0]['column'] = 't.id';
				break;
		}
		$sort = @$result_array['order'][0]['column'];
		$order = @$result_array['order'][0]['dir'];
		
		$searchType = @$result_array['searchType'];
		
		$criteria->limit = @$result_array['length'];
		$criteria->offset = @$result_array['start'];
		$criteria->order = $sort.' '.$order;
		

		if(!empty(@$result_array['search']['value'])){
			
			if($searchType == 'paid_to'){
				$query = $result_array['search']['value'];
				$criteria->addCondition('paid_to LIKE :paid');
				$params[':paid'] = '%'.@$query.'%';
				$criteria->params = $params;
			}

			if($searchType == 'hoa'){
				$hoa = $this->expenseTypeReverse($result_array['search']['value']);
				if($hoa != -1){
					$query = $hoa;
					$criteria->addCondition('expense_type = :hoa');
					$params[':hoa'] = $hoa;
					$criteria->params = $params;	
				}
				
			}

			if($searchType == 'status'){
				$st = $this->expenseTypeReverse($result_array['search']['value']);
				if($st != -1){
					$query = $st;
					$criteria->addCondition('status = :st');
					$params[':st'] = $st;
					$criteria->params = $params;	
				}
			}

			if($searchType == 'ref'){
				$query = $result_array['search']['value'];
				$criteria->addCondition('number LIKE :paid');
				$params[':paid'] = '%'.@$query.'%';
				$criteria->params = $params;
			}

			if($searchType == 'desc'){
				$query = $result_array['search']['value'];
				$criteria->addCondition('description LIKE :desc');
				$params[':desc'] = '%'.@$query.'%';
				$criteria->params = $params;
			}
			
		}
		
		$model = Expenses::model()->findAll($criteria);
		
		$result = array();
		$i=0;
		$countTotal = Expenses::model()->count($criteria);
		$result['draw'] = @$result_array['draw'];
		$result['recordsTotal'] = $countTotal;
		$result['recordsFiltered'] = $countTotal;
		$result['data'] = [];
		$list = true;

		if($model){
			foreach($model as $expense):
				$regNo = '';
				if(isset($expense->account_id)){
	                $regNo = $this->getExpenseRegNo($expense->id,'expense');
	                if($expense->expense_type != 15){
	                    //$total = $total + (($expense->status==1)?($expense->amount):0);
	                }
	            } else{
	                $regNo = $this->getExpenseRegNo($expense->id,'pettyCash');
	                //$PCETotal = $PCETotal + (($expense->status==1)?($expense->amount):0);
	            }
                
                $result['data'][$i][] = $regNo;
                $result['data'][$i][] = ucfirst($this->expenseType(@$expense->expense_type));
                $result['data'][$i][] = substr($expense->description, 0, 150);;
                $result['data'][$i][] = 'Rs. '.number_format($expense->amount);
                $result['data'][$i][] = @$expense->payment_mode;
                $result['data'][$i][] = @isset($expense->bank)?$expense->bank:'-';
                $result['data'][$i][] = isset($expense->number)?('*'.$expense->number.'*'):'';
                $result['data'][$i][] = ucfirst(@$expense->paid_to);
                $result['data'][$i][] = isset($expense->cnic)?$expense->cnic:'';
                $result['data'][$i][] = @$expense->user->first_name.''.@$expense->user->last_name;
                $result['data'][$i][] = date('d M, Y',strtotime($expense->createdOn));
                $btn = '';
                if($expense->status==0){
                	$btn .='<span class="label label-danger">Rejected</span>&nbsp;';
                }
                if($expense->status==1){
                	$btn .='<span class="label label-success">Approved</span>&nbsp;';
                }
                /*if($userModel['user_type']['id'] == 1 ||  $userModel['user_type']['id'] == 5){
	                    if(isset($expense->account_id)){
	                        $btn .= '<a class="deleteExpense" href="'.Yii::app()->baseUrl.'/expenses/delete/status/0/id/'.$expense->id.'"><span class="aLink label label-danger">Reject</span></a>&nbsp;';
	                    }
	            }*/

	            if(isset($expense->account_id)){
                    	if($expense->status==2){
                    		$btn .= '<span class="label label-warning">Pending</span><br/>';
                    		if($userModel['user_type']['id'] == 1 ||  $userModel['user_type']['id'] == 5){
                    			$btn .= '<a class="deleteExpense" href="'.Yii::app()->baseUrl.'/expenses/delete/status/1/id/'.$expense->id.'"><span class="aLink label label-success">Approve</span></a><br/><a class="deleteExpense" href="'.Yii::app()->baseUrl.'/expenses/delete/status/0/id/'.$expense->id.'"><span class="aLink label label-danger">Reject</span></a>&nbsp;';
                    		}
                    	}
                    if($userModel['user_type']['id'] ==1 || $userModel['user_type']['id'] == 5) {
                        $btn .= '<a class="deleteExpense" href="'.Yii::app()->baseUrl.'/expenses/delete/status/'.$expense->status.'/id/'.$expense->id.'"><span class="aLink label label-danger">Delete</span></a>&nbsp;';
                    }
                    $btn .= '&nbsp;<a href="'.Yii::app()->baseUrl.'/expenses/edit/'.$expense->id.'"><span class="aLink label label-warning">Edit</span></a>&nbsp;';
                    if($expense->status!=2){
                    	$btn .= '<a target="_blank" href="'.Yii::app()->baseUrl.'/expenses/expenseinvoice/'.$expense->id.'"><span class="aLink label label-success">Print</span></a>&nbsp;';
                    }
                } else{
                    if($expense->status==2){
                    	$btn .= '<span class="label label-warning">Pending</span><br/>';
                    	if($userModel['user_type']['id'] == 1 ||  $userModel['user_type']['id'] == 5){
                    		$btn .= '<a class="deleteExpense" href="'.Yii::app()->baseUrl.'pettycash/delete/status/1/id/'.$expense->id.'"><span class="aLink label label-success">Approve</span></a><br/>
                    			<a class="deleteExpense" href="'.Yii::app()->baseUrl.'pettycash/delete/status/0/id/'.$expense->id.'"><span class="aLink label label-danger">Reject</span></a>&nbsp;';
                    	}
                    }
                    if($userModel['user_type']['id'] ==1 || $userModel['user_type']['id'] == 5) {
                        $btn .= '<a class="deleteExpense" href="'.Yii::app()->baseUrl.'/pettycash/delete/status/2/id/'.$expense->id.'"><span class="aLink label label-danger">Delete</span></a>&nbsp;';                        
                    }
                    $btn .= '&nbsp;<a href="'.Yii::app()->baseUrl.'/pettycash/edit/'.$expense->id.'"><span class="aLink label label-warning">Edit</span></a>&nbsp;';
                    if($expense->status!=2){
                    	$btn .= '<a target="_blank" href="'.Yii::app()->baseUrl.'/expenses/expenseinvoice/id/'.$expense->id.'/type/pettyCash"><span class="aLink label label-success">Print</span></a>&nbsp;';
                    }
                }
                //$btn .= '<a target="_blank" href="'.Yii::app()->baseUrl.'/expenses/journal/'.$expense->id.'"><span class="aLink label label-info">Journal Ledger</span></a>&nbsp;';
                $result['data'][$i][] = $btn;
                $result['data'][$i][] = ($expense->status==1)?'-':$expense->reason;
				$i++;
			endforeach;	
		}
		
		echo json_encode($result);
	}
	
	public function actionImport(){
		$this->render('import');
	}
	
	/**
     * Normalize different date formats to Y-m-d
     * Compatible with Yii 1.1
     */
    function normalizeDate($date)
    {
        $date = trim($date);
    
        if (empty($date)) {
            return null;
        }
    
        // Replace separators for consistency
        $date = str_replace(['/', '.'], '-', $date);
    
        $formats = [
            'd-M-y', // 2-Nov-22
            'd-M-Y',
            'd-m-Y', // 15-04-2024
            'd-m-y', // 15-04-25
        ];
    
        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $date);
            if ($dt && $dt->format($format) === $date) {
                return $dt->format('Y-m-d');
            }
        }
    
        // Fallback (strtotime handles many cases)
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
    
        return null; // invalid date
    }
    
	
	
// 	public function actionExpenseData()
// 	{
// 		$uploadFolder = getcwd() . '/imports/expense/';
// 		$data['expenseTypes'] = Yii::app()->params['expenseTypes'];
//         $fileName = 'report.csv';
//         $orig_fileName = $_FILES['expense']['name'];
//         move_uploaded_file($_FILES['expense']['tmp_name'], $uploadFolder.$fileName);
//         $result = [];
//         if (($handle = fopen($uploadFolder.$fileName, 'r')) !== FALSE) {
//             $index = 0;
//             while (($row = fgetcsv($handle, 100000, ',')) !== FALSE) {

//                 if (empty($header)) {
//                     $header = $row;
//                 } else {
//                     $result[] = $row;
//                 }
//                 $index++;
//             }
//             fclose($handle);
//         }
        
        
//         if(!empty($result)){
//             foreach ($result as $i=>$value) {
            
//             }
//         }
//         echo 'Done';
// 	}

    public function actionExpenseData()
    {
        $uploadFolder = Yii::getPathOfAlias('webroot') . '/imports/expense/';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0777, true);
        }
    
        $expenseTypes = Yii::app()->params['expenseTypes']; // ID => NAME
        $expenseTypeMap = array_flip($expenseTypes);        // NAME => ID
    
        $fileName = 'report.csv';
    
        if (empty($_FILES['expense']['tmp_name'])) {
            throw new CHttpException(400, 'No file uploaded');
        }
    
        move_uploaded_file($_FILES['expense']['tmp_name'], $uploadFolder . $fileName);
    
        if (($handle = fopen($uploadFolder . $fileName, 'r')) === false) {
            throw new CHttpException(500, 'Unable to read CSV');
        }
    
        $rowNo = 0;
    
        while (($row = fgetcsv($handle, 100000, ',')) !== false) {
            $rowNo++;
    
            // Skip header
            if ($rowNo === 1) {
                continue;
            }
    
            /*
             CSV Indexes:
             [0] DATE
             [1] DESCRIPTION
             [2] REFRENCE
             [3] DEBIT
             [4] HEAD
            */
    
            if (empty($row[0]) || empty($row[3]) || empty($row[4])) {
                continue; // skip incomplete rows
            }
    
            $date = $this->normalizeDate($row[0]);
            if ($date === null) {
                continue;
            }
    
            $headName = trim($row[4]);
            if (!isset($expenseTypeMap[$headName])) {
                continue; // unknown expense head
            }
    
            $model = new Expenses();
            $description = trim($row[1]);
            if (!empty(trim($row[2]))) {
                $description .= ' / ' . trim($row[2]);
            }

            $model->expense_type = $expenseTypeMap[$headName]; // ID
            $model->description  = $description;
            $model->amount       = (float) $row[3];
            $model->status       = 1;
            $model->account_id = 5;
			$model->user_id = Yii::app()->session['userModel']['id'];
            $model->createdOn    = $date;
            $model->reason    = NULL;
            // Optional fields (NULL-safe)
            if (!$model->save(false)) {
                Yii::log([
                    'row' => $rowNo,
                    'errors' => $model->errors,
                    'data' => $row
                ], CLogger::LEVEL_ERROR);
            }
        }
    
        fclose($handle);
    
        echo 'Expense CSV Imported Successfully';
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