<?php
/**
 * @package plugins.enhancedSearch
 * @subpackage api.objects
 */
class KalturaEnhancedSearchBaseItemArray extends KalturaTypedArray {



    public function __construct()
    {
        return parent::__construct("KalturaEnhancedSearchBaseItem");
    }

	public static function fromDbArray(array $objects = null)
	{
		return array(new KalturaEnhancedSearchEntry(), new KalturaEnhancedSearchEntry());
	}



}
