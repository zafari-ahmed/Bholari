<?php

class ActivityLogs extends CActiveRecord
{
	/**
	 * Skip activity logging for these controller/action pairs (all users).
	 * Format: 'controller/action' or 'controller/*' to skip the whole controller.
	 */
	public static $bypass = array(
		'site/error',
		'api/authenticate',
		'gii/*',
		// 'dashboard/index',
		// 'booking/viewbooking',
		'activitylog/*',
	);

	public function tableName()
	{
		return 'activity_logs';
	}

	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public static function bypassList()
	{
		$list = is_array(self::$bypass) ? self::$bypass : array();
		if (!empty(Yii::app()->params['activityLogBypass']) && is_array(Yii::app()->params['activityLogBypass'])) {
			$list = array_merge($list, Yii::app()->params['activityLogBypass']);
		}
		$clean = array();
		foreach ($list as $item) {
			$item = strtolower(trim((string)$item));
			if ($item === '') {
				continue;
			}
			$clean[$item] = true;
		}
		return array_keys($clean);
	}

	public static function shouldBypass($controller, $action)
	{
		$controller = strtolower(trim((string)$controller));
		$action = strtolower(trim((string)$action));
		if ($controller === '') {
			return false;
		}
		$pair = $controller.'/'.$action;
		$all = $controller.'/*';
		foreach (self::bypassList() as $item) {
			if ($item === $pair || $item === $all || $item === $controller) {
				return true;
			}
		}
		return false;
	}

	public static function ensureTable()
	{
		$db = Yii::app()->db;
		if ($db->schema->getTable('activity_logs', true) === null) {
			$db->createCommand("
				CREATE TABLE `activity_logs` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`user_id` INT(11) NULL,
					`user_name` VARCHAR(255) NULL,
					`controller` VARCHAR(100) NULL,
					`action` VARCHAR(100) NULL,
					`booking_id` INT(11) NULL,
					`method` VARCHAR(10) NULL,
					`url` TEXT NULL,
					`ip_address` VARCHAR(45) NULL,
					`device_type` VARCHAR(20) NULL,
					`description` TEXT NULL,
					`request_data` TEXT NULL,
					`created_on` DATETIME NOT NULL,
					PRIMARY KEY (`id`),
					KEY `user_created` (`user_id`, `created_on`),
					KEY `created_on` (`created_on`),
					KEY `booking_id` (`booking_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8
			")->execute();
			$db->schema->refresh();
		}
		self::ensureColumns();
	}

	public static function ensureColumns()
	{
		$db = Yii::app()->db;
		$table = $db->schema->getTable('activity_logs', true);
		if ($table === null) {
			return;
		}
		$changed = false;
		if (!isset($table->columns['booking_id'])) {
			$db->createCommand('ALTER TABLE `activity_logs` ADD COLUMN `booking_id` INT(11) NULL AFTER `action`, ADD KEY `booking_id` (`booking_id`)')->execute();
			$changed = true;
		}
		if (!isset($table->columns['device_type'])) {
			$db->createCommand('ALTER TABLE `activity_logs` ADD COLUMN `device_type` VARCHAR(20) NULL AFTER `ip_address`')->execute();
			$changed = true;
		}
		if ($changed) {
			$db->schema->refresh();
		}
	}

	/**
	 * Detect PC / Mobile / Tablet from User-Agent.
	 */
	public static function detectDevice($userAgent = null)
	{
		$ua = $userAgent !== null
			? (string)$userAgent
			: (isset($_SERVER['HTTP_USER_AGENT']) ? (string)$_SERVER['HTTP_USER_AGENT'] : '');
		if ($ua === '') {
			return 'Unknown';
		}
		if (preg_match('/ipad|tablet|playbook|silk|(android(?!.*mobile))/i', $ua)) {
			return 'Tablet';
		}
		if (preg_match('/mobi|iphone|ipod|android.*mobile|windows phone|blackberry|opera mini|opera mobi|webos|fennec|kindle/i', $ua)) {
			return 'Mobile';
		}
		return 'PC';
	}

	/**
	 * Resolve booking id from URL / GET for booking-related screens.
	 */
	public static function extractBookingId($controller, $action, $url, $get = array())
	{
		$controller = strtolower(trim((string)$controller));
		$action = strtolower(trim((string)$action));
		$url = (string)$url;
		$get = (array)$get;

		$bookingControllers = array('booking', 'bookingpreview', 'letter', 'certificate');
		$bookingActions = array(
			'viewbooking', 'editbooking', 'bookingledger', 'ledgeronly', 'chargesledger',
			'cancel', 'transfer', 'transferamount', 'plotdetail', 'plotdetailjson',
			'addtransaction', 'addoldtransaction', 'addplantransaction',
			'printinvoice', 'printdevelopmentinvoice', 'printplaninvoice',
			'bookingcancellationletter', 'bookingwarningletter', 'bookingtransferletter',
			'unblockbooking', 'changeblockedstatus',
		);

		if (!empty($get['booking']) && ctype_digit((string)$get['booking'])) {
			return (int)$get['booking'];
		}
		if (!empty($get['id']) && ctype_digit((string)$get['id']) && (
			in_array($controller, $bookingControllers, true) || in_array($action, $bookingActions, true)
		)) {
			return (int)$get['id'];
		}
		// In this app, some booking screens pass plot_id as customer_plots.id
		if (!empty($get['plot']) && ctype_digit((string)$get['plot']) && $controller === 'booking') {
			return (int)$get['plot'];
		}

		if (preg_match('#/(?:booking|bookingpreview)/(?:viewbooking|editbooking|bookingledger|ledgeronly|chargesledger|cancel|transfer|plotdetail)/(\d+)#i', $url, $m)) {
			return (int)$m[1];
		}
		if (preg_match('#/(?:booking|bookingpreview)/[a-zA-Z]+/(\d+)(?:/|$)#i', $url, $m) && in_array($controller, $bookingControllers, true)) {
			return (int)$m[1];
		}

		return null;
	}

	public static function bookingPageLabel($action)
	{
		$map = array(
			'viewbooking' => 'View Booking',
			'editbooking' => 'Edit Booking',
			'bookingledger' => 'Booking Ledger',
			'ledgeronly' => 'Ledger Only',
			'chargesledger' => 'Charges Ledger',
			'addtransaction' => 'Add Transaction',
			'printinvoice' => 'Print Invoice',
			'cancel' => 'Cancel Booking',
			'transfer' => 'Transfer Booking',
		);
		$key = strtolower(trim((string)$action));
		return isset($map[$key]) ? $map[$key] : ucfirst((string)$action);
	}

	/**
	 * @param array $data
	 * @param bool $force Skip bypass list (used for explicit login success/fail logs)
	 */
	public static function write($data, $force = false)
	{
		try {
			$controller = isset($data['controller']) ? $data['controller'] : '';
			$action = isset($data['action']) ? $data['action'] : '';
			if (!$force && self::shouldBypass($controller, $action)) {
				return;
			}
			self::ensureTable();

			$bookingId = isset($data['booking_id']) ? $data['booking_id'] : null;
			if ($bookingId === null || $bookingId === '') {
				$get = array();
				if (!empty($data['request_data'])) {
					$decoded = json_decode($data['request_data'], true);
					if (is_array($decoded) && !empty($decoded['get'])) {
						$get = $decoded['get'];
					}
				}
				$bookingId = self::extractBookingId(
					$controller,
					$action,
					isset($data['url']) ? $data['url'] : '',
					$get
				);
			}

			$deviceType = isset($data['device_type']) ? $data['device_type'] : self::detectDevice();
			$description = isset($data['description']) ? $data['description'] : null;
			if ($bookingId && $description !== null && $description !== '') {
				$page = self::bookingPageLabel($action);
				if (stripos($description, 'GB-') === false && stripos($description, (string)$bookingId) === false) {
					$description .= ' — '.$page.' GB-'.(int)$bookingId;
				}
			}

			Yii::app()->db->createCommand()->insert('activity_logs', array(
				'user_id' => isset($data['user_id']) ? $data['user_id'] : null,
				'user_name' => isset($data['user_name']) ? substr((string)$data['user_name'], 0, 255) : null,
				'controller' => isset($data['controller']) ? substr((string)$data['controller'], 0, 100) : null,
				'action' => isset($data['action']) ? substr((string)$data['action'], 0, 100) : null,
				'booking_id' => $bookingId ? (int)$bookingId : null,
				'method' => isset($data['method']) ? substr((string)$data['method'], 0, 10) : null,
				'url' => isset($data['url']) ? $data['url'] : null,
				'ip_address' => isset($data['ip_address']) ? substr((string)$data['ip_address'], 0, 45) : null,
				'device_type' => $deviceType ? substr((string)$deviceType, 0, 20) : null,
				'description' => $description,
				'request_data' => isset($data['request_data']) ? $data['request_data'] : null,
				'created_on' => date('Y-m-d H:i:s'),
			));
		} catch (Exception $e) {
			Yii::log('Activity log failed: '.$e->getMessage(), CLogger::LEVEL_WARNING);
		}
	}

	public static function clientIp()
	{
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			return substr(trim($parts[0]), 0, 45);
		}
		$ip = Yii::app()->request->userHostAddress;
		return $ip ? substr($ip, 0, 45) : '0.0.0.0';
	}

	/**
	 * For older rows without booking_id, try to resolve from stored URL / request_data.
	 */
	public function resolveBookingId()
	{
		if (!empty($this->booking_id)) {
			return (int)$this->booking_id;
		}
		$get = array();
		if (!empty($this->request_data)) {
			$decoded = json_decode($this->request_data, true);
			if (is_array($decoded) && !empty($decoded['get'])) {
				$get = $decoded['get'];
			}
		}
		return self::extractBookingId($this->controller, $this->action, $this->url, $get);
	}

	public static function sanitizeRequest($input)
	{
		$sensitive = array(
			'password', 'conf_password', 'confirm_password', 'old_password',
			'comm_hash', 'csrf_token', 'YII_CSRF_TOKEN',
		);
		$clean = array();
		foreach ((array)$input as $key => $value) {
			$lower = strtolower((string)$key);
			if (in_array($lower, $sensitive, true) || strpos($lower, 'password') !== false) {
				$clean[$key] = '***';
				continue;
			}
			if (is_array($value)) {
				$clean[$key] = self::sanitizeRequest($value);
			} else {
				$clean[$key] = is_scalar($value) ? substr((string)$value, 0, 500) : '';
			}
		}
		return $clean;
	}
}
