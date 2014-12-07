<?php
 
/**
 * @package api
 * @subpackage model.enum
 */
class KalturaLiveReportExportParams extends KalturaObject
{	
	/**
	 * @var string
	 **/
	public $entryIds;
	
	/**
	 * @var string
	 */
	public $recpientEmail;
	
	/**
	 * Time zone offset in minutes (between client to UTC)
	 *
	 * @var int
	 */
	public $timeZoneOffset = 0;
	
}