<?php
/**
 * @package api
 * @subpackage ps2
 */
class updatebatchjobAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateBatchJob",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"batchjob_id" => array ("type" => "string", "desc" => ""),
						"batchjob" => array ("type" => "BatchJob", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"batchjob" => array ("type" => "BatchJob", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_BATCHJOB_ID ,
				)
			); 
	}
	
	// ask to fetch the kuser from puser_kuser 
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
	protected function getObjectPrefix () { return "batchjob"; }

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$prefix = $this->getObjectPrefix();
		
		$batchjob_id = $this->getPM ( "{$prefix}_id" );
		$batchjob = BatchJobPeer::retrieveByPK( $batchjob_id );

		if ( ! $batchjob )  
		{
			$this->addError( APIErrors::INVALID_BATCHJOB_ID, $batchjob_id );
			return;
		}			
		
		// get the new properties for the batchjob from the request
		$batchjob_update_data = new BatchJob();
		
		$obj_wrapper = objectWrapperBase::getWrapperClass( $batchjob_update_data , 0 );
		
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $batchjob_update_data , "{$prefix}_" , $obj_wrapper->getUpdateableFields() );
		if ( count ( $fields_modified ) > 0 )
		{
			if ( $batchjob_update_data )
				baseObjectUtils::fillObjectFromObject( $obj_wrapper->getUpdateableFields() , $batchjob_update_data , $batchjob , baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME );

			$batchjob->save();
		}
		
		$wrapper = objectWrapperBase::getWrapperClass( $batchjob , objectWrapperBase::DETAIL_LEVEL_REGULAR );
		$wrapper->removeFromCache( "batchjob" , $batchjob->getId() );			
		
		$this->addMsg ( "{$prefix}" , $wrapper );
		$this->addDebug ( "modified_fields" , $fields_modified );
	}
}
?>