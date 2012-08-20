<?php
// create xml for each entry is entry_list
if ( $debug ) { echo "Result<br><textarea cols=100 rows=50>"; }
?>

<assets>
<?php
assetsUtils::createAssets ( $kshow_entry_list , "show" );
assetsUtils::createAssets ( $kuser_entry_list , "user" );
?>
</assets>

<?php if ( $debug ) { echo "</textarea>" ; } ?>