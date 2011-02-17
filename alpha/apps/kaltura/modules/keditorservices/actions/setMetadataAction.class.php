<?php
/**
 * @package    Core
 * @subpackage kEditorServices
 */
require_once ( "myFlvStreamer.class.php");
require_once ( "myMetadataUtils.class.php");

require_once ( "defKeditorservicesAction.class.php");
require_once ( "myStatisticsMgr.class.php");
require_once ( "myKshowUtils.class.php");

/**
 * @package    Core
 * @subpackage kEditorServices
 */
class setMetadataAction extends defKeditorservicesAction
{
	/**
	 * Executes addComment action, which returns a form enabling the insertion of a comment
	 * The request may include 1 fields: entry id.
	 */
	protected function executeImpl( kshow $kshow, entry &$entry )
	{
		$kshow_id = $kshow->getId();
		
		
		if ( $this->partner_id != null )
		{
			// this part overhere should be in a more generic place - part of the services
			$multiple_roghcuts = Partner::allowMultipleRoughcuts( $this->partner_id );
			$likuser_id = $this->getLoggedInUserId();
		}
		else
		{
			// 	is the logged-in-user is not an admin or the producer - check if show can be published	
			$likuser_id = $this->getLoggedInUserId();
			$multiple_roghcuts = true;
		}
		
		if (!$likuser_id)
			return $this->securityViolation( $kshow->getId() );		
		
		$isIntro = $kshow->getIntroId() == $entry->getId();
		
		if ( $multiple_roghcuts )
		{
			// create a new entry in two cases:
			// 1. the user saving the roughcut isnt the owner of the entry
			// 2. the entry is an intro and the current entry is not show (probably an image or video)
			if ($entry->getKuserId() != $likuser_id || $isIntro && $entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_SHOW)
			{
				// TODO: add security check to whether multiple roughcuts are allowed
				
				// create a new roughcut entry by cloning the original entry
				$entry = myEntryUtils::deepClone($entry, $kshow_id, false);
				$entry->setKuserId($likuser_id);
				$entry->setCreatedAt(time());
				$entry->setMediaType(entry::ENTRY_MEDIA_TYPE_SHOW);
				$entry->save();
			}
		}
		/*
		$viewer_type = myKshowUtils::getViewerType($kshow, $likuser_id);
		if ( $viewer_type != KshowKuser::KSHOWKUSER_VIEWER_PRODUCER && ( ! $kshow->getCanPublish() ) ) 
		{
			// ERROR - attempting to publish a non-publishable show
			return $this->securityViolation( $kshow->getId() );
		}
*/
		
		$this->xml_content = "<xml><EntryID>".$entry->getId()."</EntryID></xml>";
		
		if ($isIntro)
		{
			$kshow->setIntroId($entry->getId());
		}
		else
		{
			$kshow->setShowEntryId($entry->getId());
			$has_roughcut =  @$_REQUEST["HasRoughCut"];
			if ( $has_roughcut === "0" )
			{
				$kshow->setHasRoughcut( false) ;
				$kshow->save();
				
				return ;
			}
		}
		
		$content = @$_REQUEST["xml"];
		$update_kshow = false;
		
		if ( $content != NULL )
		{
			list($xml_content, $this->comments, $update_kshow) = myMetadataUtils::setMetadata($content, $kshow, $entry);
			
			// Send an email alert to producer
			if( $kshow->getProducerId() != $likuser_id ) // don't send producer alerts when the producer is the editor 
				alertPeer::sendEmailIfNeeded(  $kshow->getProducerId(), 
									alert::KALTURAS_PRODUCED_ALERT_TYPE_ROUGHCUT_CREATED, 
									array ( 'screenname' => $this->getUser()->getAttribute('screenname'),
											'kshow_name' => $kshow->getName(),
											'kshow_id' => $kshow->getId() ) );
			
			// TODO:  efficiency: see if there is a wa to search for contributors based on some other method than full entry table scan
			// Send email alerts to contributors
			$c = new Criteria();
			$c->add(entryPeer::KSHOW_ID, $kshow_id);
			$c->add(entryPeer::KUSER_ID, $likuser_id, Criteria::NOT_EQUAL ); // the current user knows they just edited
			$c->addAnd(entryPeer::KUSER_ID, $kshow->getProducerId(), Criteria::NOT_EQUAL ); // the producer knows they just edited
			$c->add(entryPeer::TYPE, entryType::MEDIA_CLIP);
			$c->addGroupByColumn(entryPeer::KUSER_ID);
			$entries = entryPeer::doSelect( $c );
			$already_received_alert_array = array();
			foreach ( $entries as $entry )
			{
				alertPeer::sendEmailIfNeeded(  $entry->getKuserId(), 
									alert::KALTURAS_PARTOF_ALERT_TYPE_ROUGHCUT_CREATED, 
									array ( 'screenname' => $this->getUser()->getAttribute('screenname'),
											'kshow_name' => $kshow->getName(),
											'kshow_id' => $kshow->getId() ) );
				$already_received_alert_array[ $entry->getKuserId() ] = true;
			
			}								

			
			// send email alert to subscribers
			$c = new Criteria();
			$c->add(KshowKuserPeer::KSHOW_ID, $kshow_id); //only subsribers of this show
			$c->add(KshowKuserPeer::KUSER_ID, $likuser_id, Criteria::NOT_EQUAL ); // the current user knows they just edited
			$c->add(KshowKuserPeer::SUBSCRIPTION_TYPE, KshowKuser::KSHOW_SUBSCRIPTION_NORMAL); // this table stores other relations too
			$subscriptions = KshowKuserPeer::doSelect( $c );
			foreach ( $subscriptions as $subscription )
			{
					if( !isset($already_received_alert_array[ $subscription->getKuserId() ]) ) // don't send emails to subscribed users who are also contributors
						alertPeer::sendEmailIfNeeded(  $subscription->getKuserId(), 
									alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_ROUGHCUT_CREATED, 
									array ( 'screenname' => $this->getUser()->getAttribute('screenname'),
											'kshow_name' => $kshow->getName(),
											'kshow_id' => $kshow->getId()  ) );
			}								
			
			
			if ( $this->debug  )
			{
				return "text/html; charset=utf-8";
			}
		}
		else
		{
			$this->comments = "";
			// if there is no xml - receive it from the user 
			$this->debug=true;
			$file_name = myContentStorage::getFSContentRootPath() . "/" . $entry->getDataPath();
			//$this->xml_content = kFile::getFileContent( $file_name );
			return "text/html; charset=utf-8";
		}

	}

	protected function noSuchKshow ( $kshow_id )
	{
		$this->xml_content = "No such show [$kshow_id]";
	}
	


}

?>