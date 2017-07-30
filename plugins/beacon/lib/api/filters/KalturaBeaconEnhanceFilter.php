<?php
/**
 * @package plugins.beacon
 * @subpackage api.filters
 */

class KalturaBeaconEnhanceFilter extends KalturaBeaconFilter
{
    /**
     * @var string
     */
    public $externalElasticQueryObject;

    protected function createSearchObject()
    {
        $arr = parent::createSearchObject();
        $utf8Query = utf8_encode($this->externalElasticQueryObject);
        $extraElasticQuery = json_decode($utf8Query,true);
        foreach($extraElasticQuery as $key => $value)
        {
            $arr[$key] = $value;
        }
        return $arr;
    }
}