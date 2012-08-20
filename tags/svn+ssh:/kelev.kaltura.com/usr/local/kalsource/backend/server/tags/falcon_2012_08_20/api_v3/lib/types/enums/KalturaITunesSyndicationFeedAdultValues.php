<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaITunesSyndicationFeedAdultValues extends KalturaStringEnum
{
	// applied on <itunes:explicit>
	const YES = "yes"; // an "explicit" parental advisory graphic will appear next to your podcast artwork
	
	const NO = "no"; // you see no indicator in the podcast
	
	const CLEAN = "clean"; // the parental advisory type is considered Clean
}