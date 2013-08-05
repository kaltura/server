<?php
/**
 * Will read a configuration from a table-like file.
 * The file will be split into 4 parts.
 * After each part will come the part-separator which is a line starting with at least 5 - (-----)
 *
 * The first part will define the parser instructions and will be of the following structure:
 * 	first character will define the delimiter of this part (example & )
 *  the following will be key=value pairs separated by this delimiter (don't mix this delimiter with the table dilimiter)
 * 	possible options for keys:
 * 		delimiter - the table-delimiter for the table structure (will be used in part 3 and 4 to separate columns). Default '|'
 * 		null - the string to indicate the null value. The string match is case sensitive. Default 'NULL'
 * 		trim - should the values be trimmed on both sides after being read from the file. Defaultl - '1' (true).
 * 			This is handy if the table is spaced by tabs and still the values are not supposed to include leading or trailing white spaces.
 * 		pk - the index of the column that should be used as the primary key of the table. (Defualt '1')
 * 			This index start at 1 (not at 0).
 * 			The column of the pk should never allow duplicate values.
 * 		nopk - should throw exception if requested pk was not found (default '1');
 * example for the first part
 * &delimiter=|&trim=1&null=NULL&pk=1
 *
 * The second part is optional and can include as many lines of arbitrary text.
 * Use this part tp describe the structure of the table and non-standard issues
 *
 * The third part is mandatory and defined the names of the columns separated by the table-delimiter (see part-1)
 * Column names are always trimmed and should never include duplicates.
 *
 * The forth part holds the rows of the table.
 * Each row in a separate line.
 * Each line separated into columns by the table-delimiter.
 * In the column defined as the pk there should be no duplicates.
 *
 * @package server-infra
 * @subpackage config
 */
class kConfigTable
{
	const LINE_SEPARATOR = "\n";
	const SEPARATOR_LINE_PREFIX = "-----";
	const COMMENT_PREFIX = "//";
	
	const EXPIRY = 100000; // seconds

	public static $should_use_cache = true;
	
	private static $cache;
	
	private static $parser_rules = array ( "delimiter" , "trim" , "null" , "pk" , "inherit");
	
	private $delimiter = "|";
	private $trim = 1;
	private $null = "NULL";
	private $pk = 1;
	private $inherit = "~";
	
	private $id;
	private $nopk = 1; 				// throw exception if no pk was found
	private $ignore_non_existing_columns;
	
	private $comment ;				// comments from part 2 of the table
	private $columns = null;		// array of the column names ordered by their place in the table
	private $column_names = null; 	// associative array column_name=index in the column array
	private $rows = array();
	
	private $structure_defined = false;
	
	public static function getInstance ( $file_name , $ignore_non_existing_columns = false )
	{
		if ( ! self::$should_use_cache )
		{
			// if places in the code need to skip hte cahce - better to create the object fro mthe file
			return new kConfigTable ( $file_name , $ignore_non_existing_columns );
		}
		
		// see if we want to use memcache for the config tables
		if ( ! self::$cache )
		{
			self::$cache = new myObjectCache ( self::EXPIRY );
		}
		
		$ct = self::$cache->get ( "kConfigTable" , $file_name );
		if ( ! $ct )
		{
			$ct = new kConfigTable ( $file_name , $ignore_non_existing_columns );
			self::$cache->put ( $ct );
		}

		return $ct;
	}
	
	protected function kConfigTable ( $file_name , $ignore_non_existing_columns = false )
	{
		$this->id = $file_name;
		
		$this->initFromFile( $file_name );
		$this->ignore_non_existing_columns = $ignore_non_existing_columns;
	}
	
	
	public function getId ()
	{
		return $this->id;
	}
	
	// compare a vlaue with the parser's inherit-value
	public function isInheritValue ( $str )
	{
		return $str == $this->inherit;
	}
	
	// allow getting a single column or the whole line (if column_name not specified).
	// TODO - allow getting multiple columns
	public function get ( $pk , $column_name =null)
	{
		if ( $column_name != null )
		{
			$column_index = @$this->column_names[$column_name];
			// if ignore_non_existing_columns is false - throw an exception for non-existing columns
			if ( ! $column_index )
			{
//echo __METHOD__ . ":[" . $this->getId() . "][$column_name][$column_index][" . 	$this->ignore_non_existing_columns . "]\n" ;
				if ( $this->ignore_non_existing_columns )
				{
					// instead of making an issue of the missing column - return the inherit string to indicate the search should continue in the chain
					// (if part of a chain)
					return $this->inherit;
				}
				else
				{
					throw new kConfigTableException( "Unknown column name [$column_name]" );
				}
			}
		}

		$row = @$this->rows[$pk]; /// $the pk is ONE-based NOT zero-based
		if ( ! $row )
		{
			if ( $this->nopk )
				throw new kConfigTableException( "No such pk [$pk]" );
			else
				return null;
		}
		
		if ( $column_name )
		{
			return $row[$column_index];
		}
		else
		{
			return $row; // return the whole line
		}
	}
	
	public function getByColumnIndex ( $pk , $column_index )
	{
		$row = @$this->rows[$pk]; /// $the pk is ONE-based NOT zero-based
		if ( ! $row )
		{
			if ( $this->nopk )
				throw new kConfigTableException( "No such pk [$pk]" );
			else
				return null;
		}
		
		return @$row[$column_index];
	}
	
	public function getColumnNames ()
	{
		return $this->columns;
	}

	public function getColumnNameByIndex ( $index )
	{
		if ( isset ( $this->columns[$index] ) )
			return @$this->columns[$index];
		return null;
	}
	
	public function listPks ( )
	{
		return array_keys( $this->rows );
	}
	
	public function isSetPk ( $pk )
	{
		return isset ( $this->rows[$pk] ) ;
//		$row = @$this->rows[$pk];
//		return ( $row != null );
	}
	
// --------------------------------------------------------------------------------------------------

/**
 * Will construct from a file
 */
	private function initFromFile ( $file_name )
	{
		if(!file_exists($file_name))
			throw new kConfigTableException ( "Cannot init from file [$file_name]" );
		
		$content = file_get_contents ( $file_name );
		
		if ( ! $content )
			throw new kConfigTableException ( "Cannot init from file [$file_name]" );
		
		$lines = explode ( self::LINE_SEPARATOR , $content );
		$part = 1;
		
		foreach ( $lines as $line )
		{
			if ( self::isSeparatorLine ( $line ))
			{
				$part++;
				continue;
			}
			
			if ( self::isCommentLine( $line )) continue;
			
			switch ( $part )
			{
				case 1:
					$this->defineParserRules ( $line );
					break;
				case 2:
					$this->addComment ( $line );
					break;
				case 3:
					$this->defineTableStruct ( $line );
					break;
				case 4:
					$this->addRow ( $line );
					break;
				default:
					// this means there is an extra part at the end ot the table - do nothing.
			}
		}
	}
	
	private function defineParserRules ( $line )
	{
		static $internal_delimiter = null;
		if ( !$internal_delimiter  )
		{
			$internal_delimiter  = substr ( $line , 0,1); // first character of first line
//echo __METHOD__ . ":" . $internal_delimiter;
			$rules = explode ( $internal_delimiter , $line );

			foreach ( $rules as $rule )
			{
				if ( ! $rule ) continue;
				list ( $key , $value ) = explode ( "=" , $rule ); // hard-coded separator etween the kwy and value - '=' character
				if ( in_array ( $key , self::$parser_rules) )
				{
					$this->$key = trim($value); // store in the member variable;
				}
				else
				{
					throw new kConfigTableException ( "Unknown parser rule [$key]=[$value] in line [$line]" );
				}
			}
		}
	}
	
	
	private function addComment ( $line )
	{
		if ( $this->comment ) $this->commnet .= "\n";
		$this->comment .= $line;
	}
	
	private function defineTableStruct ( $line )
	{
		if ( $this->structure_defined )
			throw new kConfigTableException( "Only one definition line is allowed" );

		 $this->structure_defined = true;
		
		$columns = explode ( $this->delimiter , $line );
		
		$this->columns = array();
		$this->column_names = array();
		$i=0;
		foreach ( $columns as $column )
		{
			$column_name = trim ( $column );
			$this->columns[] = $column_name;
			$this->column_names [$column_name] = $i; // holds the reverse dictionary to access the index from the column name
			$i++;
		}
		
		if( $this->pk > $i )
		{
			throw new kConfigTableException( "pk [{$this->pk} is too big. There are only [$i] columns defined for this table" );
		}
	}
	
	private function addRow ( $line )
	{
		if ( trim($line) == "" ) return; // empty line
		$values = explode ( $this->delimiter , $line );
//echo __METHOD__ . ":$line\n" . print_r ( $values ,true ) . "\n";
		foreach ( $values as & $value )
		{
			if ( $this->trim ) $value = trim ($value );
			if ( $this->null == $value ) $value = null;
		}
		
		// TODO - validate the nuber if the same as the column number
//echo __METHOD__ . ":pk index [" . $this->pk . "]\n";
		$pk = @$values[$this->pk-1];
//echo __METHOD__ . ":pk[$pk]\n";
		if ( isset ( $this->rows[$pk] ) )
		{
			// TODO - should throw exception ??
			throw new kConfigTableException( "Duplicate pk [$pk]" );
		}
		
		$this->rows[$pk] = $values;
	}
	
	private static function isSeparatorLine ( $line )
	{
		// lines starts with SEPARATOR_LINE_PREFIX
		return ( substr ( $line , 0 , strlen ( self::SEPARATOR_LINE_PREFIX ) ) == self::SEPARATOR_LINE_PREFIX );
	}
	
	private static function isCommentLine ( $line )
	{
		// lines starts with COMMENT_PREFIX
		return ( substr ( $line , 0 , strlen ( self::COMMENT_PREFIX) ) == self::COMMENT_PREFIX );
	}
}

/**
 * Will allow chaining several kConfigTable objects in a given order and handle fallback searchs in specific cases
 */
class kConfigTableChain
{
	private $file_names;
	private $config_chain = null;
	
	// TODO - let define default seach policy in chain

	/*
	 * the FIRST file in the array is the most impornat one and is the first to search.
	 * the LAST file should always be set NOT to ignore non-existing columns
	 */
 	public function  kConfigTableChain ( array $file_names , $path = null)
	{
		$this->file_names = $file_names;
		$this->config_chain = array();
		
		$i=0; $count = count ($file_names);
		foreach ( $file_names as $file_name )
		{
			$i++;
			$ignore = $i < $count; // set $ignore to be true unless it's the last file
			if ( $file_name == null ) continue;
			if ( $path ) $file_name = $path . $file_name ;
			$this->config_chain[] = kConfigTable::getInstance ( $file_name , $ignore );
		}
	}
	
	public function get ( $pk , $column_name =null)
	{
		$i=0; $count = count ($this->config_chain);
		foreach ( $this->config_chain as $config )
		{
			$i++;
			if ( $i < $count)
			{
				if ( $config->isSetPk ( $pk ) )
				{
					$val = $config->get ( $pk , $column_name );
					if ( is_array ( $val ) )
					{
						$i=0;
						// replace all inherit values in the array -
						foreach ( $val as & $column_val )
						{
							if ( $config->isInheritValue ( $column_val ))
							{
								$column_val = $this->get ( $pk , $config->getColumnNameByIndex ( $i ) );
							}
							$i++;
						}
					}
					if ( $config->isInheritValue ( $val ) )
					{
						continue;
					}
					return $val;
				}
			}
			else
			{
				if ( ! $config->isSetPk ( $pk ) )
				{
					$err = "Cannot find [$pk] in any of the files " . print_r ( $this->file_names , true );
					throw new kConfigTableException ( $err );
				}
				// in case of the last in the chain - get wihtou questioms
				return $config->get ( $pk , $column_name );
			}
		}
	}
	
	
	public function getTables ()
	{
		return $this->config_chain;
	}
}

class kConfigTableException extends Exception {}
?>