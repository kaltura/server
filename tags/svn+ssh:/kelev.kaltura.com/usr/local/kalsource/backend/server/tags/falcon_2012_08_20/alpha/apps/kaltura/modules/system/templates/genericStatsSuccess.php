<?php

function formatData ( $tuple )
{
	$start_date = strtotime( $tuple[1]  );
	$end_date = strtotime( $tuple[2]  );
	$diff = 1 + (int)(( $end_date - $start_date ) / 86400); // add 1 to the result because the end date is actually inclusive 
	$str = $tuple[0] . " [$diff] (" . strftime( "%Y-%m-%d" , $start_date ). "->" . strftime( "%Y-%m-%d" , $end_date) . ")";
	return $str;
}
?>
<div style='font-family:calibri; font-size:14px'>
Unique Visitors (took <?php echo $time ?> seconds to calculate)<br>
<table border='1px' cellspacing='0px' cellpadding='7px'>
	<tr>
		<td>Type</td><td>7 day</td><td>30 days</td><td>180 days</td>
	</tr>
	<tr>
		<td>Cookie</td>
		<td><?php echo formatData ( $cookies_7 )?></td>
		<td><?php echo formatData ( $cookies_30 )?></td>
		<td><?php echo formatData ( $cookies_180 )?></td>
	</tr>
	<tr>
		<td>ip</td>
		<td><?php echo formatData ( $ip_7 )?></td>
		<td><?php echo formatData ( $ip_30 )?></td>
		<td><?php echo formatData ( $ip_180 )?></td>
	</tr>
	
</table>
</div>