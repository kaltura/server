<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class BatchJobFilter extends baseObjectFilter
{
	const JOB_TYPE_AND_SUB_TYPE_MAIN_DELIMITER = ';';
	const JOB_TYPE_AND_SUB_TYPE_TYPE_DELIMITER = ':';
	const JOB_TYPE_AND_SUB_TYPE_SUB_DELIMITER = ',';
	
	protected $BATCH_JOB_FIELDS =	array(
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_gte_queue_time",
			"_lte_queue_time",
			"_gte_finish_time",
			"_lte_finish_time",
			"_in_err_type");
	
	 protected $BATCH_JOB_LOCK_FIELDS = array(
			"_gte_expiration",
			"_lte_expiration",
			"_gte_execution_attempts",
			"_lte_execution_attempts",
			"_gte_lock_version",
			"_lte_lock_version",
			"_lt_estimated_effort",
			"_gt_estimated_effort",
	 		"_gte_urgency",
	 		"_lte_urgency",
	 		"_gte_batch_version",
	 		"_lte_batch_version",
	 		"_eq_batch_version");
	
	protected $BATCH_JOB_COMMON_FIELDS = array(
			"_eq_id",
			"_gte_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_notin_partner_id",
			"_eq_entry_id",
			"_eq_job_type",
			"_in_job_type",
			"_notin_job_type",
			"_eq_job_sub_type",
			"_in_job_sub_type",
			"_notin_job_sub_type",
			"_eq_status",
			"_in_status",
			"_gte_priority",
			"_lte_priority",
			"_eq_priority",
			"_in_priority",
			"_notin_priority",
			"_in_job_type_and_sub_type",);
	
	protected $queryFromBatchJob;
	
	public function BatchJobFilter($queryFromBatchJob) {
		$this->queryFromBatchJob = $queryFromBatchJob; 
		parent::__construct();
	}
	
	public function init ()
	{
		
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		
		if($this->queryFromBatchJob) {
			$fieldsArray = array_merge($this->BATCH_JOB_FIELDS, $this->BATCH_JOB_COMMON_FIELDS);
			$this->fields = kArray::makeAssociativeDefaultValue ($fieldsArray , NULL );
			$this->allowed_order_fields = array (
					"created_at",
					"updated_at",
					"status",
					"queue_time",
					"finish_time");
		} else {
			$fieldsArray = array_merge($this->BATCH_JOB_LOCK_FIELDS, $this->BATCH_JOB_COMMON_FIELDS);
			$this->fields = kArray::makeAssociativeDefaultValue ( $fieldsArray , NULL );
			$this->allowed_order_fields = array (
					"lock_expiration",
					"execution_attempts",
					"lock_version",
					"status");
		}
		
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "BatchJobFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		if($this->queryFromBatchJob) 
			return BatchJobPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		else
			return BatchJobLockPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
	}

	public function getIdFromPeer (  )
	{
		if($this->queryFromBatchJob)
			return BatchJobPeer::ID;
		else 
			return BatchJobLockPeer::ID;
	}

	
	public function attachToFinalCriteria ( Criteria $criteria )
	{
		$jobTypeAndSubTypeIn = $this->get("_in_job_type_and_sub_type");
		if ($jobTypeAndSubTypeIn !== null)
		{
			$type = $this->queryFromBatchJob ? BatchJobPeer::JOB_TYPE: BatchJobLockPeer::JOB_TYPE;
			$subType =  $this->queryFromBatchJob ? BatchJobPeer::JOB_SUB_TYPE : BatchJobLockPeer::JOB_SUB_TYPE;
			
			$finalTypesAndSubTypes = array();
			$arr = explode(self::JOB_TYPE_AND_SUB_TYPE_MAIN_DELIMITER, $jobTypeAndSubTypeIn);
			foreach($arr as $jobTypeIn)
			{
				list($jobType, $jobSubTypes) = explode(self::JOB_TYPE_AND_SUB_TYPE_TYPE_DELIMITER, $jobTypeIn);
				$finalTypesAndSubTypes[$jobType] = $jobSubTypes;
			}
			
			if(count($finalTypesAndSubTypes))
			{
				$mainCriterion = null;

				foreach($finalTypesAndSubTypes as $finalJobType => $finalSubTypes)
				{
					if($mainCriterion)
					{
						if(strlen(trim($finalSubTypes)))
						{
							$finalSubTypesArr = explode(self::JOB_TYPE_AND_SUB_TYPE_SUB_DELIMITER, $finalSubTypes);
							
							$jobTypeCriterion = $criteria->getNewCriterion($type, $finalJobType);
							$jobTypeCriterion->addAnd($criteria->getNewCriterion($subType, $finalSubTypesArr, Criteria::IN));
							$mainCriterion->addOr($jobTypeCriterion);
						}
						else
						{
							$jobTypeCriterion = $criteria->getNewCriterion($type, $finalJobType);
							$mainCriterion->addOr($jobTypeCriterion);
						}
					}
					else
					{
						if(strlen(trim($finalSubTypes)))
						{
							$finalSubTypesArr = explode(self::JOB_TYPE_AND_SUB_TYPE_SUB_DELIMITER, $finalSubTypes);
							
							$mainCriterion = $criteria->getNewCriterion($type, $finalJobType);
							$mainCriterion->addAnd($criteria->getNewCriterion($subType, $finalSubTypesArr, Criteria::IN));
						}
						else
						{
							$mainCriterion = $criteria->getNewCriterion($type, $finalJobType);
						}
					}
				}
				
				$criteria->addAnd($mainCriterion);
			}
		}
		$this->set("_in_job_type_and_sub_type", null);
		
		parent::attachToFinalCriteria($criteria);
	}
}

