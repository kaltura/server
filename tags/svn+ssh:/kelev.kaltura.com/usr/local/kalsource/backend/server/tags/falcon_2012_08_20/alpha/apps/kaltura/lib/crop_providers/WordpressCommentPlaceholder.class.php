<?php
function executeCropProvider($source_file, $target_file, $width, $height, $crop_type, $bgcolor, $force_jpeg)
{
	$content_path = myContentStorage::getFSContentRootPath();
	list ($sourcewidth, $sourceheight, $type, $attr, $thumbail_image) = myFileConverter::createImageByFile($target_file);
	
	$wordpress_placeholder_image_path = $content_path."content/templates/wordpress/wordpress_comment_player_placeholder.gif";
	list ($placeholder_width, $placeholder_height, $type, $attr, $place_holder_image) = myFileConverter::createImageByFile($wordpress_placeholder_image_path);
	
	$wordpress_placeholder_image_path = $content_path."content/templates/wordpress/wordpress_comment_play_overlay.png";
	list ($overlay_width, $overlay_height, $type, $attr, $play_overlay_image) = myFileConverter::createImageByFile($wordpress_placeholder_image_path);
	
	$width = 240;
	$height = 180;
	$im = imagecreatetruecolor($placeholder_width, $placeholder_height);
	imagecopy($im, $place_holder_image, 0, 0, 0, 0, $placeholder_width, $placeholder_height);
	//imagecopyresampled($im, $thumbail_image, 5, 30, 0, 0, $width, $height, $width, $height);
	// copy with opacity change
	imagecopymerge($im, $thumbail_image, 5 + ($width - $sourcewidth) / 2, 30 + ($height - $sourceheight) / 2, 0, 0, $sourcewidth, $sourceheight, 50);
	imagecopy($im, $play_overlay_image, ($placeholder_width - $overlay_width) / 2, ($placeholder_height - $overlay_height) / 2, 0, 0, $overlay_width, $overlay_height);
	
	imagedestroy($place_holder_image);
	imagedestroy($thumbail_image);
	imagedestroy($play_overlay_image);
	imagejpeg($im, $target_file, 90);
	imagedestroy($im);
}

?>