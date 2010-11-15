<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

class testExclusiveAction extends kalturaSystemAction
{
	/**
	 * Will investigate a single entry
	 */
	public function execute()
	{
		$mode = $this->getP ( "mode" , "get" );
		$c = new Criteria();
//		$c->add ( BatchJobPeer::JOB_TYPE , BatchJobType::DELETE );
		$peer = new BatchJobPeer();
		$location_id = "loc1";
		$server_id = "ser1";
		$execution_time = 400;
		$number_of_objects = 1; 
		
		if ( $mode == "free")
		{
			$id = $this->getP ( "id");
			$this->res = kBatchExclusiveLock::freeExclusive( $id, $peer , $location_id , $server_id );
		}
		elseif ( $mode == "update")
		{
			$id = $this->getP ( "id");
			$obj = new BatchJob();
			$obj->setProgress ( 77 );
			$this->res = kBatchExclusiveLock::updateExclusive( $id, $peer , $location_id , $server_id , $obj );
		}
		else
		{
			$partner_group = new myPartnerGroups( "+1;0;-3,4");
			
			$this->res = null;
			$cloned_c = clone $c;
			while ( $partner_group->applyPartnerGroupToCriteria ( $cloned_c , $peer ) )
			{
				$this->res = kBatchExclusiveLock::getExclusive( $cloned_c, $peer , $location_id , $server_id , $execution_time , $number_of_objects );
				if ( $this->res ) break;
				$cloned_c = clone $c;
			}
		}

	}
}
?>