<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.enum
 */
interface SphinxFieldType extends BaseEnum 
{
	const RT_FIELD = 'rt_field';
	const RT_ATTR_BIGINT = 'rt_attr_bigint';
	const RT_ATTR_TIMESTAMP = 'rt_attr_timestamp';
	const RT_ATTR_STRING = 'rt_attr_string';
	const RT_ATTR_UINT = 'rt_attr_uint';
}