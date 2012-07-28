<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

/**
 * @package Scheduler
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationTemplateStatus
{
	const DISABLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationEventObjectType
{
	const ENTRY = "1";
	const CATEGORY = "2";
	const FLAVORASSET = "4";
	const THUMBASSET = "5";
	const KUSER = "8";
	const ACCESSCONTROL = "9";
	const BATCHJOB = "10";
	const BULKUPLOADRESULT = "11";
	const CATEGORYKUSER = "12";
	const CONTROLPANELCOMMAND = "13";
	const CONVERSIONPROFILE2 = "14";
	const FLAVORPARAMS = "15";
	const FLAVORPARAMSCONVERSIONPROFILE = "16";
	const FLAVORPARAMSOUTPUT = "17";
	const GENERICSYNDICATIONFEED = "18";
	const KUSERTOUSERROLE = "19";
	const PARTNER = "20";
	const PERMISSION = "21";
	const PERMISSIONITEM = "22";
	const PERMISSIONTOPERMISSIONITEM = "23";
	const SCHEDULER = "24";
	const SCHEDULERCONFIG = "25";
	const SCHEDULERSTATUS = "26";
	const SCHEDULERWORKER = "27";
	const STORAGEPROFILE = "28";
	const SYNDICATIONFEED = "29";
	const THUMBPARAMS = "31";
	const THUMBPARAMSOUTPUT = "32";
	const UPLOADTOKEN = "33";
	const USERLOGINDATA = "34";
	const USERROLE = "35";
	const WIDGET = "36";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationEventType
{
	const BATCH_JOB_STATUS = "1";
	const OBJECT_ADDED = "2";
	const OBJECT_CHANGED = "3";
	const OBJECT_COPIED = "4";
	const OBJECT_CREATED = "5";
	const OBJECT_DATA_CHANGED = "6";
	const OBJECT_DELETED = "7";
	const OBJECT_ERASED = "8";
	const OBJECT_READY_FOR_REPLACMENT = "9";
	const OBJECT_SAVED = "10";
	const OBJECT_UPDATED = "11";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationTemplateOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationTemplateType
{
	const EMAIL = "emailNotification.Email";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaEventCondition extends KalturaObjectBase
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationTemplate extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var KalturaEventNotificationTemplateType
	 * @insertonly
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var KalturaEventNotificationTemplateStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Define that the template could be dispatched manually from the API
	 * 	 
	 *
	 * @var bool
	 */
	public $manualDispatchEnabled = null;

	/**
	 * Define that the template could be dispatched automatically by the system
	 * 	 
	 *
	 * @var bool
	 */
	public $automaticDispatchEnabled = null;

	/**
	 * Define the event that should trigger this notification
	 * 	 
	 *
	 * @var KalturaEventNotificationEventType
	 */
	public $eventType = null;

	/**
	 * Define the object that raied the event that should trigger this notification
	 * 	 
	 *
	 * @var KalturaEventNotificationEventObjectType
	 */
	public $eventObjectType = null;

	/**
	 * Define the conditions that cause this notification to be triggered
	 * 	 
	 *
	 * @var array of KalturaEventCondition
	 */
	public $eventConditions;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventFieldCondition extends KalturaEventCondition
{
	/**
	 * The field to be evaluated at runtime
	 * 	 
	 *
	 * @var KalturaBooleanField
	 */
	public $field;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationDispatchJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $templateId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaEventNotificationTemplateBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaEventNotificationTemplateType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var KalturaEventNotificationTemplateStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationTemplateFilter extends KalturaEventNotificationTemplateBaseFilter
{

}


/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationTemplateService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplate");
		return $resultObject;
	}
}
/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEventNotificationClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaEventNotificationTemplateService
	 */
	public $eventNotificationTemplate = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->eventNotificationTemplate = new KalturaEventNotificationTemplateService($client);
	}

	/**
	 * @return KalturaEventNotificationClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaEventNotificationClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'eventNotificationTemplate' => $this->eventNotificationTemplate,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'eventNotification';
	}
}

