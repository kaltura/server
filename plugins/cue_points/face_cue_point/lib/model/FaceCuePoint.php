<?php

/**
 * @package plugins.faceCuePoint
 * @subpackage model
 */
class FaceCuePoint extends CuePoint
{
    const CUSTOM_DATA_FIELD_FACE_THUMB_URL = 'faceThumbUrl';
    const CUSTOM_DATA_FIELD_FACE_KUSER_ID= 'facekuserId';
    const CUSTOM_DATA_FIELD_FACE_PUSER_ID= 'facePuserId';
    const CUSTOM_DATA_FIELD_FACE_END_TIME= 'faceEndTime';
    const CUSTOM_DATA_FIELD_FACE_ASSET_ID = 'faceAssetId';


    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    public function setThumbUrl($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_FACE_THUMB_URL, (string)$v);}
    public function getThumbUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FACE_THUMB_URL);}

    public function setKuserId($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_FACE_KUSER_ID, (string)$v);}
    public function getKuserId()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FACE_KUSER_ID);}

    public function setPuserId($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_FACE_PUSER_ID, (string)$v);}
    public function getPuserId()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FACE_PUSER_ID);}

    public function setEndTime($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_FACE_END_TIME, (string)$v);}
    public function getEndTime()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FACE_END_TIME);}

    public function setAssetId($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_FACE_ASSET_ID, (string)$v);}
    public function getAssetId()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FACE_ASSET_ID);}

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->setType(FaceCuePointPlugin::getCuePointTypeCoreValue(FaceCuePointType::FACE));
    }

    public function copyToClipEntry( entry $clipEntry, $clipStartTime, $clipDuration )
    {
        return false;
    }

    public function contributeElasticData()
    {
        $data = null;
        if($this->getPuserId())
            $data['cue_point_puser_id'] = $this->getPuserId();

        return $data;
    }

}
