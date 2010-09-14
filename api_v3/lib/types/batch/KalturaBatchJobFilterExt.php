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
}

?>