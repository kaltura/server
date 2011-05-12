<?php

/*
 * 	public $exists;
	public $full_path = NULL ;
	public $name = NULL ;
	public $size = NULL ;
	public $timestamp = NULL ;
	public $ext = NULL ;
	public $content = NULL;
	
 */
function dumpLogData( kFileData $d )
{
	$file_timestamp = $d->raw_timestamp;
	$too_old = ( time() - $file_timestamp > 300000 ); // 5 minutes

	$content = htmlentities($d->content);
	$content = str_replace ( "\n" , "<br>" ,$content );
	$content = preg_replace( "/{(.*?)}/" , '<span style="color:blue">{$1}</span>' , $content );
	
	$time = $too_old ? '<span style="color:red; font-size:14px">' . $d->timestamp . '</span>' : $d->timestamp;
	echo "<b>" . $d->full_path . " [" . $d->size . "] " . $time . "</b><br>" .$content ;
}
?>
<a href="/index.php/system/login?exit=true">logout</a> Time on Machine: <?php echo date ( "Y-m-d H:i:s." , time() ) ?>
<br>

<div style="font-family: arial;font-size: 12px">
BatchClient Log:<br>
<div style="text-indent: 20px">
<?php foreach ( $batch_client as $file_data )
{
	dumpLogData( $file_data );
	echo "<br>";
}
?>
</div>

BatchServer Log:<br>
<div style="text-indent: 20px">
<?php foreach ( $batch_server as $file_data )
{
	dumpLogData( $file_data );
	echo "<br>";
}
?>
</div>

BatchEmail Log:<br>
<div style="text-indent: 20px">
<?php foreach ( $batch_email as $file_data )
{
	dumpLogData( $file_data );
	echo "<br>";
}
?>
</div>

BatchImport Log:<br>
<div style="text-indent: 20px">
<?php foreach ( $batch_import as $file_data )
{
	dumpLogData( $file_data );
	echo "<br>";
}
?>
</div>
</div>

