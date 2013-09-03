<?php
/**
 * @package Core
 * @subpackage model.data
 */
abstract class kRegexCondition extends kMatchCondition
{
	/* (non-PHPdoc)
	 * @see kMatchCondition::matches()
	 */
	protected function matches($field, $value)
	{
		return ($field === $value) || preg_match("/$value/i", $field);
	}
}
