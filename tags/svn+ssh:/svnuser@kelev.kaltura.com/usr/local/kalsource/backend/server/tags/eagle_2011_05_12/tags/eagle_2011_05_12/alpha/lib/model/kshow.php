<?php
require_once( 'myContentStorage.class.php');
require_once( 'dateUtils.class.php');
include_once( 'model/skinContainer.class.php');
require_once( 'model/ktagword.class.php');
require_once ( "myStatisticsMgr.class.php");
// TODO - add the skinContainer !
/**
 * Subclass for representing a row from the 'kshow' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class kshow extends Basekshow
{
	const SANDBOX_ID = "2";//301599;
	
	const KSHOW_ID_USE_DEFAULT = -1;
	const KSHOW_ID_CREATE_NEW = -2;
	
	private $skin_container = null;
	private $m_show_entry = null;
	private $m_intro = null;

	private $permissions_change = false;

	const MINIMUM_ID_TO_DISPLAY = 8999;

	// different sort orders for browsing kswhos
	const KSHOW_SORT_MOST_VIEWED = 0;
	const KSHOW_SORT_MOST_RECENT = 1;
	const KSHOW_SORT_MOST_COMMENTS = 2;
	const KSHOW_SORT_MOST_FAVORITES = 3;
	const KSHOW_SORT_PRIZE = 4;
	const KSHOW_SORT_END_DATE = 5;
	const KSHOW_SORT_MOST_ENTRIES = 6;
	const KSHOW_SORT_NAME = 7;
	const KSHOW_SORT_RANK = 8;
	const KSHOW_SORT_MOST_UPDATED = 9;
	const KSHOW_SORT_MOST_CONTRIBUTORS = 10;

	// enum for different status
	// status bit 1,2
	const KSHOW_STATUS_DRAFT = 0;
	const KSHOW_STATUS_TEST = 1;
	const KSHOW_STATUS_FINAL = 2;
	const KSHOW_STATUS_TEMPLATE = 3;

	// status bit 4
	const KSHOW_STATUS_HAS_ROUGHCUT = 8;

	// status bit 6
	const KSHOW_STATUS_PLAY_ROUGHCUT = 32;

	// status bit 7
	const KSHOW_STATUS_HAS_TEAM_IMAGE = 64;

	// status bit 8
	const KSHOW_STATUS_DISABLE_PUBLISH = 128;

	// status bit 9
	const KSHOW_STATUS_VISIBLE_TO_RECIPIENT = 256;

	// enum for kshow permissions
	const KSHOW_PERMISSION_NONE = -1;
	const KSHOW_PERMISSION_EVERYONE = 1;
	const KSHOW_PERMISSION_JUST_ME = 2;
	const KSHOW_PERMISSION_INVITE_ONLY = 3;
	const KSHOW_PERMISSION_REGISTERED = 4;

	const MAX_NORMALIZED_RANK = 5;

	const KSHOW_TYPE_GROUP_GREETING = 1;
	const KSHOW_TYPE_MUSIC_VIDEO = 2;
	const KSHOW_TYPE_PODCAST = 3;
	const KSHOW_TYPE_GROUP_TRAVELOGUE = 4;
	const KSHOW_TYPE_CONTEST = 5;
	const KSHOW_TYPE_FAN_SCRAPBOOK = 6;
	const KSHOW_TYPE_SPORTS = 7;
	const KSHOW_TYPE_DOCUMENTARY = 8;
	const KSHOW_TYPE_NEWSCAST = 9;
	const KSHOW_TYPE_DIGITAL_STORY = 10;
	const KSHOW_TYPE_CALL_FOR_ACTION = 11;
	const KSHOW_TYPE_MASHUP = 12;
	const KSHOW_TYPE_SLIDESHOW = 13;
	const KSHOW_TYPE_HOWTO = 14;
	const KSHOW_TYPE_OTHER = 0;

	private $roughcut_count = -1;

	public static function getColumnNames()	{		return array ( "name" , "description" , "tags" ) ; }
	public static function getSearchableColumnName () { return "search_text" ; }

	
	// for now we'll have it hard-coded and not read from DB
	private static $s_type_text = array
		(
		kshow::KSHOW_TYPE_GROUP_GREETING => "Group Greeting" ,
		kshow::KSHOW_TYPE_MUSIC_VIDEO => "Music video" ,
		kshow::KSHOW_TYPE_PODCAST => "Podcast" ,
		kshow::KSHOW_TYPE_GROUP_TRAVELOGUE => "Group travelogue" ,
		kshow::KSHOW_TYPE_CONTEST => "Contest" ,
		kshow::KSHOW_TYPE_FAN_SCRAPBOOK => "Fan scrapbook" ,
		kshow::KSHOW_TYPE_SPORTS => "Sports coverage" ,
		kshow::KSHOW_TYPE_DOCUMENTARY => "Documentary" ,
		kshow::KSHOW_TYPE_NEWSCAST => "Newscast & Events" ,
		kshow::KSHOW_TYPE_DIGITAL_STORY => "Digital story" ,
		kshow::KSHOW_TYPE_CALL_FOR_ACTION => "Call for action (activism)" ,
		kshow::KSHOW_TYPE_MASHUP => "Mashup" ,
		kshow::KSHOW_TYPE_SLIDESHOW => "Slideshow" ,
		kshow::KSHOW_TYPE_HOWTO => "Howto and advice",
		kshow::KSHOW_TYPE_OTHER => "Other" ,

		);
		/*
		//Background Title Links Description Headers
	private static $s_color_schemes = array(
		array('#303030', '#ffffff', '#7AAAC8', '#ffffff', '#c1da84'),	// default
		array('#FFFFFF', '#222222', '#97c3e6', '#FFFFFF', '#EEEEEE'),   // white
		array('#d65b9d', '#290414', '#ffb8e5', '#FFFFFF', '#FFFFFF'),	// pink
		array('#938255', '#FBF7EC', '#b0a177', '#FFFFFF', '#FFFFFF'),	// brown
		array('#dbd32b', '#383605', '#ebe789', '#ffffff', '#FFFFFF'),	// yellow
		array('#e88f14', '#4F2B05', '#f4c17b', '#fbead3', '#FFFFFF'),	// orange
		array('#ccc7b8', '#5a5440', '#CD7979', '#ffffff', '#FFFFFF'),	// offwhite
		array('#8f907b', '#535447', '#9FA769', '#FFFFFF', '#FFFFFF'),	// beigh
		array('#8b709b', '#51405b', '#beafc7', '#e9e4ec', '#FFFFFF'),	// light purple
		array('#8eb6c0', '#34555d', '#9bbec7', '#FFFFFF', '#FFFFFF'),	// light blue
		array('#49759e', '#dce6ef', '#95b3ce', '#dce6ef', '#FFFFFF'),	// blue
		array('#9b3a3a', '#e7bfbf', '#cd7979', '#e7bfbf', '#FFFFFF'),	// red
		array('#7ca244', '#36461e', '#b8d192', '#e8f0db', '#FFFFFF'),	// green
		array('#3a641a', '#e3f0b9', '#708c5b', '#a8ada4', '#FFFFFF')	// uglygreen
		);
	*/
		//Background, Title, Links, text, Headers, boxes_opacity, boxes_topleft_borders, boxes_bottomright_borders
	private static $s_color_schemes = array(
		array('#4d4d4d', '#ffffff', '#96e5ff', '#ffffff', '#a0ba68', '#262626', '#555555', '#111111'),	// 1. default
		array('#baccdb', '#3a4244', '#007296', '#282828', '#111111', '#ffffff', '#ffffff', '#ffffff'),	// 2. white
		array('#c87591', '#ebd0d9', '#f4aac1', '#501125', '#ffffff', '#9b3e5b', '#c490a2', '#914d64'),	// 3. pink
		array('#978b73', '#e1dcd0', '#d1cabd', '#2f2614', '#ffffff', '#5f5846', '#9a9280', '#706754'),	// 4. brown
		array('#fff9cb', '#55473b', '#7b762a', '#47401c', '#3a3a3a', '#faeeb2', '#cfc277', '#cfc277'),	// 5. yellow
		array('#fea44d', '#ffffff', '#9f3400', '#fff1e3', '#ffffff', '#f6973c', '#ffdbb8', '#ffdbb8'),	// 6. orange
		array('#e2e2e1', '#2f2823', '#612b20', '#2b100b', '#2f2823', '#b5b5ad', '#ffffff', '#ffffff'),	// 7. offwhite
		array('#404040', '#FFFFFF', '#eeaf65', '#FFFFFF', '#FFFFFF', '#000000', '#000000', '#444444'),	// 8. black-orange
		array('#9a9fc5', '#cfdaf0', '#3f489d', '#ffffff', '#cfdaf0', '#747aac', '#c6c9de', '#c6c9de'),	// 9. light purple
		array('#98c5ec', '#ecffff', '#daf7ff', '#daf7ff', '#093860', '#67a3d7', '#ffffff', '#ffffff'),	// 10. light blue
		array('#0b203c', '#ffffff', '#bfd17f', '#ffffff', '#ffffff', '#071a33', '#2f4159', '#2f4159'),	// 11. blue
		array('#6e0000', '#fcfcfc', '#f19898', '#390000', '#ffffff', '#7a0000', '#933030', '#4f0000'),	// 12. red
		array('#84a15e', '#ebf9d5', '#b8e770', '#1b330b', '#ffffff', '#4e6133', '#a9bd8f', '#a9bd8f'),	// 13. green
		array('#ec9898', '#fff0f3', '#ffddd9', '#ffffff', '#c84a4b', '#e2807f', '#f2b7b7', '#f2b7b7')	// 14. nice pink
		);

	public static function create ( $partner_id , $subp_id , $producer_id , $kshow_type )
	{
		$kshow = new kshow();
		$kshow->setPartnerId( $partner_id );
		$kshow->setSubpId ( $subp_id ) ;
		$kshow->setProducerId( $producer_id );
		$kshow->setType( $kshow_type ); // will make sure the intro will have a good default
		$kshow->save();  // to create a new kshow in the DB and use its ID for the entries creation
		$kshow->createEntry ( entry::ENTRY_MEDIA_TYPE_SHOW , $producer_id  ); // roughcut
		$kshow->createEntry ( entry::ENTRY_MEDIA_TYPE_VIDEO , $producer_id  ); // intro
		$kshow->save(); // to finally save
		return $kshow;
	}

	public function setRoughcutCount ( $count )
	{
		$this->roughcut_count = $count ;
	}


	// TODO - move implementation to kshowPeer - i'm not doing so now because there are changes i don't want to commit
	public function getRoughcutCount ()
	{
		if ( $this->roughcut_count == -1 )
		{
			$c = new Criteria();
			$c->add ( entryPeer::TYPE , entryType::MIX );
			$c->add ( entryPeer::KSHOW_ID , $this->getId() );
			$this->roughcut_count = entryPeer::doCount( $c );
		}
		return $this->roughcut_count;
	}

	// TODO - modify the schema so show_entry_id will be a foreignReference in the entry table
	public function getShowEntry ($con = null)
	{
		if ($this->m_show_entry === null && ($this->show_entry_id !== null))
		{
			$this->m_show_entry = entryPeer::retrieveByPK($this->show_entry_id, $con);
		}

		return $this->m_show_entry;
	}

	public function setShowEntry ( $entry )
	{
		$this->m_show_entry = $entry ;
		$this->setShowEntryId( $entry->getId() );
	}

		// TODO - modify the schema so intro_id will be a foreignReference in the entry table
	public function getIntro ($con = null)
	{
		if ($this->m_intro === null && ($this->intro_id !== null))
		{
			$this->m_intro = entryPeer::retrieveByPK($this->intro_id, $con);
		}

		return $this->m_intro;
	}

	// don't stop until a unique hash is created for this object
	private static function calculateId ( )
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ( $i = 0 ; $i < 10 ; ++$i)
		{
			$id = $dc["id"].'_'.kString::generateStringId();
			$existing_object = kshowPeer::retrieveByPk( $id );
			
			if ( ! $existing_object ) return $id;
		}
		
		die();
	}
	
	public function save(PropelPDO $con = null)
	{
		$is_new = false;
		if ( $this->isNew() )
		{
			$is_new = true;
			$this->setId(self::calculateId());
			myStatisticsMgr::addKshow( $this );
		}

		myPartnerUtils::setPartnerIdForObj( $this );

		mySearchUtils::setDisplayInSearch( $this );
		
		$res =  parent::save( $con );
		if ($is_new)
		{
			$obj = kshowPeer::retrieveByPk($this->getId());
			$this->setIntId($obj->getIntId());
		}
		
		return $res;
	}


	public function delete(PropelPDO $con = null)
	{
		myStatisticsMgr::deleteKshow( $this );

		parent::delete( $con );
	}


	// TODO - PERFORMANCE DB - move to use cache !!
	// will increment the views by 1
	public function incViews ( $should_save = true )
	{
		myStatisticsMgr::incKshowViews( $this );
/*
		$v = $this->getViews ( );
		if ( ! is_numeric( $v ) ) $v=0;
		$this->setViews( $v + 1 );

		if ( $should_save) $this->save();
*/
	}

	// TODO - PERFORMANCE DB - move to use cache !!
	// will increment the views by 1
/*
	public function incVotes (  $should_save = true )
	{
		$v = $this->getVotes();
		if ( ! is_numeric( $v ) ) $v=0;
		$this->setVotes( $v + 1 );

		if ( $should_save) $this->save();
	}
*/

	public function incPlays( )
	{
		myStatisticsMgr::incKshowPlays( $this );
	}

	/**
	 * This function returns the file system path for a requested content entity.
	 * @return string the content path
	 */
	public function getThumbnailPath()
	{
		if ( $this->getThumbnail() == NULL )
			return "";
		return myContentStorage::getGeneralEntityPath("kshow/thumbnail", $this->getIntId(), $this->getId(), $this->getThumbnail());
	}

	/**
	 * Use the thumbnailUrl of the show entry -
	 * TODO - cache to prevent the extra hit to DB
	 */
	public function getThumbnailUrl( $version = NULL )
	{
		$show_entry = $this->getShowEntry();
		if ( $show_entry )
		{
			return $show_entry->getThumbnailUrl();			
		}
		else
		{
			$path = $this->getThumbnailPath ( $version );
			$url = requestUtils::getHost() . $path ;
			return $url;
		}
	}

	/**
	 * This function sets and returns a new path for a requested content entity.
	 * @param string $filename = the original fileName from which the extension is cut.
	 * @return string the content file name
	 */
	public function setThumbnail($filename)
	{
		Basekshow::SetThumbnail(myContentStorage::generateRandomFileName($filename, $this->getThumbnail()));
		return $this->getThumbnail();
	}


	/**
	 * This function returns the file system path for a requested content entity.
	 * @return string the content path
	 */
	public function getIntroPath()
	{
		return myContentStorage::getGeneralEntityPath("kshow/intro", $this->getIntId(), $this->getId(), $this->getIntro());
	}

	/**
	 * This function sets and returns a new path for a requested content entity.
	 * @param string $filename = the original fileName from which the extension is cut.
	 * @return string the content file name
	 */
	public function setIntro($filename)
	{
		Basekshow::SetIntro(myContentStorage::generateRandomFileName($filename, $this->getIntro()));
		return $this->getIntro();
	}

	public function getTeamPicturePath()
	{
		return myContentStorage::getGeneralEntityPath("kshow/team", $this->getIntId(), $this->getId(), ".png" );
	}

	public function getTeam2PicturePath()
	{
		return myContentStorage::getGeneralEntityPath("kshow/team2", $this->getIntId(), $this->getId(), ".png" );
	}

	// TODO - IMPROVE - this is actually methods that partially belong to the skinContainer.
	// the only part that belongs to the kshow is the beginning of the path : /kshow.
	// consider moving...
	/* This function returns the file system path for a requested content entity that is a property of the skin.
	 * @return string the content path
	 */
	public function getSkinResourcePathByProperty ( $prop_name , skinContainer $skin_container  )
	{
		if ( $this->getId() == "" )
		{
			return "";
		}

		// TODO - these are hacks around the name of the field/clas/property - FIX !!!
		// the prop_name is the field name not the class_name !!!
		$prop_name = skinContainer::fixClassName( $prop_name );
		$param_value = 	$skin_container->getParamFromObject(  $prop_name );

		if ( kString::isEmpty( $param_value ) )
		{
			// TODO - i don't think the hack should be here -
			// i think that the myContentStorage will generate this value if the originqal value is empty
			// - Eran ?
			$param_value = myContentStorage::MIN_OBFUSCATOR_VALUE;
		}
		return myContentStorage::getGeneralEntityPath("kshow/skin/" . $prop_name ,
			$this->getIntId(), $this->getId(),
			$param_value );
	}

	/**
	 * This function sets and returns a new path for a requested content entity.
	 * @param string $filename = the original fileName from which the extension is cut.
	 * @param string $prop_name = the property name to update in the skinContainer.
	 * @param string $$skin_container = should be passed to avoid serializing & deserailizing from $this->getSkin
	 * @return string the content file name
	 */
	public function setSkinResourceByProperty ( $filename , $prop_name , skinContainer & $skin_container )
	{
		$skin_container->setByName ( $prop_name , myContentStorage::generateRandomFileName ( $filename ,
			$skin_container->getParamFromObject( $prop_name )  ) );

		return $skin_container->getParamFromObject( $prop_name ) ;
	}


	/**
	 * This helps create special entries in a kshow - the show_entry & intro
	 * $type can be either entry::ENTRY_MEDIA_TYPE_SHOW (for the show_entry) or  entry::ENTRY_MEDIA_TYPE_VIDEO  (for the intro)
	 */
	public function createEntry ( $type , $kuser_id, $thumbnail = null , $entry_name = null)
	{
		// for invites we use the default invites from the kaltura gallery show
		if ( $type != entry::ENTRY_MEDIA_TYPE_SHOW )
		{
			$kshow_type = $this->getType();

			$intros = array(kshow::KSHOW_TYPE_MASHUP,
				kshow::KSHOW_TYPE_MUSIC_VIDEO,
				kshow::KSHOW_TYPE_HOWTO,
				kshow::KSHOW_TYPE_CALL_FOR_ACTION,
				kshow::KSHOW_TYPE_CONTEST,
				kshow::KSHOW_TYPE_GROUP_GREETING,
				kshow::KSHOW_TYPE_SPORTS,
				kshow::KSHOW_TYPE_DIGITAL_STORY,
				kshow::KSHOW_TYPE_GROUP_TRAVELOGUE);

			$id = 0;
			if (in_array($kshow_type, $intros))
			{
				$id = $kshow_type;
			}

			$id = 120 + $id;

			$entry = entryPeer::retrieveByPK($id);

			if ($entry)
			{
				$this->setIntroId ( $entry->getId());
				$this->m_intro = $entry;
			}

			return $entry;
		}

		$kshow = $this;

		$entry = new entry();

		$entry->setKshowId($kshow->getId () );
		$entry->setKuserId($kuser_id);
		if ( $this->getPartnerId() !== null )
			$entry->setPartnerId( $this->getPartnerId() ); // inherit partner_id from kshow
		if ( $this->getSubpId() !== null )
			$entry->setSubpId( $this->getSubpId() ); // inherit subp_id from kshow
		$entry->setStatus(entryStatus::READY);

		if ( $entry_name )
		{
			$type_text = $entry_name;
		}
		else
		{
			$type_text = "Kaltura Video";
		}
		//$entry->setData ( "&kal_show.flv");
		$entry->setThumbnail ( $thumbnail ? $thumbnail : "&kal_show.jpg");
		$entry->setType( entryType::MIX );
		$entry->setMediaType( entry::ENTRY_MEDIA_TYPE_SHOW );
		$entry->setEditorType ( myMetadataUtils::METADATA_EDITOR_SIMPLE );

		$entry->setName($type_text);
		$entry->setTags($type_text . "," . $kshow->getTags() );

		$entry->save();
		$this->setShowEntryId ( $entry->getId());
		$this->m_show_entry = $entry;

		return $entry;
	}

	public function setTags($tags , $update_db = true )
	{
		if ($this->tags !== $tags) {
			$tags = ktagword::updateTags($this->tags, $tags , $update_db );
			parent::setTags($tags);
		}
	}

	public function getCreatedAtAsInt ()
	{
		return $this->getCreatedAt( null );
	}

	public function getUpdateAtAsInt ()
	{
		return $this->getUpdatedAt( null );
	}

	public function getFormattedCreatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getCreatedAt' , $format );
	}

	public function getFormattedUpdatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getUpdatedAt' , $format );
	}

	public function verifyViewPassword ( $unhashed_password )
	{
		return $unhashed_password ==  $this->getViewPassword() ;
	}

	public function verifyContribPassword ( $unhashed_password )
	{
		return $unhashed_password ==  $this->getContribPassword() ;
	}

	public function verifyEditPassword ( $unhashed_password )
	{
		return $unhashed_password == $this->getEditPassword() ;
	}

	public function setSkin ( $skin_str )
	{
		parent::setSkin ( $skin_str );
		$this->skin_container = NULL;
	}

	public function getSkinObj()
	{
		if ( $this->skin_container == NULL )
		{
			$this->skin_container =new  skinContainer ();
			$this->skin_container->deserializeFromString ( $this->getSkin () );
		}

		return $this->skin_container;
	}

	public function setSkinObj( skinContainer $skin_container )
	{
		$this->skin_container = $skin_container;
		parent::setSkin ( $skin_container->serializeToString() );
	}


	public static function getColorSchemes ( )
	{
		return 	self::$s_color_schemes;
	}

	public static function getTypes ( )
	{
		return 	self::$s_type_text;
	}

	public function getTypeText ( )
	{
		$res = @self::$s_type_text[$this->getType()];
		if ( $res == NULL )
		{
			return self::$s_type_text[0]; // use 0 as the default
		}

		return $res;
	}

	/*
	 * uses a mask on the status field
	 */
	public function getState ()
	{
		return  ( $this->getStatus()& ~7 );
	}

	public function setState ( $new_state )
	{
		$this->setStatus ( ( $this->getStatus()& ~7 ) | $new_state );
	}

	/*
	 * uses a mask on the status field
	 */
	public function getHasRoughcut ( )
	{
		return (  $this->getStatus() & self::KSHOW_STATUS_HAS_ROUGHCUT ) ;
	}

	public function setHasRoughcut ( $has )
	{
		$new_val = $has ?
			$this->getStatus() | self::KSHOW_STATUS_HAS_ROUGHCUT  :
			$this->getStatus() & ~self::KSHOW_STATUS_HAS_ROUGHCUT;
		$this->setStatus ( $new_val );
	}

	/*
	 * uses a mask on the status field
	 */
	public function getPlayRoughcut ( )
	{
		return ( $this->getStatus() & self::KSHOW_STATUS_PLAY_ROUGHCUT );
	}

	public function setPlayRoughcut ( $play )
	{
		$new_val = $play ?
			$this->getStatus() | self::KSHOW_STATUS_PLAY_ROUGHCUT  :
			$this->getStatus() & ~self::KSHOW_STATUS_PLAY_ROUGHCUT;
		$this->setStatus ( $new_val );
	}

	/*
	 * uses a mask on the status field
	 */
	public function getHasTeamImage ( )
	{
		return ( ( $this->getStatus() & self::KSHOW_STATUS_HAS_TEAM_IMAGE ) > 0);
	}

	public function setHasTeamImage ( $has )
	{
		$new_val = $has ?
			$this->getStatus() | self::KSHOW_STATUS_HAS_TEAM_IMAGE  :
			$this->getStatus() & ~self::KSHOW_STATUS_HAS_TEAM_IMAGE;
		$this->setStatus ( $new_val );
	}

	/*
	 * uses a mask on the status field - the flag is infact should disable publishing -
	 * 	when it's turned on - it cannot be published
	 */
	public function getCanPublish ( )
	{
		return ( self::KSHOW_STATUS_DISABLE_PUBLISH - ( $this->getStatus() & self::KSHOW_STATUS_DISABLE_PUBLISH ) ) ;
	}

	public function setCanPublish ( $can_publish )
	{
		$has = !$can_publish;
		$new_val = $has ?
			$this->getStatus() | self::KSHOW_STATUS_DISABLE_PUBLISH  :
			$this->getStatus() & ~self::KSHOW_STATUS_DISABLE_PUBLISH;
		$this->setStatus ( $new_val );
	}

	/*
	 * uses a mask on the status field - the flag is infact is fhould disable publishing -
	 * 	when it's turned on - it cannot be published
	 */
	public function getVisibleToRecipient ( )
	{
		return (  $this->getStatus() & self::KSHOW_STATUS_VISIBLE_TO_RECIPIENT ) ;
	}

	public function setVisibleToRecipient ( $visible )
	{
		$new_val = $visible ?
			$this->getStatus() | self::KSHOW_STATUS_VISIBLE_TO_RECIPIENT  :
			$this->getStatus() & ~self::KSHOW_STATUS_VISIBLE_TO_RECIPIENT;
		$this->setStatus ( $new_val );
	}

	public function getNormalizedRank ()
	{
		$res = round($this->rank / 1000);

		if ( $res > self::MAX_NORMALIZED_RANK ) return self::MAX_NORMALIZED_RANK;

		return $res;
	}

	public function getMetadata()
	{
		if ( !$this->getShowEntryId() || $this->getShowEntry() == null )
		{
			return null;
		}

		return ( $this->getShowEntry()->getMetadata() );
	}

	public function setMetadata( $content , $override_existing = false , $duration = null )
	{
		if ( !$this->getShowEntryId() )
		{
			return null;
		}

		return ( $this->getShowEntry()->setMetadata($this , $content , $override_existing , $duration ) );
	}

	public function getVersion()
	{
		if ( !$this->getShowEntryId() )
		{
			return null;
		}

		$show_entry = $this->getShowEntry();
		if ( $show_entry )		return ( $show_entry->getVersion() );
		else return -1;
	}

	public function rollbackVersion( $desired_version )
	{
		if ( !$this->getShowEntryId() )
		{
			return null;
		}

		return ( $this->getShowEntry()->rollbackVersion( $desired_version ) );
	}

	public function initFromTemplate ( $kuser_id , $sample_text = null )
	{
		if ( !$this->getHasRoughcut() && $sample_text != null )
		{
			$show_entry = $this->getShowEntry();
			if ( !$show_entry )
			{
				// have to save the kshow before creating the default entries
				$show_entry =  $this->createEntry( entry::ENTRY_MEDIA_TYPE_SHOW , $kuser_id , null , $this->getName() ); // roughcut
			}

			if ( !$this->getIntroId() )
			{
				$this->createEntry( entry::ENTRY_MEDIA_TYPE_VIDEO , $kuser_id ); // intro
			}

			// this text should be placed in the partner-config
			KalturaLog::info("before modifyEntryMetadataWithText:\n$sample_text");
			myEntryUtils::modifyEntryMetadataWithText ( $show_entry , $sample_text , 0 , true  );  // override the current entry

			$this->setHasRoughcut ( false );
		}
	}

	// kuserId is an alias to producerId
	public function  getKuserId()
	{
		return $this->getProducerId();
	}

	public function getPuserId()
	{
		return PuserKuserPeer::getPuserIdFromKuserId ( $this->getPartnerId(), $this->getProducerId() );
	}

	public function moderate ($new_moderation_status)
	{
		$error_msg = "Moderation status not supported by kshow";
		switch($new_moderation_status)
		{
			case moderation::MODERATION_STATUS_APPROVED:
				throw new Exception(error_msg);
				break;
			case moderation::MODERATION_STATUS_BLOCK:
				throw new Exception(error_msg);
				break;
			case moderation::MODERATION_STATUS_DELETE:
				throw new Exception(error_msg);
				break;
			case moderation::MODERATION_STATUS_PENDING:
				throw new Exception(error_msg);
				break;
			case moderation::MODERATION_STATUS_REVIEW:
				throw new Exception(error_msg);
				break;
			default:
				throw new Exception(error_msg);
				break;
		}

		$this->save();
	}
	
	// TODO - remove
	public function getModerationStatus()
	{
		return moderation::MODERATION_STATUS_APPROVED;
	}
	
	public function getAllowQuickEdit()
	{
		return $this->getFromCustomData( "allowQuickEdit" , null );
	}
	
	public function setAllowQuickEdit( $v )
	{
		return $this->putInCustomData( "allowQuickEdit", $v );
	}	
	
	// this will make sure that the extra data set in the search_text won't leak out 
	public function getSearchText()
	{
		return mySearchUtils::removePartner( parent::getSearchText() );
	}

	public function getSearchTextRaw()
	{
		return parent::getSearchText();
	}

	
}
