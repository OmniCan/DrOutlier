-- Test data for New Spotters module
-- Run this SQL in your database after the tables are created

-- Insert parent categories (top level)
INSERT INTO `new_spotters_categories` (`name`, `parent_id`, `color`, `animation_file`, `status`, `created_at`, `updated_at`) VALUES
('Radiology Basics', 0, 'hue-rotate(180deg)', '/animantion/Blue circle 2.json', 1, NOW(), NOW()),
('Advanced Imaging', 0, 'hue-rotate(90deg)', '/animantion/Green circle.json', 1, NOW(), NOW());

-- Get the IDs (adjust these based on your actual inserted IDs)
-- If Radiology Basics = 1, Advanced Imaging = 2

-- Insert child categories (chapters) under "Radiology Basics" (parent_id = 1)
INSERT INTO `new_spotters_categories` (`name`, `parent_id`, `color`, `animation_file`, `status`, `created_at`, `updated_at`) VALUES
('Chapter 1: CNS', 1, 'hue-rotate(180deg)', '/animantion/Blue circle 2.json', 1, NOW(), NOW()),
('Chapter 2: Chest', 1, 'hue-rotate(180deg)', '/animantion/Blue circle 2.json', 1, NOW(), NOW()),
('Chapter 3: Abdomen', 1, 'hue-rotate(180deg)', '/animantion/Blue circle 2.json', 1, NOW(), NOW());

-- Insert child categories (chapters) under "Advanced Imaging" (parent_id = 2)
INSERT INTO `new_spotters_categories` (`name`, `parent_id`, `color`, `animation_file`, `status`, `created_at`, `updated_at`) VALUES
('Chapter 1: MRI Techniques', 2, 'hue-rotate(90deg)', '/animantion/Green circle.json', 1, NOW(), NOW()),
('Chapter 2: CT Angiography', 2, 'hue-rotate(90deg)', '/animantion/Green circle.json', 1, NOW(), NOW());

-- Insert content items for "Chapter 1: CNS" (category = 3, assuming that's the ID)
-- Adjust the category ID based on your actual chapter IDs

INSERT INTO `new_spotters` (`category`, `title`, `sort_order`, `pdf_file`, `is_premium`, `created_at`, `updated_at`) VALUES
(3, 'Brain MRI Cases - Set 1', 1, 'brain_mri_set1.pdf', 0, NOW(), NOW()),
(3, 'Brain CT Cases - Set 1', 2, 'brain_ct_set1.pdf', 0, NOW(), NOW()),
(3, 'Skull X-Ray Cases', 3, 'skull_xray_cases.pdf', 0, NOW(), NOW()),
(3, 'Spine MRI Cases', 4, 'spine_mri_cases.pdf', 1, NOW(), NOW());

-- Insert content items for "Chapter 2: Chest" (category = 4)
INSERT INTO `new_spotters` (`category`, `title`, `sort_order`, `pdf_file`, `is_premium`, `created_at`, `updated_at`) VALUES
(4, 'Chest X-Ray - Normal Anatomy', 1, 'chest_xray_normal.pdf', 0, NOW(), NOW()),
(4, 'Chest X-Ray - Pathology Set 1', 2, 'chest_xray_pathology1.pdf', 0, NOW(), NOW()),
(4, 'Chest CT - Basic Cases', 3, 'chest_ct_basic.pdf', 1, NOW(), NOW());

-- Insert content items for "Chapter 3: Abdomen" (category = 5)
INSERT INTO `new_spotters` (`category`, `title`, `sort_order`, `pdf_file`, `is_premium`, `created_at`, `updated_at`) VALUES
(5, 'Abdominal X-Ray Cases', 1, 'abd_xray_cases.pdf', 0, NOW(), NOW()),
(5, 'Abdominal CT - Set 1', 2, 'abd_ct_set1.pdf', 1, NOW(), NOW());

-- Note: Make sure to:
-- 1. Upload actual PDF files to: admin/assets/admin/images/new_spotters_pdf/
-- 2. Adjust the category IDs in the INSERT statements based on your actual database IDs
-- 3. The PDF filenames should match the files you upload
