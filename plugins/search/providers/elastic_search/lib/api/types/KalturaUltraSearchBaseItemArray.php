<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaUltraSearchBaseItemArray extends KalturaTypedArray {



    public function __construct()
    {
        return parent::__construct("KalturaUltraSearchBaseItem");
    }

	public static function fromDbArray(array $objects = null)
	{
		return array(new KalturaUltraSearchEntry(), new KalturaUltraSearchEntry());
	}



}
