<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRule instead
 * @abstract
 */
abstract class KalturaBaseRestriction extends KalturaObject
{
	/**
	 * @param KalturaRestrictionArray $restrictions enable one restriction to be affected by other restrictions
	 * @return kAccessControlRestriction
	 * @abstract must be implemented
	 */
	abstract public function toRule(KalturaRestrictionArray $restrictions);
}