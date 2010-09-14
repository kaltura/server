<?php
// WIll do  a better job than The default Criteria
class myCriteria extends Criteria
{
	const HINT_USE = 1; 
	const HINT_IGNORE = 2;
	const HINT_FORCE = 3;
	
	private $ignore_select_column_list = array();

	private $index_hints_map = array();
	
	// will create a unique key for the criteria
	public static function getCriteriaCacheKey ( Criteria $c )
	{
		$cloned_c = clone  $c;
	        $arr = array();
                $str = BasePeer::createSelectSql ( $c , $arr );
	
		$param_map = array();
		
		$keys = $c->keys();
		foreach ( $keys as $k ) 
		{
			$val =  $c->getCriterion( $k );

			if ( $val )
			{
				$param_map[$k] = $val->hashCode();
			}
		}
		

		$str .= print_r( $param_map , true );

		$cache_key =  md5($str);
		
		return $cache_key;
	}
	
	// Default joins are Left joins
	public function addJoin($left, $right, $operator = null)
	{
		foreach($this->getJoins() as $join)
		{
			if ($join->getLeftColumn() == $left && $join->getRightColumn() == $right)
				return $this;
		}
		
		if ( $operator == null ) $operator = Criteria::LEFT_JOIN;
		parent::addJoin( $left, $right, $operator );
	}

	// Don't add some selected columns	
	public function removeSelectColumn($name)
	{
		$this->ignore_select_column_list[] = $name;
		return $this;
	}

	// override the addSelectColumn to better control the additions of columns
	// there is no point on removing an item from the list because it is added in doSelect anyway 
	public function addSelectColumn($name)
	{
		if ( in_array( $name , $this->ignore_select_column_list) )
			return;
		return parent::addSelectColumn( $name );
	}
	
	public function clearSelectColumns() 
	{
		$this->ignore_select_column_list = array();
		return parent::clearSelectColumns();
	}
	
	// make sure the hint_type is taken into account properly
	// see http://dev.mysql.com/doc/refman/5.0/en/index-hints.html for all the options
	public function addHint ( $table_name , $index_to_use , $hint_type = null )
	{
		if ( $hint_type == null ) $hint = self::HINT_USE;
		$this->hint[$table_name] = $index_to_use;
	}
	
	
	public static function addComment ( Criteria $c , $str )
	{
		//$c->addAsColumn( "'c' /* $str */" , "comment" );
		//$c->add ("id.id" , "/* $str */" , Criteria::CUSTOM ); 
		if (method_exists( $c , "setComment"))		$c->setComment  ( $str  );
	}
}

?>