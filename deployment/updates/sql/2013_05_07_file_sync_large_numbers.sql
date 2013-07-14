ALTER TABLE file_sync 
MODIFY id BIGINT(20) NOT NULL AUTO_INCREMENT, 
MODIFY linked_id BIGINT, 
ADD deleted_id BIGINT DEFAULT 0 AFTER custom_data,
DROP INDEX object_id_object_type_version_subtype_dc, 
ADD UNIQUE KEY unique_index (object_id,object_type,version,object_sub_type,dc,deleted_id);
