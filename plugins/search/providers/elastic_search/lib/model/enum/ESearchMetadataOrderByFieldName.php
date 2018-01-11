<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchMetadataOrderByFieldName extends BaseEnum
{
	const METADATA_VALUE_TEXT = 'metadata.value_text.keyword';
	const METADATA_VALUE_INT = 'metadata.value_int';
}
