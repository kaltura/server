<?php
/**
 * @package plugins.virusScan
 * @subpackage model.enum
 */ 
interface VirusScanProfileStatus extends BaseEnum
{
	const DISABLED = 1;
	const ENABLED  = 2;
	const DELETED  = 3;
}