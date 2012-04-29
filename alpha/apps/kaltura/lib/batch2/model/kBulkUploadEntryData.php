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
}