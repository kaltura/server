<?php
function executeCropProvider($source_file, $target_file, $width, $height, $crop_type, $bgcolor, $force_jpeg)
{
	$content_path = myContentStorage::getFSContentRootPath();
	list ($sourcewidth, $sourceheight, $type, $attr, $thumbail_image) = myFileConverter::createImageByFile($target_file);
	
	if ($width == 400 && $height == 300) // this is the large player placeholder
	{
		$wordpress_placeholder_image_path = $content_path."content/templates/wordpress/wordpress_large_player_placeholder.jpg";
		list ($sourcewidth, $sourceheight, $type, $attr, $place_holder_image) = myFileConverter::createImageByFile($wordpress_placeholder_image_path);
		imagecopyresampled($place_holder_image, $thumbail_image, 6, 36, 0, 0, $width, $height, $width, $height);
	}
	elseif ($width == 400 && $height == 225) // this is the large wide screen player place holder
	{
		$wordpress_placeholder_image_path = $content_path."content/templates/wordpress/wordpress_large_wide_screen_player_placeholder.jpg";
		list ($sourcewidth, $sourceheight, $type, $attr, $place_holder_image) = myFileConverter::createImageByFile($wordpress_placeholder_image_path);
		imagecopyresampled($place_holder_image, $thumbail_image, 6, 36, 0, 0, $width, $height, $width, $height);
	}
	else // this is the small player placeholder
	{
		$width = 240;
		$height = 180;
		$wordpress_placeholder_image_path = $content_path."content/templates/wordpress/wordpress_small_player_placeholder.jpg";
		list ($sourcewidth, $sourceheight, $type, $attr, $place_holder_image) = myFileConverter::createImageByFile($wordpress_placeholder_image_path);
		imagecopyresampled($place_holder_image, $thumbail_image, 6, 28, 0, 0, $width, $height, $width, $height);
	}
	
	imagedestroy($thumbail_image);
	imagejpeg($place_holder_image, $target_file, 90);
	imagedestroy($place_holder_image);
}

?>