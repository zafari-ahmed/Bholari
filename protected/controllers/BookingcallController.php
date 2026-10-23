<?php

class BookingcallController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionSave()
	{
	    if (isset($_POST)) {

	        $model = new BookingCallHistory();

	        $model->booking_id      = $_POST['booking_id'];
	        $model->user_id         = Yii::app()->session['userModel']['id'];
	        $model->remarks         = $_POST['remarks'];
	        $model->call_details 	= $_POST['remarks'];
	        $model->call_status  = $_POST['call_status'];
	        $model->booking_status  = $_POST['booking_status'];
	        $model->created_at      = date('Y-m-d H:i:s');
	        // Optional fields
	        $model->next_reminder = $_POST['next_reminder'];

	        $uploadFolder = getcwd() . '/uploads/booking_audio/';
	        if(!empty($_FILES['callFile']['name'])){
	        	$fileName = time().'_'.$_FILES['callFile']['name'];
	        	move_uploaded_file($_FILES['callFile']['tmp_name'], $uploadFolder.$fileName);
	        	$model->call_audio_file = $fileName;
	        } else{
	        	$model->call_audio_file = '';
	        }
	        if ($model->save()) {
	        	Yii::app()->user->setFlash('success','Call Record added successfully');
	            $this->redirect($this->callHistoryRedirectUrl($model->booking_id));
	        } else {
	            Yii::app()->user->setFlash('danger','Call Record not saved.');
	            $this->redirect($this->callHistoryRedirectUrl($_POST['booking_id']));
	        }
	    }
	}

	protected function callHistoryRedirectUrl($bookingId)
	{
		$bookingId = intval($bookingId);
		if (isset($_POST['return_to']) && $_POST['return_to'] === 'callings') {
			return Yii::app()->baseUrl.'/callings?booking_id='.$bookingId;
		}
		return Yii::app()->baseUrl.'/booking/viewbooking/'.$bookingId;
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