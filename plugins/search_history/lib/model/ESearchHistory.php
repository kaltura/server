<?php
/**
 * @package plugins.searchHistory
 * @subpackage model
 */
class ESearchHistory extends BaseObject
{

    /**
     * @var string
     */
    protected $searchTerm;

    /**
     * @var string
     */
    protected $searchedObject;

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param string $searchTerm
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return string
     */
    public function getSearchedObject()
    {
        return $this->searchedObject;
    }

    /**
     * @param string $searchedObject
     */
    public function setSearchedObject($searchedObject)
    {
        $this->searchedObject = $searchedObject;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

}
