<?php
class Kaltura_BatchJobFilter extends KalturaBatchJobFilterExt
{
	const JOB_TYPE_AND_SUB_TYPE_MAIN_DELIMITER = ';';
	const JOB_TYPE_AND_SUB_TYPE_TYPE_DELIMITER = ':';
	const JOB_TYPE_AND_SUB_TYPE_SUB_DELIMITER = ',';
	
	private $jobTypeAndSubType = array();
	
	public function hasJobTypeFilter()
	{
		return count($this->jobTypeAndSubType);
	}
	
	public function addJobType($jobType, array $jobSubTypes = null)
	{
		$jobSubTypesStr = '';
		if(!is_null($jobSubTypes))
			$jobSubTypesStr = implode(self::JOB_TYPE_AND_SUB_TYPE_SUB_DELIMITER, $jobSubTypes);
			
		$this->jobTypeAndSubType[$jobType] = $jobType . self::JOB_TYPE_AND_SUB_TYPE_TYPE_DELIMITER . $jobSubTypesStr;
		$this->jobTypeAndSubTypeIn = implode(self::JOB_TYPE_AND_SUB_TYPE_MAIN_DELIMITER, $this->jobTypeAndSubType);
	}
}