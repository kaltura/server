<?php
class myFileIndicator
{
	private $file_name;
	private $pattern;
	private static $s_indicator_path = null;// myContentStorage::getFSContentRootPath() . "../indicators/";
	
	public function myFileIndicator ( $file_name )
	{
		if ( ! self::$s_indicator_path  )
		{
			self::$s_indicator_path =  myContentStorage::getFSContentRootPath() . "/indicators/";
			kFile::fullMkdir( self::$s_indicator_path . "dummy.txt" );			
		}
		
		$this->file_name = $file_name;
		$this->pattern = "/". $this->file_name . "\..*/";
	}
	
	public function isIndicatorSet ()
	{
		$indicator_list = kFile::recursiveDirList( self::$s_indicator_path ,true , false , $this->pattern );
		return ( count ( $indicator_list ) > 0 );
	}
	
	public function addIndicator ( $suffix = null )
	{
		$indicator_list = kFile::recursiveDirList( self::$s_indicator_path ,true , false , $this->pattern , 0 , 5 );
		if ( count ( $indicator_list ) >= 5 ) return; // dn't add the indicators if there are already more than 5
		if ( ! $suffix )
		{
			$suffix = rand (0 , 1000 );
		}
		touch ( self::$s_indicator_path . $this->file_name  . "." .$suffix);
		
	}
	
	public function removeIndicator ( )
	{
		// delete any of the indicators available
		$indicator_list = kFile::recursiveDirList( self::$s_indicator_path ,true , false , $this->pattern );
		if ( count ( $indicator_list ) > 0 )
		{
			kFile::deleteFile( $indicator_list[0] );
		}
		if ( count ( $indicator_list ) > 100 )
		{
			// many file indicator - no real reason to leave them all
			for ( $i = 1 ; $i < 50 ; $i++)
			{
				kFile::deleteFile( $indicator_list[$i] );
			}
		}
		
	}	
	
}
?>