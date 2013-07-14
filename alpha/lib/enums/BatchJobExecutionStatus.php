<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface BatchJobExecutionStatus extends BaseEnum
{
	const NORMAL = 0;
	const ABORTED = 1;
}
