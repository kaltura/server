<?php
class myTemplateUtils
{
	const DEFAUTL_SEPARATOR_PATTERN =  '/---------.*/';
	
	private static $generic_markup = null; 
	 
	public  static function getGenericMarkup ()
	{ 
		$host = @requestUtils::getHost();
		
		if ( empty ( $host ) || strlen( $host ) < 8 )
		{
			$host = "http://www.kaltura.com";	
		}
		
		$base = $host. "/index.php";
		
		$a_start = "<a href='" ;
		$a_end = "'>" ;
		$a_close = "</a>" ;
		
		if ( self::$generic_markup == null )
		{
			self::$generic_markup = array (
				"link-home" =>  $a_start  . $base . $a_end . "Kaltura" . $a_close,
				"link-browse-kshow-start" => $a_start  .$base . "/browse?kshow_id='" ,
				"link-browse-kshow-middle" => "'>" ,
				"link-browse-kshow-end" => "</a>" ,
				"link-forum" => $a_start  .$base . "/forum" . $a_end . "Forum" . $a_close ,
				"link-mykaltura" => $a_start  .$base . "/mykaltura/viewprofile?screenname=<kl:screenName>" . $a_end . "<kl:screenName>" . $a_close ,
				"link-contact" => $a_start  .$base . "/static/contactus" . $a_end . "Contact Us" . $a_close ,
				"link-block-email-url" => $base . "/mail/blockMail?e=" ,
			);
		}
		
		return self::$generic_markup;
	}
		 
	
	public static function getTemplateDir()
	{
		$dir =  myContentStorage::getFSContentRootPath() . "/email_templates/";
		kFile::fullMkdir( $dir );;
		return $dir;
	}
	
	public static function getAllTemplateNames ( )
	{
		return kFile::dirList( self::getTemplateDir() ,false );
	}
	
	public static function getTemplateContent ( $template_name )
	{
		$file_name = self::getTemplateDir () . "$template_name";
		if ( !file_exists( $file_name ))
		{
			throw new Exception ( "Cannot find template $file_name" );
		}
		return file_get_contents( $file_name );
	}
		
	public static function getTemplateContentByParts ( $template_name , $separator = self::DEFAUTL_SEPARATOR_PATTERN )
	{
		$content =  file_get_contents( self::getTemplateDir () . "$template_name" );
		
		// replace the derective {template-name} with the template
		$template_replace_derective_pat = "/\{([a-zA-Z0-9\-_]+?)\}/";

		// replace recirsively
		$replace_count = 0;
		while ( preg_match_all ( $template_replace_derective_pat , $content , $match ) > 0 )
		{
			foreach ( $match[0] as $id => $template_match  )
			{
				// 0 is the full pattern including the {} bracket
				// 1 is the template name
				$inner_tamplate_content = self::getTemplateContent ( $match[1][$id] );
				
				$content = str_replace ( $template_match  , $inner_tamplate_content , $content  )	;
			}
			
			$replace_count++;
			if ( $replace_count > 5 )
			{
				throw new Exception ( "Found what is probably an endless loop while parcing template directive in $template_name" );
			}
		}
		return self::getParts ( $content , $separator);
	}
	
	public static function renameTemplate ( $tamplate_name , $new_name )
	{
		 
	}
	
	public static function saveTemplate ( $tamplate_name , $content )
	{
		$file_name = preg_replace ( "/[^a-zA-Z0-9_\-]/" , "_" , $tamplate_name );
		$file_path = self::getTemplateDir() . $file_name;
		file_put_contents( $file_path , $content );		
	}
	
	public static function replaceMarkupImpl ( $template_content , array $name_list , array $value_list )
	{
		return str_replace( $name_list , $value_list , $template_content  );
	}
	
	public static function replaceMarkup ( $template_name , array $name_value_list , $prefix = "kl:" )
	{
		$nams = array();
		$values = array();
		
		foreach ( $name_value_list as $name_value => $value )
		{
			$names[] = "<$prefix" .  $name_value . ">" ;
			$values[] = $value;
		}

		$template_content = self::getTemplateContent( $template_name ) ;
		return self::replaceMarkupImpl ( $template_content , $names , $values );
	}
	
	
	public static function replaceMarkupByParts ( $template_name , array $name_value_list , $prefix = "kl:" , $sepaerator = self::DEFAUTL_SEPARATOR_PATTERN )
	{
		$nams = array();
		$values = array();

		$parts = self::getTemplateContentByParts ( $template_name ,  $sepaerator ) ;
		
		$results = array();
		
		list ( $names , $values ) = self::getNameValueArrays ( $prefix , $name_value_list );
		list ( $global_names , $global_values ) = self::getNameValueArrays ( $prefix ,  self::getGenericMarkup() );
		
/*		
		foreach ( $name_value_list as $name_value => $value )
		{
			$names[] = "<$prefix" .  $name_value . ">" ;
			$values[] = $value;
		}
	*/	
		foreach ( $parts as $part )
		{
			$after_generic_replace = self::replaceMarkupImpl ( $part , $global_names , $global_values );
			$result [] = self::replaceMarkupImpl ( $after_generic_replace , $names , $values );
		}
		return $result; 
	}
	
	public static function getParts ( $content , $separator = self::DEFAUTL_SEPARATOR_PATTERN ) 
	{
		return preg_split( $separator , $content );		 
	}
	
	private static function getNameValueArrays ( $prefix , array $name_value_list )
	{
		foreach ( $name_value_list as $name_value => $value )
		{
			$names[] = "<$prefix" .  $name_value . ">" ;
			$values[] = $value;
		}

		return array ( $names , $values );
	}
	
	
	
	 
	  
}
?>