<?php

	
class ComcastMediaService extends ComcastClient
{
	const WSDL_URL = 'http://admin.theplatform.com/API/urn:service.wsdl';
	
	function __construct($username, $password)
	{
		parent::__construct(self::WSDL_URL, $username, $password);
	}
	
	
	/**
	 * 
	 * @param ComcastPermissionTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastPermissionSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastPermissionList
	 **/
	public function getPermissions(ComcastPermissionTemplate $template, ComcastQuery $query, ComcastPermissionSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:PermissionTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:PermissionSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getPermissions", $params, 'ComcastPermissionList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $directoryIDs
	 * @param string $userName
	 * @param string $password
	 * @param string $IPAddress
	 * 
	 * @return long
	 **/
	public function authenticate(array $directoryIDs, $userName, $password, $IPAddress)
	{
		$params = array();
		
		$params["directoryIDs"] = $this->parseParam($directoryIDs, 'ns12:IDSet');
		$params["userName"] = $this->parseParam($userName, 'xsd:string');
		$params["password"] = $this->parseParam($password, 'xsd:string');
		$params["IPAddress"] = $this->parseParam($IPAddress, 'xsd:string');

		$result = $this->doCall("authenticate", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPermissionList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addPermissions(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:PermissionList');

		$result = $this->doCall("addPermissions", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRoleTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastRoleSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastRoleList
	 **/
	public function getRoles(ComcastRoleTemplate $template, ComcastQuery $query, ComcastRoleSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:RoleTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:RoleSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getRoles", $params, 'ComcastRoleList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastUserTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastUserSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastUserList
	 **/
	public function getUsers(ComcastUserTemplate $template, ComcastQuery $query, ComcastUserSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:UserTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:UserSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getUsers", $params, 'ComcastUserList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMedia $media
	 * @param ComcastMediaFileList $mediaFiles
	 * @param ComcastAddContentOptions $options
	 * 
	 **/
	public function setContent(ComcastMedia $media, array $mediaFiles, ComcastAddContentOptions $options)
	{
		$params = array();
		
		$params["media"] = $this->parseParam($media, 'ns17:Media');
		$params["mediaFiles"] = $this->parseParam($mediaFiles, 'ns17:MediaFileList');
		$params["options"] = $this->parseParam($options, 'ns17:AddContentOptions');

		$result = $this->doCall("setContent", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastChoiceList $objects
	 * 
	 **/
	public function setChoices(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:ChoiceList');

		$result = $this->doCall("setChoices", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPermissionList $objects
	 * 
	 **/
	public function setPermissions(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:PermissionList');

		$result = $this->doCall("setPermissions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return string
	 **/
	public function getSessionID()
	{
		$params = array();
		

		$result = $this->doCall("getSessionID", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $userName
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getGroupsForUser($userName)
	{
		$params = array();
		
		$params["userName"] = $this->parseParam($userName, 'xsd:string');

		$result = $this->doCall("getGroupsForUser", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $plainText
	 * 
	 * @return string
	 **/
	public function encrypt($plainText)
	{
		$params = array();
		
		$params["plainText"] = $this->parseParam($plainText, 'xsd:string');

		$result = $this->doCall("encrypt", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastAccountList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addAccounts(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:AccountList');

		$result = $this->doCall("addAccounts", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastAccountList $objects
	 * 
	 **/
	public function setAccounts(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:AccountList');

		$result = $this->doCall("setAccounts", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastAccountTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastAccountSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastAccountList
	 **/
	public function getAccounts(ComcastAccountTemplate $template, ComcastQuery $query, ComcastAccountSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:AccountTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:AccountSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getAccounts", $params, 'ComcastAccountList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteAccounts(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteAccounts", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countAccounts(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countAccounts", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastAccountTemplate
	 **/
	public function getRequiredAccountFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredAccountFields", $params, 'ComcastAccountTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastAccountTemplate $template
	 * 
	 **/
	public function setRequiredAccountFields(ComcastAccountTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:AccountTemplate');

		$result = $this->doCall("setRequiredAccountFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRoleList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addRoles(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:RoleList');

		$result = $this->doCall("addRoles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRoleList $objects
	 * 
	 **/
	public function setRoles(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:RoleList');

		$result = $this->doCall("setRoles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteRoles(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteRoles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countRoles(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countRoles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyRoles(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyRoles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastRoleTemplate
	 **/
	public function getRequiredRoleFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredRoleFields", $params, 'ComcastRoleTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRoleTemplate $template
	 * 
	 **/
	public function setRequiredRoleFields(ComcastRoleTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:RoleTemplate');

		$result = $this->doCall("setRequiredRoleFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRestrictionList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addRestrictions(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:RestrictionList');

		$result = $this->doCall("addRestrictions", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRestrictionList $objects
	 * 
	 **/
	public function setRestrictions(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:RestrictionList');

		$result = $this->doCall("setRestrictions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRestrictionTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastRestrictionSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastRestrictionList
	 **/
	public function getRestrictions(ComcastRestrictionTemplate $template, ComcastQuery $query, ComcastRestrictionSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:RestrictionTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:RestrictionSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getRestrictions", $params, 'ComcastRestrictionList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteRestrictions(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteRestrictions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countRestrictions(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countRestrictions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyRestrictions(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyRestrictions", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastRestrictionTemplate
	 **/
	public function getRequiredRestrictionFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredRestrictionFields", $params, 'ComcastRestrictionTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRestrictionTemplate $template
	 * 
	 **/
	public function setRequiredRestrictionFields(ComcastRestrictionTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:RestrictionTemplate');

		$result = $this->doCall("setRequiredRestrictionFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCustomFieldList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addCustomFields(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:CustomFieldList');

		$result = $this->doCall("addCustomFields", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCustomFieldList $objects
	 * 
	 **/
	public function setCustomFields(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:CustomFieldList');

		$result = $this->doCall("setCustomFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCustomFieldTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastCustomFieldSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastCustomFieldList
	 **/
	public function getCustomFields(ComcastCustomFieldTemplate $template, ComcastQuery $query, ComcastCustomFieldSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:CustomFieldTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:CustomFieldSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getCustomFields", $params, 'ComcastCustomFieldList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteCustomFields(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteCustomFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countCustomFields(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countCustomFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastCustomFieldTemplate
	 **/
	public function getRequiredCustomFieldFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredCustomFieldFields", $params, 'ComcastCustomFieldTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCustomFieldTemplate $template
	 * 
	 **/
	public function setRequiredCustomFieldFields(ComcastCustomFieldTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:CustomFieldTemplate');

		$result = $this->doCall("setRequiredCustomFieldFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastLocationList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addLocations(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:LocationList');

		$result = $this->doCall("addLocations", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastLocationList $objects
	 * 
	 **/
	public function setLocations(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:LocationList');

		$result = $this->doCall("setLocations", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastLocationTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastLocationSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastLocationList
	 **/
	public function getLocations(ComcastLocationTemplate $template, ComcastQuery $query, ComcastLocationSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:LocationTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:LocationSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getLocations", $params, 'ComcastLocationList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countLocations(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countLocations", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteLocations(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteLocations", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastLocationTemplate
	 **/
	public function getRequiredLocationFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredLocationFields", $params, 'ComcastLocationTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastLocationTemplate $template
	 * 
	 **/
	public function setRequiredLocationFields(ComcastLocationTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:LocationTemplate');

		$result = $this->doCall("setRequiredLocationFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deletePermissions(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deletePermissions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countPermissions(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countPermissions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastPermissionTemplate
	 **/
	public function getRequiredPermissionFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredPermissionFields", $params, 'ComcastPermissionTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPermissionTemplate $template
	 * 
	 **/
	public function setRequiredPermissionFields(ComcastPermissionTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:PermissionTemplate');

		$result = $this->doCall("setRequiredPermissionFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastServerList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addServers(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:ServerList');

		$result = $this->doCall("addServers", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countServers(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countServers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteServers(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteServers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastServerTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastServerSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastServerList
	 **/
	public function getServers(ComcastServerTemplate $template, ComcastQuery $query, ComcastServerSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:ServerTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:ServerSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getServers", $params, 'ComcastServerList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastServerList $objects
	 * 
	 **/
	public function setServers(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:ServerList');

		$result = $this->doCall("setServers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastServerTemplate
	 **/
	public function getRequiredServerFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredServerFields", $params, 'ComcastServerTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastServerTemplate $template
	 * 
	 **/
	public function setRequiredServerFields(ComcastServerTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:ServerTemplate');

		$result = $this->doCall("setRequiredServerFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastSystemTaskList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addSystemTasks(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:SystemTaskList');

		$result = $this->doCall("addSystemTasks", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countSystemTasks(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countSystemTasks", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteSystemTasks(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteSystemTasks", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastSystemTaskTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastSystemTaskSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastSystemTaskList
	 **/
	public function getSystemTasks(ComcastSystemTaskTemplate $template, ComcastQuery $query, ComcastSystemTaskSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:SystemTaskTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:SystemTaskSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getSystemTasks", $params, 'ComcastSystemTaskList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastSystemTaskList $objects
	 * 
	 **/
	public function setSystemTasks(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:SystemTaskList');

		$result = $this->doCall("setSystemTasks", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastUserList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addUsers(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:UserList');

		$result = $this->doCall("addUsers", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countUsers(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countUsers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteUsers(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteUsers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastUserList $objects
	 * 
	 **/
	public function setUsers(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:UserList');

		$result = $this->doCall("setUsers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastUserTemplate
	 **/
	public function getRequiredUserFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredUserFields", $params, 'ComcastUserTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastUserTemplate $template
	 * 
	 **/
	public function setRequiredUserFields(ComcastUserTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:UserTemplate');

		$result = $this->doCall("setRequiredUserFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCustomCommandList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addCustomCommands(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:CustomCommandList');

		$result = $this->doCall("addCustomCommands", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyCustomCommands(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyCustomCommands", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countCustomCommands(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countCustomCommands", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteCustomCommands(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteCustomCommands", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCustomCommandTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastCustomCommandSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastCustomCommandList
	 **/
	public function getCustomCommands(ComcastCustomCommandTemplate $template, ComcastQuery $query, ComcastCustomCommandSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:CustomCommandTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:CustomCommandSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getCustomCommands", $params, 'ComcastCustomCommandList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCustomCommandList $objects
	 * 
	 **/
	public function setCustomCommands(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:CustomCommandList');

		$result = $this->doCall("setCustomCommands", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastCustomCommandTemplate
	 **/
	public function getRequiredCustomCommandFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredCustomCommandFields", $params, 'ComcastCustomCommandTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCustomCommandTemplate $template
	 * 
	 **/
	public function setRequiredCustomCommandFields(ComcastCustomCommandTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:CustomCommandTemplate');

		$result = $this->doCall("setRequiredCustomCommandFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastDirectoryList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addDirectories(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:DirectoryList');

		$result = $this->doCall("addDirectories", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyDirectories(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyDirectories", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countDirectories(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countDirectories", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteDirectories(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteDirectories", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastDirectoryTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastDirectorySort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastDirectoryList
	 **/
	public function getDirectories(ComcastDirectoryTemplate $template, ComcastQuery $query, ComcastDirectorySort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:DirectoryTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:DirectorySort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getDirectories", $params, 'ComcastDirectoryList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastDirectoryList $objects
	 * 
	 **/
	public function setDirectories(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:DirectoryList');

		$result = $this->doCall("setDirectories", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastDirectoryTemplate
	 **/
	public function getRequiredDirectoryFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredDirectoryFields", $params, 'ComcastDirectoryTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastDirectoryTemplate $template
	 * 
	 **/
	public function setRequiredDirectoryFields(ComcastDirectoryTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:DirectoryTemplate');

		$result = $this->doCall("setRequiredDirectoryFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCategoryList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addCategories(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:CategoryList');

		$result = $this->doCall("addCategories", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countCategories(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countCategories", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteCategories(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteCategories", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCategoryTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastCategorySort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastCategoryList
	 **/
	public function getCategories(ComcastCategoryTemplate $template, ComcastQuery $query, ComcastCategorySort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:CategoryTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:CategorySort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getCategories", $params, 'ComcastCategoryList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCategoryList $objects
	 * 
	 **/
	public function setCategories(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:CategoryList');

		$result = $this->doCall("setCategories", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastCategoryTemplate
	 **/
	public function getRequiredCategoryFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredCategoryFields", $params, 'ComcastCategoryTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastCategoryTemplate $template
	 * 
	 **/
	public function setRequiredCategoryFields(ComcastCategoryTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:CategoryTemplate');

		$result = $this->doCall("setRequiredCategoryFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastChoiceList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addChoices(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:ChoiceList');

		$result = $this->doCall("addChoices", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyChoices(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyChoices", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countChoices(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countChoices", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteChoices(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteChoices", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastChoiceTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastChoiceSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastChoiceList
	 **/
	public function getChoices(ComcastChoiceTemplate $template, ComcastQuery $query, ComcastChoiceSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:ChoiceTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:ChoiceSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getChoices", $params, 'ComcastChoiceList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastChoiceTemplate
	 **/
	public function getRequiredChoiceFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredChoiceFields", $params, 'ComcastChoiceTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastChoiceTemplate $template
	 * 
	 **/
	public function setRequiredChoiceFields(ComcastChoiceTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:ChoiceTemplate');

		$result = $this->doCall("setRequiredChoiceFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMediaList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addMedia(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:MediaList');

		$result = $this->doCall("addMedia", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countMedia(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countMedia", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteMedia(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteMedia", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMediaTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastMediaSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastMediaList
	 **/
	public function getMedia(ComcastMediaTemplate $template, ComcastQuery $query, ComcastMediaSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:MediaTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:MediaSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getMedia", $params, 'ComcastMediaList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMediaList $objects
	 * 
	 **/
	public function setMedia(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:MediaList');

		$result = $this->doCall("setMedia", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastMediaTemplate
	 **/
	public function getRequiredMediaFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredMediaFields", $params, 'ComcastMediaTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMediaTemplate $template
	 * 
	 **/
	public function setRequiredMediaFields(ComcastMediaTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:MediaTemplate');

		$result = $this->doCall("setRequiredMediaFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMediaFileList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addMediaFiles(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:MediaFileList');

		$result = $this->doCall("addMediaFiles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countMediaFiles(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countMediaFiles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteMediaFiles(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteMediaFiles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMediaFileTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastMediaFileSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastMediaFileList
	 **/
	public function getMediaFiles(ComcastMediaFileTemplate $template, ComcastQuery $query, ComcastMediaFileSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:MediaFileTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:MediaFileSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getMediaFiles", $params, 'ComcastMediaFileList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMediaFileList $objects
	 * 
	 **/
	public function setMediaFiles(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:MediaFileList');

		$result = $this->doCall("setMediaFiles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastMediaFileTemplate
	 **/
	public function getRequiredMediaFileFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredMediaFileFields", $params, 'ComcastMediaFileTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMediaFileTemplate $template
	 * 
	 **/
	public function setRequiredMediaFileFields(ComcastMediaFileTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:MediaFileTemplate');

		$result = $this->doCall("setRequiredMediaFileFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPlaylistList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addPlaylists(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:PlaylistList');

		$result = $this->doCall("addPlaylists", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyPlaylists(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyPlaylists", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countPlaylists(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countPlaylists", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deletePlaylists(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deletePlaylists", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPlaylistTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastPlaylistSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastPlaylistList
	 **/
	public function getPlaylists(ComcastPlaylistTemplate $template, ComcastQuery $query, ComcastPlaylistSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:PlaylistTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:PlaylistSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getPlaylists", $params, 'ComcastPlaylistList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPlaylistList $objects
	 * 
	 **/
	public function setPlaylists(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:PlaylistList');

		$result = $this->doCall("setPlaylists", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastPlaylistTemplate
	 **/
	public function getRequiredPlaylistFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredPlaylistFields", $params, 'ComcastPlaylistTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPlaylistTemplate $template
	 * 
	 **/
	public function setRequiredPlaylistFields(ComcastPlaylistTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:PlaylistTemplate');

		$result = $this->doCall("setRequiredPlaylistFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastReleaseList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addReleases(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:ReleaseList');

		$result = $this->doCall("addReleases", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countReleases(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countReleases", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteReleases(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteReleases", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastReleaseTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastReleaseSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastReleaseList
	 **/
	public function getReleases(ComcastReleaseTemplate $template, ComcastQuery $query, ComcastReleaseSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:ReleaseTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:ReleaseSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getReleases", $params, 'ComcastReleaseList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastReleaseList $objects
	 * 
	 **/
	public function setReleases(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:ReleaseList');

		$result = $this->doCall("setReleases", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastReleaseTemplate
	 **/
	public function getRequiredReleaseFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredReleaseFields", $params, 'ComcastReleaseTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastReleaseTemplate $template
	 * 
	 **/
	public function setRequiredReleaseFields(ComcastReleaseTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:ReleaseTemplate');

		$result = $this->doCall("setRequiredReleaseFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRequestTemplate $template
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countGroupedRequests(ComcastRequestTemplate $template, ComcastQuery $query)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:RequestTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countGroupedRequests", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastRequestTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastRequestSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastRequestList
	 **/
	public function getGroupedRequests(ComcastRequestTemplate $template, ComcastQuery $query, ComcastRequestSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:RequestTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:RequestSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getGroupedRequests", $params, 'ComcastRequestList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEncodingProfileList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addEncodingProfiles(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:EncodingProfileList');

		$result = $this->doCall("addEncodingProfiles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyEncodingProfiles(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyEncodingProfiles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countEncodingProfiles(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countEncodingProfiles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteEncodingProfiles(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteEncodingProfiles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEncodingProfileTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastEncodingProfileSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastEncodingProfileList
	 **/
	public function getEncodingProfiles(ComcastEncodingProfileTemplate $template, ComcastQuery $query, ComcastEncodingProfileSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:EncodingProfileTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:EncodingProfileSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getEncodingProfiles", $params, 'ComcastEncodingProfileList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEncodingProfileList $objects
	 * 
	 **/
	public function setEncodingProfiles(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:EncodingProfileList');

		$result = $this->doCall("setEncodingProfiles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastEncodingProfileTemplate
	 **/
	public function getRequiredEncodingProfileFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredEncodingProfileFields", $params, 'ComcastEncodingProfileTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEncodingProfileTemplate $template
	 * 
	 **/
	public function setRequiredEncodingProfileFields(ComcastEncodingProfileTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:EncodingProfileTemplate');

		$result = $this->doCall("setRequiredEncodingProfileFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addEndUsers(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:EndUserList');

		$result = $this->doCall("addEndUsers", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countEndUsers(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countEndUsers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteEndUsers(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteEndUsers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastEndUserSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastEndUserList
	 **/
	public function getEndUsers(ComcastEndUserTemplate $template, ComcastQuery $query, ComcastEndUserSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:EndUserTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:EndUserSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getEndUsers", $params, 'ComcastEndUserList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserList $objects
	 * 
	 **/
	public function setEndUsers(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:EndUserList');

		$result = $this->doCall("setEndUsers", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastEndUserTemplate
	 **/
	public function getRequiredEndUserFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredEndUserFields", $params, 'ComcastEndUserTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserTemplate $template
	 * 
	 **/
	public function setRequiredEndUserFields(ComcastEndUserTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:EndUserTemplate');

		$result = $this->doCall("setRequiredEndUserFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserPermissionList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addEndUserPermissions(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:EndUserPermissionList');

		$result = $this->doCall("addEndUserPermissions", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countEndUserPermissions(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countEndUserPermissions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteEndUserPermissions(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteEndUserPermissions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserPermissionTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastEndUserPermissionSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastEndUserPermissionList
	 **/
	public function getEndUserPermissions(ComcastEndUserPermissionTemplate $template, ComcastQuery $query, ComcastEndUserPermissionSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:EndUserPermissionTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:EndUserPermissionSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getEndUserPermissions", $params, 'ComcastEndUserPermissionList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserPermissionList $objects
	 * 
	 **/
	public function setEndUserPermissions(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:EndUserPermissionList');

		$result = $this->doCall("setEndUserPermissions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastEndUserPermissionTemplate
	 **/
	public function getRequiredEndUserPermissionFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredEndUserPermissionFields", $params, 'ComcastEndUserPermissionTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserPermissionTemplate $template
	 * 
	 **/
	public function setRequiredEndUserPermissionFields(ComcastEndUserPermissionTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:EndUserPermissionTemplate');

		$result = $this->doCall("setRequiredEndUserPermissionFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastLicenseList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addLicenses(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:LicenseList');

		$result = $this->doCall("addLicenses", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyLicenses(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyLicenses", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countLicenses(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countLicenses", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteLicenses(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteLicenses", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastLicenseTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastLicenseSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastLicenseList
	 **/
	public function getLicenses(ComcastLicenseTemplate $template, ComcastQuery $query, ComcastLicenseSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:LicenseTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:LicenseSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getLicenses", $params, 'ComcastLicenseList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastLicenseList $objects
	 * 
	 **/
	public function setLicenses(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:LicenseList');

		$result = $this->doCall("setLicenses", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastLicenseTemplate
	 **/
	public function getRequiredLicenseFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredLicenseFields", $params, 'ComcastLicenseTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastLicenseTemplate $template
	 * 
	 **/
	public function setRequiredLicenseFields(ComcastLicenseTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:LicenseTemplate');

		$result = $this->doCall("setRequiredLicenseFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPortalList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addPortals(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:PortalList');

		$result = $this->doCall("addPortals", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyPortals(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyPortals", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countPortals(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countPortals", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deletePortals(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deletePortals", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPortalTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastPortalSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastPortalList
	 **/
	public function getPortals(ComcastPortalTemplate $template, ComcastQuery $query, ComcastPortalSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:PortalTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:PortalSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getPortals", $params, 'ComcastPortalList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPortalList $objects
	 * 
	 **/
	public function setPortals(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:PortalList');

		$result = $this->doCall("setPortals", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastPortalTemplate
	 **/
	public function getRequiredPortalFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredPortalFields", $params, 'ComcastPortalTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPortalTemplate $template
	 * 
	 **/
	public function setRequiredPortalFields(ComcastPortalTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:PortalTemplate');

		$result = $this->doCall("setRequiredPortalFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastStorefrontPageList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addStorefrontPages(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:StorefrontPageList');

		$result = $this->doCall("addStorefrontPages", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyStorefrontPages(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyStorefrontPages", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countStorefrontPages(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countStorefrontPages", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteStorefrontPages(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteStorefrontPages", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastStorefrontPageTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastStorefrontPageSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastStorefrontPageList
	 **/
	public function getStorefrontPages(ComcastStorefrontPageTemplate $template, ComcastQuery $query, ComcastStorefrontPageSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:StorefrontPageTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:StorefrontPageSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getStorefrontPages", $params, 'ComcastStorefrontPageList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastStorefrontPageList $objects
	 * 
	 **/
	public function setStorefrontPages(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:StorefrontPageList');

		$result = $this->doCall("setStorefrontPages", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastStorefrontPageTemplate
	 **/
	public function getRequiredStorefrontPageFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredStorefrontPageFields", $params, 'ComcastStorefrontPageTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastStorefrontPageTemplate $template
	 * 
	 **/
	public function setRequiredStorefrontPageFields(ComcastStorefrontPageTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:StorefrontPageTemplate');

		$result = $this->doCall("setRequiredStorefrontPageFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastStorefrontList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addStorefronts(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:StorefrontList');

		$result = $this->doCall("addStorefronts", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyStorefronts(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyStorefronts", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countStorefronts(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countStorefronts", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteStorefronts(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteStorefronts", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastStorefrontTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastStorefrontSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastStorefrontList
	 **/
	public function getStorefronts(ComcastStorefrontTemplate $template, ComcastQuery $query, ComcastStorefrontSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:StorefrontTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:StorefrontSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getStorefronts", $params, 'ComcastStorefrontList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastStorefrontList $objects
	 * 
	 **/
	public function setStorefronts(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:StorefrontList');

		$result = $this->doCall("setStorefronts", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastStorefrontTemplate
	 **/
	public function getRequiredStorefrontFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredStorefrontFields", $params, 'ComcastStorefrontTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastStorefrontTemplate $template
	 * 
	 **/
	public function setRequiredStorefrontFields(ComcastStorefrontTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:StorefrontTemplate');

		$result = $this->doCall("setRequiredStorefrontFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastUsagePlanList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addUsagePlans(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:UsagePlanList');

		$result = $this->doCall("addUsagePlans", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyUsagePlans(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyUsagePlans", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countUsagePlans(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countUsagePlans", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteUsagePlans(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteUsagePlans", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastUsagePlanTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastUsagePlanSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastUsagePlanList
	 **/
	public function getUsagePlans(ComcastUsagePlanTemplate $template, ComcastQuery $query, ComcastUsagePlanSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:UsagePlanTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:UsagePlanSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getUsagePlans", $params, 'ComcastUsagePlanList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastUsagePlanList $objects
	 * 
	 **/
	public function setUsagePlans(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:UsagePlanList');

		$result = $this->doCall("setUsagePlans", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastUsagePlanTemplate
	 **/
	public function getRequiredUsagePlanFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredUsagePlanFields", $params, 'ComcastUsagePlanTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastUsagePlanTemplate $template
	 * 
	 **/
	public function setRequiredUsagePlanFields(ComcastUsagePlanTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:UsagePlanTemplate');

		$result = $this->doCall("setRequiredUsagePlanFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserTransactionList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addEndUserTransactions(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:EndUserTransactionList');

		$result = $this->doCall("addEndUserTransactions", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countEndUserTransactions(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countEndUserTransactions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteEndUserTransactions(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteEndUserTransactions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserTransactionTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastEndUserTransactionSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastEndUserTransactionList
	 **/
	public function getEndUserTransactions(ComcastEndUserTransactionTemplate $template, ComcastQuery $query, ComcastEndUserTransactionSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:EndUserTransactionTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:EndUserTransactionSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getEndUserTransactions", $params, 'ComcastEndUserTransactionList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserTransactionList $objects
	 * 
	 **/
	public function setEndUserTransactions(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:EndUserTransactionList');

		$result = $this->doCall("setEndUserTransactions", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastEndUserTransactionTemplate
	 **/
	public function getRequiredEndUserTransactionFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredEndUserTransactionFields", $params, 'ComcastEndUserTransactionTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEndUserTransactionTemplate $template
	 * 
	 **/
	public function setRequiredEndUserTransactionFields(ComcastEndUserTransactionTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:EndUserTransactionTemplate');

		$result = $this->doCall("setRequiredEndUserTransactionFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPriceList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addPrices(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:PriceList');

		$result = $this->doCall("addPrices", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countPrices(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countPrices", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deletePrices(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deletePrices", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPriceTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastPriceSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastPriceList
	 **/
	public function getPrices(ComcastPriceTemplate $template, ComcastQuery $query, ComcastPriceSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:PriceTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns22:PriceSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getPrices", $params, 'ComcastPriceList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPriceList $objects
	 * 
	 **/
	public function setPrices(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns20:PriceList');

		$result = $this->doCall("setPrices", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastPriceTemplate
	 **/
	public function getRequiredPriceFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredPriceFields", $params, 'ComcastPriceTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPriceTemplate $template
	 * 
	 **/
	public function setRequiredPriceFields(ComcastPriceTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns26:PriceTemplate');

		$result = $this->doCall("setRequiredPriceFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastAssetTypeList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addAssetTypes(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:AssetTypeList');

		$result = $this->doCall("addAssetTypes", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastAssetTypeList $objects
	 * 
	 **/
	public function setAssetTypes(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns17:AssetTypeList');

		$result = $this->doCall("setAssetTypes", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteAssetTypes(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteAssetTypes", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyAssetTypes(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');

		$result = $this->doCall("copyAssetTypes", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastAssetTypeTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastAssetTypeSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastAssetTypeList
	 **/
	public function getAssetTypes(ComcastAssetTypeTemplate $template, ComcastQuery $query, ComcastAssetTypeSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:AssetTypeTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns19:AssetTypeSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getAssetTypes", $params, 'ComcastAssetTypeList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countAssetTypes(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countAssetTypes", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastAssetTypeTemplate
	 **/
	public function getRequiredAssetTypeFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredAssetTypeFields", $params, 'ComcastAssetTypeTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastAssetTypeTemplate $template
	 * 
	 **/
	public function setRequiredAssetTypeFields(ComcastAssetTypeTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns25:AssetTypeTemplate');

		$result = $this->doCall("setRequiredAssetTypeFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastJobList $objects
	 * 
	 * @return ComcastIDList
	 **/
	public function addJobs(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:JobList');

		$result = $this->doCall("addJobs", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastQuery $query
	 * 
	 * @return long
	 **/
	public function countJobs(ComcastQuery $query)
	{
		$params = array();
		
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("countJobs", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * 
	 **/
	public function deleteJobs(array $IDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');

		$result = $this->doCall("deleteJobs", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastJobTemplate $template
	 * @param ComcastQuery $query
	 * @param ComcastJobSort $sort
	 * @param ComcastRange $range
	 * 
	 * @return ComcastJobList
	 **/
	public function getJobs(ComcastJobTemplate $template, ComcastQuery $query, ComcastJobSort $sort, ComcastRange $range)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:JobTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');
		$params["sort"] = $this->parseParam($sort, 'ns15:JobSort');
		$params["range"] = $this->parseParam($range, 'ns12:Range');

		$result = $this->doCall("getJobs", $params, 'ComcastJobList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastJobList $objects
	 * 
	 **/
	public function setJobs(array $objects)
	{
		$params = array();
		
		$params["objects"] = $this->parseParam($objects, 'ns11:JobList');

		$result = $this->doCall("setJobs", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastJobTemplate
	 **/
	public function getRequiredJobFields()
	{
		$params = array();
		

		$result = $this->doCall("getRequiredJobFields", $params, 'ComcastJobTemplate');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastJobTemplate $template
	 * 
	 **/
	public function setRequiredJobFields(ComcastJobTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:JobTemplate');

		$result = $this->doCall("setRequiredJobFields", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastArrayOfstring $PIDs
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getMediaOwners(array $PIDs)
	{
		$params = array();
		
		$params["PIDs"] = $this->parseParam($PIDs, 'ns14:ArrayOfstring');

		$result = $this->doCall("getMediaOwners", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEncodingProvider $encodingProvider
	 * 
	 * @return ComcastArrayOfFormat
	 **/
	public function getPossibleSourceFormats(ComcastEncodingProvider $encodingProvider)
	{
		$params = array();
		
		$params["encodingProvider"] = $this->parseParam($encodingProvider, 'ns18:EncodingProvider');

		$result = $this->doCall("getPossibleSourceFormats", $params, 'ComcastArrayOfFormat');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEncodingProvider $encodingProvider
	 * 
	 * @return ComcastArrayOfFormat
	 **/
	public function getPossibleTargetFormats(ComcastEncodingProvider $encodingProvider)
	{
		$params = array();
		
		$params["encodingProvider"] = $this->parseParam($encodingProvider, 'ns18:EncodingProvider');

		$result = $this->doCall("getPossibleTargetFormats", $params, 'ComcastArrayOfFormat');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastArrayOfstring $PIDs
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getPlaylistOwners(array $PIDs)
	{
		$params = array();
		
		$params["PIDs"] = $this->parseParam($PIDs, 'ns14:ArrayOfstring');

		$result = $this->doCall("getPlaylistOwners", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastArrayOfstring $PIDs
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getReleaseOwners(array $PIDs)
	{
		$params = array();
		
		$params["PIDs"] = $this->parseParam($PIDs, 'ns14:ArrayOfstring');

		$result = $this->doCall("getReleaseOwners", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEncodingProvider $encodingProvider
	 * @param ComcastContentType $contentType
	 * @param long $codecID
	 * 
	 * @return ComcastCodec
	 **/
	public function getCodec(ComcastEncodingProvider $encodingProvider, ComcastContentType $contentType, $codecID)
	{
		$params = array();
		
		$params["encodingProvider"] = $this->parseParam($encodingProvider, 'ns18:EncodingProvider');
		$params["contentType"] = $this->parseParam($contentType, 'ns18:ContentType');
		$params["codecID"] = $this->parseParam($codecID, 'xsd:long');

		$result = $this->doCall("getCodec", $params, 'ComcastCodec');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastArrayOfstring $PIDs
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getPortalOwners(array $PIDs)
	{
		$params = array();
		
		$params["PIDs"] = $this->parseParam($PIDs, 'ns14:ArrayOfstring');

		$result = $this->doCall("getPortalOwners", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastArrayOfstring $PIDs
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getStorefrontOwners(array $PIDs)
	{
		$params = array();
		
		$params["PIDs"] = $this->parseParam($PIDs, 'ns14:ArrayOfstring');

		$result = $this->doCall("getStorefrontOwners", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $prefix
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getAccountNames($prefix)
	{
		$params = array();
		
		$params["prefix"] = $this->parseParam($prefix, 'xsd:string');

		$result = $this->doCall("getAccountNames", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastArrayOfCapability
	 **/
	public function getCurrentCapabilities()
	{
		$params = array();
		

		$result = $this->doCall("getCurrentCapabilities", $params, 'ComcastArrayOfCapability');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastArrayOfCapability $capabilities
	 * 
	 * @return ComcastArrayOfCapability
	 **/
	public function getMissingCapabilities(array $capabilities)
	{
		$params = array();
		
		$params["capabilities"] = $this->parseParam($capabilities, 'ns11:ArrayOfCapability');

		$result = $this->doCall("getMissingCapabilities", $params, 'ComcastArrayOfCapability');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastArrayOfCapability $capabilities
	 * @param long $userID
	 * 
	 * @return ComcastArrayOfCapability
	 **/
	public function getMissingConsoleCapabilities(array $capabilities, $userID)
	{
		$params = array();
		
		$params["capabilities"] = $this->parseParam($capabilities, 'ns11:ArrayOfCapability');
		$params["userID"] = $this->parseParam($userID, 'xsd:long');

		$result = $this->doCall("getMissingConsoleCapabilities", $params, 'ComcastArrayOfCapability');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastArrayOfCapability
	 **/
	public function getPossibleCapabilities()
	{
		$params = array();
		

		$result = $this->doCall("getPossibleCapabilities", $params, 'ComcastArrayOfCapability');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getAllSystemTaskServiceTokens()
	{
		$params = array();
		

		$result = $this->doCall("getAllSystemTaskServiceTokens", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $title
	 * 
	 * @return long
	 **/
	public function openJob($title)
	{
		$params = array();
		
		$params["title"] = $this->parseParam($title, 'xsd:string');

		$result = $this->doCall("openJob", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $title
	 * 
	 **/
	public function closeJob($title)
	{
		$params = array();
		
		$params["title"] = $this->parseParam($title, 'xsd:string');

		$result = $this->doCall("closeJob", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 **/
	public function signOut()
	{
		$params = array();
		

		$result = $this->doCall("signOut", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return long
	 **/
	public function authenticateSelf()
	{
		$params = array();
		

		$result = $this->doCall("authenticateSelf", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * @param long $masterID
	 * 
	 * @return long
	 **/
	public function combineMedia(array $IDs, $masterID)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');
		$params["masterID"] = $this->parseParam($masterID, 'xsd:long');

		$result = $this->doCall("combineMedia", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMedia $media
	 * @param ComcastMediaFileList $mediaFiles
	 * @param ComcastArrayOfstring $encodingProfileTitles
	 * @param ComcastDelivery $createReleases
	 * @param string $releaseOutletAccount
	 * 
	 * @return long
	 **/
	public function addMediaWithFiles(ComcastMedia $media, array $mediaFiles, array $encodingProfileTitles, ComcastDelivery $createReleases, $releaseOutletAccount)
	{
		$params = array();
		
		$params["media"] = $this->parseParam($media, 'ns17:Media');
		$params["mediaFiles"] = $this->parseParam($mediaFiles, 'ns17:MediaFileList');
		$params["encodingProfileTitles"] = $this->parseParam($encodingProfileTitles, 'ns14:ArrayOfstring');
		$params["createReleases"] = $this->parseParam($createReleases, 'ns16:Delivery');
		$params["releaseOutletAccount"] = $this->parseParam($releaseOutletAccount, 'xsd:string');

		$result = $this->doCall("addMediaWithFiles", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMedia $media
	 * @param ComcastMediaFileList $mediaFiles
	 * @param boolean $generateThumbnail
	 * @param ComcastArrayOfstring $encodingProfileTitles
	 * @param ComcastDelivery $createReleases
	 * @param string $releaseOutletAccount
	 * 
	 * @return long
	 **/
	public function addMediaWithFilesAndThumbnail(ComcastMedia $media, array $mediaFiles, $generateThumbnail, array $encodingProfileTitles, ComcastDelivery $createReleases, $releaseOutletAccount)
	{
		$params = array();
		
		$params["media"] = $this->parseParam($media, 'ns17:Media');
		$params["mediaFiles"] = $this->parseParam($mediaFiles, 'ns17:MediaFileList');
		$params["generateThumbnail"] = $this->parseParam($generateThumbnail, 'xsd:boolean');
		$params["encodingProfileTitles"] = $this->parseParam($encodingProfileTitles, 'ns14:ArrayOfstring');
		$params["createReleases"] = $this->parseParam($createReleases, 'ns16:Delivery');
		$params["releaseOutletAccount"] = $this->parseParam($releaseOutletAccount, 'xsd:string');

		$result = $this->doCall("addMediaWithFilesAndThumbnail", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMedia $media
	 * @param long $sourceMediaFileID
	 * @param long $startTime
	 * @param long $endTime
	 * @param ComcastIDList $encodingProfileIDs
	 * @param long $storageServerID
	 * 
	 * @return ComcastAddContentResults
	 **/
	public function addClippedMedia(ComcastMedia $media, $sourceMediaFileID, $startTime, $endTime, array $encodingProfileIDs, $storageServerID)
	{
		$params = array();
		
		$params["media"] = $this->parseParam($media, 'ns17:Media');
		$params["sourceMediaFileID"] = $this->parseParam($sourceMediaFileID, 'xsd:long');
		$params["startTime"] = $this->parseParam($startTime, 'xsd:long');
		$params["endTime"] = $this->parseParam($endTime, 'xsd:long');
		$params["encodingProfileIDs"] = $this->parseParam($encodingProfileIDs, 'ns12:IDList');
		$params["storageServerID"] = $this->parseParam($storageServerID, 'xsd:long');

		$result = $this->doCall("addClippedMedia", $params, 'ComcastAddContentResults');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastMedia $media
	 * @param ComcastMediaFileList $mediaFiles
	 * @param ComcastAddContentOptions $options
	 * 
	 * @return ComcastAddContentResults
	 **/
	public function addContent(ComcastMedia $media, array $mediaFiles, ComcastAddContentOptions $options)
	{
		$params = array();
		
		$params["media"] = $this->parseParam($media, 'ns17:Media');
		$params["mediaFiles"] = $this->parseParam($mediaFiles, 'ns17:MediaFileList');
		$params["options"] = $this->parseParam($options, 'ns17:AddContentOptions');

		$result = $this->doCall("addContent", $params, 'ComcastAddContentResults');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * @param ComcastIDSet $accountIDs
	 * 
	 **/
	public function publishMedia(array $IDs, array $accountIDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');
		$params["accountIDs"] = $this->parseParam($accountIDs, 'ns12:IDSet');

		$result = $this->doCall("publishMedia", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $sourceMediaFileIDs
	 * @param long $thumbnailServerID
	 * 
	 * @return ComcastIDList
	 **/
	public function generateThumbnailMediaFiles(array $sourceMediaFileIDs, $thumbnailServerID)
	{
		$params = array();
		
		$params["sourceMediaFileIDs"] = $this->parseParam($sourceMediaFileIDs, 'ns12:IDList');
		$params["thumbnailServerID"] = $this->parseParam($thumbnailServerID, 'xsd:long');

		$result = $this->doCall("generateThumbnailMediaFiles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $sourceMediaFileID
	 * @param ComcastIDList $encodingProfileIDs
	 * @param long $storageServerID
	 * 
	 * @return ComcastIDList
	 **/
	public function encodeMediaFiles($sourceMediaFileID, array $encodingProfileIDs, $storageServerID)
	{
		$params = array();
		
		$params["sourceMediaFileID"] = $this->parseParam($sourceMediaFileID, 'xsd:long');
		$params["encodingProfileIDs"] = $this->parseParam($encodingProfileIDs, 'ns12:IDList');
		$params["storageServerID"] = $this->parseParam($storageServerID, 'xsd:long');

		$result = $this->doCall("encodeMediaFiles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $sourceMediaFileID
	 * @param ComcastIDList $encodingProfileIDs
	 * @param long $storageServerID
	 * @param long $startTime
	 * @param long $endTime
	 * 
	 * @return ComcastIDList
	 **/
	public function encodeClippedMediaFiles($sourceMediaFileID, array $encodingProfileIDs, $storageServerID, $startTime, $endTime)
	{
		$params = array();
		
		$params["sourceMediaFileID"] = $this->parseParam($sourceMediaFileID, 'xsd:long');
		$params["encodingProfileIDs"] = $this->parseParam($encodingProfileIDs, 'ns12:IDList');
		$params["storageServerID"] = $this->parseParam($storageServerID, 'xsd:long');
		$params["startTime"] = $this->parseParam($startTime, 'xsd:long');
		$params["endTime"] = $this->parseParam($endTime, 'xsd:long');

		$result = $this->doCall("encodeClippedMediaFiles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $IDs
	 * @param ComcastIDList $storageServerIDs
	 * 
	 * @return ComcastIDList
	 **/
	public function copyMediaFiles(array $IDs, array $storageServerIDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDList');
		$params["storageServerIDs"] = $this->parseParam($storageServerIDs, 'ns12:IDList');

		$result = $this->doCall("copyMediaFiles", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDSet $IDs
	 * @param ComcastIDSet $accountIDs
	 * 
	 **/
	public function publishPlaylists(array $IDs, array $accountIDs)
	{
		$params = array();
		
		$params["IDs"] = $this->parseParam($IDs, 'ns12:IDSet');
		$params["accountIDs"] = $this->parseParam($accountIDs, 'ns12:IDSet');

		$result = $this->doCall("publishPlaylists", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastCodecs
	 **/
	public function getAllCodecs()
	{
		$params = array();
		

		$result = $this->doCall("getAllCodecs", $params, 'ComcastCodecs');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastEncodingProvider $encodingProvider
	 * @param long $codecID
	 * @param ComcastBitrateMode $bitrateMode
	 * 
	 * @return ComcastPossibleAudioEncodings
	 **/
	public function getPossibleAudioEncodings(ComcastEncodingProvider $encodingProvider, $codecID, ComcastBitrateMode $bitrateMode)
	{
		$params = array();
		
		$params["encodingProvider"] = $this->parseParam($encodingProvider, 'ns18:EncodingProvider');
		$params["codecID"] = $this->parseParam($codecID, 'xsd:long');
		$params["bitrateMode"] = $this->parseParam($bitrateMode, 'ns18:BitrateMode');

		$result = $this->doCall("getPossibleAudioEncodings", $params, 'ComcastPossibleAudioEncodings');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastPossibleAudioEncodings
	 **/
	public function getAllPossibleAudioEncodings()
	{
		$params = array();
		

		$result = $this->doCall("getAllPossibleAudioEncodings", $params, 'ComcastPossibleAudioEncodings');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return dateTime
	 **/
	public function getEarliestRequestDate()
	{
		$params = array();
		

		$result = $this->doCall("getEarliestRequestDate", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $challenge
	 * @param dateTime $expirationDate
	 * @param int $maximumPlays
	 * @param int $timeAllowed
	 * @param ComcastTimeUnits $timeAllowedUnits
	 * @param dateTime $timeAllowedStart
	 * @param string $replaceQuotesWith
	 * @param long $maximumBurns
	 * @param long $maximumTransfersToDevice
	 * @param boolean $disableOnPC
	 * @param boolean $disableOnClockRollback
	 * 
	 * @return string
	 **/
	public function getWMRMLicenseResponse($challenge, $expirationDate, $maximumPlays, $timeAllowed, ComcastTimeUnits $timeAllowedUnits, $timeAllowedStart, $replaceQuotesWith, $maximumBurns, $maximumTransfersToDevice, $disableOnPC, $disableOnClockRollback)
	{
		$params = array();
		
		$params["challenge"] = $this->parseParam($challenge, 'xsd:string');
		$params["expirationDate"] = $this->parseParam($expirationDate, 'xsd:dateTime');
		$params["maximumPlays"] = $this->parseParam($maximumPlays, 'xsd:int');
		$params["timeAllowed"] = $this->parseParam($timeAllowed, 'xsd:int');
		$params["timeAllowedUnits"] = $this->parseParam($timeAllowedUnits, 'ns12:TimeUnits');
		$params["timeAllowedStart"] = $this->parseParam($timeAllowedStart, 'xsd:dateTime');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');
		$params["maximumBurns"] = $this->parseParam($maximumBurns, 'xsd:long');
		$params["maximumTransfersToDevice"] = $this->parseParam($maximumTransfersToDevice, 'xsd:long');
		$params["disableOnPC"] = $this->parseParam($disableOnPC, 'xsd:boolean');
		$params["disableOnClockRollback"] = $this->parseParam($disableOnClockRollback, 'xsd:boolean');

		$result = $this->doCall("getWMRMLicenseResponse", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $challenge
	 * @param ComcastLicense $license
	 * @param string $replaceQuotesWith
	 * 
	 * @return string
	 **/
	public function getV1WMRMLicenseResponse($challenge, ComcastLicense $license, $replaceQuotesWith)
	{
		$params = array();
		
		$params["challenge"] = $this->parseParam($challenge, 'xsd:string');
		$params["license"] = $this->parseParam($license, 'ns20:License');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getV1WMRMLicenseResponse", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $challenge
	 * @param ComcastLicense $license
	 * @param string $replaceQuotesWith
	 * 
	 * @return string
	 **/
	public function getWMRMLicenseResponseEx($challenge, ComcastLicense $license, $replaceQuotesWith)
	{
		$params = array();
		
		$params["challenge"] = $this->parseParam($challenge, 'xsd:string');
		$params["license"] = $this->parseParam($license, 'ns20:License');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMLicenseResponseEx", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $systemInfo
	 * @param ComcastIDList $releaseIDs
	 * @param dateTime $expirationDate
	 * @param int $maximumPlays
	 * @param int $timeAllowed
	 * @param ComcastTimeUnits $timeAllowedUnits
	 * @param dateTime $timeAllowedStart
	 * @param string $replaceQuotesWith
	 * @param long $maximumBurns
	 * @param long $maximumTransfersToDevice
	 * @param boolean $disableOnPC
	 * @param boolean $disableOnClockRollback
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getWMRMLicenseResponses($systemInfo, array $releaseIDs, $expirationDate, $maximumPlays, $timeAllowed, ComcastTimeUnits $timeAllowedUnits, $timeAllowedStart, $replaceQuotesWith, $maximumBurns, $maximumTransfersToDevice, $disableOnPC, $disableOnClockRollback)
	{
		$params = array();
		
		$params["systemInfo"] = $this->parseParam($systemInfo, 'xsd:string');
		$params["releaseIDs"] = $this->parseParam($releaseIDs, 'ns12:IDList');
		$params["expirationDate"] = $this->parseParam($expirationDate, 'xsd:dateTime');
		$params["maximumPlays"] = $this->parseParam($maximumPlays, 'xsd:int');
		$params["timeAllowed"] = $this->parseParam($timeAllowed, 'xsd:int');
		$params["timeAllowedUnits"] = $this->parseParam($timeAllowedUnits, 'ns12:TimeUnits');
		$params["timeAllowedStart"] = $this->parseParam($timeAllowedStart, 'xsd:dateTime');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');
		$params["maximumBurns"] = $this->parseParam($maximumBurns, 'xsd:long');
		$params["maximumTransfersToDevice"] = $this->parseParam($maximumTransfersToDevice, 'xsd:long');
		$params["disableOnPC"] = $this->parseParam($disableOnPC, 'xsd:boolean');
		$params["disableOnClockRollback"] = $this->parseParam($disableOnClockRollback, 'xsd:boolean');

		$result = $this->doCall("getWMRMLicenseResponses", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $systemInfo
	 * @param ComcastIDList $releaseIDs
	 * @param ComcastLicense $license
	 * @param string $replaceQuotesWith
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getWMRMLicenseResponsesEx($systemInfo, array $releaseIDs, ComcastLicense $license, $replaceQuotesWith)
	{
		$params = array();
		
		$params["systemInfo"] = $this->parseParam($systemInfo, 'xsd:string');
		$params["releaseIDs"] = $this->parseParam($releaseIDs, 'ns12:IDList');
		$params["license"] = $this->parseParam($license, 'ns20:License');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMLicenseResponsesEx", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $releaseID
	 * @param string $replaceQuotesWith
	 * 
	 * @return string
	 **/
	public function getWMRMHeader($releaseID, $replaceQuotesWith)
	{
		$params = array();
		
		$params["releaseID"] = $this->parseParam($releaseID, 'xsd:long');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMHeader", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastIDList $releaseIDs
	 * @param string $replaceQuotesWith
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getWMRMHeaders(array $releaseIDs, $replaceQuotesWith)
	{
		$params = array();
		
		$params["releaseIDs"] = $this->parseParam($releaseIDs, 'ns12:IDList');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMHeaders", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $systemInfo
	 * @param ComcastIDList $releaseIDs
	 * @param long $endUserID
	 * @param string $replaceQuotesWith
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getWMRMLicenseResponsesForEndUser($systemInfo, array $releaseIDs, $endUserID, $replaceQuotesWith)
	{
		$params = array();
		
		$params["systemInfo"] = $this->parseParam($systemInfo, 'xsd:string');
		$params["releaseIDs"] = $this->parseParam($releaseIDs, 'ns12:IDList');
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMLicenseResponsesForEndUser", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $challenge
	 * @param long $releaseID
	 * @param long $endUserID
	 * @param string $replaceQuotesWith
	 * 
	 * @return string
	 **/
	public function getWMRMLicenseResponseForEndUser($challenge, $releaseID, $endUserID, $replaceQuotesWith)
	{
		$params = array();
		
		$params["challenge"] = $this->parseParam($challenge, 'xsd:string');
		$params["releaseID"] = $this->parseParam($releaseID, 'xsd:long');
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMLicenseResponseForEndUser", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $challenge
	 * @param long $endUserPermissionID
	 * @param string $replaceQuotesWith
	 * 
	 * @return string
	 **/
	public function getV1WMRMLicenseResponseForEndUserPermission($challenge, $endUserPermissionID, $replaceQuotesWith)
	{
		$params = array();
		
		$params["challenge"] = $this->parseParam($challenge, 'xsd:string');
		$params["endUserPermissionID"] = $this->parseParam($endUserPermissionID, 'xsd:long');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getV1WMRMLicenseResponseForEndUserPermission", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $challenge
	 * @param long $endUserPermissionID
	 * @param string $replaceQuotesWith
	 * 
	 * @return string
	 **/
	public function getWMRMLicenseResponseForEndUserPermission($challenge, $endUserPermissionID, $replaceQuotesWith)
	{
		$params = array();
		
		$params["challenge"] = $this->parseParam($challenge, 'xsd:string');
		$params["endUserPermissionID"] = $this->parseParam($endUserPermissionID, 'xsd:long');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMLicenseResponseForEndUserPermission", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $systemInfo
	 * @param ComcastIDList $releaseIDs
	 * @param ComcastIDList $endUserPermissionIDs
	 * @param string $replaceQuotesWith
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getWMRMLicenseResponsesForEndUserPermissions($systemInfo, array $releaseIDs, array $endUserPermissionIDs, $replaceQuotesWith)
	{
		$params = array();
		
		$params["systemInfo"] = $this->parseParam($systemInfo, 'xsd:string');
		$params["releaseIDs"] = $this->parseParam($releaseIDs, 'ns12:IDList');
		$params["endUserPermissionIDs"] = $this->parseParam($endUserPermissionIDs, 'ns12:IDList');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMLicenseResponsesForEndUserPermissions", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * 
	 * @return ComcastWMRMSignatureKeys
	 **/
	public function generateWMRMSignatureKeys()
	{
		$params = array();
		

		$result = $this->doCall("generateWMRMSignatureKeys", $params, 'ComcastWMRMSignatureKeys');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $challenge
	 * 
	 * @return ComcastDRMChallengeState
	 **/
	public function getWMRMChallengeState($challenge)
	{
		$params = array();
		
		$params["challenge"] = $this->parseParam($challenge, 'xsd:string');

		$result = $this->doCall("getWMRMChallengeState", $params, 'ComcastDRMChallengeState');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $systemInfo
	 * @param ComcastIDList $parentLicenseIDs
	 * @param long $endUserID
	 * @param string $replaceQuotesWith
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function getWMRMParentLicenseResponses($systemInfo, array $parentLicenseIDs, $endUserID, $replaceQuotesWith)
	{
		$params = array();
		
		$params["systemInfo"] = $this->parseParam($systemInfo, 'xsd:string');
		$params["parentLicenseIDs"] = $this->parseParam($parentLicenseIDs, 'ns12:IDList');
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');
		$params["replaceQuotesWith"] = $this->parseParam($replaceQuotesWith, 'xsd:string');

		$result = $this->doCall("getWMRMParentLicenseResponses", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastDRMRevocationOptions $options
	 * 
	 * @return ComcastArrayOfstring
	 **/
	public function revokeWMRMLicenses(ComcastDRMRevocationOptions $options)
	{
		$params = array();
		
		$params["options"] = $this->parseParam($options, 'ns20:DRMRevocationOptions');

		$result = $this->doCall("revokeWMRMLicenses", $params, 'ComcastArrayOfstring');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $endUserID
	 * @param string $creditCardNumber
	 * @param int $creditCardExpirationMonth
	 * @param int $creditCardExpirationYear
	 * @param int $creditCardSecurityCode
	 * 
	 **/
	public function generateCreditCardToken($endUserID, $creditCardNumber, $creditCardExpirationMonth, $creditCardExpirationYear, $creditCardSecurityCode)
	{
		$params = array();
		
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');
		$params["creditCardNumber"] = $this->parseParam($creditCardNumber, 'xsd:string');
		$params["creditCardExpirationMonth"] = $this->parseParam($creditCardExpirationMonth, 'xsd:int');
		$params["creditCardExpirationYear"] = $this->parseParam($creditCardExpirationYear, 'xsd:int');
		$params["creditCardSecurityCode"] = $this->parseParam($creditCardSecurityCode, 'xsd:int');

		$result = $this->doCall("generateCreditCardToken", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $endUserID
	 * 
	 **/
	public function validateCreditCardToken($endUserID)
	{
		$params = array();
		
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');

		$result = $this->doCall("validateCreditCardToken", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param string $userName
	 * @param string $password
	 * 
	 * @return long
	 **/
	public function authenticateEndUser($userName, $password)
	{
		$params = array();
		
		$params["userName"] = $this->parseParam($userName, 'xsd:string');
		$params["password"] = $this->parseParam($password, 'xsd:string');

		$result = $this->doCall("authenticateEndUser", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastDigest $digest
	 * 
	 * @return long
	 **/
	public function authenticateEndUserDigest(ComcastDigest $digest)
	{
		$params = array();
		
		$params["digest"] = $this->parseParam($digest, 'ns12:Digest');

		$result = $this->doCall("authenticateEndUserDigest", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastKeySettings $keySettings
	 * 
	 * @return long
	 **/
	public function authenticateEndUserKey(ComcastKeySettings $keySettings)
	{
		$params = array();
		
		$params["keySettings"] = $this->parseParam($keySettings, 'ns12:KeySettings');

		$result = $this->doCall("authenticateEndUserKey", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $endUserPermissionID
	 * @param string $creditCardNumber
	 * @param int $creditCardExpirationMonth
	 * @param int $creditCardExpirationYear
	 * @param int $creditCardSecurityCode
	 * 
	 **/
	public function updateCreditCard($endUserPermissionID, $creditCardNumber, $creditCardExpirationMonth, $creditCardExpirationYear, $creditCardSecurityCode)
	{
		$params = array();
		
		$params["endUserPermissionID"] = $this->parseParam($endUserPermissionID, 'xsd:long');
		$params["creditCardNumber"] = $this->parseParam($creditCardNumber, 'xsd:string');
		$params["creditCardExpirationMonth"] = $this->parseParam($creditCardExpirationMonth, 'xsd:int');
		$params["creditCardExpirationYear"] = $this->parseParam($creditCardExpirationYear, 'xsd:int');
		$params["creditCardSecurityCode"] = $this->parseParam($creditCardSecurityCode, 'xsd:int');

		$result = $this->doCall("updateCreditCard", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastPaymentGateway $paymentGateway
	 * @param string $report
	 * 
	 * @return string
	 **/
	public function reconcilePaymentGatewayReport(ComcastPaymentGateway $paymentGateway, $report)
	{
		$params = array();
		
		$params["paymentGateway"] = $this->parseParam($paymentGateway, 'ns16:PaymentGateway');
		$params["report"] = $this->parseParam($report, 'xsd:string');

		$result = $this->doCall("reconcilePaymentGatewayReport", $params);
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $endUserID
	 * @param ComcastArrayOfStorefrontOrder $orders
	 * @param ComcastArrayOfstring $couponCodes
	 * @param string $endUserIPAddress
	 * 
	 * @return ComcastArrayOffloat
	 **/
	public function priceStorefrontOrders($endUserID, array $orders, array $couponCodes, $endUserIPAddress)
	{
		$params = array();
		
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');
		$params["orders"] = $this->parseParam($orders, 'ns20:ArrayOfStorefrontOrder');
		$params["couponCodes"] = $this->parseParam($couponCodes, 'ns14:ArrayOfstring');
		$params["endUserIPAddress"] = $this->parseParam($endUserIPAddress, 'xsd:string');

		$result = $this->doCall("priceStorefrontOrders", $params, 'ComcastArrayOffloat');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $endUserID
	 * @param ComcastArrayOfStorefrontOrder $orders
	 * @param ComcastArrayOfstring $couponCodes
	 * @param string $endUserIPAddress
	 * 
	 * @return ComcastIDList
	 **/
	public function placeStorefrontOrders($endUserID, array $orders, array $couponCodes, $endUserIPAddress)
	{
		$params = array();
		
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');
		$params["orders"] = $this->parseParam($orders, 'ns20:ArrayOfStorefrontOrder');
		$params["couponCodes"] = $this->parseParam($couponCodes, 'ns14:ArrayOfstring');
		$params["endUserIPAddress"] = $this->parseParam($endUserIPAddress, 'xsd:string');

		$result = $this->doCall("placeStorefrontOrders", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $endUserID
	 * @param ComcastArrayOfStorefrontOrder $orders
	 * @param ComcastStorefrontOrderOptions $options
	 * @param string $creditCardNumber
	 * @param int $creditCardExpirationMonth
	 * @param int $creditCardExpirationYear
	 * @param int $creditCardSecurityCode
	 * 
	 * @return ComcastIDList
	 **/
	public function placeStorefrontOrdersWithCard($endUserID, array $orders, ComcastStorefrontOrderOptions $options, $creditCardNumber, $creditCardExpirationMonth, $creditCardExpirationYear, $creditCardSecurityCode)
	{
		$params = array();
		
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');
		$params["orders"] = $this->parseParam($orders, 'ns20:ArrayOfStorefrontOrder');
		$params["options"] = $this->parseParam($options, 'ns21:StorefrontOrderOptions');
		$params["creditCardNumber"] = $this->parseParam($creditCardNumber, 'xsd:string');
		$params["creditCardExpirationMonth"] = $this->parseParam($creditCardExpirationMonth, 'xsd:int');
		$params["creditCardExpirationYear"] = $this->parseParam($creditCardExpirationYear, 'xsd:int');
		$params["creditCardSecurityCode"] = $this->parseParam($creditCardSecurityCode, 'xsd:int');

		$result = $this->doCall("placeStorefrontOrdersWithCard", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param long $endUserID
	 * @param ComcastArrayOfStorefrontOrder $orders
	 * @param ComcastStorefrontOrderOptions $options
	 * 
	 * @return ComcastIDList
	 **/
	public function placeStorefrontOrdersUsingCardToken($endUserID, array $orders, ComcastStorefrontOrderOptions $options)
	{
		$params = array();
		
		$params["endUserID"] = $this->parseParam($endUserID, 'xsd:long');
		$params["orders"] = $this->parseParam($orders, 'ns20:ArrayOfStorefrontOrder');
		$params["options"] = $this->parseParam($options, 'ns21:StorefrontOrderOptions');

		$result = $this->doCall("placeStorefrontOrdersUsingCardToken", $params, 'ComcastIDList');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastSystemStatusTemplate $template
	 * 
	 * @return ComcastSystemStatus
	 **/
	public function getSystemStatus(ComcastSystemStatusTemplate $template)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:SystemStatusTemplate');

		$result = $this->doCall("getSystemStatus", $params, 'ComcastSystemStatus');
		$this->logError();
		return $result;
	}
	
	/**
	 * 
	 * @param ComcastSystemRequestLogTemplate $template
	 * @param ComcastQuery $query
	 * 
	 * @return ComcastSystemRequestLog
	 **/
	public function getSystemRequestLog(ComcastSystemRequestLogTemplate $template, ComcastQuery $query)
	{
		$params = array();
		
		$params["template"] = $this->parseParam($template, 'ns24:SystemRequestLogTemplate');
		$params["query"] = $this->parseParam($query, 'ns12:Query');

		$result = $this->doCall("getSystemRequestLog", $params, 'ComcastSystemRequestLog');
		$this->logError();
		return $result;
	}
	
}		
	
