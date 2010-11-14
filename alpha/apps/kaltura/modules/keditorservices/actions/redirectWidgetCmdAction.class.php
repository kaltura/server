<?php

class redirectWidgetCmdAction extends kalturaAction
{
	public function execute()
	{
		$kshow_id = $this->getRequestParameter( "kshow_id" , "0" );
		$entry_id = $this->getRequestParameter( "entry_id" , "0" );
		$cmd = $this->getRequestParameter( "cmd" , "" );
		$links_str = $this->getRequestParameter( "kdata" , "" );

		if ( $links_str )
		{
			$links_arr = @unserialize ( base64_decode (str_replace ( array ( "|02" , "|01" ) , array ( "/" , "|" ) , $links_str ) ) ) ;
			//if $cmd is one of the following - 
			// add , edit , kshow
			$link_to_follow = @$links_arr[$cmd];
			if ($link_to_follow)
			{
				// if the link is relative we concatenate it to the "base" link
				if (strstr($link_to_follow, "http") !== 0)
					$link_to_follow = @$links_arr['base'].$link_to_follow;
					
				header("status:302");
    			header("location: $link_to_follow");
    			die;
			}
		}
		
		if ($entry_id == -1)
		{
			$kshow = kshowPeer::retrieveByPK($kshow_id);
			if (!$kshow)
		  		$this->redirect('corp/userzone' );
			else
				$entry_id = $kshow->getShowEntryId();
		}
		
		if ($entry_id == 1002)
	  		$this->redirect('corp/userzone' );
		
		if ( $entry_id )
		{
			$entry = entryPeer::retrieveByPK( $entry_id );
			if ($entry)
			{
				if (!$kshow_id || $kshow_id == -1)
					$kshow_id = $entry->getKshowId();
					
				if (!@$kshow)
				{
					$kshow = kshowPeer::retrieveByPK($kshow_id);
					if (!$kshow)
				  		$this->redirect('corp/userzone' );
					if (!$kshow->getPartnerId() && !$this->forceViewPermissions ( $kshow, $kshow_id , false , false ))
				  		$this->redirect('corp/userzone' );
				}
					
				if ($cmd == "contribute" || $cmd == "contriubte" || $cmd == "add" )
				{
					if (!$kshow->getPartnerId() && !$this->forceContribPermissions ( $kshow, $kshow_id , false , false ))
				  		$this->redirect('corp/userzone' );
					//$this->getContext()->getResponse()->setCookie( 'browseCmd', 'contribute', time() + 31536000 , '/' );
					//$this->redirect( "/browse/browse2?kshow_id=$kshow_id&entry_id=$entry_id&browseCmd=contribute" );
					$this->redirect( "/corp/contribute?kshow_id=$kshow_id&entry_id=$entry_id&__temp=1" );
				}
				else if ($cmd == "edit")
				{
					if ($entry->getType() != entryType::MIX)
					{
						$kshow = kshowPeer::retrieveByPK($kshow_id);
						if (!$kshow)
				  			$this->redirect('corp/userzone' );
						else
							$entry_id = $kshow->getShowEntryId();
					}
					$this->redirect( "/edit/defEdit?kshow_id=$kshow_id&entry_id=$entry_id" );
				}
			}
		}
	}
}

?>