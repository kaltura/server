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
	 * @var string
	 */
	protected $vodEntryId;

    /**
     * @return string liveEntryId
     */
    public function getLiveEntryId()
    {
        return $this->liveEntryId;
    }

	/**
	 * @return string vodEntryId
	 */
	public function getVodEntryId()
	{
		return $this->vodEntryId;
	}

    /**
     * @param string $liveEntryId
     */
    public function setLiveEntryId($liveEntryId)
    {
        $this->liveEntryId = $liveEntryId;
    }

	/**
	 * @param string $vodEntryId
	 */
	public function setVodEntryId($vodEntryId)
	{
		$this->vodEntryId = $vodEntryId;
	}

}