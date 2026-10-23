<?php

class RecoveryStaff extends CActiveRecord
{
	public function tableName()
	{
		return 'recovery_staff';
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
