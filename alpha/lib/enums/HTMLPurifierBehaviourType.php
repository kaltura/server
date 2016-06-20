<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface HTMLPurifierBehaviourType extends BaseEnum
{
	const IGNORE = 0;
	const NOTIFY = 1;
	const SANITIZE = 2;
	const BLOCK = 3;
}
