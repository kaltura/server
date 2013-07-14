<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class BatchJobLogFilter extends baseObjectFilter
{
	/* (non-PHPdoc)
     * @see baseObjectFilter::getFieldNameFromPeer()
     */
    protected function getFieldNameFromPeer ($field_name)
    {
        $res = BatchJobLogPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
        
    }

	/* (non-PHPdoc)
     * @see baseObjectFilter::getIdFromPeer()
     */
    protected function getIdFromPeer ()
    {
        return BatchJobLogPeer::ID;
        
    }

	/* (non-PHPdoc)
     * @see myBaseObject::init()
     */
    protected function init ()
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
			
		    "_eq_param_1",
		    "_in_param_1",
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

    
}