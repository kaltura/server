<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.objects
 */
class KalturaESearchHistoryArray extends KalturaTypedArray
{

    public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
    {
        $newArr = new KalturaESearchHistoryArray();
        if ($arr == null)
            return $newArr;

        foreach ($arr as $obj)
        {
            $nObj = new KalturaESearchHistory();
            $nObj->fromObject($obj);
            $newArr[] = $nObj;
        }

        return $newArr;
    }

    public function __construct()
    {
        parent::__construct("KalturaESearchHistory");
    }

}
