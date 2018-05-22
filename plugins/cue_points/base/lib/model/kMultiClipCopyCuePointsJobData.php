<?php
/**
 * @package plugins.cue_points
 * @subpackage model.data
 */
class kMultiClipCopyCuePointsJobData extends kCopyCuePointsJobData
{
    /**
     * the sources start time and duration
     * @var array
     */
    private $clipsDescriptionArray;
    
    /**
     * @return array
     */
    public function getClipsDescriptionArray()
    {
        return $this->clipsDescriptionArray;
    }

    /**
     * @param array $clipsDescriptionArray
     */
    public function setClipsDescriptionArray($clipsDescriptionArray)
    {
        $this->clipsDescriptionArray = $clipsDescriptionArray;
    }


}