<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface SearchIndexFieldEscapeType extends BaseEnum 
{
	const DEFAULT_ESCAPE  = '1';
	const NO_ESCAPE = '2';
	const MD5_LOWER_CASE  = '3';
	const FULL_ESCAPE  = '4';
	const PREFIXED_MD5_LOWER_CASE  = '5';
	
}