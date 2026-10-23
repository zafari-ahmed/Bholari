<?php

class BookingpreviewController extends Controller
{
	public function actionPreview($id)
	{
		$data['booking'] = CustomerPlotsPreview::model()->findByPk($id);
		$this->renderPartial('preview',$data);
	}

	public function actionDuplicate($id)
	{
		$data['booking'] = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		$this->renderPartial('previewDuplicate',$data);
	}
	
	public function actionDuplicateback($id)
	{
		$data['booking'] = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		$this->renderPartial('previewDuplicateBack',$data);
	}
	
	public function actionWelcome($id)
	{
		$data['booking'] = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		$this->renderPartial('welcome',$data);
	}
	
	public function actionAllocation($id)
	{
		$data['booking'] = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		$this->renderPartial('allocation',$data);
	}
	
	public function actionConfirmation($id)
	{
		$data['booking'] = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		$this->renderPartial('confirmation',$data);
	}
	
	public function actionPayment($id)
	{
		$data['booking'] = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		$this->renderPartial('payment_schedule',$data);
	}
	
	public function actionPaymentsoftware($id)
	{
		$data['booking'] = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		$this->renderPartial('payment_schedule_software',$data);
	}
	
	public function actionAddps($id)
	{
		$data['booking'] = $booking = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		
		// If form submitted, collect and build JSON
        $jsonOut = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // rows comes in as: $_POST['rows']['row1']['heading1'] etc.
            $rows = $_POST['rows'] ?? [];
        
            // (Optional) trim values
            foreach ($rows as $rk => &$r) {
                $r['heading1'] = isset($r['heading1']) ? trim($r['heading1']) : '';
                $r['heading2'] = isset($r['heading2']) ? trim($r['heading2']) : '';
                $r['value']    = isset($r['value'])    ? trim($r['value'])    : '';
            }
            unset($r);
        
            // Wrap as required
            $payload = ['rows' => $rows,'cop'=>@$_POST['cost_of_plot']];
            //print_r($payload);exit;
            // Pretty JSON
            $jsonOut = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $booking->payment_schedule_json = @$jsonOut;
            $booking->save(false);
            
            //Yii::app()->user->setFlash('success','Added payment schedule');
            $this->redirect(Yii::app()->baseUrl.'/bookingpreview/payment/'.$booking->id);
        }
		   
		$this->renderPartial('add_payment_schedule',$data);
	}
	
	public function actionAddpssoftware($id)
	{
		$data['booking'] = $booking = CustomerPlots::model()->findByPk($id);
		$this->layout = '';
		
		// If form submitted, collect and build JSON
        $jsonOut = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // rows comes in as: $_POST['rows']['row1']['heading1'] etc.
            $rows = $_POST['rows'] ?? [];
        
            // (Optional) trim values
            foreach ($rows as $rk => &$r) {
                $r['heading1'] = isset($r['heading1']) ? trim($r['heading1']) : '';
                $r['heading2'] = isset($r['heading2']) ? trim($r['heading2']) : '';
                $r['value']    = isset($r['value'])    ? trim($r['value'])    : '';
            }
            unset($r);
        
            // Wrap as required
            $payload = ['rows' => $rows,'cop'=>@$_POST['cost_of_plot']];
            //print_r($payload);exit;
            // Pretty JSON
            $jsonOut = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $booking->payment_schedule_software_json = @$jsonOut;
            $booking->save(false);
            
            //Yii::app()->user->setFlash('success','Added payment schedule');
            $this->redirect(Yii::app()->baseUrl.'/bookingpreview/paymentsoftware/'.$booking->id);
        }
		   
		$this->renderPartial('add_payment_schedule_software',$data);
	}

	public function actionFile($id)
	{
		$data['booking'] = CustomerPlots::model()->findByPk($id);
		$this->layout = 'ledger';
		$this->render('fullattachfile',$data);
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