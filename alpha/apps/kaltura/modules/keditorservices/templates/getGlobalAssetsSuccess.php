<?php
// create xml for each entry is entry_list
if ( $debug ) { echo "Result<br><textarea cols=100 rows=50>"; }
?>

<assets>
<?php
foreach ( $entry_list as $entry )
{
	$is_ready = $entry->getStatus() == entry::ENTRY_STATUS_READY;
	$data = $entry->getDataPath();

	echo "\t" .  baseObjectUtils::objToXml ( $entry , array ( 'id' , 'name' , 'type' => 'media_type'  ) , 
		'asset' , true ,
		array ( 'url' => $data , 'ready' => $is_ready , 'thumbnail_path' => $entry->getThumbnailPath() ));

}
?>
</assets>

<?php if ( $debug ) { echo "</textarea>" ; } ?>