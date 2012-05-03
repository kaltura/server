<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface FeatureStatusType extends BaseEnum
{
	const LOCK_CATEGORY = 1;
	const INDEX_CATEGORY = 2;
	const INDEX_CATEGORY_ENTRY = 3;
	const INDEX_ENTRY = 4;
	const INDEX_CATEGORY_KUSER = 5;
}
