<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface ReportType extends BaseEnum
{
   /**
    * same as myReportsMgr::REPORT_TYPE_...
    *
    */
   const TOP_CONTENT = 1;
   const CONTENT_DROPOFF = 2;
   const CONTENT_INTERACTIONS = 3;
   const MAP_OVERLAY = 4;
   const TOP_CONTRIBUTORS = 5;
   const TOP_SYNDICATION = 6;
   const CONTENT_CONTRIBUTIONS = 7;
//	const WIDGETS_STATS = 8;
//	const ADMIN_CONSOLE = 10;		// shouldn't be accessable to users through the API
   const USER_ENGAGEMENT = 11;
   const SPECIFIC_USER_ENGAGEMENT = 12;
   const USER_TOP_CONTENT = 13;
   const USER_CONTENT_DROPOFF = 14;
   const USER_CONTENT_INTERACTIONS = 15;
   const APPLICATIONS = 16;
   const USER_USAGE = 17;
   const SPECIFIC_USER_USAGE = 18;
   const PARTNER_USAGE = 201;
   const VAR_USAGE = 19;
   const TOP_CREATORS = 20;
   const PLATFORMS = 21;
   const OPERATING_SYSTEM = 22;
   const BROWSERS = 23;
   const LIVE = 24;
   const TOP_PLAYBACK_CONTEXT = 25;
   const VPAAS_USAGE = 26;
   const ENTRY_USAGE = 27;
}