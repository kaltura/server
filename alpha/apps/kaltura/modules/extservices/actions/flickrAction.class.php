<?php

/**
 * extservices actions.
 *
 * @package    Core
 * @subpackage externalServices
 */
class flickrAction extends kalturaAction
{
	public function execute()
	{
		$this->followRedirectCookie();
		
		$frob = @$_REQUEST['frob'];
			
		$kalt_token = @$_COOKIE['flickr_kalttoken'];

		if (!$kalt_token)
		{
			$kuserId = $this->getLoggedInUserId();
			if ($kuserId)
				$kalt_token = $kuserId.':';
		}
		else
			$kalt_token = base64_decode($kalt_token);

		if (!$frob || !$kalt_token)
			return;

		myFlickrServices::setKuserToken($kalt_token, $frob);

		return;
	}
}
