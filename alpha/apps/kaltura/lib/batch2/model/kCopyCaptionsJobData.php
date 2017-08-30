<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kCopyCaptionsJobData extends kJobData
{
    /** source entry Id
     * @var string
     */
    private $sourceEntryId;

    /** entry Id
     * @var string
     */
    private $entryId;

    /** clip offset
     * @var int
     */
    private $offset;

    /** clip duration
     * @var int
     */
    private $duration;


    /**
     * @return string
     */
    public function getSourceEntryId()
    {
        return $this->sourceEntryId;
    }

    /**
     * @param string $sourceEntryId
     */
    public function setSourceEntryId($sourceEntryId)
    {
        $this->sourceEntryId = $sourceEntryId;
    }

    /**
     * @return string
     */
    public function getEntryId()
    {
        return $this->entryId;
    }

    /**
     * @param string $entryId
     */
    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }


}