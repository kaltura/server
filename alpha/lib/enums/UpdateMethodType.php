<?php
/**
 * last update method to be saved on the object and later to b able to distinguish beetween manual changes and nightly batch sync scripts. 
 * @package Core
 * @subpackage model.enum
 */ 
interface UpdateMethodType extends BaseEnum
{
	const MANUAL = 0;
	const AUTOMATIC = 1;
}
