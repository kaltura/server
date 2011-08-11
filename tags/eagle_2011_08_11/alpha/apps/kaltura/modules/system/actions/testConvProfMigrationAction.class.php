<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class testConvProfMigrationAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();

		myDbHelper::$use_alternative_con = null;//myDbHelper::DB_HELPER_CONN_PROPEL3;
		
		$entry_id = $this->getP ( "entry_id" );

		echo ( "Creating new conversion profile for entry [$entry_id]<br>" );
		$new_conversion_profile = myPartnerUtils::getConversionProfile2ForEntry ( $entry_id );

		echo ( "result:\n" . print_r ( $new_conversion_profile ,true . "<br>" ));
		
		die();
	}
}