<?php
/**
 * @package Core
 * @subpackage lib.paging
 */
class mySmartPagerRenderer
{
	const PAGES_TO_DISPLAY = 5;			// the number of pages to display between prev & next
	const MID_PAGE_TO_DISPLAY = 2 ;

/* output example:
 * 
 * 						<li class="disabled">&laquo; Previous</li>
 *						<li class="active">1</li>
 *						<li>2</li>
 *						<li>3</li>
 *						<li>4</li>
 *						<li>5</li>
 *						<li>Next &raquo;</li>
 */	


	/**
	 * @param int  $number_of_pages
	 * @param int  $current_page
	 * @return string Html of the pager
	 */
	public static function createHtmlPager( $number_of_pages , $current_page, $pager_id = null )
	{
//		echo "number_of_pages: $number_of_pages, current_page: $current_page\n";
		// TODO - what should be displayed when no results $number_of_pages = 0 / 1  ?
		$str = '';

		// start
		$str .= "<li ";
		$str .=  $current_page <= 1 ?
			"value=\"0\" class=\"disabled bold\"" : 
			"value=\"" . ( 1 ) . "\" class=\"passive bold\"";
		$str .= ">&laquo;</li>";
		
		// prev
		$str .= "<li ";
		$str .=  $current_page <= 1 ?
			"value=\"0\" class=\"disabled bold\"" : 
			"value=\"" . ( $current_page - 1 ) . "\" class=\"passive bold\"";
		$str .= ">&lsaquo;</li>";
		
		// if possibe - the current_page should be placed in the middle
		$start_page = max ( 1 , $current_page - self::MID_PAGE_TO_DISPLAY );
		$end_page = min ( $start_page + self::PAGES_TO_DISPLAY , $number_of_pages + 1);
		
		if ( $end_page - $start_page < self::PAGES_TO_DISPLAY )
		{
			// readjust $start_page
			$start_page = max ( 1 , $end_page - self::PAGES_TO_DISPLAY );
		}
		
		for ( $p = $start_page ; $p <  $end_page ; ++$p )
		{
			$str .= "<li value=\"" . $p . "\" class=\"" . ( $p == $current_page ? "active" : "passive" ) . "\">" . $p . "</li>" ;
		}
		
		// next
		$str .= "<li ";
		$str .= $current_page >= $number_of_pages ? 
			"value=\"0\" class=\"disabled bold\"" : 
			"value=\"" . ( $current_page + 1 ) . "\" class=\"passive bold\"";
				$str .= ">&rsaquo;</li>";
		
		// end
		$str .= "<li ";
		$str .= $current_page >= $number_of_pages ? 
			"value=\"0\" class=\"bold disabled\"" : 
			"value=\"" . ( $number_of_pages ) . "\" class=\"passive bold\"";
		$str .= ">&raquo;</li>";

		if ($pager_id)
			return '<ul id="'.$pager_id.'" class="pager unselectable">'.$str.'</ul>';
		else
			return $str;
	}
}
?>