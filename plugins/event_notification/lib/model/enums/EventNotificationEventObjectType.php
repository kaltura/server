<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.enum
 */ 
interface EventNotificationEventObjectType extends BaseEnum
{
    const ENTRY = 1;
    const CATEGORY = 2;
    const ASSET = 3;
    const FLAVORASSET = 4;
    const THUMBASSET = 5;
    const KUSER = 8;
    const ACCESSCONTROL = 9;
	const BATCHJOB = 10;
	const BULKUPLOADRESULT = 11;
	const CATEGORYKUSER = 12;
	const CONVERSIONPROFILE2 = 14;
	const FLAVORPARAMS = 15;
	const FLAVORPARAMSCONVERSIONPROFILE = 16;
	const FLAVORPARAMSOUTPUT = 17;
	const GENERICSYNDICATIONFEED = 18;
	const KUSERTOUSERROLE = 19;
	const PARTNER = 20;
	const PERMISSION = 21;
	const PERMISSIONITEM = 22;
	const PERMISSIONTOPERMISSIONITEM = 23;
	const SCHEDULER = 24;
	const SCHEDULERCONFIG = 25;
	const SCHEDULERSTATUS = 26;
	const SCHEDULERWORKER = 27;
	const STORAGEPROFILE = 28;
	const SYNDICATIONFEED = 29;
	const THUMBPARAMS = 31;
	const THUMBPARAMSOUTPUT = 32;
	const UPLOADTOKEN = 33;
	const USERLOGINDATA = 34;
	const USERROLE = 35;
	const WIDGET = 36;
	const CATEGORYENTRY = 37;
	const LIVE_STREAM = 38;
	const SERVER_NODE = 39;
}