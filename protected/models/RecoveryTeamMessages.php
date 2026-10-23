<?php

class RecoveryTeamMessages extends CActiveRecord
{
	public function tableName()
	{
		return 'recovery_team_messages';
	}

	public function rules()
	{
		return array(
			array('sender_type, sender_id, message, status, created_at', 'required'),
			array('sender_id, booking_id, approved_by_manager_id, yii_edited_by', 'numerical', 'integerOnly' => true),
			array('message, approved_message, yii_edited_message', 'safe'),
		);
	}

	public function getDisplayMessage()
	{
		if (!empty($this->yii_edited_message)) {
			return $this->yii_edited_message;
		}
		if (!empty($this->approved_message)) {
			return $this->approved_message;
		}
		return $this->message;
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
