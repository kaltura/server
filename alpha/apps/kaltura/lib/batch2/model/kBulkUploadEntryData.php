<?php
/**
 * This class represents entry-specific data passed to the 
 * bulk upload job.
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadEntryData extends kBulkUploadObjectData
{
    
    /**
     * Selected profile id for all bulk entries
     * @var int
     */
    protected $conversionProfileId;
    
	/**
     * @return the $conversionProfileId
     */
    public function getConversionProfileId ()
    {
        return $this->conversionProfileId;
    }

	/**
     * @param int $conversionProfileId
     */
    public function setConversionProfileId ($conversionProfileId)
    {
        $this->conversionProfileId = $conversionProfileId;
    }

}