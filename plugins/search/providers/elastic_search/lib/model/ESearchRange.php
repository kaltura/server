<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchRange extends BaseObject
{
    /**
     * @var int
     */
    protected $greaterThanOrEqual;

    /**
     * @var int
     */
    protected $lessThanOrEqual;

    /**
     * @var int
     */
    protected $greaterThan;

    /**
     * @var int
     */
    protected $lessThan;

    /**
     * @return int
     */
    public function getGreaterThanOrEqual()
    {
        return $this->greaterThanOrEqual;
    }

    /**
     * @param int $greaterThanOrEqual
     */
    public function setGreaterThanOrEqual($greaterThanOrEqual)
    {
        $this->greaterThanOrEqual = $greaterThanOrEqual;
    }

    /**
     * @return int
     */
    public function getLessThanOrEqual()
    {
        return $this->lessThanOrEqual;
    }

    /**
     * @param int $lessThanOrEqual
     */
    public function setLessThanOrEqual($lessThanOrEqual)
    {
        $this->lessThanOrEqual = $lessThanOrEqual;
    }

    /**
     * @return int
     */
    public function getGreaterThan()
    {
        return $this->greaterThan;
    }

    /**
     * @param int $greaterThan
     */
    public function setGreaterThan($greaterThan)
    {
        $this->greaterThan = $greaterThan;
    }

    /**
     * @return int
     */
    public function getLessThan()
    {
        return $this->lessThan;
    }

    /**
     * @param int $lessThan
     */
    public function setLessThan($lessThan)
    {
        $this->lessThan = $lessThan;
    }

}
