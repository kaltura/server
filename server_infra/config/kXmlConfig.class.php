<?php
/**
 * This class will merge 2 xml configurztion files to reduce code duplication.
 * This will lbe done by referencing elements from one xml to another.
 * The result can be stored on the disk so the relatively heavy merge will be done only when really required.
 * 
 * @package infra
 * @subpackage utils
 */
class kXmlConfig
{
	const ATTR_EXTENDS = "extends";
	const ATTR_INHERITS = "inherits";
	const ATTR_ID = "id";

	public static $s_debug = true;

	private $should_merge = true;
	private $created_cache = false;
	private $base_file, $referrer_file;
	private $base_xml, $referrer_xml;
	/**
	 * constructor with 2 files to merge.
	 *
	 * Throws as excpetion if the files don't exist or not valid XMLs
	 *
	 * @param array $files
	 * @return kXmlConfig
	 */
	public function  kXmlConfig ( $base_file , $referrer_file )
	{
		$this->referrer_file = $referrer_file;
		$this->referrer_xml = simplexml_load_file ( $referrer_file ,null,LIBXML_NOERROR );
		
		if ( $base_file != null )
		{
			$this->base_file = $base_file;
			$this->base_xml = simplexml_load_file ( $base_file );
		}
		else
		{
			// base_class will be specified in the referrer file in the root element's extends attribute
			$extends =  self::getAttr( $this->referrer_xml , "extends" );
			if ( ! $extends )
			{
				// his is a stand-alone file - no need to merga
				$this->should_merge = false;
			}
			else
			{
				// if the extends starrts with a '.' - it is assumed to be relative to the referred_file
				if ( strpos ( $extends , '.' ) === 0 )
				{
					$path = dirname (  $referrer_file ) . "/" . $extends; 
				}
				else
				{
					$path = $extends;
				}
			
				$this->base_file = realpath( $path );
				$this->base_xml = simplexml_load_file ( $this->base_file );
			}
		}
		
	}

	/**
	 * merges the file and returns a string.
	 *
	 */
	public function merge (  )
	{
		if ( $this->should_merge )
		{
			// 	iterate over $this->referrer_xml and replace elements that are references or fills elements that are partial
			$this->recurseXml ( $this->referrer_xml , null , "" );
		}
		else
		{
			// else - return the file as-is
		}

		// TODO - make a copy of the referrer and work n top of it.
		// create from scratch ? manipulate / delete current elements ?
		return trim($this->referrer_xml->asXml());
	}

	/**
	 * Will check if the file_name exists.
	 * If not - will run merge and store it.
	 * If exists but older than one or more than the files in the $this->files, will re-merge
	 *
	 * If there is no need to use the cached file - set it do be null
	 * @param unknown_type $file_name
	 */
	public function getConfig ( $file_name )
	{
		$should_create = true;
		if ( $file_name != null && file_exists( $file_name ) )
		{
			// check that the output file is newer than the 2 sources  
			$t1 = filemtime ( $this->base_file );
			$t2 = filemtime ( $this->referrer_file );
			$t_out = filemtime ( $file_name );
			if ( $t_out >= $t1 && $t_out >= $t2 )
				$should_create = false;
		}


		
		if ( $should_create )
		{
			$res = $this->merge ( );
			
			if ( $file_name != null )
			{
				file_put_contents( $file_name , $res ); // sync - OK
				$this->created_cache = true;   
			}
		}
		else
		{
			$res = file_get_contents( $file_name );
		}
		
		return $res;
	}

	public function createdCache ()
	{
		return $this->created_cache;
	}

// ---------------------- private worker functions ----------------------------	
	
	// the xml object is referenced and can be completly replaced
	private function recurseXml( SimpleXMLElement $xml, $parent = null , $parent_path="")
	{
		$child_count = 0;

		foreach( $xml as $key => $value)
		{
			$attrs = $value->attributes();
			$extends = @$attrs[self::ATTR_EXTENDS];
			//print ( "[" . print_r ( $attrs , true ) . "]" );
			if ($extends )
			{
//print ( "\n>> $parent_path.$key\n" ) ;
				// have to reference the base_xml to complete this elements
				$base_element = $this->getBaseElement ( $value , $parent_path , $extends );
					
				// at the top level - no inherits is equal to true
				$inherits =  self::getAttr ( $value , self::ATTR_INHERITS );
 				
				if ( $inherits == null ) $inherits = 1;
				// modify the xml element to fit the $base_element

				$this->mergeElement ( $value , $base_element , $inherits , $parent_path.$key );
//print ( "<< $parent_path.$key\n" ) ;
			}
			//			print($parent_path . "." . (string)$key . " = " . (string)$value . "\n");

			$this->recurseXml($value , $xml , $parent_path.".".$key);
		}
	}

	/*
	 at this point the merge should be done recursivly within the $base_element
	 there are 2 methods to iterate the trees:
	 inerits=true -
		iterate the base_element -
		search the element on the referrer (element anme and id should be unique) :
		any node that does not exist in the referrer - add it
		any node that does exist in the referrer - mergeElement according to the attributes on the refferer (inherits)
		inherits=false
		iterate the referrer_element
		there should be a twin element on the base_element for every element here - if not - error !?!?
		recurse using the inherits attribute
		if an element extends -

		inherits is	the state of the parent scope and the $referrer_element - meaning it was already determind for
		this specific pair of elements
		*/
	private function mergeElement ( SimpleXMLElement $referrer_element , SimpleXMLElement $base_element, $inherits , $path )
	{
		if ( ! $inherits || $inherits == "0" || $inherits == "no" || $inherits == "false" ||  $inherits === false || $inerits = null )
		{
			// don't copy the content or attributes
			// iterate the children
			foreach ( $referrer_element as $key => $referre_element_child )
			{
				$current_inherits = self::getAttr ( $referre_element_child , self::ATTR_INHERITS );
				if ( $current_inherits != null ) $inherits = $current_inherits;

				// find twin element
				$id = self::getAttr( $referre_element_child , self::ATTR_ID );
				// we might reach here on a dead branc of the base_element
				$elem_on_base_element = $base_element != null ? self::getChild ( $base_element , $key , $id ) : null;
				$extends = self::getAttr( $referre_element_child , self::ATTR_EXTENDS );
				
				$current_path = "$path.{$key}[$id]";
				
				if ( ! $elem_on_base_element )
				{
					if ( $extends )
					{
						// error !
						self::error ( "element [$current_path] cannot extend [$extends]" );
					}
					// OK as long as no child element will attempt to extend
					// from this point onwards inherits is in fact=0 and extends should not be used
					$this->mergeElement (  $referre_element_child , null , $current_inherits , $current_path );
				}
				else
				{
					if ( $extends )
					{
						// at this point when extending - it's like switching on the inherits
						// else we would enter the node and did nothing (which is the same as extends=<nothig> anyway)
						$current_inherits = true;
					}

					$this->mergeElement (  $referre_element_child , $elem_on_base_element , $current_inherits , $current_path );
				}
			}
		}
		else
		{
			//print ( "now merging: [" . $referrer_element->getName() . "] " . print_r ( $referrer_element , true ) ) ;

			// copy the element value (direct text) if not empty (spaces and \n are considered empty)
			$direct_value =  (string)$base_element[0];
			if ( trim(str_replace ( "\n" , "" , $direct_value ) ) ) $xml[0] = trim($direct_value);

			$referrer_element_children = $referrer_element->children();
			$referrer_element_attributes = $referrer_element->attributes();

//print "[[current_inherits:$current_inherits)]]\n";

			if( $base_element == null ) return ;  // this is an error and will happen only when debug is false

			// copy all child elements
			foreach ( $base_element as $key => $base_element_child )
			{
				// if there is a list of elements - 2 children will be considdered the same if there is no id attribute or if the id attr is the same
				$id = self::getAttr( $base_element_child , self::ATTR_ID );
				// find twin element
				$elem_on_referrer_element = self::getChild ( $referrer_element , $key , $id );

				$current_path = "$path.{$key}[$id]";
					
				if ( $elem_on_referrer_element == null )
				{
//print "-- adding [[$current_path]] ($inherits)\n" ;

					// doesn't exist - create and merge
					$new_element = $referrer_element->addChild( $key , $base_element_child );
					$this->mergeElement( $new_element , $base_element_child , $inherits , $current_path);
				}
				else
				{
//print "-- appending to [[$current_path]] ($inherits)\n";					
					$current_inherits = self::getAttr ( $elem_on_referrer_element , self::ATTR_INHERITS );
					if ( $current_inherits != null ) $inherits = $current_inherits;

					// 	does exist - if inherits - go recursive
					$this->mergeElement( $elem_on_referrer_element , $base_element_child , $inherits , $current_path );
				}
			}

			// copy all attributes
			foreach ( $base_element->attributes() as $key => $base_element_attr )
			{
				$attr_on_referrer_element = $referrer_element_attributes[$key];
				if ( ! $attr_on_referrer_element )
					$referrer_element->addAttribute( $key , $base_element_attr );
			}
		}
	}

	private function getBaseElement ( $referrer_element , $referrer_parent_path , $referrer_id )
	{
		// the base element should be of the same name as the referrer and with an id attribute equal to the desired by the referrer ('extends')
		$base_xpath =  "//" . $referrer_element->getName() . "[@id='$referrer_id']";
//print ( "$referrer_parent_path.{$referrer_element->getName()} extends: $base_xpath \n" )	;

		$results = $this->base_xml->xpath ( $base_xpath );
		if ( count ( $results ) == 0 )
		{
			// referrencing to an undefined element object
			self::error ( "Unknown element in the base xml [$base_xpath] from referrer [$referrer_parent_path] [{$referrer_element->getName()}]" );
			return null;
		}
		if ( count ( $results ) > 1 )
		{
			// referencing an element which is not unique
			// TODO - this is infact an error in the base_xml and should be validated on load time not reference time
			self::error ( "Multiple occurences of element in the base xml [$base_xpath] from referrer
				[$referrer_parent_path] [{$referrer_element->getName()}]" );
			
			return null;
		}

		$base_element = $results[0] ; // one and only element
		return $base_element;
	}

	// finds a child element with the same name. 
	// if the id is not null - searchs for a child with the same name and the same id attribute or the extends attribute as the given id
	private static function getChild ( SimpleXMLElement $xml , $child_name , $id  )
	{
		foreach ( $xml as $key => $value )
		{
			if ( $child_name == $key )
			{
				if ( !$id) return $value;
				else
				{
					$current_elem_id = self::getAttr ( $value , self::ATTR_ID );
					if ( $current_elem_id == $id ) 
					{
						return $value;
					}
					else
					{
						// try matching the extends attribute
						$current_elem_extends = self::getAttr ( $value , self::ATTR_EXTENDS );
						if ( $current_elem_extends == $id ) 
						{
							return $value;
						}
					}
				}
			}
		}

		return null;
	}

	private static function getAttr ( SimpleXMLElement $xml , $attr_name )
	{
		$attrs = $xml->attributes();
		foreach ( $attrs as $attr => $value )
		{
			if ( $attr == $attr_name )
			{
				return 	(string)$value;
			}
		}
		
		return null;

	}

	private static function error ( $str )
	{
		if ( self::$s_debug )
			throw new Exception ( $str );

	}
}
