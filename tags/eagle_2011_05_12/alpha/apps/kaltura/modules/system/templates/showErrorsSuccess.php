<?php

?>
<table border=1 cellspacing=0
	style="font-family:verdana; font-size:12px">
	<tr>
		<td>Id</td>
		<td>Name</td>
		<td>Kshow Id</td>
		<td>Kuser Id</td>
		<td>Status</td>
		<td>Type</td>
		<td>Media Type</td>
		<td>Appears In</td>
		<td>Thumbnail</td>
		<td>Data</td>
		<td>Duration</td>		
		<td>Created At</td>
		<td>Updated At</td>
	</tr>

	<tr>
		<td colspan=13>Error converting (<?php echo count ( $error_converting ) ?>)</td>
	</tr>	
	
<?php foreach 	( $error_converting as $entry ) {	echo investigate::printEntry ( $entry , true ) ;} ?>
	
	<tr>
		<td colspan=13>Too long... <?php echo "(" . count ( $error_waiting_too_long ) . ") started before [$start_date] and didn't yet end" ?></td>
	</tr>	
<?php foreach 	( $error_waiting_too_long as $entry ) {	echo investigate::printEntry ( $entry , true ) ;} ?>
	
</table>