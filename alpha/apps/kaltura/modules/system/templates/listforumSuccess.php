<?php
include_once ( "_header.php" );
?>

Forum posts (<?php echo $count ?>)<br> 
<form>
	ID <input name="id" value="<?php echo $id ?>"> Tags <input name="tags" value="<?php echo $tags ?>"> 
	Page <input size=3 name="page" value="<?php echo $page ?>"> 
	Encode <input type="checkbox" name="encode" value="1" <?php echo ( $encode ? "checked" : "" ) ?>>
	<input type="submit" name="submit" value="submit"> 
</form>
<table border=1 cellspacing=0 	style="font-family:verdana; font-size:12px">
<?php echo investigate::printBBPostHeader( );
foreach ( $list as $post ) {
	echo investigate::printBBPost( $post , true , $encode );
}

?>
</table>


<script>
function goto ( post_id )
{
	handle = window.open( "<?php echo url_for ( "/system/editforum") . "?post_id=" ?>
		" + post_id , "editpost" , "status=0,toolbar=0, height=550 , width=1000" );
	handle.focus();
}
</script>