<?php
/**
 * 
 * will wrap the service configuration file per partner.
 *  
 */
class myServiceConfig
{
	const DEFAULT_COFIG_TABLE_FILE_NAME = "services.ct";
	
	public static $secondary_config_tables = null;
	
	public static $path = null;
	public static $strict_mode = true;
	public static $default_config_table = null;
	
	public $all_config_tables = null;
//	private $config_table = null;
	private $config_chain = null;
	
	private $service_name = null;
	

	public static function getInstance ( $file_name , $service_name = null )
	{
		// TODO - maybe cache ??
		return new myServiceConfig ( $file_name , $service_name  );
	}
	
	/*
	 * should be used if the setSection is called with a section name that isn't necessarily in  the default config table
	 */
	public static function setStrictMode ( $strict )
	{
		self::$strict_mode = $strict;
	}
		
	// set to null if want to use the default
	public static function setPath ( $path )
	{
		self::$path = $path ; 
	}
	
	
	/*
	 *  will allow setting a list of config_tables  between the explicit file_name (strongest)  and the system defualt (weakest)
	 */
	public static function setSecondaryConfigTables ( $secondary_config_tables )
	{
		if ( is_array ( $secondary_config_tables ))
			self::$secondary_config_tables = $secondary_config_tables;
		else
			self::$secondary_config_tables = array ( $secondary_config_tables );
	}
	
	public static function addSecondaryConfigTables ( $secondary_config_tables )
	{
		// if secondary_config_tables is not array, convert it to array
		if( !is_array(self::$secondary_config_tables) && self::$secondary_config_tables)
		{
			$configTable = self::$secondary_config_tables;
			self::$secondary_config_tables = array();
			self::$secondary_config_tables[] = $configTable;
		}
		
		if(is_array($secondary_config_tables))
		{
			foreach($secondary_config_tables as $table)
				self::$secondary_config_tables[] = $table;
		}
		else
		{
			self::$secondary_config_tables[] = $secondary_config_tables;
		}
	}
	
	
	public function getConfigPath () 
	{
		return $this->getPath();
	}

	protected function getDefaultName ()
	{
		return self::DEFAULT_COFIG_TABLE_FILE_NAME;
	}
		
	protected function getDefaultPath ()
	{
		return SF_ROOT_DIR."/../service_config/";
	}
		
	protected function getPath ()
	{
		if ( self::$path )
			return self::$path;
		return $this->getDefaultPath();
	}
	

	
	public function myServiceConfig ( $file_name , $service_name = null, $useDefualt = true )
	{
		$path = $this->getPath();
		
		$config_table_list = array ( );
		
		if ( $file_name == $this->getDefaultName() )
		{
		}
		else
		{
			if($file_name)
				$config_table_list[] = $path.$file_name ;
		}

		if ( self::$secondary_config_tables )
		{
			// add the secondary before the end
			$config_table_list = array_merge ( $config_table_list , self::$secondary_config_tables ); 
		}
		
		// always append the defualt to the end
		if ($useDefualt) {
			$config_table_list[] = $path.$this->getDefaultName() ;
		}

		 // don't use the common path feature - add it to each config file separatly
		$this->config_chain = new kConfigTableChain( $config_table_list , null );  
		$tables =  $this->config_chain->getTables();
		self::$default_config_table = end ( $tables );

		$this->all_config_tables = $tables;
		
		if ( $service_name )
		{
			$this->setServiceName( $service_name );
		}
	}
	
	public function setServiceName ( $service_name )
	{
		// verify the service exists
		if ( self::$strict_mode && ! self::$default_config_table->isSetPk ( $service_name ) )
		{
			throw new Exception ( "Unknown service [$service_name]" );
		}
		$this->service_name = $service_name;
	}
	
	public function getTicketType() 		{	return $this->get ( "ticket" ); 	}
	public function getRequirePartner()		{	return $this->get ( "rp" );			}	
	public function getNeedKuserFromPuser()	{	return $this->get ( "nkfp" ); 		}
	public function getCreateUserOnDemand()	{	return $this->get ( "cuod" );		}
	public function getAllowEmptyPuser()	{	return $this->get ( "aep" );		}
	//public function getReadWrite()			{	return $this->get ( "wr" );			}
	public function getPartnerGroup()		{	return $this->get ( "pg" );			}
	public function getKalturaNetwork()		{	return $this->get ( "kn" );			}
	public function getMatchIp()			{	return $this->get ( "mip" );		}
	public function getTags()				{	return $this->get ( "tags" );		}

	
	public function getServiceProperties()
	{
		return $this->get ( null );	
	}
	
	public function get ( $property )
	{
		return $this->config_chain->get ( $this->service_name , $property );
	}
	
	public function getServices ( )
	{
		return self::$default_config_table->listPks();	
	}
	
	public function getAllServices ( )
	{
		$services = array();	
		foreach ($this->all_config_tables as $cur)
		{
			$services = array_merge($services, $cur->listPks());
		}
		return $services;
	}
	
	public function isSetService ( $service_name )
	{
		// TODO - implement
	}
}
?>