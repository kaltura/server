<?php

?>

<div style="margin:10px">
<form action="" method="post">
<?php
$i=0; 
foreach ( $embed_code_list as $embed_code ) 
{
?>
<div id="embed_<?php echo $i ?>_xml">
	<textarea style='width:75%; height:120px; ' id="embed_<?php echo $i ?>_xml" name="embed_<?php echo $i ?>_xml">
<?php  echo html_entity_decode( $embed_code )?>	
	</textarea>
</div>
<?php
$i++; 
} ?>

	<input type="submit" name="merge" value="merge">
</form>

<textarea style='width:75%; height:120px;'>
<?php echo $embed_merge ?>
</textarea>
<?php echo $embed_merge ?>
</div>