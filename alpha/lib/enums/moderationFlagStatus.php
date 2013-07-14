<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface moderationFlagStatus extends BaseEnum
{
	const PENDING = 1;
	const MODERATED = 2;
}