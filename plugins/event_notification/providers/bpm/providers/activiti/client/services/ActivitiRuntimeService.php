<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');

	

class ActivitiRuntimeService extends ActivitiService
{
	
	/**
	 * Signal event received
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15E72 Signal event received}
	 */
	public function signalEventReceived($signalName, $tenantId = null, $async = null, array $variables = null)
	{
		$data = array(
			'signalName' => $signalName
		);
		
		if(!is_null($tenantId))
			$data['tenantId'] = $tenantId;
		if(!is_null($async))
			$data['async'] = $async;
		if(!is_null($variables))
			$data['variables'] = $variables;
		
		return $this->client->request("runtime/signals", 'POST', $data, array(200,202), array(400 => "Signal not processed. The signal name is missing or variables are used toghether with async, which is not allowed. Response body contains additional information about the error."));
	}
	
}

