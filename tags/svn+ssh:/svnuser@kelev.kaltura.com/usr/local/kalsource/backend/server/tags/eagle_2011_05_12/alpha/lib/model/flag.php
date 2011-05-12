<?php

/**
 * Subclass for representing a row from the 'flag' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class flag extends Baseflag
{
	// We're using the same table to store favorites of different types, use this integer constant to differentiate
	const SUBJECT_TYPE_ENTRY = '1';
	const SUBJECT_TYPE_USER = '2';
	const SUBJECT_TYPE_COMMENT = '3';
	
	const FLAG_TYPE_UNKNOWN = '0';
	const FLAG_TYPE_OFFENSIVE = '1';
	const FLAG_TYPE_SPAM = '2';
	const FLAG_TYPE_COPYRIGHT = '3';
	const FLAG_TYPE_OTHER = '4';
	
	
}
