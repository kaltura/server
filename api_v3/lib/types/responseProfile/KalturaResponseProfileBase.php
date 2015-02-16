<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaResponseProfileBase extends KalturaObject
{
	/**
	 * @return array<KalturaResponseProfileBase>
	 */
	abstract public function getRelatedProfiles();
}