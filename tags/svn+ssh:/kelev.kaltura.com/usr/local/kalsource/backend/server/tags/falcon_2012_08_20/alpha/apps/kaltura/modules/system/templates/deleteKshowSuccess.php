<?php
//$kshow_id = $kshow ? $kshow->getId() : "";
$band_id = $kshow ? $kshow->getIndexedCustomData1() : "";
$delete_text = $should_delete ? "Deleted " : "Will delete ";

$kshow_count = count ( $other_kshows_by_producer );

echo $error . "<br>";

$url = url_for ( "/system") . "/deleteKshow?kshow_id="; 
if ( $kshow_count > 1 )
{
	$str = "";
	foreach ( $other_kshows_by_producer as $other_kshow )
	{
		$str .= "<a href='$url" . $other_kshow->getId() ."'>" .$other_kshow->getId() . "</a> "; 
	}
	
	echo $str;
}

if ( $kuser_count > 1 )
{
	echo "There are [$kuser_count] results for [$kuser_name]. Bellow is displayed the first one.<br>You may want to better specify the screen name." ; 
}
?>
 
<form id="form1" method=get>
	kshow id: <input name="kshow_id" value="<?php echo $kshow_id ?>"> band id: <input name="band_id" value="<?php echo $band_id ?>">
	User name: <input name="kuser_name" value="<?php echo $kuser_name ?>">
	<input type="hidden" id="deleteme" name="deleteme" value="false">
	<input type="submit"  name="find" value="find">
</form>

<?php if ( !$kshow ) 
{
	if ( $kshow_id )
	{
		echo "Kshow id [" . $kshow_id . "] does not exist in the DB";
	}	
	return ;
}

?>

<?php if ( $kuser && $kshow_count < 2 ) 
{
	echo $delete_text . "kuser '" . $kuser->getScreenName() . "' [" . $kuser->getId()
	. "] which was created at " . $kuser->getCreatedAt() . " (" .  $kuser->getFormattedCreatedAt() . ")" ; 
} ?>
<br> 

<?php echo $delete_text . "'" . $kshow->getName() ."' [" . $kshow->getId() ."] with band id . " . $kshow->getIndexedCustomData1() . ":" ?>
<br>
<table>
<?php 
echo investigate::printKshowHeader();
echo investigate::printKshow( $kshow );
?>
</table>
<br>
and entries:<br>
<table>
<?php
echo investigate::printEntryHeader();
foreach ( $entries as $entry )
{
	echo investigate::printEntry( $entry );	
}
?>
</table>

<br>
<input type="button" name="Delete" value="Delete" onclick="deleteme()">

<script>
function deleteme()
{
<?php if ( $kshow_count ) { ?> 
	text = "kuser '<?php echo $kuser->getScreenName()?>' will not be deleted becuase he/she has (<?php echo $kshow_count ?>) kshows.'\n" + 
		"One of the kshows: kshow '<?php echo $kshow->getName() ?>' with all (<?php echo count ( $entries ) ?>) entries\n" +
			"????\n\n" +
			"Remember - this action is NOT reversible!!" ;
	
<?php } else { ?>
	text = "Do you really want to delete poor kuser '<?php echo $kuser->getScreenName()?>'\n" + 
		"and it's kshow '<?php echo $kshow->getName() ?>' with all (<?php echo count ( $entries ) ?>) entries\n" +
			"????\n\n" +
			"Remember - this action is NOT reversible!!" ;
<?php } ?>
	if ( confirm ( text ) )
	{
		text2 = "I'll ask again...\n\n" + text + "\n\n\n";
		if (  confirm ( text2) ) 
		{
			deleteImpl();
		}
	}
}

function deleteImpl()
{
	e = jQuery ( "#deleteme" );
	e.attr ("value", "true" ); 
	
	jQuery ( "#form1")[0].submit()
}
</script>
	