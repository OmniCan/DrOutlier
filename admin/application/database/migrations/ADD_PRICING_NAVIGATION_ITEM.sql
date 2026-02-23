-- Add Pricing/Plans page to navigation
-- Run this SQL on your production database

-- Insert the Pricing navigation item
INSERT INTO navigation_items (
    title,
    url,
    icon,
    module_id,
    sort_order,
    is_active,
    show_in_navbar,
    requires_auth,
    type,
    visibility_type,
    created_at,
    updated_at
) VALUES (
    'Plans',
    '/pricing',
    'fas fa-tags',
    NULL,
    999,
    1,
    1,
    0,
    'custom',
    'public',
    NOW(),
    NOW()
);

-- Verify the insertion
SELECT * FROM navigation_items WHERE url = '/pricing';
