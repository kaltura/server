
UPDATE	file_sync fs,  flavor_asset fa
SET		fs.object_id = fa.id
WHERE	fs.object_id = fa.int_id
AND		fs.object_type = 4;
