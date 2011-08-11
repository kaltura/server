<?php
class MonitoringController extends Zend_Controller_Action
{
	private $xymonUrl;
	private $baseUrl;
	
    public function init()
    {
		$settings = Zend_Registry::get('config')->settings;
		$this->xymonUrl = $settings->xymonUrl;
		
		$this->baseUrl = '/';
		$arr = null;
		if(preg_match('/(http:\/\/[^\/]+)/', $this->xymonUrl, $arr))
			$this->baseUrl = $arr[1];
    }

    public function getHtml($xymonUrl)
    {
		$request = $this->getRequest();
		
    	$requestUrl = $request->getParam('post', false);
    	if($requestUrl)
    	{
    		$xymonUrl = $this->baseUrl . '/' . $this->xymonUrlDecode($requestUrl);
    		$this->view->debugText = "TRY: $xymonUrl";
    		
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $xymonUrl);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
			
			$html = curl_exec($ch);
			if(!$html)
				$this->view->error = "POST is not accessable: $xymonUrl";
				
			return $html;
    	}
    	
    	$requestUrl = $request->getParam('url', false);
		if($requestUrl)
			$xymonUrl = $this->baseUrl . '/' . $this->xymonUrlDecode($requestUrl);
			
		$html = @file_get_contents($xymonUrl);
		if(!$html)
			$this->view->error = "URL is not accessable: $xymonUrl";
			
		return $html;
    }

    public function setHtml($xymonUrl, $actionUrl)
    {
    	$src = $this->getHtml($xymonUrl);
		if(!$src)
		{
			$this->view->html = null;
			return;
		}
		
		$src = str_replace(array("\n", "\r"), '', $src);
		$html = $src;
		if(preg_match('/<BODY[^>]*>(.+)<\/BODY>/', $src, $arr))
		{
			$html = $arr[1];
			
			$html = preg_replace('/<font[^>]*>/i', '', $html);
			$html = preg_replace('/<\/font\s*>/i', '', $html);
			
			// remove top & bottom tables 
			$html = preg_replace('/<TABLE SUMMARY="Topline" WIDTH="100%">(.*?)<\/TABLE>/i', '', $html);
			$html = preg_replace('/<TABLE SUMMARY="Bottomline" WIDTH="100%">(.*?)<\/TABLE>/i', '', $html);
			
			// some cleanup
			$html = preg_replace('/<A NAME=hosts-blk[\-0-9]*>&nbsp;<\/A>/', '', $html);
			$html = preg_replace('/<A NAME="(.*?)">&nbsp;<\/A>/', '', $html);
			
			// colors for history
			$html = preg_replace('/#000000/', '#B7BABC', $html);
			$html = preg_replace('/#555555/', '#F4F4F4', $html);
			
			if(preg_match_all('/href="\/([^"]*)"/i', $html, $arr))
			{
				$searches = array();
				$replaces = array();
				for($i = 0; $i < count($arr[0]); $i++)
				{
					$srcHref = $arr[0][$i];
					$srcUrl = $arr[1][$i];
					
					$destUrl = "$actionUrl/url/" . $this->xymonUrlEncode($srcUrl);
					$destHref = str_replace("/$srcUrl", $destUrl, $srcHref);
					
					$searches[] = $srcHref;
					$replaces[] = $destHref;
				}
				$html = str_replace($searches, $replaces, $html);
			}
		
			if(preg_match_all('/<form [^>]+action="\/([^"]*)"/i', $html, $arr))
			{
				$searches = array();
				$replaces = array();
				for($i = 0; $i < count($arr[0]); $i++)
				{
					$srcHref = $arr[0][$i];
					$srcUrl = $arr[1][$i];
					
					$destUrl = "$actionUrl/post/" . $this->xymonUrlEncode($srcUrl);
					$destHref = str_replace("/$srcUrl", $destUrl, $srcHref);
					
					$searches[] = $srcHref;
					$replaces[] = $destHref;
				}
				$html = str_replace($searches, $replaces, $html);
			}
			
			$html = preg_replace('/<script.+<\/script>/i', '', $html);
			$html = str_replace(array('SRC="/', 'src="/'), 'SRC="' . $this->baseUrl . '/', $html);
			if (strpos($html, '<INPUT TYPE=SUBMIT VALUE="HISTORY">'))
			{
				$html = $this->fixHistoryButton($html, "HISTORY");
			}
			else if (strpos($html, '<INPUT TYPE=SUBMIT VALUE="Full History">'))
			{
				$html = $this->fixHistoryButton($html, "Full History");
			}
			//echo $html;
		}
    	
		$this->view->html = $html;
    }

    public function xymonAction()
    {
    	$actionUrl = $this->view->url(array('controller' => 'monitoring', 'action' => 'xymon'), null, true);
		$this->setHtml($this->xymonUrl, $actionUrl);
    }

    public function ackAlertsAction()
    {
    	$actionUrl = $this->view->url(array('controller' => 'monitoring', 'action' => 'ack-alerts'), null, true);
    	$xymonUrl = $this->baseUrl . '/xymon-seccgi/bb-ack.sh';
		$this->setHtml($xymonUrl, $actionUrl);
    }

    public function enableDisableAction()
    {
    	$actionUrl = $this->view->url(array('controller' => 'monitoring', 'action' => 'enable-disable'), null, true);
    	$xymonUrl = $this->baseUrl . '/xymon-seccgi/hobbit-enadis.sh';
		$this->setHtml($xymonUrl, $actionUrl);
    }
    
    public function xymonUrlEncode($url)
    {
    	return str_replace('/', '@', base64_encode(html_entity_decode($url)));
    }
    
    public function xymonUrlDecode($url)
    {
    	return base64_decode(str_replace('@', '/', $url));
    }
    
    private function fixHistoryButton($html, $txt)
    {
    	preg_match('/<FORM ACTION=\"\/xymon-cgi\/bb-hist\.sh\">(.*)<\/FORM>/mi', $html, $out);
    	$formHtml = $out[0];
    	preg_match('/NAME="HISTFILE" VALUE="([^\"]*)"/', $formHtml, $histfile);
    	$histfile = $histfile[1];
        preg_match('/NAME="ENTRIES" VALUE="([^\"]*)"/', $formHtml, $entries);
    	$entries = $entries[1];
    	preg_match('/NAME="IP" VALUE="([^\"]*)"/', $formHtml, $ip);
    	$ip = $ip[1];
    	preg_match('/NAME="DISPLAYNAME" VALUE="([^\"]*)"/', $formHtml, $displayname);
    	$displayname = $displayname[1];
    	$url = "/xymon-cgi/bb-hist.sh?&HISTFILE=$histfile&ENTRIES=$entries&IP=$ip&DISPLAYNAME=$displayname";
    	$html = str_replace('<INPUT TYPE=SUBMIT VALUE="'.$txt.'">', '<INPUT TYPE=SUBMIT VALUE="'.$txt.'" onclick="window.location = \''.$this->xymonUrlEncode($url).'\';return false;">', $html);
    	
    	//<INPUT TYPE=HIDDEN NAME="HISTFILE" VALUE="testbox.bbd"> 			
    	//<INPUT TYPE=HIDDEN NAME="ENTRIES" VALUE="50"> 			
    	//<INPUT TYPE=HIDDEN NAME="IP" VALUE="192.168.192.239"> 			
    	//<INPUT TYPE=HIDDEN NAME="DISPLAYNAME" VALUE="testbox">
    	
    	return $html;
    }
    
}