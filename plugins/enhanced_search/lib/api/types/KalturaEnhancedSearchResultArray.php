<?php
/**
 * @package plugins.enhancedSearch
 * @subpackage api.objects
 */
class KalturaEnhancedSearchResultArray extends KalturaTypedArray {



    public function __construct()
    {
        return parent::__construct("KalturaEnhancedSearchResult");
    }

	public static function fromDbArray(array $strings = null)
	{
		return array(new KalturaEnhancedSearchResult(), new KalturaEnhancedSearchResult());
	}

}
