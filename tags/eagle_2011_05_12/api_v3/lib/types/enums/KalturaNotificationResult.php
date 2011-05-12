<?php 
/**
 * @package api
 * @subpackage enum
 */
class KalturaNotificationResult  extends KalturaEnum 
{
	const OK = 0; 
	const ERROR_RETRY = -1;
	const ERROR_NO_RETRY = -2;
	
}