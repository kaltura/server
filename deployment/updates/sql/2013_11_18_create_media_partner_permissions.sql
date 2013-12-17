
INSERT INTO permission (type, name, friendly_name, description, partner_id, status, created_at, updated_at, custom_data) VALUES 
(1, 'MEDIA_SERVER_BASE', 'Media server system permission', 'Media server system permission', -5, 1, NOW(), NOW(), NULL),
(4, 'PARTNER_-5_GROUP_*_PERMISSION', 'Partner -5 permission for group *', 'Partner -5 permission for group *', -5, 1, NOW(), NOW(), 'a:1:{s:13:"partner_group";s:1:"*";}');
