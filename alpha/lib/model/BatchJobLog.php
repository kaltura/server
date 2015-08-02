<?php


/**
 * Skeleton subclass for representing a row from the 'batch_job_log' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class BatchJobLog extends BaseBatchJobLog implements IBaseObject {

	/**
	 * Initializes internal state of BatchJobLog object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}
	
		/**
	 * @param boolean  $bypassSerialization enables PS2 support
	 */
	public function getData($bypassSerialization = false)
	{
		if($bypassSerialization)
			return parent::getData();
		$data = parent::getData();
		if(!is_null($data))
				return unserialize ( $data );
		
		return null;
	}
	
	/**
	 * @param boolean  $bypassSerialization enables PS2 support
	 */
	public function setData($v, $bypassSerialization = false) {
		if ($bypassSerialization)
			return parent::setData ( $v );
		if (! is_null ( $v )) {
			$sereializedValue = serialize ( $v );
			parent::setData ( $sereializedValue );	
		} else
			parent::setData ( null );
	} 
	
    /* (non-PHPdoc)
     * @see BaseBatchJobLog::preUpdate()
     * The implementation is unusual because the created_at and updated_at dates do not belong to this object but to the BatchJob object it represents.
     */
    public function preUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
		{
			return true;
		}	
		$this->tempModifiedColumns = $this->modifiedColumns;
		return true;
  	}
  	
    /* (non-PHPdoc)
     * @see BaseBatchJobLog::preInsert()
     * The implementation is unusual because the created_at and updated_at dates do not belong to this object but to the BatchJob object it represents.
     */
    public function preInsert(PropelPDO $con = null)
	{
		return true;
	}

} // BatchJobLog
