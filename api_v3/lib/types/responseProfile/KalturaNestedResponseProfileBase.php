<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaNestedResponseProfileBase extends KalturaResponseProfileBase
{
	/**
	 * @return KalturaResponseProfileBase
	 */
	abstract public function get();
}