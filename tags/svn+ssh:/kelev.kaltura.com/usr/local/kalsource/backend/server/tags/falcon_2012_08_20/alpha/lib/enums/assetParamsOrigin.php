<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface assetParamsOrigin extends BaseEnum
{
	const CONVERT = 0;
	const INGEST = 1;
	const CONVERT_WHEN_MISSING = 2;
}
