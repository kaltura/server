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
	
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_gte_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_notin_partner_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_gte_processor_expiration",
			"_lte_processor_expiration",
			"_gte_execution_attempts",
			"_lte_execution_attempts",
			"_gte_lock_version",
			"_lte_lock_version",
		
			"_eq_entry_id",
			"_eq_job_type",
			"_in_job_type",
			"_notin_job_type",
			"_eq_job_sub_type",
			"_in_job_sub_type",
			"_notin_job_sub_type",
			"_in_on_stress_divert_to",
			"_eq_status",
			"_in_status",
			"_gte_priority",
			"_lte_priority",
			"_gte_queue_time",
			"_lte_queue_time",
			"_gte_finish_time",
			"_lte_finish_time",
			"_in_err_type",
			"_lt_file_size",
			"_gt_file_size",
			
			"_in_job_type_and_sub_type",
			) , NULL );
			
		$this->allowed_order_fields = array (
			"created_at",
			"updated_at",
			"processor_expiration",
			"execution_attempts",
			"lock_version",
			"status",
			"queue_time",
			"finish_time",
		);
			
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
		$res = BatchJobPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return BatchJobPeer::ID;
	}

	
	public function attachToFinalCriteria ( Criteria $criteria )
	{
		$jobTypeAndSubTypeIn = $this->get("_in_job_type_and_sub_type");
		if ($jobTypeAndSubTypeIn !== null)
		{

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
							
							$jobTypeCriterion = $criteria->getNewCriterion(BatchJobPeer::JOB_TYPE, $finalJobType);
							$jobTypeCriterion->addAnd($criteria->getNewCriterion(BatchJobPeer::JOB_SUB_TYPE, $finalSubTypesArr, Criteria::IN));
							$mainCriterion->addOr($jobTypeCriterion);
						}
						else
						{
							$jobTypeCriterion = $criteria->getNewCriterion(BatchJobPeer::JOB_TYPE, $finalJobType);
							$mainCriterion->addOr($jobTypeCriterion);
						}
					}
					else
					{
						if(strlen(trim($finalSubTypes)))
						{
							$finalSubTypesArr = explode(self::JOB_TYPE_AND_SUB_TYPE_SUB_DELIMITER, $finalSubTypes);
							
							$mainCriterion = $criteria->getNewCriterion(BatchJobPeer::JOB_TYPE, $finalJobType);
							$mainCriterion->addAnd($criteria->getNewCriterion(BatchJobPeer::JOB_SUB_TYPE, $finalSubTypesArr, Criteria::IN));
						}
						else
						{
							$mainCriterion = $criteria->getNewCriterion(BatchJobPeer::JOB_TYPE, $finalJobType);
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

