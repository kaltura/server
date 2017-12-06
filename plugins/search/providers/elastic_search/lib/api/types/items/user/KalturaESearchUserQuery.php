<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchUserQuery extends KalturaESearchUserBaseItem
{
	/**
	 * @var string
	 */
	public $eSearchQuery;
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		return KalturaESearchQueryImpl::eSearchItemToObjectImpl($this->eSearchQuery, $object_to_fill, $props_to_skip);
	}
	
}
