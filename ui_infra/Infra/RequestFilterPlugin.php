<?php

class Infra_RequestFilterPlugin extends Zend_Controller_Plugin_Abstract
{
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		$params = $request->getParams();
		foreach ($params as $param => $val)
		{
			$request->setParam($param, $this->handleHtmlSpecialChars($val));
		}
	}
	
	private function handleHtmlSpecialChars($val)
	{
		if(!is_array($val))
		{
			return htmlspecialchars($val);
		}
		
		$res = array();
		foreach($val as $k => $v)
		{
			if(!is_array($v))
			{
				$res[$k] = htmlspecialchars($v);
			}
			else
			{
				$res[$k] = $this->handleHtmlSpecialChars($v);
			}
		}
		
		return $res;
	}
}
