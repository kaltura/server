<?php
interface entryType extends BaseEnum
{
	const AUTOMATIC = -1;
	const MEDIA_CLIP = 1;
	const MIX = 2;
	const PLAYLIST = 5;
	const DATA = 6;
	const LIVE_STREAM = 7;
	const DOCUMENT = 10;
}
