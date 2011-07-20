<?php
class ContentDistributionSphinxConfiguration{
	public static function getConfiguration() {
		return array(
		kSphinxSearchManager::getSphinxIndexName('entry') => array (
			'fields' => array(
				ContentDistributionPlugin::getSphinxFieldName(ContentDistributionPlugin::SPHINX_EXPENDER_FIELD_DATA) => SphinxFieldType::RT_FIELD,
				)
		),
		kSphinxSearchManager::getSphinxIndexName('entry_distribution') => array (	
			'type'					=> 'rt',
			'path'					=> '/sphinx/kaltura_distribution_rt',
			
			'fields' => array (
				'entry_id' => SphinxFieldType::RT_FIELD,
				'thumb_asset_ids' => SphinxFieldType::RT_FIELD,
				'flavor_asset_ids' => SphinxFieldType::RT_FIELD,
				'remote_id' => SphinxFieldType::RT_FIELD,
				
				'int_entry_id' => SphinxFieldType::RT_ATTR_BIGINT,
				'entry_distribution_id' => SphinxFieldType::RT_ATTR_BIGINT,
				'partner_id' => SphinxFieldType::RT_ATTR_BIGINT,
				'distribution_profile_id' => SphinxFieldType::RT_ATTR_BIGINT,
				'entry_distribution_status' => SphinxFieldType::RT_ATTR_BIGINT,
				'dirty_status' => SphinxFieldType::RT_ATTR_BIGINT,
				'sun_status' => SphinxFieldType::RT_ATTR_BIGINT,
				'plays' => SphinxFieldType::RT_ATTR_BIGINT,
				'views' => SphinxFieldType::RT_ATTR_BIGINT,
				'error_type' => SphinxFieldType::RT_ATTR_BIGINT,
				'error_number' => SphinxFieldType::RT_ATTR_BIGINT,
				
				'created_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
				'updated_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
				'submitted_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
				'sunrise' => SphinxFieldType::RT_ATTR_TIMESTAMP,
				'sunset' => SphinxFieldType::RT_ATTR_TIMESTAMP,
				'last_report' => SphinxFieldType::RT_ATTR_TIMESTAMP,
				'next_report' => SphinxFieldType::RT_ATTR_TIMESTAMP,
				
				'str_entry_id' => SphinxFieldType::RT_ATTR_STRING),
			
			'blend_chars'			=> '`, ~, !, @, U+23, $, %, ^, &, *, (, ), -, _, =, +, [, ], {, }, ;, :, \', \, |, /, ?, U+0C, ., <, >',
			'charset_type'			=> 'utf-8',
			'charset_table'			=> '0..9, A..Z, a..z, _, a..z, U+410..U+42F->U+430..U+44F, U+430..U+44F'
	));
	}
}