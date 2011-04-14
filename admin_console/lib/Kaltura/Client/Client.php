<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Client extends Kaltura_Client_ClientBase
{
	/**
	 * @var string
	 */
	protected $apiVersion = '3.1.2';

	/**
	 * batch service lets you handle different batch process from remote machines.
	 * As oppesed to other ojects in the system, locking mechanism is critical in this case.
	 * For this reason the GetExclusiveXX, UpdateExclusiveXX and FreeExclusiveXX actions are important for the system's intergity.
	 * In general - updating batch object should be done only using the UpdateExclusiveXX which in turn can be called only after 
	 * acuiring a batch objet properly (using  GetExclusiveXX).
	 * If an object was aquired and should be returned to the pool in it's initial state - use the FreeExclusiveXX action 
	 * 
	 * @var Kaltura_Client_BatchcontrolService
	 */
	public $batchcontrol = null;

	/**
	 * Retrieve information and invoke actions on Flavor Asset
	 * @var Kaltura_Client_FlavorAssetService
	 */
	public $flavorAsset = null;

	/**
	 * Add & Manage Flavor Params
	 * @var Kaltura_Client_FlavorParamsService
	 */
	public $flavorParams = null;

	/**
	 * batch service lets you handle different batch process from remote machines.
	 * As oppesed to other ojects in the system, locking mechanism is critical in this case.
	 * For this reason the GetExclusiveXX, UpdateExclusiveXX and FreeExclusiveXX actions are important for the system's intergity.
	 * In general - updating batch object should be done only using the UpdateExclusiveXX which in turn can be called only after 
	 * acuiring a batch objet properly (using  GetExclusiveXX).
	 * If an object was aquired and should be returned to the pool in it's initial state - use the FreeExclusiveXX action 
	 * 
	 * @var Kaltura_Client_JobsService
	 */
	public $jobs = null;

	/**
	 * partner service allows you to change/manage your partner personal details and settings as well
	 * @var Kaltura_Client_PartnerService
	 */
	public $partner = null;

	/**
	 * PermissionItem service lets you create and manage permission items
	 * @var Kaltura_Client_PermissionItemService
	 */
	public $permissionItem = null;

	/**
	 * Permission service lets you create and manage user permissions
	 * @var Kaltura_Client_PermissionService
	 */
	public $permission = null;

	/**
	 * Session service
	 * @var Kaltura_Client_SessionService
	 */
	public $session = null;

	/**
	 * Retrieve information and invoke actions on Thumb Asset
	 * @var Kaltura_Client_ThumbAssetService
	 */
	public $thumbAsset = null;

	/**
	 * Add & Manage Thumb Params
	 * @var Kaltura_Client_ThumbParamsService
	 */
	public $thumbParams = null;

	/**
	 * UiConf service lets you create and manage your UIConfs for the various flash components
	 * This service is used by the KMC-ApplicationStudio
	 * @var Kaltura_Client_UiConfService
	 */
	public $uiConf = null;

	/**
	 * UserRole service lets you create and manage user roles
	 * @var Kaltura_Client_UserRoleService
	 */
	public $userRole = null;

	/**
	 * Manage partner users on Kaltura's side
	 * The userId in kaltura is the unique Id in the partner's system, and the [partnerId,Id] couple are unique key in kaltura's DB
	 * @var Kaltura_Client_UserService
	 */
	public $user = null;

	/**
	 * Kaltura client constructor
	 *
	 * @param Kaltura_Client_Configuration $config
	 */
	public function __construct(Kaltura_Client_Configuration $config)
	{
		parent::__construct($config);
		
		$this->batchcontrol = new Kaltura_Client_BatchcontrolService($this);
		$this->flavorAsset = new Kaltura_Client_FlavorAssetService($this);
		$this->flavorParams = new Kaltura_Client_FlavorParamsService($this);
		$this->jobs = new Kaltura_Client_JobsService($this);
		$this->partner = new Kaltura_Client_PartnerService($this);
		$this->permissionItem = new Kaltura_Client_PermissionItemService($this);
		$this->permission = new Kaltura_Client_PermissionService($this);
		$this->session = new Kaltura_Client_SessionService($this);
		$this->thumbAsset = new Kaltura_Client_ThumbAssetService($this);
		$this->thumbParams = new Kaltura_Client_ThumbParamsService($this);
		$this->uiConf = new Kaltura_Client_UiConfService($this);
		$this->userRole = new Kaltura_Client_UserRoleService($this);
		$this->user = new Kaltura_Client_UserService($this);
	}
	
}
