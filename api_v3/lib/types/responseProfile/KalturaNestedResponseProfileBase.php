<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaNestedResponseProfileBase extends KalturaResponseProfileBase
{
	/**
	 * @param KalturaObject $this
	 * @return KalturaFilter
	 */
	abstract public function getFilter($this, $srcObj);
}