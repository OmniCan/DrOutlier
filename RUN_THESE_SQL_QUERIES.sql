-- DrOutlier Subscription System - Database Setup SQL
-- Run these queries in phpMyAdmin or your database management tool

-- 1. Create modules table
CREATE TABLE `modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `frontend_url` varchar(255) DEFAULT NULL,
  `admin_url` varchar(255) DEFAULT NULL,
  `description` text,
  `icon` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_name_unique` (`name`),
  UNIQUE KEY `modules_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create plans table
CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `duration_type` enum('days','months','years') NOT NULL DEFAULT 'months',
  `duration_value` int NOT NULL DEFAULT '1',
  `razorpay_plan_id` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create plan_modules table
CREATE TABLE `plan_modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` bigint unsigned NOT NULL,
  `module_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_modules_plan_id_module_id_unique` (`plan_id`,`module_id`),
  KEY `plan_modules_plan_id_index` (`plan_id`),
  KEY `plan_modules_module_id_index` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create user_subscriptions table
CREATE TABLE `user_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `razorpay_subscription_id` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `status` enum('active','expired','cancelled','pending') NOT NULL DEFAULT 'pending',
  `amount_paid` decimal(10,2) NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `payment_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_subscriptions_user_id_index` (`user_id`),
  KEY `user_subscriptions_plan_id_index` (`plan_id`),
  KEY `user_subscriptions_user_id_status_index` (`user_id`,`status`),
  KEY `user_subscriptions_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Insert default modules data
INSERT INTO `modules` (`name`, `display_name`, `slug`, `frontend_url`, `admin_url`, `description`, `icon`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
('notes', 'Notes', 'notes', '/notes', '/admin/note', 'Access to study notes and materials', 'fas fa-book', 1, 1, NOW(), NOW()),
('spotters', 'Spotters', 'spotters', '/spotters', '/admin/spotters', 'Access to spotter questions and materials', 'fas fa-eye', 1, 2, NOW(), NOW()),
('osce', 'OSCE', 'osce', '/osce', '/admin/osce', 'Access to OSCE practice materials', 'fas fa-stethoscope', 1, 3, NOW(), NOW()),
('ai_rad', 'AI Rad (Munchies)', 'ai-rad', '/ai-rad', '/admin/munchies', 'Access to AI Radiology content', 'fas fa-brain', 1, 4, NOW(), NOW()),
('practical_essentials', 'Practical Essentials', 'practical-essentials', '/practical-essentials', '/admin/basic', 'Access to practical essentials and basics', 'fas fa-hands-helping', 1, 5, NOW(), NOW()),
('watch_and_learn', 'Watch and Learn', 'watch-and-learn', '/watch-and-learn', '/admin/watch-and-learn', 'Access to video learning materials', 'fas fa-video', 1, 6, NOW(), NOW()),
('quizora', 'Quizora', 'quizora', '/quizora', '/admin/quiz', 'Access to quiz and practice questions', 'fas fa-question-circle', 1, 7, NOW(), NOW());

-- Done! Now you can create plans in the admin panel.
