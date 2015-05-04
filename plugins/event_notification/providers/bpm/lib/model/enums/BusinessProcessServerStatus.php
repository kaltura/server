<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model.enum
 */ 
interface BusinessProcessServerStatus extends BaseEnum
{
	const DISABLED = 1;
	const ENABLED = 2;
	const DELETED = 3;
}