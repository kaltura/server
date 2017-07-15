<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 7/24/2017
 * Time: 7:57 AM
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