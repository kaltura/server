<?php
class dashboardUtils
{
	private static function option ( $current_value , $value , $text )
	{
		$str = "<option value=\"$value\" " . ( $current_value==$value ? "selected=\"selected\"" : "" ) . ">$text</option>";
		return $str;
	}

	private static function options ( $current_value , array $arr )
	{
		$str = "";
		foreach ( $arr as $name => $value )
		{
			$str .= self::option( $current_value , $name , $value );
		}
		return $str;
	}
	
	public static function partnerOptions ( $current_value  )
	{
		$options[-1] = "All";
		
		$c = new Criteria();
		$partners = PartnerPeer::doSelect($c);
		foreach($partners as $partner)
			$options[$partner->getId()] = $partner->getPartnerName();
			
		return dashboardUtils::options ( $current_value , $options );
	}
}
