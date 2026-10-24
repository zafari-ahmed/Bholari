<?php

class ActivitylogController extends Controller
{
	public $layout = 'main';

	public function init()
	{
		$this->checkSession();
		if (!Users::isSuperAdmin()) {
			throw new CHttpException(403, 'Only super admin can access activity logs.');
		}
		ActivityLogs::ensureTable();
	}

	protected function buildCriteria()
	{
		$criteria = new CDbCriteria();
		$criteria->order = 't.id DESC';
		$criteria->with = array('user', 'user.userType');

		if (!empty($_GET['user_id'])) {
			$criteria->compare('t.user_id', (int)$_GET['user_id']);
		}
		if (!empty($_GET['date_from'])) {
			$criteria->addCondition('t.created_on >= :from');
			$criteria->params = array_merge($criteria->params, array(':from' => $_GET['date_from'].' 00:00:00'));
		}
		if (!empty($_GET['date_to'])) {
			$criteria->addCondition('t.created_on <= :to');
			$criteria->params = array_merge($criteria->params, array(':to' => $_GET['date_to'].' 23:59:59'));
		}
		if (!empty($_GET['q'])) {
			$q = '%'.$_GET['q'].'%';
			$criteria->addCondition('t.description LIKE :q1 OR t.url LIKE :q2 OR t.user_name LIKE :q3 OR t.ip_address LIKE :q4 OR t.controller LIKE :q5 OR t.action LIKE :q6');
			$criteria->params = array_merge($criteria->params, array(
				':q1' => $q,
				':q2' => $q,
				':q3' => $q,
				':q4' => $q,
				':q5' => $q,
				':q6' => $q,
			));
		}
		return $criteria;
	}

	protected function filterValues()
	{
		return array(
			'user_id' => isset($_GET['user_id']) ? $_GET['user_id'] : '',
			'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : '',
			'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : '',
			'q' => isset($_GET['q']) ? $_GET['q'] : '',
		);
	}

	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('ActivityLogs', array(
			'criteria' => $this->buildCriteria(),
			'pagination' => array(
				'pageSize' => 50,
				'pageVar' => 'page',
			),
		));

		$data['logs'] = $dataProvider;
		$data['users'] = Users::model()->with('userType')->findAll(array('order' => 't.first_name ASC'));
		$data['filters'] = $this->filterValues();
		$this->render('index', $data);
	}

	public function actionExport()
	{
		$criteria = $this->buildCriteria();
		$criteria->limit = 5000;
		$logs = ActivityLogs::model()->findAll($criteria);

		$fName = 'activity-log-'.date('Y-m-d-His').'.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="'.$fName.'"');
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array('Date', 'User', 'Role', 'IP', 'Controller', 'Action', 'Description', 'URL', 'Method','Request Data'));
		foreach ($logs as $log) {
			$role = ($log->user && $log->user->userType) ? $log->user->userType->name : '';
			fputcsv($fp, array(
				$log->created_on,
				$log->user_name,
				$role,
				$log->ip_address,
				$log->controller,
				$log->action,
				$log->description,
				$log->url,
				$log->method,
				$log->request_data,
			));
		}
		fclose($fp);
		Yii::app()->end();
	}
}
