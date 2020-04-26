<?php
/**
 * @package Core
 * @subpackage model.enum
 */

interface PlaylistType extends BaseEnum
{
	const DYNAMIC = entry::ENTRY_MEDIA_TYPE_XML;
	const STATIC_LIST = entry::ENTRY_MEDIA_TYPE_TEXT;
	const EXTERNAL = entry::ENTRY_MEDIA_TYPE_GENERIC_1;
	const PATH = entry::ENTRY_MEDIA_TYPE_GENERIC_2;
}