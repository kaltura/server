<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface EntrySourceType extends BaseEnum
{
    //TODO: duplicates + missing values are in entry::ENTRY_MEDIA_SOURCE_* consts
    
    const FILE = 1;
	const WEBCAM = 2;
	const URL = 5;
	const SEARCH_PROVIDER = 6;

	const AKAMAI_LIVE = 29;
	
}