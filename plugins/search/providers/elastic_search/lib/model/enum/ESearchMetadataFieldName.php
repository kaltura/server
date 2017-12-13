<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchMetadataFieldName extends BaseEnum
{
	const VALUE_TEXT = 'metadata.value_text';
	const VALUE_INT = 'metadata.value_int';
	const PROFILE_ID = 'metadata.metadata_profile_id';
	const FIELD_ID = 'metadata.metadata_field_id';
	const XPATH = 'metadata.xpath';
}
