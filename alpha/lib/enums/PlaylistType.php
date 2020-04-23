<?php
/**
 * @package Core
 * @subpackage model.enum
 */

interface PlaylistType extends BaseEnum
{
	const DYNAMIC = 10;//entry::ENTRY_MEDIA_TYPE_XML;
	const STATIC_LIST = 3;//entry::ENTRY_MEDIA_TYPE_TEXT;
	const EXTERNAL = 101;//entry::ENTRY_MEDIA_TYPE_GENERIC_1;
	const PATH = 102;//entry::ENTRY_MEDIA_TYPE_GENERIC_2;
}