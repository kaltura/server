<?php

/**
 * Define vendor intelligent tagging task data object
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kIntelligentTaggingVendorTaskData extends kVendorTaskData
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
     * @param string $captionAssetId
     */
    public function setAssetId($assetId)
    {
        $this->assetId = $assetId;
    }
}