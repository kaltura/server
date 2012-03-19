<?php
/**
 * Last update method to be saved on the object and later used to distinguish beetween manual 
 * changes and async automatic scripts (for example, nightly sync scripts). 
 * @package Core
 * @subpackage model.enum
 */ 
interface InheritanceType extends BaseEnum
{
	const INHERIT = 1;
	const MANUAL = 2;
}
