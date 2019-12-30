<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kLiveEntryArchiveJobData extends kJobData
{
    /**
     * @var string
     */
    private $liveEntryId;

    /**
     * @var string
     */
    private $nonDeletedCuePointsTags;

    /**
     * @return string liveEntryId
     */
    public function getLiveEntryId()
    {
        return $this->liveEntryId;
    }

    /**
     * @param string $liveEntryId
     */
    public function setLiveEntryId($liveEntryId)
    {
        $this->liveEntryId = $liveEntryId;
    }

    /**
     * @return string
     */
    public function getNonDeletedCuePointsTags()
    {
        return $this->nonDeletedCuePointsTags;
    }

    /**
     * @param string $nonDeletedCuePointsTags
     */
    public function setNonDeletedCuePointsTags($nonDeletedCuePointsTags)
    {
        $this->nonDeletedCuePointsTags = $nonDeletedCuePointsTags;
    }

}