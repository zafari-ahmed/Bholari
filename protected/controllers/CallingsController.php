<?php

class CallingsController extends Controller
{
	public function init()
	{
		$this->checkSession();
	}

	public function actionIndex()
	{
		$this->render('index', array(
			'statusMeta' => CallingFollowUpTracker::statusMeta(),
			'today' => date('Y-m-d'),
		));
	}

	public function actionEvents()
	{
		$this->layout = false;
		$start = $this->parseDateParam('start', date('Y-m-01'));
		$end = $this->parseDateParam('end', date('Y-m-t', strtotime('+1 month')));
		$tracker = $this->tracker();
		header('Content-Type: application/json');
		echo CJSON::encode($tracker->calendarEvents($start, $end));
		Yii::app()->end();
	}

	public function actionSummary()
	{
		$this->layout = false;
		$date = $this->parseDateParam('date', date('Y-m-d'));
		$tracker = $this->tracker();
		header('Content-Type: application/json');
		echo CJSON::encode($tracker->summary($date));
		Yii::app()->end();
	}

	public function actionDay()
	{
		$this->layout = false;
		$date = $this->parseDateParam('date', date('Y-m-d'));
		$type = Yii::app()->request->getParam('type', 'all');
		$allowed = array(
			'all',
			CallingFollowUpTracker::NEVER_CALLED,
			CallingFollowUpTracker::FIRST_CALL,
			CallingFollowUpTracker::DUE,
			CallingFollowUpTracker::ON_TIME,
			CallingFollowUpTracker::LATE,
			CallingFollowUpTracker::MISSED,
			CallingFollowUpTracker::STILL_MISSED,
			CallingFollowUpTracker::UPCOMING,
		);
		if (!in_array($type, $allowed, true)) {
			$type = 'all';
		}

		$tracker = $this->tracker();
		$this->renderPartial('day', array(
			'date' => $date,
			'type' => $type,
			'groups' => $tracker->dayItems($date, $type),
			'statusMeta' => CallingFollowUpTracker::statusMeta(),
			'callStatus' => Yii::app()->params['callStatus'],
			'bookingStatus' => Yii::app()->params['bookingStatus'],
			'callStatusColor' => Yii::app()->params['callStatusColor'],
			'bookingStatusColor' => Yii::app()->params['bookingStatusColor'],
			'today' => $tracker->getToday(),
		));
	}

	public function actionTimeline()
	{
		$this->layout = false;
		$bookingId = intval(Yii::app()->request->getParam('booking_id'));
		$tracker = $this->tracker();
		$data = $tracker->timeline($bookingId);
		$this->renderPartial('timeline', array(
			'booking' => $data['booking'],
			'entries' => $data['entries'],
			'neverCalled' => $data['neverCalled'],
			'statusMeta' => CallingFollowUpTracker::statusMeta(),
			'callStatus' => Yii::app()->params['callStatus'],
			'bookingStatus' => Yii::app()->params['bookingStatus'],
		));
	}

	protected function tracker()
	{
		$phaseId = Yii::app()->session->get('userModel')['phase_id'];
		return new CallingFollowUpTracker($phaseId);
	}

	protected function parseDateParam($name, $default)
	{
		$value = Yii::app()->request->getParam($name, $default);
		$ts = strtotime($value);
		if ($ts === false) {
			return $default;
		}
		return date('Y-m-d', $ts);
	}
}
