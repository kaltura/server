<?php
class msnContentDistributionConf
{
	protected static $map = array(
//		'provider_sub_types' => array(
//			'fox_sports' => array(
//				'name' => 'Fox Sports',
//				'xsl' => 'fox-sports.xsl',
//				'validator' => 'FoxSportsMsnDistributionProfileValidator',
//				'update_required_metadata_xpaths' => array(
//					"/*[local-name()='metadata']/*[local-name()='LongTitle']",
//					"/*[local-name()='metadata']/*[local-name()='LongDescription']",
//				),
//				'update_required_entry_fields' => array(
//					entryPeer::TAGS, 
//				),
//			),
//			'fox_soccer' => array(
//				'name' => 'Fox Soccer',
//				'xsl' => 'fox-soccer.xsl',
//			),
//		),
		
		'default_update_required_metadata_xpaths' => array(
			"/*[local-name()='metadata']/*[local-name()='MSNPublic']",
			"/*[local-name()='metadata']/*[local-name()='MSNVideoCat']",
			"/*[local-name()='metadata']/*[local-name()='MSNVideoTop']",
			"/*[local-name()='metadata']/*[local-name()='MSNVideoTopCat']",
		),
		
		'default_update_required_entry_fields' => array(
			entryPeer::NAME, 
			entryPeer::DESCRIPTION, 
			entryPeer::TAGS, 
		),
	);
	
	public static function get($param_name)
	{
		if(!self::hasParam($param_name))
			throw new Exception( "Cannot find [$param_name] in config" ) ;
			
		return self::$map[$param_name]; 
	}
	
	public static function hasParam($param_name)
	{
		return isset(self::$map[$param_name]);
	}
}