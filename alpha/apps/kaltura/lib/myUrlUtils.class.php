<?php
class myUrlUtils
{
	public static function encodeUrl($url)
	{
		return str_replace(array('?', '|', '*', '\\', '/' , '>' , '<', '&', '[', ']'), '_', $url);
	}
}
