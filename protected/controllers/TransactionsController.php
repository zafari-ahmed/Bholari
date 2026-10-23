<?php

class TransactionsController extends Controller
{
	/*public function actionIndex()
	{
		$status = isset($_GET['status'])?$_GET['status']:1;
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$data['status'] = $status;
		// if($status==0){
		// 	//$transactions = CustomerPlotTransactions::model()->with('plot')->findAll("t.status = $status AND t.`reason` IS NOT NULL AND t.phase_id = $phaseId");		
		// } else{
		// 	$transactions = CustomerPlotTransactions::model()->with('plot')->findAll("t.status = $status AND t.phase_id = $phaseId");	
		// }
		
		// $transactionsExtra = [];
		// //$transactionsExtra = CustomerPlotExtraTransactions::model()->with('plot')->findAll("t.status = $status AND t.phase_id = $phaseId");
		// $data['trasactions'] = array_merge($transactions,$transactionsExtra);
		$criteria = new CDbCriteria();
		if($status==0){
			$criteria->addCondition("t.status = $status AND t.`reason` IS NOT NULL AND t.phase_id = $phaseId");
		} else{
			$criteria->addCondition("t.status = $status AND t.phase_id = $phaseId");
		}
		//$criteria->limit = 50;
		//$criteria->offset = 1000;
		$criteria->with = array('plot','plotPaymentMode','customer');
		$criteria->order = "t.transaction_number DESC";
		$transactions = CustomerPlotTransactions::model()->with('plot')->findAll($criteria);


		$criteriaExt = new CDbCriteria();
		$criteriaExt->addCondition("t.status = $status AND t.phase_id = $phaseId");
		//$criteriaExt->limit = 50;
		$criteria->order = "t.transaction_number DESC";
		$transactionsExtra = CustomerPlotExtraTransactions::model()->with('plot')->findAll($criteriaExt);		
		$data['trasactions'] = array_merge($transactions,$transactionsExtra);
		//echo count($transactionsExtra).'<br/>';
		//echo count($transactions);
		//exit;
		$this->render('index',$data);
	}*/


	public function actionGettransactionmessage($id,$type='transaction'){;
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		if($type=='transaction'){
			$transaction = CustomerPlotTransactions::model()->findAll("phase_id = $phaseId AND transaction_number = :key AND status = 1",array(':key'=>$id));	
		} else{
			$transactionExtra = CustomerPlotExtraTransactions::model()->findAll("phase_id = $phaseId AND transaction_number = :key AND status = 1",array(':key'=>$id));
		}
		$transaction = CustomerPlotTransactions::model()->findAll("phase_id = $phaseId AND transaction_number = :key AND status = 1",array(':key'=>$id));	
		$transactionExtra = CustomerPlotExtraTransactions::model()->findAll("phase_id = $phaseId AND transaction_number = :key AND status = 1",array(':key'=>$id));

		
		$transactions = array_merge($transaction,$transactionExtra);
		//echo '<pre>';print_r($transactions);exit;
		//$transaction = CustomerPlotTransactions::model()->findAll("phase_id = $phaseId AND transaction_number = :key",array(':key'=>$id));
		$total = 0;
		$msg = '';
		if($transactions){
			$id = @$transaction[0]->plot_id;
			$cplots = CustomerPlots::model()->findByPk(@$transaction[0]->plot_id);
			foreach ($transactions as $key => $value) {
				$total = $total + $value->amount;
			}
			if($cplots){
				$msg = "Dear ".ucfirst($transaction[0]->customer->name).",\nThankyou for Paying Amount(PKR): ".number_format($total)."/= on ".date('d M, o',strtotime($transaction[0]->createdOn))." against your Plot ".$cplots->plot->plot_type."-".$cplots->plot->plot_number." in Block ".$cplots->plot->block_number.".\n\nFor details, please call 021-37440935";	
			}
			
		}
		echo $msg;
	}


	public function actionReportalltransactioncancelled(){

		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		$list = array (
	        array('Receipt No','Client Name','Block #','Plot Type','Plot #','Payment Mode','Total','Comment','Date','Transaction','bank','Reference Number','Sub Dealer', 'Dealer'),
	    );
      		
      	
		$model = new CustomerPlotTransactions;
      	
		//$sql = "SELECT t.transaction_number,c.name as customer_name,SUM(t.amount) as total,GROUP_CONCAT( DISTINCT p.mode ORDER BY p.id SEPARATOR ', ') as p_modes,pl.block_number as block_number ,pl.plot_type,pl.plot_number,cp.id,t.comment,t.createdOn,t.transaction_type,t.bank,t.branch,a.name as agent,ap.name as agentParent,t.reference_number FROM `customer_plot_transactions` t LEFT JOIN payment_schedule_payment_modes p ON p.id = t.plot_payment_mode_id LEFT JOIN customers c ON c.id = t.customer_id LEFT JOIN customer_plots cp ON cp.id = t.plot_id LEFT JOIN plots pl ON pl.id = cp.plot_id LEFT JOIN agents a ON cp.agent_id = a.id LEFT JOIN agents ap ON ap.id = a.parent_id WHERE (t.status = 1 AND cp.status = 1) AND cp.phase_id =  $phaseId GROUP BY transaction_number ORDER BY `transaction_number` ASC";
		$sql = "SELECT t.transaction_number,c.name as customer_name,t.amount as total,p.mode as p_modes,pl.block_number as block_number ,pl.plot_type,pl.plot_number,cp.id,t.comment,t.createdOn,t.transaction_type,t.bank,t.branch,a.name as agent,ap.name as agentParent,t.reference_number FROM `customer_plot_transactions` t LEFT JOIN payment_schedule_payment_modes p ON p.id = t.plot_payment_mode_id LEFT JOIN customers c ON c.id = t.customer_id LEFT JOIN customer_plots cp ON cp.id = t.plot_id LEFT JOIN plots pl ON pl.id = cp.plot_id LEFT JOIN agents a ON cp.agent_id = a.id LEFT JOIN agents ap ON ap.id = a.parent_id WHERE (t.status = 1 AND cp.status = 1) AND cp.phase_id =  $phaseId ORDER BY `transaction_number` ASC";
		//echo $sql;exit;
		$bookings = Yii::app()->db->createCommand($sql)->queryAll();

		$sqlExtra = "SELECT t.transaction_number,c.name as customer_name,t.amount as total,t.plot_payment_mode as p_modes,pl.block_number as block_number ,pl.plot_type,pl.plot_number,cp.id,t.comment,t.createdOn,t.transaction_type,t.bank,t.branch,a.name as agent,ap.name as agentParent,t.reference_number FROM `customer_plot_extra_transactions` t LEFT JOIN customers c ON c.id = t.customer_id LEFT JOIN customer_plots cp ON cp.id = t.plot_id LEFT JOIN plots pl ON pl.id = cp.plot_id LEFT JOIN agents a ON cp.agent_id = a.id LEFT JOIN agents ap ON ap.id = a.parent_id WHERE (t.status = 1 AND cp.status = 1) AND cp.phase_id =  $phaseId ORDER BY `transaction_number` ASC";
		$bookingsExtra = Yii::app()->db->createCommand($sqlExtra)->queryAll();

		$finalTransactions = array_merge($bookings,$bookingsExtra);
		array_multisort( array_column($finalTransactions, "transaction_number"), SORT_ASC, $finalTransactions );
        $count = 1;
        foreach($finalTransactions as $tt):
        	//$list[$count][] = @$count;
	        $list[$count][] = @$tt['transaction_number'];
	        $list[$count][] = @$tt['customer_name'];
            $list[$count][] = @$tt['block_number'];
            $list[$count][] = @$tt['plot_type'];
	        $list[$count][] = @$tt['plot_number'];
	        //$list[$count][] = @$tt['size->size'];
            $list[$count][] = @$tt['p_modes'];
            $list[$count][] = @$tt['total'];
            $list[$count][] = @$tt['comment'];
            $list[$count][] = $tt['createdOn'];//date('d M,o',strtotime(@$tt['createdOn']));
            $list[$count][] = $tt['transaction_type'];
            $list[$count][] = ($tt['transaction_type']!='cash')?$tt['bank'].' - '.$tt['branch']:'-';
            $list[$count][] = @$tt['reference_number'];
            $list[$count][] = @$tt['agent'];
            $list[$count][] = @$tt['agentParent'];
			$count++;
		endforeach;
		$fName = 'alltransactions_'.date('dMY-h:i').'.csv';
		$fp = fopen($fName, 'w');
      	foreach ($list as $fields) {
          fputcsv($fp, $fields);
      	}

      	$filename = getcwd().'/'.$fName;
      	header("Content-type: text/csv");
		header("Content-disposition: attachment; filename = $fName");
		readfile($filename);
	}
	
	public function actionIndex()
    {
        $status  = isset($_GET['status']) ? (int)$_GET['status'] : 1;
        $phaseId = Yii::app()->session->get('userModel')['phase_id'];
    
        $data = [];
        $data['status'] = $status;
    
        // IMPORTANT: Do NOT load transactions here anymore.
        $this->render('index-fetchall', $data);
    }
    
    /**
     * Server-side fetch for DataTables (Transactions + Extra Transactions merged)
     */
     public function actionFetchall()
    {
        $userModel = Yii::app()->session->get('userModel');
        $phaseId   = (int)$userModel['phase_id'];
    
        // DataTables params
        $draw   = (int)Yii::app()->request->getParam('draw', 1);
        $start  = (int)Yii::app()->request->getParam('start', 0);
        $length = (int)Yii::app()->request->getParam('length', 25);
    
        //$status = (int)Yii::app()->request->getParam('status', 1);
        $search = trim(Yii::app()->request->getParam('search', [])['value'] ?? '');
    
        // Order mapping (index -> column)
        $orderCol = (int)(Yii::app()->request->getParam('order', [])[0]['column'] ?? 0);
        $orderDir = strtolower(Yii::app()->request->getParam('order', [])[0]['dir'] ?? 'desc');
        $orderDir = ($orderDir === 'asc') ? 'ASC' : 'DESC';
    
        // Safe column map for ordering after merge (we sort in PHP)
        // Hidden col is transaction_number (same as your view)
        $sortKey = 'transaction_number';
    
        // -------------------------
        // Criteria for MAIN transactions
        // -------------------------
        $criteria = new CDbCriteria();
        $criteria->with = ['plot', 'plotPaymentMode', 'customer']; // same as your code
    
        // Search (safe) - search on tx no, ref, customer fields
        if ($search !== '') {
            $criteria->addCondition("(
                t.transaction_number LIKE :q OR
                t.reference_number LIKE :q OR
                customer.name LIKE :q OR
                customer.cnic LIKE :q
            )");
            $criteria->params[':q'] = '%' . $search . '%';
        }
    
        // Paging (apply to each dataset; we’ll merge after)
        $criteria->limit  = $length;
        $criteria->offset = $start;
        $criteria->order  = "t.transaction_number {$orderDir}";
        
        $transactions = CustomerPlotTransactions::model()->findAll($criteria);
        
        // FIRST object
        $firstObj = !empty($transactions) ? reset($transactions) : null;
        // LAST object
        $lastObj  = !empty($transactions) ? end($transactions) : null;
        
        $minTxn = $firstObj ? (int) ltrim($firstObj->transaction_number, '#') : null;
        $maxTxn = $lastObj  ? (int) ltrim($lastObj->transaction_number, '#') : null;

        // Total counts (main)
        $criteriaCount = clone $criteria;
        $criteriaCount->limit = -1;
        $criteriaCount->offset = -1;
        $criteriaCount->order = '';
        $totalMainFiltered = (int)CustomerPlotTransactions::model()->count($criteriaCount);
    
        $criteriaTotalMain = new CDbCriteria();
        $totalMain = (int)CustomerPlotTransactions::model()->count($criteriaTotalMain);
    
        // -------------------------
        // Criteria for EXTRA transactions
        // -------------------------
        $criteriaExt = new CDbCriteria();
        $criteriaExt->with = ['plot', 'customer'];
    
        if ($search !== '') {
            $criteriaExt->addCondition("(
                t.transaction_number LIKE :q OR
                t.reference_number LIKE :q OR
                customer.name LIKE :q OR
                customer.cnic LIKE :q
            )");
            $criteriaExt->params[':q'] = '%' . $search . '%';
        }
        
        // if ($minTxn > 0 && $maxTxn > 0) {
        //     $criteriaExt->addCondition("t.transaction_number BETWEEN :minTxn AND :maxTxn");
        //     $criteriaExt->params[':minTxn'] = $minTxn;
        //     $criteriaExt->params[':maxTxn'] = $maxTxn;
        // }
    
        $criteriaExt->limit  = $length;
        $criteriaExt->offset = $start;
        $criteriaExt->order  = "t.transaction_number {$orderDir}";
    
        $transactionsExtra = CustomerPlotExtraTransactions::model()->findAll($criteriaExt);
        // echo '<pre>';
        // echo $search;
        // print_r($criteriaExt);
        // print_r($transactionsExtra);exit;
        // Total counts (extra)
        $criteriaExtCount = clone $criteriaExt;
        if ($minTxn > 0 && $maxTxn > 0) {
            $criteriaExt->addCondition("t.transaction_number BETWEEN :minTxn AND :maxTxn");
            $criteriaExt->params[':minTxn'] = $minTxn;
            $criteriaExt->params[':maxTxn'] = $maxTxn;
        }
        $criteriaExtCount->limit = -1;
        $criteriaExtCount->offset = -1;
        $criteriaExtCount->order = '';
        $totalExtraFiltered = (int)CustomerPlotExtraTransactions::model()->count($criteriaExtCount);
    
        $criteriaTotalExtra = new CDbCriteria();
        $totalExtra = (int)CustomerPlotExtraTransactions::model()->count($criteriaTotalExtra);
    
        // -------------------------
        // OPTION 1: merge (your request)
        // -------------------------
        $merged = array_merge($transactions, $transactionsExtra);
        
        // Sort after merge so the final list is correct (DESC by transaction_number)
        usort($merged, function ($a, $b) use ($orderDir) {
            $av = ltrim((string)$a->transaction_number, '#');
            $bv = ltrim((string)$b->transaction_number, '#');
            if ($av == $bv) return 0;
            if ($orderDir === 'ASC') {
                return ($av < $bv) ? -1 : 1;
            }
            return ($av > $bv) ? -1 : 1;
        });
        
        
    
        // Optional: trim to $length (because we fetched $length from each table, merge becomes up to 2*$length)
        //$merged = array_slice($merged, 0, $length);
    
        // -------------------------
        // Build DataTables response rows (same columns as your view)
        // -------------------------
        $dataRows = [];
        foreach ($merged as $row) {
    
            $isMain = ($row instanceof CustomerPlotTransactions);
            $typeMsg = $isMain ? 'transaction' : 'other';
    
            $tranRaw = (string)$row->transaction_number;
            $tranNo  = (strpos($tranRaw, '#') === 0) ? $tranRaw : '#' . ltrim($tranRaw, '0');
    
            // Plot link (based on your view)
            $plotLink = '-';
            if ($row->plot && $row->plot->plot) {
                $plotLink = '<a href="' . Yii::app()->baseUrl . '/booking/viewbooking/' . (int)$row->plot->id . '">' .
                    //CHtml::encode($row->plot->plot->block_number . '-' . $row->plot->plot->plot_type . '-' . $row->plot->plot->plot_number) . '*</a>';
                    CHtml::encode($row->plot->plot->block_number . '-'.$row->plot->plot->plot_number) . '*</a>';
            }
    
            // Payment mode
            $payMode = $isMain
                ? ucfirst(@$row->plotPaymentMode->mode)
                : ucfirst(@$row->plot_payment_mode);
    
            // Created on
            $createdOn = $row->createdOn ? date('d M,Y', strtotime($row->createdOn)) : '';
    
            // Created by + reason line (same logic you had)
            $createdByCell = CHtml::encode(@$row->createdBy);
            if (@$row->status == 0 || (@$row->plot && @$row->plot->status == 0)) {
                $reasonText = $isMain ? @$row->reason : @$row->monthlyDate;
                $createdByCell .= '<br/>Reason: ' . CHtml::encode($reasonText);
            }
    
            // Actions (same URLs)
            $printUrl = Yii::app()->baseUrl . '/booking/dublicateinvoice/plot/' . (int)$row->plot->id .
                '/transaction/' . str_replace('#', '', ltrim($tranRaw, '0'));
    
            $msgUrl = Yii::app()->baseUrl . '/api/messages/type/' . $typeMsg .
                '/id/' . str_replace('#', '', $tranRaw);
    
            $deleteUrl = Yii::app()->baseUrl . '/booking/deletetransaction/id/' . (int)$row->id . '/type/' . $typeMsg;
    
            $actions = '<a target="_blank" href="' . $printUrl . '"><span class="label label-success">Print</span></a>&nbsp;';
            //if ($status != 0) {
                $actions .= '<a target="_blank" class="performTask" href="' . $msgUrl . '">
                    <span class="label label-primary getMsg" data-type="' . CHtml::encode($typeMsg) . '"
                          data-tran="' . CHtml::encode(str_replace('#', '', $tranRaw)) . '">
                        Transaction Message
                    </span></a>&nbsp;';
            //}
            if($userModel['user_type_id']==1){
                $actions .= '<a href="' . $deleteUrl . '"><span class="label label-danger">Delete</span></a>&nbsp;';    
            }
            
    
            $dataRows[] = [
                (int)ltrim($tranRaw, '#'),          // hidden sort col
                $plotLink,
                CHtml::encode(@$row->customer->name),
                CHtml::encode($tranNo),
                CHtml::encode(ucfirst(@$row->transaction_type)),
                CHtml::encode(@$row->reference_number),
                CHtml::encode($payMode),
                'Rs. ' . number_format((float)@$row->amount),
                CHtml::encode(@$row->comment),
                CHtml::encode($createdOn),
                $createdByCell,
                $actions,
            ];
        }
        //echo '<pre>';print_r($dataRows);exit;
    
        // Totals
        $recordsTotal    = $totalMain + $totalExtra;
        $recordsFiltered = $totalMainFiltered + $totalExtraFiltered;
    
        echo CJSON::encode([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataRows,
        ]);
        Yii::app()->end();
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