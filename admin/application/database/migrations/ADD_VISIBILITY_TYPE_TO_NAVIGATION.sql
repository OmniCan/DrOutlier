-- Add visibility_type column to navigation_items table
-- Run this SQL on your production database

ALTER TABLE `navigation_items`
ADD COLUMN `visibility_type` VARCHAR(50) NOT NULL DEFAULT 'public' AFTER `type`;

-- Possible values:
-- 'public' - Show to everyone (logged in or not)
-- 'subscription' - Show only to users with subscription access to the module
-- 'auth' - Show only to logged in users (regardless of subscription)

-- Update existing records to 'subscription' since they are module-based
UPDATE `navigation_items` SET `visibility_type` = 'subscription' WHERE `module_id` IS NOT NULL;
