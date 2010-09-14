<?php
require_once ( "baseObjectUtils.class.php" );

if (!$kuser)
	die;
	
$user_name = $kuser->getScreenName();
$show_klogo = 1;
if($entry)
{
	$user_name = $entry->getSubpId() == 10003 ? "Facelift" : $kuser->getScreenName();
	$show_klogo = ($widget_type == 3 && $entry->getPartnerId() == 18) ? 0 : 1; // dont show the kaltura logo while playing on wikieducator
}

?>
<xml>
	<?php echo baseObjectUtils::objToXml ( $entry , array ( 'id' , 'name', 'kshow_id' , 'tags', 'media_type', 'length_in_msecs', 'status' ) , 'entry' , true , 
		array ( "thumbnail_path" => $thumbnail , "user_name" => $user_name,
			"message" => $message,
			"server_time" => time(),
			"kshow_category" => $kshow_category,
			"kshow_name" => $kshow_name,
			"kshow_description" => $kshow_description,
			"kshow_tags" => $kshow_tags,
			"generic_embed_code" => $generic_embed_code, "myspace_embed_code" => $myspace_embed_code,
			"share_url" => $share_url,
			"show_klogo" => $show_klogo) ) ; ?>
</xml>