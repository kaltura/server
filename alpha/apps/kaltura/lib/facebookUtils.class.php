<?php

class facebookUtils
{
	static public function getCustomDataXml($data)
	{
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $data );
		$xpath = new DOMXPath($xml_doc);
		
		return array($xml_doc, $xpath);
	}
	
	static public function setAttribute($xpath, $xpathSelector, $attribute, $value)
	{
		$nodes = $xpath->query($xpathSelector);
		
		foreach ( $nodes as $node )
			$node->setAttribute ( $attribute , $value );
	}
	
	static public function getAttribute($xpath, $xpathSelector, $attribute)
	{
		$nodes = $xpath->query($xpathSelector);
		
		foreach ( $nodes as $node )
			return $node->getAttribute ( $attribute );
			
		return null;
	}
	
	static public function getUsersListFromCustomData($data, $userRoleString, $users = null)
	{
		list($xml_doc, $xpath) = self::getCustomDataXml($data);

		$usersNodes = $xpath->query("//contributor");
		$newUsers = array();
		
		//"<contributor uid='$puser_id' name='$name' pic='$pic' sex='$sex'/>";
		foreach ( $usersNodes as $userNode)
		{
			$uid = $userNode->getAttribute ( "uid" );
			$name = $userNode->getAttribute ( "name" );
			$pic = $userNode->getAttribute ( "pic" );
			$sex = $userNode->getAttribute ( "sex" );
			
			if ($users === null || !array_key_exists($uid, $users))
			{
				$newUsers[$uid] = array(
					"uid" => $uid,
					"name" => $name,
					"pic" => $pic,
					"sex" => $sex);
			}
		}
		
		return $newUsers;
	}
	
	static public function rebuildUsersListXml($users)
	{
		//"<contributor uid='$puser_id' name='$name' pic='$pic' sex='$sex'/>";
		$data = "";
		
		foreach($users as $user)	
		{
			$puser_id = $user['uid'];
			$name = $user['name'];
			$pic = $user['pic'];
			$sex = $user['sex'];
			
			$data .= "<contributor uid='$puser_id' name='".kString::xmlEncode($name)."' pic='$pic' sex='$sex'/>";
		}
		
		return $data;
	}
	
	static public function replaceXmlTag($xmlData, $tag, $content)
	{
		$pattern = "<$tag/>";
		if (strpos($xmlData, $pattern) === FALSE)
			$pattern = '<'.$tag.'>.*?<\/'.$tag.'>';
		else
			$pattern = "<$tag\/>";
			
		return preg_replace("/$pattern/", '<'.$tag.'>'.$content.'</'.$tag.'>', $xmlData, 1);
	}
	
	static public function getMakeoverEntries($subp_id, $puser_id, $puser_ids)
	{
		$c = new Criteria();
		$c->add(entryPeer::TYPE, entryType::MEDIA_CLIP);
		$c->add(entryPeer::STATUS, entryStatus::READY);
		$c->addSelectColumn("MAX(".entryPeer::ID.")");
		$c->addSelectColumn(PuserRolePeer::PUSER_ID);
		$c->addGroupByColumn(PuserRolePeer::KSHOW_ID);
		$c->addJoin(entryPeer::KSHOW_ID, PuserRolePeer::KSHOW_ID);
		$c->add(PuserRolePeer::SUBP_ID, $subp_id);
		$c->add(PuserRolePeer::ROLE, PuserRole::PUSER_ROLE_RECIPIENT);
		$c->add(PuserRolePeer::PUSER_ID, explode(",", $puser_ids.','.$puser_id), Criteria::IN);
		$rs = entryPeer::doSelectStmt($c);

		$entry_ids = array();
		$entry_puser_ids = array();
	
		$res = $rs->fetchAll();
		foreach($res as $record) 
		{
			$entry_ids[] = $record[0];
			$entry_puser_ids[] = $record[1];
		}
		
//		// old code from doSelectRs
//		while($rs->next())
//		{
//			$entry_ids[] = $rs->getInt(1);
//			$entry_puser_ids[] = $rs->getString(2);
//		}
		
		return array($entry_ids, $entry_puser_ids);
	}
	
	static public function getFriendsMakover($subp_id, $puser_id)
	{
		$c = new Criteria();
		$c->add(PuserRolePeer::SUBP_ID, $subp_id);
		$c->add(PuserRolePeer::PUSER_ID, $puser_id);
		$c->add(PuserRolePeer::ROLE, PuserRole::PUSER_ROLE_RECIPIENT);
		$puserRoles = PuserRolePeer::doSelectJoinkshow($c);
		
		if (!$puserRoles)
			return array(0, null);
		
		$kshow = $puserRoles[0]->getkshow();
		$kshow_id = $kshow->getId();
			
		// fetch the roughcut which is not the default roughcut
		$c = new Criteria();
		$c->add(entryPeer::KSHOW_ID, $kshow_id);
		$c->add(entryPeer::TYPE, entryType::MIX);
		$c->add(entryPeer::ID, $kshow->getShowEntryId(), Criteria::NOT_EQUAL);
		
		$roughcut_entry = entryPeer::doSelectOne($c);
		
		return array($kshow_id, $roughcut_entry);
	}
	
	static public function createFriendsMakeover ( $subp_id, $puser_id, $puser_ids )
	{
		list($kshow_id, $roughcut_entry) = self::getFriendsMakover($subp_id, $puser_id);
		
		if (!$kshow_id)
			return array(0, 0, 0);
		
		$kshow = kshowPeer::retrieveByPK($kshow_id);
		
		if (!$roughcut_entry) // create a new roughcut
		{
			$roughcut_entry = new entry();
	 
			$roughcut_entry->setKshowId($kshow->getId () );
			$roughcut_entry->setKuserId($kshow->getProducerId());
			$roughcut_entry->setPartnerId($kshow->getPartnerId() );
			$roughcut_entry->setSubpId( $kshow->getSubpId() );
			$roughcut_entry->setStatus(entryStatus::READY);
			$roughcut_entry->setThumbnail( "&kal_show.jpg");
			$roughcut_entry->setType(entryType::MIX);
			$roughcut_entry->setMediaType(entry::ENTRY_MEDIA_TYPE_SHOW);
			$roughcut_entry->setName("Kaltura Video");
			$roughcut_entry->setTags("");
	
			$roughcut_entry->save();
		}
		
		list($entry_ids, $entry_puser_ids) = self::getMakeoverEntries($subp_id, $puser_id, $puser_ids);
		
		$custom_data = implode(",", $entry_puser_ids);
		if ($roughcut_entry->getFromCustomData("facelift", $subp_id) == $custom_data) // if the users list didnt change use the current roughcut
			return array($kshow_id, $roughcut_entry->getId(), 0);
		
		$c = new Criteria();
		$c->add(entryPeer::ID, $entry_ids, Criteria::IN);
		$entries = entryPeer::doSelect($c);
		
		self::createKEditorMetadata($kshow, $roughcut_entry, $entries);
		$roughcut_entry->putInCustomData("facelift", $custom_data, $subp_id);
		$roughcut_entry->save();
		
		return array($kshow_id, $roughcut_entry->getId(), 1);
	}
	
	static public function createMakeoverRoughcut($kshow_id)
	{
		$c = new Criteria();
		$c->add(entryPeer::KSHOW_ID, $kshow_id);
		$c->add(entryPeer::TYPE, entryType::MEDIA_CLIP);
		$c->add(entryPeer::STATUS, entryStatus::READY);
		
		$entries = entryPeer::doSelect($c);
		
		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		
		self::createKEditorMetadata($kshow, $kshow->getShowEntry(), $entries);
	}
	
	static public function createKEditorMetadata ($kshow, $show_entry, $entries )
	{
		$vidassets = '';
		$overlays = '';
		$totalTime = 0;
		
		$soundtrackEntry = null;
		$soundTimes = array();
		$lastSoundTime = 0;
		$isPrevVideo = null;
		$addLastFadeoutTime = 0;
		
		foreach($entries as $entry)
		{
			$assetStartTime = $totalTime;
  			
			$media_type = $entry->getMediaType();
			if ( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE || $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO)
			{
				$startTime = 0;
				$lenTime = $entry->getLengthInMsecs() / 1000;
				
				if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO)
				{
					$isVideo = true;
					$media_type_str = 'VIDEO';
				}
				else if ($media_type == entry::ENTRY_MEDIA_TYPE_IMAGE)
				{
					$isVideo = false;
					$media_type_str = 'IMAGE';
					$lenTime = 4;
				}
				else
					continue;

  				$addLastFadeoutTime = 1;
				$totalTime += $lenTime - 1;
			
	  			$entry_id = $entry->getId();
	  			$media_name = $entry->getName();
	  			
				$media_url = $entry->getDataUrl();
				$relMedia_url = strstr($media_url, "/content");
				
				
				$vidassets .= 
					'<vidAsset k_id="'.$entry_id.'" type="'.$media_type_str.'" name="'.$media_name.'" url="'.$media_url.'">'.
						'<StreamInfo file_name="'.$relMedia_url.'" start_time="'.$startTime.'" len_time="'.$lenTime.'" posX="0" posY="0" start_byte="-1" end_byte="-1" total_bytes="-1" real_seek_time="-1" volume="1" pan="0" isSingleFrame="0" real_start_byte="-1" real_end_byte="-1"/>'.
//					'<EndTransition type="None" StartTime="'.$lenTime.'" length="0"/>'.
						'<EndTransition type="dissolve" StartTime="'.($lenTime-1).'" length="1">'.
							'<arguments>'.
								'<name>dissolve</name>'.
								'<version>1.00</version>'.
								'<arguments/>'.
							'</arguments>'.
						'</EndTransition>'.
					'</vidAsset>';
			}
			else if ( $type == "soundtrack")
			{
				$soundtrackEntry = $entry;
	  			continue;
			}
			
			if ($isPrevVideo === null)
				$isPrevVideo = $isVideo;
			
			if ($isVideo != $isPrevVideo)
			{
				$endTime = $isVideo ? $assetStartTime : ($assetStartTime + 1);
				$soundTimes[] = array("type" => $isPrevVideo, "startTime" => $lastSoundTime, "endTime" => $endTime);
				
				$isPrevVideo = $isVideo;
				$lastSoundTime = $endTime;
			}
  		}
  		
  		$totalTime += $addLastFadeoutTime;
		
		$soundTimes[] = array("type" => $isPrevVideo, "startTime" => $lastSoundTime, "endTime" => $totalTime);
		
		// add soundtrack
		
		//echo print_r($soundTimes, true);
		
		$audassets = '';
		
		$entry = entryPeer::retrieveByPK(209);
		$loop = true;
		$quiet = true;
		$volume = 1;
		$quietVolume = $quiet ? $volume * 0.2 : 0;
		$lenTime = $entry->getLengthInMsecs() / 1000;
		
		if ($entry && $totalTime)
		{
			$entry_id = $entry->getId();
			$media_name = $entry->getName();
			$media_url = $entry->getDataUrl();
			$relMedia_url = strstr($media_url, "/content");
  			
  			$startTime = 0;
  			$soundTime = current($soundTimes);
  			$currentVolume = $soundTime["type"] ? $quietVolume : $volume;
  			
  			// the first sound time is always at the start of the first clip.
  			// because every clip gets a volPoint at its begining and end we can just skip this entry
  			$soundTime = next($soundTimes);
  			
  			while($startTime < $totalTime)
  			{
  				$clippedLenTime = min($totalTime - $startTime, $lenTime);
  				
  				$endTime = $startTime + $clippedLenTime;
  				
  				// always add the required volume at the start of the clip
  				$volPoints = '<VolumePoints><VolumePoint time="0" volume="'.$currentVolume.'"/>';

  				// add any volume changes found within the duration of the current clip
  				while($soundTime !== FALSE && $soundTime['startTime'] < $endTime)
  				{
  					// make a spike by putting the current volume and then the next one within 0.1 seconds
  					$volPoints .= '<VolumePoint time="'.($soundTime["startTime"] - $startTime - 0.1).'" volume="'.$currentVolume.'"/>';
  					$currentVolume = $soundTime["type"] ? $quietVolume : $volume;
  					$volPoints .= '<VolumePoint time="'.($soundTime["startTime"] - $startTime).'" volume="'.$currentVolume.'"/>';
		  			$soundTime = next($soundTimes);
  				}
  				
  				// always add the required volume at the end of the clip
  				$volPoints .= '<VolumePoint time="'.$clippedLenTime.'" volume="'.$currentVolume.'"/></VolumePoints>';
  				
				$audassets .= 
						'<AudAsset k_id="'.$entry_id.'" type="AUDIO" name="'.$media_name.'" url="'.$media_url.'">'.
							'<StreamInfo file_name="'.$relMedia_url.'" start_time="0" len_time="'.$clippedLenTime.'" posX="0" posY="0" start_byte="-1" end_byte="-1" total_bytes="-1" real_seek_time="-1" volume="'.$volume.'" pan="0" isSingleFrame="0" real_start_byte="-1" real_end_byte="-1"/>'.
							'<EndTransition type="None" StartTime="'.$lenTime.'" length="0"/>'.
							$volPoints.
						'</AudAsset>';
						
  				if (!$loop)
					break;
					
				$startTime += $lenTime;
  			}
		}

		$xmlData =
			'<?xml version="1.0"?>'.
			"<xml>".
				"<MetaData>".
					"<SeqDuration>$totalTime</SeqDuration>".
					"<ShowVersion></ShowVersion>".
				"</MetaData>".
				"<VideoAssets>$vidassets</VideoAssets><AudioAssets>$audassets</AudioAssets><VoiceAssets/><LoaderObjectAssets/>".
				"<Plugins><Overlays>$overlays</Overlays></Plugins>".
			"</xml>";
			
		myMetadataUtils::setMetadata($xmlData, $kshow, $show_entry);
	}	
	
}
?>