<?php
/**
 * @package plugins.metadata
 * @subpackage model.enum
 */
interface MetadataObjectType extends BaseEnum
{
	const ENTRY = 1;
	const CATEGORY = 2;
	const USER = 3;
	const PARTNER = 4;
	const DYNAMIC_OBJECT = 5;
}