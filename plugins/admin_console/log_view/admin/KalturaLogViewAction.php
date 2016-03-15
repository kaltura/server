<?php
/**
 * @package plugins.logView
 * @subpackage admin
 */
class KalturaLogViewAction extends KalturaApplicationPlugin
{
	
	public function __construct()
	{
		$this->action = 'KalturaLogViewAction';
		$this->label = null;
		$this->rootLabel = null;
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array();
	}

	private function query($type, array $condition, array $sort, $size, array $fields = null)
	{
		$post = array(
				"query" => array(
						"terms" => $condition,
				),
				"sort" => $sort,
				"size" => $size
		);
		if($fields)
		{
			$post["fields"] = $fields;
		}
	
		$post = json_encode($post);
	
		$settings = Zend_Registry::get('config')->settings;
		$logViewUrl = 'http://localhost:9200';
		if(isset($settings->logViewUrl))
		{
			$logViewUrl = $settings->logViewUrl;
		}
	
		$index = '*';
		if($type)
		{
			$index .= "/$type";
		}
	
		$url = "$logViewUrl/$index/_search";
	
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$results = curl_exec($ch);
		curl_close($ch);
	
		return json_decode($results);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
	
		$field = $request->getParam('field', null);
		$id = $request->getParam('id', null);
		$type = $request->getParam('type', null);
	
		$reqponse = $this->query($type, array("{$field}Id" => array($id)), array("@timestamp" => "desc"), 100, array("sessionId"));
		$json = array();
		if(isset($reqponse->hits) && isset($reqponse->hits->hits))
		{
			$sessions = array();
			foreach($reqponse->hits->hits as $hit)
			{
				if(isset($hit->fields) && isset($hit->fields->sessionId))
				{
					foreach($hit->fields->sessionId as $sessionId)
					{
						if(!in_array("$sessionId", $sessions))
							$sessions[] = "$sessionId";
					}
				}
			}
			$reqponse = $this->query($type, array("sessionId" => $sessions), array("@timestamp" => "asc", "sessionIndex" => "asc"), 100000);
				
			if(isset($reqponse->hits) && isset($reqponse->hits->hits))
			{
				$json = $reqponse->hits->hits;
			}
		}
	
		echo $action->getHelper('json')->sendJson($json, false);
	}
}

