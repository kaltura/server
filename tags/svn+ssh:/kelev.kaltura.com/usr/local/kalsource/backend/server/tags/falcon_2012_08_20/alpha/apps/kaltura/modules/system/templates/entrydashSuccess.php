<?php

?>
<a href="/index.php/system/login?exit=true">logout</a> Time on Machine: <?php echo date ( "Y-m-d H:i:s." , time() ) ?>
<br>

Entry dashboard
<br>
Last (<?php echo $conversion_count ?>) conversions:
<br>
<table border=1 cellspacing=0 	style="font-family:verdana; font-size:12px">
	<?php echo investigate::printConversionHeader() ; ?>
	<?php foreach ( $conversions as $conversion ) { 
			echo investigate::printConversion( $conversion , true );
	} ?>
</table>

<br>
Last (<?php echo $import_count ?>) imports:
<br>
<table border=1 cellspacing=0
	style="font-family:verdana; font-size:12px">
<?php echo investigate::printBatchjobHeader() ?>
	<?php foreach ( $imports as $bj ) {
		echo investigate::printBatchjob( $bj ,true );
	} ?>
</table>