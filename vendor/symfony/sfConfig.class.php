<?php
/**
 * This is used in the Creole package - a very simple hard-coded configuration
 *
 */
class sfConfig
{
	private static $map;
	
	private static function init()
	{
		if ( ! self::$map )
		{
			self::$map = array (
				'sf_debug' => false,
				'sf_logging_enabled' => true, 
				'sf_root_dir' => SF_ROOT_DIR,
			);
		}
	}
	
	
	public static function get ( $param )
	{
//		return kConf::get( $param );
		
		self::init();
		$res = @self::$map[$param];
		return $res;
	
	}
	
	
}
?>