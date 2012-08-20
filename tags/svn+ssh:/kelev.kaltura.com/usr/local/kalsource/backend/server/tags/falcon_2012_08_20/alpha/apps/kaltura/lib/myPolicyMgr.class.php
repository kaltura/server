<?php
/**
 * Will help find the policy for features deppending on the partner, kshow, entry , kuser and other dynamic parameters 
 */
class myPolicyMgr
{
	/*
	 * method is assumed to ge a getter for the property
	 * - use this function for when all objects have the same property & getter
	 */
	public static function getPolicyFor ( $property , $obj1 /* ... */ )
	{
		$args = func_get_args();
		array_shift($args); // skip $$property
		$method = "get{$property}";
		$result = null;
		$i = 1;
		foreach ( $args as $objs_or_values )
		{
			if ( is_object( $objs_or_values ) )
			{
				$result = @call_user_func_array(array ( $objs_or_values , $method ), null );
			}
			else
			{
				// primative - check for its value
				$result = $objs_or_values;
			}
			if ( $result !== null && $result !== "" )	
			{	
				KalturaLog::log ( __METHOD__ . " property [$property] found for arg [$i]" );
				return $result;
			}
			++$i;
		}
		 
		return $result;
	}
}
?>