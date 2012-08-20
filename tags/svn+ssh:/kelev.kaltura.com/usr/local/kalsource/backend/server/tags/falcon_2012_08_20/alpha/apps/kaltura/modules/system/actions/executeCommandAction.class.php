<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class executeCommandAction extends kalturaSystemAction
{
	// TODO - read from the entryWrapper 
	private static $allowed_names = array ( "conversionQuality" );
	
	/**
	 * Will anipulate a single entry
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		myDbHelper::$use_alternative_con = null;
		

		$command = $this->getP ( "command" );

		if ( $command == "updateEntry" )
		{
			$id = $this->getP ( "id" );
			$entry = entryPeer::retrieveByPK( $id );
			
			if ( $entry )
			{
				$name = $this->getP ( "name" );
				$value = $this->getP ( "value" );
								
				$obj_wrapper = objectWrapperBase::getWrapperClass( $entry , 0 );
				$updateable_fields = $obj_wrapper->getUpdateableFields( "2" );
				if ( ! in_array ( $name ,  $updateable_fields ) ) die();

				if ( $name )
				{
					$setter = "set" . $name;
					call_user_func( array ( $entry , $setter ) , $value );	
					$entry->save();		
				}
			}
		}
		die();
	}
}
?>