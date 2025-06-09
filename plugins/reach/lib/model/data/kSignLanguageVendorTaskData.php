<?php

/**
 * Define vendor sign language task data object
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kSignLanguageVendorTaskData extends kVendorTaskData
{
    /**
     * @var string
     */
    public $assetId;

    /**
     * @return string
     */
    public function getAssetId()
    {
        return $this->assetId;
    }

    /**
     * @param string $assetId
     */
    public function setAssetId($assetId)
    {
        $this->assetId = $assetId;
    }
}
