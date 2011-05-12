<?php
/**
 * wraps a real kshow object and copies only very little data to be stored in some cache.
 * all the baseobject classes handle DB issues and are very heavy when persisting.
 */
class kshowStub extends myBaseObject
{

	public function kshowStub ( kshow $kshow )
	{
		$this->init();
		$this->fillObjectFromObject( $kshow , 
			self::CLONE_FIELD_POLICY_THIS, 
			self::CLONE_POLICY_PREFER_NEW, 
/* fields to ignore */
			array ( "producer_name" , "team_picture_path" , "thumbnail_path" ,"type_text" , "intro_url" , "intro_duration" , 
				"intro_duration_formatted" , "intro_created_at_formatted" , "intro_thumbnail", "custom_data_3" ) ,   
/* envoke getters */			
			array ( "producer_name" => "kuser.screenName" , 
					"team_picture_path" => "teamPicturePath" , 
					"thumbnail_path" => "thumbnailPath" ,
					"type_text" => "typeText" , 
					"intro_url" => "intro.dataPath"	,
					"intro_duration" => "intro.lengthInMsecs" ,
					"intro_duration_formatted" => "intro.formattedLengthInMsecs" , 
					"intro_created_at_formatted" => "intro.formattedCreatedAt" ,
					"intro_thumbnail" => "intro.thumbnailPath" ,
					"custom_data_3" => "indexedCustomData3"));
	}

	
	protected function init ()
	{
		if ( $this->fields == NULL )
			$this->fields = kArray::makeAssociative( array ( "id" , "name" , "type" , 
				"type_text" , "producer_name" , "rank", "views" , "entries" , "team_picture_path" , "thumbnail_path", "intro_id" , 
				"intro_url" , "intro_duration" , "intro_duration_formatted" , "intro_created_at_formatted" ,"intro_thumbnail" , "custom_data_3" ));
	}

	public function getId() 
	{
		return $this->getByName ( "id");
	}
	
	public function getName()
	{
		return $this->getByName ( "name");
	}
	
	public function getTeamPicturePath()
	{
		return $this->getByName ( "team_picture_path");
	}
	
	public function getThumbnailPath()
	{
		return $this->getByName ( "thumbnail_path");
	}

	public function getTypeText()
	{
		return $this->getByName ( "type_text");
	}
	

	
}

?>