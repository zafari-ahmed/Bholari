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
		if ($db->schema->getTable('activity_logs', true) !== null) {
			return;
		}
		$db->createCommand("
			CREATE TABLE `activity_logs` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`user_id` INT(11) NULL,
				`user_name` VARCHAR(255) NULL,
				`controller` VARCHAR(100) NULL,
				`action` VARCHAR(100) NULL,
				`method` VARCHAR(10) NULL,
				`url` TEXT NULL,
				`ip_address` VARCHAR(45) NULL,
				`description` TEXT NULL,
				`request_data` TEXT NULL,
				`created_on` DATETIME NOT NULL,
				PRIMARY KEY (`id`),
				KEY `user_created` (`user_id`, `created_on`),
				KEY `created_on` (`created_on`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		")->execute();
		$db->schema->refresh();
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
			Yii::app()->db->createCommand()->insert('activity_logs', array(
				'user_id' => isset($data['user_id']) ? $data['user_id'] : null,
				'user_name' => isset($data['user_name']) ? substr((string)$data['user_name'], 0, 255) : null,
				'controller' => isset($data['controller']) ? substr((string)$data['controller'], 0, 100) : null,
				'action' => isset($data['action']) ? substr((string)$data['action'], 0, 100) : null,
				'method' => isset($data['method']) ? substr((string)$data['method'], 0, 10) : null,
				'url' => isset($data['url']) ? $data['url'] : null,
				'ip_address' => isset($data['ip_address']) ? substr((string)$data['ip_address'], 0, 45) : null,
				'description' => isset($data['description']) ? $data['description'] : null,
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
