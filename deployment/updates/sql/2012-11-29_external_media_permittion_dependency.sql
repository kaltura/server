UPDATE	permission
SET		depends_on_permission_names = null
WHERE	partner_id = 0
AND		name = 'EXTERNAL_MEDIA_BASE'
LIMIT	1;
