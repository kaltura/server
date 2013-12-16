<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface FileAssetStatus extends BaseEnum
{
	const PENDING = 0;
	const UPLOADING = 1;
	const READY = 2;
	const DELETED = 3;
	const ERROR = 4;
}