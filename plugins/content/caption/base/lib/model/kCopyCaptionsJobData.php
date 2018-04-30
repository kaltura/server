<?php
/**
 * @package plugins.caption
 * @subpackage model.data
 */
class kCopyCaptionsJobData extends kJobData
{

	/** entry Id
	* @var string
	*/
	private $entryId;

	/**
	 * the sources start time and duration
	 * @var array
	 */
	private $clipsDescriptionArray;


	/**
	 * @var bool
	 */
	private $fullCopy;


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

    /**
     * @return bool
     */
    public function getFullCopy()
    {
        return $this->fullCopy;
    }

    /**
     * @param bool $fullCopy
     */
    public function setFullCopy($fullCopy)
    {
        $this->fullCopy = $fullCopy;
    }

}
