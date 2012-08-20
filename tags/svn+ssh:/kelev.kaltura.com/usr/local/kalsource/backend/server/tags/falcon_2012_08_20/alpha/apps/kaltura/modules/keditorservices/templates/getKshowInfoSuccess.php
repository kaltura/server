<?php
require_once ( "baseObjectUtils.class.php" );

// create xml for each entry is entry_list
if ( $debug ) { echo "Result<br><textarea cols=100 rows=50>"; }
?>
<xml>
	<?php echo baseObjectUtils::objToXml ( $kshow , array ( 'id' , 'name', "show_entry_id" ) , 'kshow' , true , 
		array ( "entry_name" => $entry->getName(),
				"thumbnail_path" => $thumbnail , 
				"can_publish" => $can_publish ) ) ?>
	<?php echo baseObjectUtils::objToXml ( $producer , array ( 'id' , 'screen_name' ) , 'producer' , true ) ?>
	<?php echo baseObjectUtils::objToXml ( $editor , array ( 'id' , 'screen_name' ) , 'editor' , true ) ?>
	
	<?php 
	echo "<versions>";
	foreach ( $show_versions as $version_info )
	{
		echo "<version_info version=\"" . $version_info[3] . "\" date=\"" . strftime( "%d/%m/%y %H:%M:%S" , $version_info[2] ). "\"/>\n"; 
	}
	echo "</versions>";
	?>
</xml>

<?php if ( $debug ) { echo "</textarea>" ; } ?>