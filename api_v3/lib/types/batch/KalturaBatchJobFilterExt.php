<?php
/**
 * @package api
 * @subpackage filters
 */

/**
 */
class KalturaBatchJobFilterExt extends KalturaBatchJobFilter
{
	private $map_between_objects = array
	(
		"jobTypeAndSubTypeIn" => "_in_job_type_and_sub_type",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	/**
	 * 
	 * 
	 * @var string
	 */
	public $jobTypeAndSubTypeIn; 
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$dbFilter = parent::toObject($object_to_fill, $props_to_skip);
		
		$jobTypeAndSubTypeIn = $this->jobTypeAndSubTypeIn;
		if (is_null($this->jobTypeAndSubTypeIn))
			return $dbFilter;
			
		$finalTypesAndSubTypes = array();
		$arr = explode(BatchJobFilter::JOB_TYPE_AND_SUB_TYPE_MAIN_DELIMITER, $this->jobTypeAndSubTypeIn);
		foreach($arr as $jobTypeIn)
		{
			list($jobType, $jobSubTypes) = explode(BatchJobFilter::JOB_TYPE_AND_SUB_TYPE_TYPE_DELIMITER, $jobTypeIn);
			$jobType = KalturaBatchJobType::getCoreValue($jobType);
			
			$finalTypesAndSubTypes[] = $jobType . BatchJobFilter::JOB_TYPE_AND_SUB_TYPE_TYPE_DELIMITER . $jobSubTypes;
		}
		$jobTypeAndSubTypeIn = implode(BatchJobFilter::JOB_TYPE_AND_SUB_TYPE_MAIN_DELIMITER, $finalTypesAndSubTypes);
		$dbFilter->set('_in_job_type_and_sub_type', $jobTypeAndSubTypeIn);
			
		return $dbFilter;
	}
}

