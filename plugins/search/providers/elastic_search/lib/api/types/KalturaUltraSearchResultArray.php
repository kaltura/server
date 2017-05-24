<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaUltraSearchResultArray extends KalturaTypedArray {



    public function __construct()
    {
        return parent::__construct("KalturaUltraSearchResult");
    }

	public static function fromDbArray(array $strings = null)
	{
		return array(new KalturaUltraSearchResult(), new KalturaUltraSearchResult());
	}

}
