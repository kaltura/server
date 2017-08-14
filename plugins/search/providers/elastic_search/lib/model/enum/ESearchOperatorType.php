<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchOperatorType extends BaseEnum
{
	const AND_OP = 1;
	const OR_OP = 2;
}