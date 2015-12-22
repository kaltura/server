<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaBaseEntryCloneOptionItem extends KalturaObject
{
    private static $mapBetweenObjects = array
    (
    );


    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
    }



}