<?php

//require_once ( "kalturaAction.class.php" );
//require_once("KalturaClient.php");

class createmixAction extends kalturaAction
{
	public function execute ( )
	{
		require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "api_v3" . DIRECTORY_SEPARATOR . "bootstrap.php");
		
		$ks = $this->getP("ks");
		$userId = $this->getP("user_id");
		$entryId = $this->getP("entry_id");
		$entryName = $this->getP("entry_name");
		$editorType = $this->getP("editor_type");
		if($editorType == "2" )
		{
			$editorType = entry::MIX_EDITOR_TYPE_ADVANCED;
			$mixType = " - Advanced Mix";
		}
		else
		{
			$editorType = entry::MIX_EDITOR_TYPE_SIMPLE;
			$mixType = " - Mix";
		}
		$entryName = $entryName . $mixType;
		$dispatcher = KalturaDispatcher::getInstance();
		
			$params = array(
				"ks"=> $ks, 
				"mixEntry:name" => $entryName,
				"mixEntry:editorType" => $editorType,
				"userId" => $userId,
			);
			try{
				$mix = $dispatcher->dispatch("mixing", "add", $params);
			}
			catch(Exception $ex)
			{
				echo $ex->getMessage(); die;
			}
			
			$params = array(
				"ks"=> $ks, 
				"mixEntryId" => $mix->id,
				"mediaEntryId" => $entryId
			);
			try{
				$mix = $dispatcher->dispatch("mixing", "appendMediaEntry", $params);
			}
			catch(Exception $ex)
			{
				echo $ex->getMessage(); die;
			}
//			var_dump($mix);
			echo $mix->id;
			die;
	}
}

