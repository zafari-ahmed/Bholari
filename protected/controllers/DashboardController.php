<?php

class DashboardController extends Controller
{
	public $layout='main';
	
	public function init()
    {
        $this->checkSession();

    }
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$userModel = Yii::app()->session->get('userModel');
		if($userModel['user_type']['id']==7){
		    $this->render('dashboard-view');
		} else{
		    $this->render('index');    
		}
		
	}

	/**
	 * Plot & booking payment states overview.
	 */
	public function actionStats()
	{
		$userModel = Yii::app()->session->get('userModel');
		$phase_id = (int)$userModel['phase_id'];

		$totalPlots = (int)Plots::model()->count(
			't.phase_id = :phase_id',
			array(':phase_id' => $phase_id)
		);
		// Same as Dashboard "Available Plots"
		$activePlots = (int)Plots::model()->count(
			't.status = 0 AND t.phase_id = :phase_id',
			array(':phase_id' => $phase_id)
		);
		// Same as Dashboard "Bookings" tile
		$totalBookings = (int)CustomerPlots::model()->count();
		// Same as Dashboard "Cancelled Bookings" tile
		$cancelledBookings = (int)CustomerPlotCancelled::model()->count(array('group' => 'booking_id'));
		$cancelledRefundTotal = (float)CustomerPlotCancelled::model()
			->getDbConnection()
			->createCommand()
			->select('SUM(amount)')
			->from(CustomerPlotCancelled::model()->tableName())
			->queryScalar();

		$sql = "
			SELECT
				cp.id AS booking_id,
				p.block_number AS block_number,
				(
					(COALESCE(p.total,0) - COALESCE(p.discount,0))
					+ CASE WHEN p.is_corner = 1 THEN (COALESCE(p.is_corner_amount,0)/100) * COALESCE(p.total,0) ELSE 0 END
					+ CASE WHEN p.is_road_facing = 1 THEN (COALESCE(p.is_road_facing_amount,0)/100) * COALESCE(p.total,0) ELSE 0 END
					+ CASE WHEN p.is_park_facing = 1 THEN (COALESCE(p.is_park_facing_amount,0)/100) * COALESCE(p.total,0) ELSE 0 END
					+ CASE WHEN p.is_west_open = 1 THEN (COALESCE(p.is_west_open_amount,0)/100) * COALESCE(p.total,0) ELSE 0 END
				) AS booking_total,
				(
					COALESCE((
						SELECT SUM(cpt.amount) FROM customer_plot_transactions cpt
						WHERE cpt.plot_id = cp.id AND cpt.status = 1
					), 0)
					+ COALESCE((
						SELECT SUM(cpet.amount) FROM customer_plot_extra_transactions cpet
						WHERE cpet.plot_id = cp.id AND cpet.status = 1
					), 0)
				) AS total_paid
			FROM customer_plots cp
			INNER JOIN plots p ON p.id = cp.plot_id
			WHERE cp.status != 0 AND cp.status != 3 AND cp.phase_id = :phase_id
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':phase_id' => $phase_id));
		$fullPaymentCount = 0;
		$fullPaymentPlotAmount = 0;
		$fullPaymentPaidAmount = 0;
		$remainingCount = 0;
		$remainingAmountTotal = 0;
		$dueCount = 0;
		$dueAmountTotal = 0;
		$fullPaymentByBlock = array();
		$remainingByBlock = array();
		$dueByBlock = array();
		$regularByBlock = array();

		foreach ($rows as $row) {
			$bookingTotal = (float)$row['booking_total'];
			$totalPaid = (float)$row['total_paid'];
			$remaining = max(0, $bookingTotal - $totalPaid);
			$block = trim((string)$row['block_number']);
			if ($block === '') {
				$block = 'N/A';
			}

			if ($remaining <= 0) {
				$fullPaymentCount++;
				$fullPaymentPlotAmount += $bookingTotal;
				$fullPaymentPaidAmount += $totalPaid;
				if (!isset($fullPaymentByBlock[$block])) {
					$fullPaymentByBlock[$block] = 0;
				}
				$fullPaymentByBlock[$block]++;
			} else {
				$remainingCount++;
				$remainingAmountTotal += $remaining;
				if (!isset($remainingByBlock[$block])) {
					$remainingByBlock[$block] = 0;
				}
				$remainingByBlock[$block]++;
			}

			$dues = CustomerPlots::calculateDues((int)$row['booking_id']);
			$dueAmount = ($dues['status'] === 'ok')
				? (float)$dues['due_amount']
				: $remaining;

			// Due / Regular only among bookings that still have remaining balance
			if ($remaining > 0 && $dueAmount > 0) {
				$dueCount++;
				$dueAmountTotal += $dueAmount;
				if (!isset($dueByBlock[$block])) {
					$dueByBlock[$block] = 0;
				}
				$dueByBlock[$block]++;
			} elseif ($remaining > 0) {
				if (!isset($regularByBlock[$block])) {
					$regularByBlock[$block] = 0;
				}
				$regularByBlock[$block]++;
			}
		}

		// Regular = remaining balance bookings that are not overdue
		$regularBookings = max(0, $remainingCount - $dueCount);

		ksort($fullPaymentByBlock, SORT_NATURAL | SORT_FLAG_CASE);
		ksort($remainingByBlock, SORT_NATURAL | SORT_FLAG_CASE);
		ksort($dueByBlock, SORT_NATURAL | SORT_FLAG_CASE);
		ksort($regularByBlock, SORT_NATURAL | SORT_FLAG_CASE);

		$this->render('stats', array(
			'totalPlots' => $totalPlots,
			'activePlots' => $activePlots,
			'totalBookings' => $totalBookings,
			'cancelledBookings' => $cancelledBookings,
			'cancelledRefundTotal' => $cancelledRefundTotal,
			'fullPaymentCount' => $fullPaymentCount,
			'fullPaymentPlotAmount' => $fullPaymentPlotAmount,
			'fullPaymentPaidAmount' => $fullPaymentPaidAmount,
			'remainingCount' => $remainingCount,
			'remainingAmountTotal' => $remainingAmountTotal,
			'dueCount' => $dueCount,
			'dueAmountTotal' => $dueAmountTotal,
			'regularBookings' => $regularBookings,
			'fullPaymentByBlock' => $fullPaymentByBlock,
			'remainingByBlock' => $remainingByBlock,
			'dueByBlock' => $dueByBlock,
			'regularByBlock' => $regularByBlock,
		));
	}

	public function actionFetchall(){
		$term = @$_REQUEST['q'];
		$blocks = Plots::model()->findAll(array(
            'select'=>'t.block_number',
            'distinct'=>true,
            'condition'=>"t.block_number LIKE :number",
            'params'    => array(':number' => "%$term%")
        )); 
		$data = [];

		foreach($blocks as $bb){
			$d['id'] = $bb->block_number;
			$d['text'] = $bb->block_number;
			array_push($data, $d);
		}

        $transactions = CustomerPlotTransactions::model()->findAll(array(
            'select'=>'t.transaction_number',
            'distinct'=>true,
            'condition'=>"t.transaction_number LIKE :number",
            //'params'=>array(':number'=>$term)
            'params'    => array(':number' => "%$term%")
        ));


        foreach($transactions as $transaction){
			$d['id'] = $transaction->transaction_number;
			$d['text'] = $this->startsWith($transaction->transaction_number, '#')?$transaction->transaction_number:'#'.$transaction->transaction_number;
			array_push($data, $d);
		}


		$transactions = CustomerPlotExtraTransactions::model()->findAll(array(
            'select'=>'t.transaction_number',
            'distinct'=>true,
            'condition'=>"t.transaction_number LIKE :number",
            //'params'=>array(':number'=>$term)
            'params'    => array(':number' => "%$term%")
        ));


        foreach($transactions as $transaction){
			$d['id'] = 'Dev-'.$transaction->transaction_number;
			$d['text'] = 'Dev- '.($this->startsWith($transaction->transaction_number, '#')?$transaction->transaction_number:'#'.$transaction->transaction_number);
			array_push($data, $d);
		}


		echo json_encode($data);


	}

}