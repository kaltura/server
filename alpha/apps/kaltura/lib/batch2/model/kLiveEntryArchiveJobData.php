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
    protected $liveEntryId;

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

}