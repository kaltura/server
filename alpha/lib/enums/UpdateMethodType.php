<?php
/**
 * Last update method to be saved on the object and later used to distinguish beetween manual 
 * changes and async automatic scripts (for example, nightly sync scripts). 
 * @package Core
 * @subpackage model.enum
 */ 
interface UpdateMethodType extends BaseEnum
{
	const MANUAL = 0;
	const AUTOMATIC = 1;
}
