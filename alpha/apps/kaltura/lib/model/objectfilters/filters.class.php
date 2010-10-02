<?php

include_once( 'allUtils.class.php');

/**
 * This base class will help filter the BaseObject class by using the Criteria object.
 * Each filter is coupled with a concrete class derived from BaseObject (and maybe to its Peer object ??).
 * It hold the fields of the original object that can be filtered by.
 *
 */

// TODO - should go global -> go into lib !
abstract class baseObjectFilter extends myBaseObject
{
	public $fields;
	
	/**
	 * @var AdvancedSearchFilter
	 */
	protected $advancedSearch;

	const FILTER_PREFIX = "_";

	const LT = "lt";
	const LTE = "lte";
	const GT = "gt";
	const GTE = "gte";
	const LT_OR_NULL = "ltornull";
	const LTE_OR_NULL = "lteornull";
	const GT_OR_NULL = "gtornull";
	const GTE_OR_NULL = "gteornull";
	const EQ = "eq";
	const LIKE = "like";
	const XLIKE = "xlike";
	const LIKEX = "likex";
	const IN = "in";
	const NOT_IN = "notin";
	const NOT = "not";
	const BIT_AND = "bitand";
	const BIT_OR = "bitor";
	const MULTI_LIKE_OR = "mlikeor";
	const MULTI_LIKE_AND = "mlikeand";
	const MATCH_OR = "matchor";
	const MATCH_AND = "matchand";

	// TODO - make sure client-generators know how to generate this OR_SEPARATOR
	const OR_SEPARATOR = "-";
	
	const QUERY_AND = 'and';
	const QUERY_OR= 'or';
	
	const IN_SEPARATOR = ",";
	const AND_SEPARATOR = " ";
	
	//const MULTI_LIKE_SEPARATOR = " ";		// this is the old separator - the correct separator should be ","
	const MULTI_LIKE_SEPARATOR = ",";
	const MULTI_LIKE_PATTERN = "/[ ,]/"; // TODO - remove and use the MULTI_LIKE_SEPARATOR
	
	const ORDER = "_order_by";
	const LIMIT = "_limit";

	const SQL_LIKE_CHAR = "%";
	/*	const LIKE_LEFT = 0;
	 const LIKE_RIGHT = 1;
	 const LIKE_BOTH = 2;
	 */

	const MATCH_KALTURA_NETWORK_AND_PRIVATE = "-1"; 
	
	protected $partner_search_scope = null;
		 
	protected $aliases;

	protected  $field_name_translation_type ; //= BasePeer::TYPE_FIELDNAME;

	protected $allowed_order_fields = array();

	protected $field_operators = null;
	
	public function getLimit()
	{
		return $this->get ( self::LIMIT );
	}

	public function setLimit( $lim )
	{
		return $this->set( self::LIMIT , $lim );
	}
	
	public function getAllowedOrderFields ()
	{
		return $this->allowed_order_fields;
	}

	public function setPartnerSearchScope ( $partner_id )
	{
		$this->partner_search_scope = $partner_id;
	}

	public function getPartnerSearchScope()
	{
		return $this->partner_search_scope;
	}
	
	public function getOperatorsForField ( $field_name )
	{
		if ( ! $this->field_operators )
		{
			 // build the matrix for all fields
			$this->field_operators = array();
			foreach ( $this->fields as $op_field => $value)
			{
				$tokens = explode ( baseObjectFilter::FILTER_PREFIX,  $op_field , 3 ); 
				$operator = @$tokens[1];
				$field = str_replace ( "_" , "" , strtolower ( @$tokens[2] ) ) ;
				
				$operators_for_field = @$this->field_operators[$field];
				if ( !$operators_for_field ) 
				{
					$operators_for_field =array();
					$this->field_operators[$field] = $operators_for_field;
				}
				$this->field_operators[$field][] = $operator;
			}
		}
		
		return @$this->field_operators[strtolower ( $field_name )];
	}
	
	public function getOrdersForField ( $field_name )
	{
		if ( ! $this->field_orders )
		{
			 // build the matrix for all fields
			$this->field_orders = array();
			foreach ( $this->allowed_order_fields as $order_field )
			{
				$order = str_replace ( "_" , "" , strtolower ( $order_field ) ) ;
				$this->field_orders[$order] =$order;
			}
		}
		
		return @$this->field_orders[strtolower ( $field_name )];
	}
		
	// couples the filter to the proper peer object
	abstract protected function getFieldNameFromPeer ( $field_name );

	protected function getRealFieldNameFromPeer ( $field_name )
	{
		if ( is_array (  $this->aliases ) && isset ( $this->aliases[$field_name] ) )
		{
			// this means the field_name is only an alias to the colname and does not hold the exact name
			$real_field_name = $this->aliases[$field_name];
		}
		else
		{
			$real_field_name = $field_name;
		}
		return $this->getFieldNameFromPeer ( $real_field_name );
	}

	abstract protected function getIdFromPeer (  );

	public function __construct ( $field_name_translation_type = BasePeer::TYPE_FIELDNAME )
	{
		$this->field_name_translation_type = $field_name_translation_type;
		$res = parent::__construct();
		$this->fields[self::ORDER] = null; // use the order field to contain the  data for addOrderToCriteria
		$this->fields[self::LIMIT] = null; // use the limit field to contain the  data for setLimit

		$this->allowed_order_fields[] = ( "id" )	; // always can filter by id
		return $res;
	}

/*	public function baseObjectFilter ( $field_name_translation_type = BasePeer::TYPE_FIELDNAME )
	{
		echo "baseObjectFilter: $field_name_translation_type<br>";
		$this->field_name_translation_type = $field_name_translation_type;
	}
	*/

	public function addIdsToCriteria ( Criteria $criteria , array $id_list )
	{
		$criteria->add ( $this->getIdFromPeer() , $id_list , Criteria::IN );
	}

	private static function getFieldAndDirection ( $field_name )
	{
		if ( $field_name[0] == "-" )
		{
			$ascending = false;
			$field_name = substr ( $field_name , 1 );
		}
		elseif ( $field_name[0] == "+" )
		{
			$ascending = true;
			$field_name = substr ( $field_name , 1 );
		}
		else
		{
			$ascending = true;
			// column is left as is
		}
		return array ( $field_name , $ascending )	;
	}
	/**
	 * to set the direction of the sort use the sign +comlumn for ascending and -column for descending.
	 * if no sign is used - ascending is assumed.
	 */
	public function addOrderToCriteria ( Criteria $criteria , $field_name  )
	{
		list ( $field_name , $ascending ) = self::getFieldAndDirection ( $field_name );
/*
		if ( $field_name[0] == "-" )
		{
			$ascending = false;
			$field_name = substr ( $field_name , 1 );
		}
		elseif ( $field_name[0] == "+" )
		{
			$ascending = true;
			$field_name = substr ( $field_name , 1 );
		}
		else
		{
			$ascending = true;
			// column is left as is
		}
*/
		$column = $this->getRealFieldNameFromPeer ( $field_name  );

		if ( $ascending )
		{
			$criteria->addAscendingOrderByColumn( $column );
		}
		else
		{
			$criteria->addDescendingOrderByColumn( $column );
		}
	}


	/**
	 * Use when need to search a single keyword in several columns with the same search operator (eq/le/like)
	 * The default operator is LIKE %keywords%
	 * If more than one keyword is set - the OR operator is used between the keywords
	 *
	 * $or_criteria will be used to OR the outcome of the keyword manipulation, mening:
	 * 	criteria AND (or_criteria OR keyword_criteria)
	 */
	// TODO PERFORMANCE - check if the query created is good in terms of preformance !
	public function addOrToCriteria ( Criteria $criteria , $or_criteria , $keys_to_search , array $field_names , $operator = Criteria::LIKE )
	{
		if ( $keys_to_search == NULL )
		{
//			echo "Emty keywors list!" ;
			return ;
		}


		// for ease of programming - assume that the keys_to_search is always an array. if not - it's a string that should de placed in a 1-obj array
		if ( is_string( $keys_to_search ) )
		{
			$keys_to_search = array ( $keys_to_search );
		}

		if ( $operator == Criteria::LIKE )
		{
			$keys = array ();
			foreach ( $keys_to_search as $key_to_search )
			{
				$keys[] = self::createSqlLike ( $key_to_search , self::LIKE );
			}
		}
		else
		{
			$keys  = $keys_to_search;
		}

		// this implementation makes sure that all the keywords exist at least in one of the columns
		$accumulated_or_criterion = NULL;
		$accumulated_and_criterion = NULL;
		foreach ( $field_names as $field_name )
		{
			//echo ( "col: " . $column . "<br>" );
			$column = $this->getRealFieldNameFromPeer( $field_name );
			$accumulated_and_criterion = NULL;
			foreach ( $keys as $key )
			{
				if ( $or_criteria != NULL )
				{
					// first time - create criterion, second time onwards - append to it
					$accumulated_and_criterion == NULL ?
						$accumulated_and_criterion = $criteria->getNewCriterion( $column , $key , $operator ) :
						$accumulated_and_criterion->addOr ( $criteria->getNewCriterion( $column , $key , $operator ) );
						//$accumulated_and_criterion->addAnd ( $criteria->getNewCriterion( $column , $key , $operator ) );
				}
				else
				{
					// 	first time - create criterion, second time onwards - append to it
					$accumulated_and_criterion == NULL ?
						$accumulated_and_criterion = $criteria->getNewCriterion( $column , $key , $operator ) :
						$accumulated_and_criterion->addAnd ( $criteria->getNewCriterion( $column , $key , $operator ) );
				}
			}

			$accumulated_or_criterion == NULL ?
				$accumulated_or_criterion = $accumulated_and_criterion :
				$accumulated_or_criterion->addOr ( $accumulated_and_criterion );

		}
		if ( $or_criteria == NULL )
		{
			$criteria->add ( $accumulated_or_criterion );
		}
		else
		{
			$outer_or_criterion = $or_criteria->addOr ( $accumulated_or_criterion );
			$criteria->add ( $outer_or_criterion );
		}
	}


	/**
	 * Will be used for the KCW's search - this is slightly different  
	 *
	 * @param Criteria $criteria
	 * @param array $keys_to_search
	 * @param array $field_names
	 */
	public function addSearchMatchToCriteria ( $criteria , $keys_to_search , $field_names  )
	{
		$operator = self::MATCH_OR; 
		// accumulate all the $keys_to_search for the specific filed_names 

		$columns = array();
		// in case there is only one field_name not in an array - place it in an array only to make it easy for the next step
		if ( !is_array ( $field_names ))
		{
			$field_names = array ( $field_names );
		}

		foreach ( $field_names as $field_name)
		{
			$columns[] = $this->getRealFieldNameFromPeer( $field_name );
		}
		$match_str = implode( "," , $columns );

		
		$against_str  = self::formatMySqlAgainst ( $keys_to_search , $operator , "," , self::MATCH_AND );

		// depending on the partner_search_scope - alter the against_str 
		if ( self::MATCH_KALTURA_NETWORK_AND_PRIVATE == $this->partner_search_scope )
		{
			// add nothing the the match
		}
		elseif ( $this->partner_search_scope == null  )
		{
			// the kaltura_netrowk keyword is mandatory !
			// - find all the NOT partner only
			//$against_str = "+" . mySearchUtils::getKalturaNetworkKeyword() . " " . $against_str;
			$against_str = "-" . mySearchUtils::getPartnerOnlyKeyword() . " " . "-" . mySearchUtils::getPartnerNoneKeyword() . " " . $against_str;
		}
		else
		{
			$against_str = "+" . mySearchUtils::getPartnerKeyword( $this->partner_search_scope ) . " " . $against_str;
		}
		
		if ( trim($against_str) )
		{
			// if nothing to add - don't add
			// the string to search is quoted by double-quotes
			$match_against_str = "MATCH (" . $match_str . ") AGAINST ('" . $against_str . "' IN BOOLEAN MODE )" ;
	
			$accumulated_match_criterion = $criteria->getNewCriterion( $columns[0] , $match_against_str , Criteria::CUSTOM );
	
			$criteria->addAsColumn( "_score" , $match_against_str );
			$criteria->addAnd ( $accumulated_match_criterion );
		}
	}
		
	
	

	// keys_to_search can be either an array of keywords - each is an exact phrase to search (according to the operator)
	// OR a string that should be parsed using the matchAgainstCreator
	// all matches for the same column will be consolidated into a single match-agains clause
	protected function addMatchToCriteria ( $criteria , $or_criteria , $keys_to_search , $field_names , $operator = Criteria::LIKE , 
		$leave_field_names_untouched = false )
	{
//echo __METHOD__ . ":[$or_criteria][" . print_r ( $keys_to_search , true ) ."][" . print_r ( $field_names , true ) . 
//		"][$operator][$leave_field_names_untouched]" ;

		// accumulate all the $keys_to_search for the specific filed_names 
		if ( $keys_to_search == NULL )
		{
//			echo "Emty keywors list!" ;
			return ;
		}

		$columns = array();
		// in case there is only one field_name not in an array - place it in an array only to make it easy for the next step
		if ( !is_array ( $field_names ))
		{
			$field_names = array ( $field_names );
		}

		foreach ( $field_names as $field_name)
		{
			if( $leave_field_names_untouched )
				$columns[] = $field_name ;
			else
				$columns[] = $this->getRealFieldNameFromPeer( $field_name );
		}
		$match_str = implode( "," , $columns );

		// the logic here calls to force the exteral_operator to be MATCH_AND
		$temp_against_str = self::formatMySqlAgainst ( $keys_to_search , $operator , "," , self::MATCH_AND );

		$against_str = null;
		// append to the existing string if there is something worth adding
		if ( trim($temp_against_str) )
		{
			$against_str = self::getAllKeywordsForColumn ( $criteria , $field_names ) . " " . $temp_against_str;
			// store them so if will have to be used again with another set of keywords - the result we be ready to use
			self::setAllKeywordsForColumn ( $criteria , $field_names , $against_str );
		}
		
		if ( $against_str )
		{
			// the string to search is quoted by double-quotes
			$match_against_str = "MATCH (" . $match_str . ") AGAINST ('" . $against_str . "' IN BOOLEAN MODE )" ;
			$accumulated_match_criterion = $criteria->getNewCriterion( $columns[0] , $match_against_str , Criteria::CUSTOM );
	
	//echo "<br>$match_against_str<br>";		
	//		$criteria->addAsColumn( "_score" , $match_against_str );
		$criteria->add ( $accumulated_match_criterion );
		}
	}

	
	// the behavior is different is depending on the operator -
	// if the operator is OR - there will be "+()" on the whole group - meaning at least one of the kyes will have to appear
	// if the operator is AND - there will be "+()" for each phraze - a key with a " " as a separator - meaning each phrase within the group is mandatory
	protected  static function formatMatchKey ( $key , $operator )
	{

		if ( is_array ( $key ) )
		{
			$res = "";
			foreach ( $key as $single_key )
				$res .= self::formatMatchKey( $single_key , $operator );
			
			if ( $operator == self::MATCH_OR && $res ) 
				$res = "+({$res})";
			return $res;
		}
		
		// the separator is whitespace - 
		$key_arr = explode ( "," , trim($key) );

		
		$key_res = $trimmed = "";
		foreach ( $key_arr as $single_key )
		{
			$trimmed = preg_replace ( "/[^a-zA-Z0-9\- ]/" , "" , trim($single_key) );
//			if ( $trimmed && strlen( $trimmed ) > 2 ) $key_res .= $trimmed . "* ";  // don't add strings less than 3 characters long !
			if ( $trimmed  ) $key_res .= $trimmed . "* ";  // don't add strings less than 3 characters long !
		}

		if ( ! $key_res ) return "";
		if ( $operator == self::MATCH_OR )
			$key = "({$key_res})";	 // will allow only some keywords
		elseif ( $operator == self::MATCH_AND )
			$key = "+({$key_res})";   // all keywords should appear
		else
			$key = "+({$key_res})";   // the default is to have all the keywirds

		return $key . " " ;
					
	}

	/**
	 * The separator between keywords is ',' - it will be used to spit the whole string into phrases.
	 * important ! - will use quotes for each word to be treated as exact phrase. 
	 * DUE TO STRANGE BEHAVIOR in the MySqlPreparedStatement - there is a '\' BEFORE each quote in the AGAINST clause. 
	 * Removing the slash character will cuase the preparedStatement not to replace place holders coming after the unescaped " character 
	 * 
	 * TODO - Will be able to parse an extended set of rules such as ( ) and the word OR 
	 * TODO - make sure no invalid character are placed in the text !
	 * @param string  $keywords
	 * @param int_type $operator
	 * @param string $separator
	 * @return string
	 */
	public static function formatMySqlAgainst ( $keywords , $operator , $separator = "," , $external_operator = null )
	{
		// if for any reason we recieve an arra - first make it a string and them follow the instructions
		if ( is_array ( $keywords ) )
		{
        	$keywords = implode ( $separator ,  $keywords );
		}
		
		$keyword_arr = explode ( $separator , $keywords );
		if ( is_null($external_operator )) $external_operator = $operator;
		// the external_operator will indicate whether to force the whole set of keywords 
		$against_str = ( $external_operator == self::MATCH_AND ? " +(" : "(" );
		
		$is_empty = true;
		foreach ( $keyword_arr as $k )
		{
			// clear invalid characters from the text:
			$k = preg_replace ( "/[\\\"\'\?\*\;]/" , " " , trim($k) );
			// the operator (internal one) will indicate whether to force each keyword separatly
			if ( empty ( $k ) ) continue;
			$is_empty = false; // once we have a real value - the whole state is non-empty
			if ( $operator == self::MATCH_AND )
			{
				$against_str .= "+\\\"$k\\\" ";
			}
			else
			{
				$against_str .= "\\\"$k\\\" ";
			}
		}
		
		// if there was nothing to add - return an empty string and NOT an 'against' clause
		if ( $is_empty ) return "";
		
		$against_str .= ") ";
		return $against_str;
	}
	
	/**
	 * After the filter is full of values, it can be attached to a criteria.
	 * After this is done, the original criteria is *more* strict than before - meaning
	 *
	 * @param Criteria $criteria
	 */
	public final function attachToCriteria ( Criteria $criteria )
	{
		// later will call all filters attachToFinalCriteria before the doSelect
		if($criteria instanceof KalturaCriteria)
			return $criteria->attachFilter($this);
			
		return $this->attachToFinalCriteria($criteria);
	}
	
	/**
	 * @param Criteria $criteria
	 */
	public function attachToFinalCriteria ( Criteria $criteria )
	{
		$c = new Criteria();

		foreach ( $this->fields as $field => $name )
		{
			$pos = strpos ( $field , baseObjectFilter::FILTER_PREFIX );

//			KalturaLog::debug( "field [$field] prefix [" . baseObjectFilter::FILTER_PREFIX . "] name[$name]" );

			if ( $pos === 0 )
			{
				if ( $field == self::ORDER ) continue;
				if ( $field == self::LIMIT ) continue;

				// this is the case of a 'auto-named-field' - the prefix indicates the type of the criterion
				$end_of_prefix_index = strpos ( $field , baseObjectFilter::FILTER_PREFIX , 1) + 1;

				$tokens = explode ( baseObjectFilter::FILTER_PREFIX,  $field );
				// the first token is the operator

				$value = $this->fields[$field];

				if ( $value === NULL )		continue;
				if ( $value === "" )  continue;
				$operator_str = $tokens[1];

				// the rest is the name of the field
				// TODO - crack down the rest of the string
				$field_names = substr ( $field , $end_of_prefix_index );

				// IMPORTANT - if the '-' separator is used , the values MUST be separated by '-' too
				$field_names_arr = explode ( self::OR_SEPARATOR , $field_names );
				
				if ( count ( $field_names_arr ) == 1 )
				{
					$field_name = $field_names_arr[0];
					$colname = $this->getRealFieldNameFromPeer ( $field_name  );
					
					// there is not xxx-yyy fields to OR - simple the criterion object can be the criteria itself;
					$this->attachCriterionToCriteria( $criteria , null , false , $operator_str , $value , $colname  );
				}
				else
				{
					// the values are assumed to be too spit by the same separator
					$valus_arr = explode ( self::OR_SEPARATOR , $value );
					$criterion = null;
					$i = 0;
					
					$value_arr_size = count ( $valus_arr );
					$last_value = @$valus_arr[$value_arr_size-1]; // use the last value for all the places that "forgot" to supply it
					foreach ( $field_names_arr as $field_name )
					{
						$colname = $this->getRealFieldNameFromPeer ( $field_name  );
						$value = $i >= $value_arr_size ? $last_value : @$valus_arr[$i];
//echo "[$i] [$operator_str] , [$value] , [$colname]<br>";						
						$criterion = $this->attachCriterionToCriteria( $criteria , $criterion , true , $operator_str , $value , $colname  );
						++$i;
					}
					// finally add the criterion as AND to the criteria
					$criteria->addAnd ( $criterion );
				}
			}
			else
			{
				// should lookup the criterion in the fields map iteslf (it should be a property for that field)
			}
		}

		$this->addOrder ( $criteria );
		$this->attachLimit ( $criteria );
	}

	// TODO - document ...
	private function attachCriterionToCriteria( $criteria , $criterion , $add_or_to_criterion , $operator_str , $value , $colname )
	{
		//echo ( " operator_str " . $operator_str );
		list ( $criteria_operator , $value_to_set , $query_append_method ) = 
			$criteria_operator = baseObjectFilter::getCriteriaOperatorFromStr ( $operator_str , $value , $colname );

	//				echo ( "<br>Adding to criteria [" . $colname . "] = [" . $value . "] , " . $criteria_operator . "<br>");

		// TODO - is this huristics OK ? can we really say for sure that fields that end with _date are time objects ?
		if ( kString::endsWith( $colname , "_date" ) )
		{
			$value_to_set = strtotime ( $value );
		}

		// special case where match can be used
		if ( $criteria_operator == self::MATCH_AND )
		{
			self::addMatchToCriteria ( $criteria,null,$value_to_set,$colname,$criteria_operator,true);
		}
		elseif ( $criteria_operator == self::MATCH_OR )
		{
			self::addMatchToCriteria ( $criteria,null,$value_to_set,$colname,$criteria_operator,true);
		}
		// in 3 cases we need to simply add an and clause:	
		// in | not_in & when the result is not an array
		elseif ( ( $criteria_operator == Criteria::IN || $criteria_operator == Criteria::NOT_IN ) || ! is_array($value_to_set ) )
		{
			if ( $add_or_to_criterion )
			{ 
				$new_crit = $criteria->getNewCriterion( $colname , $value_to_set , $criteria_operator )	;
				
				if ( $criterion == null )
				{
					// need to create a new criterion for the colname
					$criterion = $new_crit;
				}
				else
				{
					$criterion->addOr ( $new_crit );
				}
			}
			else
			{
				// add or null
				if (in_array($operator_str, array(self::LT_OR_NULL, self::GT_OR_NULL, self::LTE_OR_NULL, self::GTE_OR_NULL)))
				{
					$accumulated_criterion = $criteria->getNewCriterion($colname , $value_to_set  , $criteria_operator);
					$or_null_criterion = $criteria->getNewCriterion($colname, null);
					$accumulated_criterion->addOr($or_null_criterion);
					$criteria->addAnd($accumulated_criterion);
				}
				else
				{
					// simply addAnd to the criteria
					$criteria->addAnd ( $colname , $value_to_set  , $criteria_operator );
				}
			}
		}
		else
		{
			$accumulated_criterion  = null;
			foreach ( $value_to_set as $single_value )
			{
				// here use the $criteria object 
				$single_crit = $criteria->getNewCriterion( $colname , $single_value , $criteria_operator )	;
			
				if ( $accumulated_criterion == NULL )
					$accumulated_criterion = $single_crit;
				else
				{
					if ( $query_append_method == self::QUERY_OR )
						$accumulated_criterion->addOr ( $single_crit );
					else
						$accumulated_criterion->addAnd ( $single_crit );
				}
			}

			if ( $add_or_to_criterion )
			{ 
				if ( $criterion == null )
				{
					// need to create a new criterion for the colname - use the one just created
					$criterion = $accumulated_criterion;
				}
				else
				{
 					
					$criterion->addOr ( $accumulated_criterion );
				}
			}
			else
			{	
				// simply addAnd to the criteria
				$criteria->addAnd ( $accumulated_criterion );
			}
		}
		
		return $criterion;
	}
	
	
	// take the string in the ORDER field and attach to the criteria
	private function addOrder ( $criteria )
	{
		$order_string = $this->fields[self::ORDER];

		if ( empty ( $order_string )) return;

		$order_arr = explode ( "," , $order_string );
		$allowed_order_fields;
		foreach ( $order_arr as $order )
		{
			list ( $field_name , $ascending ) = self::getFieldAndDirection ( $order );
			if ( in_array ( $field_name , $this->allowed_order_fields ) )
			{
				$this->addOrderToCriteria( $criteria , $order );
			}
		}
	}
	
	// take the string in the LIMIT field and attach to the criteria as the limit
	private function attachLimit ( $criteria )
	{
		$limit = $this->fields[self::LIMIT];

		if ( empty ( $limit )) return;

		$criteria->setLimit ( $limit );
	}
		
	/*
	 * The value will be manipulated to oin case of like operators (a % will be added to the original values if where needed)
	 */
	protected static function getCriteriaOperatorFromStr ( $operator_str , &$value , $colname )
	{
		//echo ( "getCriteriaOperatorFromStr [" . $operator_str . "," . $value );
		$new_value = $value;
		$query_append_method = self::QUERY_AND;
		if ( $operator_str == self::EQ )			$crit = Criteria::EQUAL;
		elseif ( $operator_str == self::LT 	|| $operator_str == self::LT_OR_NULL )		$crit = Criteria::LESS_THAN;
		elseif ( $operator_str == self::LTE || $operator_str == self::LTE_OR_NULL )		$crit = Criteria::LESS_EQUAL ;
		elseif ( $operator_str == self::GT 	|| $operator_str == self::GT_OR_NULL )		$crit = Criteria::GREATER_THAN;
		elseif ( $operator_str == self::GTE || $operator_str == self::GTE_OR_NULL )		$crit = Criteria::GREATER_EQUAL;
		elseif ( $operator_str == self::NOT )		$crit = Criteria::NOT_EQUAL;
		elseif ( $operator_str == self::IN )
		{
			$new_value = self::createSqlIn ( $value );
			$crit = Criteria::IN;
		}
		elseif ( $operator_str == self::NOT_IN )
		{
			$new_value = self::createSqlIn ( $value );
			$crit = Criteria::NOT_IN;
		}
		elseif ( $operator_str == self::LIKE ||
		$operator_str == self::XLIKE ||
		$operator_str == self::LIKEX )
		{
			$value = str_replace(array('_','%'),array('\_','\%'), $value);
			$new_value =self::createSqlLike ( $value , $operator_str );
			$crit = Criteria::LIKE;
		}
		elseif ( $operator_str == self::BIT_AND )
		{
			$new_value = "($colname & $value) = $value";
			$crit = Criteria::CUSTOM;
		}
		elseif ( $operator_str == self::BIT_OR )
		{
			$new_value = $colname . " & " . $value;
			$crit = Criteria::CUSTOM;
		}
		elseif ( $operator_str == self::MULTI_LIKE_OR || 
		 	$operator_str == self::MULTI_LIKE_AND )
		{
			$value = trim(str_replace(array('_','%'),array('\_','\%'), $value));
			// use every single value (separated by ' ') with %val% 
			$new_value = explode ( self::MULTI_LIKE_SEPARATOR  , $value );
//			$new_value = preg_split  ( self::MULTI_LIKE_PATTERN  , $value );
			// use this array to add only the real values - not multiple spaces if inserted. this is needed because the separator is space.
			// when we'll switch to separator ',' - it can be removed
			$fixed_new_value = array(); 
			
			foreach ( $new_value as & $val )
			{
				$val = trim( $val );
				if ( $val ) $fixed_new_value[] = $val;	
			}
			$new_value = $fixed_new_value;
			if ( count($new_value ) > 1 )
			{
				foreach ( $new_value as & $val )
				{
					$val = trim( $val );
					if ( $val ) $val = self::createSqlLike ( $val , self::LIKE );
				}
			}
			else
			{
				// single value - don't create an array
				$new_value = self::createSqlLike ( $value , self::LIKE );
			}
			$crit = Criteria::LIKE;
			
			if ( $operator_str == self::MULTI_LIKE_OR) $query_append_method = self::QUERY_OR;
			else $query_append_method = self::QUERY_AND;
		}
		elseif ( $operator_str == self::MATCH_AND || $operator_str == self::MATCH_OR )
		{
			$crit = $operator_str; // this is the only case where we return our own crit rather than Criteria
			$new_value = $value ;
		}	
		else
		{
			throw new Exception ( "Unknown operator [" . $operator_str . "] followed by value [" . $value . "]" );
		}
		
		return array ( $crit , $new_value , $query_append_method );
	}

	/*
	 * The seapartor is assume to be IN_SEPARATOR
	 */
	private static function createSqlIn ( $origonal_val )
	{
		$in_values = explode ( self::IN_SEPARATOR , $origonal_val );

		return $in_values;
	}

	private static function createSqlLike ( $origonal_val , $type )
	{
		if ( $type == self::LIKE )
		return self::SQL_LIKE_CHAR . $origonal_val . self::SQL_LIKE_CHAR;
		elseif ( $type == self::XLIKE )
		return self::SQL_LIKE_CHAR . $origonal_val;
		elseif ( $type == self::LIKEX )
		return $origonal_val . self::SQL_LIKE_CHAR ;
		else
		{
			throw new Exception  ( "createSqlLike can only handle types 0 | 1 | 2. Unknown type [". $type . "]" );
		}

	}

	// store all the previous data for the column on the criteria
	// this will accumulate to the correct string once the whole filter is created
	private static function getAllKeywordsForColumn ( $criteria , $column_arr )
	{
		$column = "";
		foreach ( $column_arr as $col )	{			$column .= $col;		}
	
		if ( isset ( $criteria->keywordsForColumn ) )
		{
			if ( isset ( $criteria->keywordsForColumn[$column] ) )
				$against_clause = @$criteria->keywordsForColumn[$column];
			else 
				return "";
			return $against_clause;
		}
		else
		{
			return "";
		}
	}
	
	private static function setAllKeywordsForColumn ( $criteria , $column_arr , $keywords )
	{
		$column = "";
		foreach ( $column_arr as $col )	{			$column .= $col;		}
		
		if ( isset ( $criteria->keywordsForColumn ) )
		{
			if ( isset ( $criteria->keywordsForColumn[$column] ) )
				$against_clause = @$criteria->keywordsForColumn[$column];
			else
				$against_clause = "";
			$criteria->keywordsForColumn[$column] = $against_clause . " " . $keywords;
		}
		else
		{
			$criteria->keywordsForColumn = array( $column => $keywords );
		}
	}	
	
	public function setAdvancedSearch(AdvancedSearchFilter $advancedSearch)
	{
		$this->advancedSearch = $advancedSearch;
	}	
	
	/**
	 * @return AdvancedSearchFilter
	 */
	public function getAdvancedSearch()
	{
		return $this->advancedSearch;
	}
	
	public function addAdvancedSearchToXml(SimpleXMLElement &$xmlElement)
	{
		if(!is_object($this->advancedSearch) || !$this->advancedSearch instanceof AdvancedSearchFilter)
			return;
		
		$advancedXmlElement = $xmlElement->addChild('advancedSearch');
		$advancedXmlElement->addAttribute('type', get_class($this->advancedSearch));
		
		$this->advancedSearch->addToXml($advancedXmlElement);
	}

	public function fillObjectFromXml ( SimpleXMLElement $simple_xml_node , $prefix_to_add , $exclude_params=null )
	{
		if(!is_array($exclude_params))
			$exclude_params = array();
			
		$exclude_params[] = 'advancedSearch';
		$set_field_count = parent::fillObjectFromXml($simple_xml_node, $prefix_to_add, $exclude_params);

		if(isset($simple_xml_node->advancedSearch))
		{
			$attr = $simple_xml_node->advancedSearch->attributes();
			if(isset($attr['type']) && class_exists($attr['type']))
			{
				$type = (string) $attr['type'];
				KalturaLog::debug("Advanced Search type[$type] and value[" . $simple_xml_node->advancedSearch->asXML() . "]");
				$this->advancedSearch = new $type();
				$this->advancedSearch->fillObjectFromXml($simple_xml_node->advancedSearch);
			}
			$set_field_count++;
		}

		return $set_field_count;
	}
}
?>
