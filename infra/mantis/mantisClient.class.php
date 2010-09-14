<?php

class MantisClient extends nusoap_client
{
	var $soapClient = null;
	var $username = "";
	var $password = "";
	
	function __construct($wsdlUrl, $username, $password)
	{
		$this->username = $username;
		$this->password = $password;
		
		parent::__construct($wsdlUrl, 'wsdl');
	}
	
	function logError()
	{
		if ($this->getError())
			KalturaLog::err("MantisClient error calling operation: [".$this->operation."], error: [".$this->getError()."], request: [".$this->request."], response: [".$this->response."]");
	}
	
	function getBaseParams()
	{
		return array ("username" => $this->username, "password" => $this->password);
	}
	
	function getMyProjects()
	{
		$params = $this->getBaseParams();
		$result = $this->call("mc_projects_get_user_accessible", $params);
		$this->logError();
		return $result;
	}
	
	function getProjectCustomFields($projectId)
	{
		$params = $this->getBaseParams();
		$params["project_id"] = $projectId;
		$result = $this->call("mc_project_get_custom_fields", $params);
		$this->logError();
		return $result;
	}
	
	function getProjectVersions($projectId)
	{
		$params = $this->getBaseParams();
		$params["project_id"] = $projectId;
		$result = $this->call("mc_project_get_versions", $params);
		$this->logError();
		return $result;
	}
	
	function addIssue($issue)
	{
		$params = $this->getBaseParams();
		$params["issue"] = $issue;
		$result = $this->call("mc_issue_add", $params);
		$this->logError();
		return $result;
	}
	
	function addAttachmentToIssue($issueId, $fileName, $fileType, $fileContent)
	{
		$params = $this->getBaseParams();
		$params["issue_id"] = $issueId;
		$params["name"] = $fileName;
		$params["file_type"] = $fileType;
		$params["content"] = $fileContent;
		$result = $this->call("mc_issue_attachment_add", $params);
		$this->logError();
		return $result;
	}
}
?>