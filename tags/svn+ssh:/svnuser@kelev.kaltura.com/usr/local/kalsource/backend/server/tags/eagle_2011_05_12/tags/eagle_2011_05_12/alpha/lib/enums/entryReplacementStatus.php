<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface entryReplacementStatus extends BaseEnum
{
	const NONE = 0;
	const APPROVED_BUT_NOT_READY = 1;
	const READY_BUT_NOT_APPROVED = 2;
	const NOT_READY_AND_NOT_APPROVED = 3;
}
