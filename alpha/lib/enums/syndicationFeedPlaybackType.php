<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface syndicationFeedPlaybackType extends BaseEnum
{
	const SERVE_FLAVOR = 0;
	const PLAY_MANIFEST = 1;
	const PLAY_SERVER_MANIFEST = 2;
}