
ALTER TABLE search_entry ADD FULLTEXT(entry_id, name, tags, source_link, duration_type, group_id, description, admin_tags, categories, flavor_params, plugin_data);

