<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCopyCaptionsJobData extends KalturaJobData
{

    /** source entry Id
     * @var string
     */
    public $sourceEntryId = null;

    /** entry Id
     * @var string
     */
    public $entryId = null;

    /** clip offset
     * @var int
     */
    public $offset;

    /** clip duration
     * @var int
     */
    public $duration;

    private static $map_between_objects = array
    (
        'entryId',
        'sourceEntryId',
        'offset',
        'duration',
    );

    /* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
    public function getMapBetweenObjects ( )
    {
        return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }

    /* (non-PHPdoc)
     * @see KalturaObject::toObject()
     */
    public function toObject($dbData = null, $props_to_skip = array())
    {
        if(is_null($dbData))
            $dbData = new kCopyCaptionsJobData();

        return parent::toObject($dbData, $props_to_skip);
    }
}