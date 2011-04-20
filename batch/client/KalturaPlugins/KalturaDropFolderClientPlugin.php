<?php
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
class KalturaDropFolderContentFileHandlerMatchPolicy
{
	const ADD_AS_NEW = 1;
	const MATCH_EXISTING_OR_ADD_AS_NEW = 2;
	const MATCH_EXISTING_OR_KEEP_IN_FOLDER = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFileDeletePolicy
{
	const MANUAL_DELETE = 1;
	const AUTO_DELETE = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFileHandlerType
{
	const CONTENT = "1";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFileOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const FILE_NAME_ASC = "+fileName";
	const FILE_NAME_DESC = "-fileName";
	const FILE_SIZE_ASC = "+fileSize";
	const FILE_SIZE_DESC = "-fileSize";
	const FILE_SIZE_LAST_SET_AT_ASC = "+fileSizeLastSetAt";
	const FILE_SIZE_LAST_SET_AT_DESC = "-fileSizeLastSetAt";
	const PARSED_SLUG_ASC = "+parsedSlug";
	const PARSED_SLUG_DESC = "-parsedSlug";
	const PARSED_FLAVOR_ASC = "+parsedFlavor";
	const PARSED_FLAVOR_DESC = "-parsedFlavor";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFileStatus
{
	const UPLOADING = 1;
	const PENDING = 2;
	const WAITING = 3;
	const HANDLED = 4;
	const IGNORE = 5;
	const DELETED = 6;
	const PURGED = 7;
	const NO_MATCH = 8;
	const ERROR_HANDLING = 9;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderStatus
{
	const DISABLED = 0;
	const ENABLED = 1;
	const DELETED = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderType
{
	const LOCAL = "1";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDropFolderBaseFilter extends KalturaFilter
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
	 * @var string
	 */
	public $nameLike = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderType
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
	 * @var KalturaDropFolderStatus
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
	public $ingestionProfileIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ingestionProfileIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dcEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $pathLike = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileHandlerType
	 */
	public $fileHandlerTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileHandlerTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatternsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatternsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatternsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

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
class KalturaDropFolderFilter extends KalturaDropFolderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFileHandlerConfig extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaDropFolderFileHandlerType
	 */
	public $handlerType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolder extends KalturaObjectBase
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
	public $description = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $ingestionProfileId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dc = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $path = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSizeCheckInterval = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileDeletePolicy
	 */
	public $fileDeletePolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $autoFileDeleteDays = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileHandlerType
	 */
	public $fileHandlerType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatterns = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileHandlerConfig
	 */
	public $fileHandlerConfig;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

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


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaDropFolder
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFile extends KalturaObjectBase
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
	 * @var int
	 * @insertonly
	 */
	public $dropFolderId = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $fileName = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSize = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $fileSizeLastSetAt = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedSlug = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedFlavor = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorDescription = null;

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


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDropFolderFileBaseFilter extends KalturaFilter
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
	 * @var int
	 */
	public $dropFolderIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dropFolderIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNameLike = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileStatus
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
	 * @var string
	 */
	public $parsedSlugEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedSlugIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedSlugLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedFlavorEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedFlavorIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedFlavorLike = null;

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
class KalturaDropFolderFileFilter extends KalturaDropFolderFileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFileListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaDropFolderFile
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFileResource extends KalturaContentResource
{
	/**
	 * Id of the drop folder file object
	 *
	 * @var int
	 */
	public $dropFolderFileId = null;


}


/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listAction(KalturaDropFolderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderListResponse");
		return $resultObject;
	}
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderFileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function add(KalturaDropFolderFile $dropFolderFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFile", $dropFolderFile->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	function get($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	function update($dropFolderFileId, KalturaDropFolderFile $dropFolderFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->addParam($kparams, "dropFolderFile", $dropFolderFile->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	function delete($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	function listAction(KalturaDropFolderFileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFileListResponse");
		return $resultObject;
	}

	function ignore($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "ignore", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}
}
/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDropFolderClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaDropFolderClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaDropFolderService
	 */
	public $dropFolder = null;

	/**
	 * @var KalturaDropFolderFileService
	 */
	public $dropFolderFile = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->dropFolder = new KalturaDropFolderService($client);
		$this->dropFolderFile = new KalturaDropFolderFileService($client);
	}

	/**
	 * @return KalturaDropFolderClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaDropFolderClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'dropFolder' => $this->dropFolder,
			'dropFolderFile' => $this->dropFolderFile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'dropFolder';
	}
}

