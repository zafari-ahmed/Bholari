<?php

class RecoverynoteController extends Controller
{
	public function init()
	{
		$this->checkSession();
	}

	public function actionSaveedit()
	{
		if (!isset($_POST['message_id'], $_POST['yii_edited_message'])) {
			Yii::app()->user->setFlash('danger', 'Invalid request.');
			$this->redirectBack(isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0);
		}

		$userModel = Yii::app()->session->get('userModel');
		$allowedTypes = array(1, 3, 5);
		if (!in_array((int)$userModel['user_type']['id'], $allowedTypes, true)) {
			Yii::app()->user->setFlash('danger', 'You are not allowed to edit recovery notes.');
			$this->redirectBack((int)$_POST['booking_id']);
		}

		$message = RecoveryTeamMessages::model()->findByPk((int)$_POST['message_id']);
		if (!$message || $message->status !== 'approved') {
			Yii::app()->user->setFlash('danger', 'Recovery note not found.');
			$this->redirectBack((int)$_POST['booking_id']);
		}

		$message->yii_edited_message = trim($_POST['yii_edited_message']);
		$message->yii_edited_by = (int)$userModel['id'];
		$message->yii_edited_at = date('Y-m-d H:i:s');
		if ($message->save(false)) {
			Yii::app()->user->setFlash('success', 'Recovery note updated.');
		} else {
			Yii::app()->user->setFlash('danger', 'Unable to update recovery note.');
		}

		$this->redirectBack((int)$_POST['booking_id']);
	}

	protected function redirectBack($bookingId)
	{
		if ($bookingId > 0) {
			$this->redirect(Yii::app()->baseUrl . '/booking/viewbooking/' . $bookingId);
		}
		$this->redirect(Yii::app()->baseUrl . '/booking');
	}
}
