-- Recovery module schema for bholari

INSERT INTO `user_types` (`id`, `name`)
SELECT 8, 'Recovery Manager'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `user_types` WHERE `id` = 8);

CREATE TABLE IF NOT EXISTS `recovery_staff` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_by` int NOT NULL COMMENT 'users.id of recovery manager',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `recovery_staff_blocks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_id` int NOT NULL,
  `block_number` varchar(50) NOT NULL,
  `phase_id` int NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_block_phase` (`staff_id`, `block_number`, `phase_id`),
  KEY `block_number` (`block_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `recovery_team_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_type` enum('staff','manager') NOT NULL,
  `sender_id` int NOT NULL,
  `booking_id` int DEFAULT NULL,
  `message` text NOT NULL,
  `approved_message` text DEFAULT NULL,
  `yii_edited_message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by_manager_id` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `yii_edited_by` int DEFAULT NULL,
  `yii_edited_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Extend booking_call_history for recovery staff calls
ALTER TABLE `booking_call_history`
  MODIFY COLUMN `user_id` int NULL;

-- Run these only if columns do not exist yet:
-- ALTER TABLE `booking_call_history` ADD COLUMN `recovery_staff_id` int NULL AFTER `user_id`;
-- ALTER TABLE `booking_call_history` ADD COLUMN `source` enum('yii','recovery') NOT NULL DEFAULT 'yii' AFTER `recovery_staff_id`;
