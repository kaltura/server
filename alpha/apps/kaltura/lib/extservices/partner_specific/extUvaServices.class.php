<?php

/**
 * As a request from UVA, this class implements an entry search using the '*' wildcard.
 * The search is only done on the entry's name and each of its tags, and not on the
 * search_text column like our regular search.
 *
 * To use this search, the relevant UICONF should be configured to use :
 * 		- SearchView.swf
 * 		- Code 100
 * 		- search_type token set to one of the defined constants below
 *
 */

class extUvaServices extends myBaseMediaSource implements IMediaSource
{
	const SEARCH_TYPE_USER_CLIPS    = 1;
	const SEARCH_TYPE_PARTNER_CLIPS = 2;

	protected $supported_media_types = 7; // support all media//self::SUPPORT_MEDIA_TYPE_VIDEO + (int)self::SUPPORT_MEDIA_TYPE_IMAGE;
	protected $source_name = "Search with wildcards";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );
	protected $logo = "http://www.kaltura.com/images/wizard/logo_kaltura.gif";
	protected $id = entry::ENTRY_MEDIA_SOURCE_PARTNER_SPECIFIC;

	private static $NEED_MEDIA_INFO = "0";
	private static $maxPageSize = 20;

	public function getAuthData( $kuserId, $userName, $password, $token)
	{}

	public function getMediaInfo( $media_type ,$objectId)
	{ return ""; }



	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData= null )
	{
		$pageSize = $pageSize > self::$maxPageSize ? self::$maxPageSize : $pageSize ;
		$page--;
		if ( $page < 0 ) $page = 0;

		$status = 'ok';
		$message = '';
		$objects = array();
		$shouldSearch = true;

		$searchType = @$_REQUEST["search_type"]; // this is an addition sent from the KCW
		if (!$searchType) {
			$searchType = extUvaServices::SEARCH_TYPE_USER_CLIPS;
		}

		$c = new EntrySphinxCriteria();
		$c->setIgnoreCase(true);

		// add type specific criterias
		switch ($searchType) {
			case extUvaServices::SEARCH_TYPE_USER_CLIPS:
				// search in user clips only
				$kuserId = $this->findKuserId();
				if ($kuserId === false) {
					$shouldSearch = false;
				}
				else {
					$c->addAnd ( entryPeer::KUSER_ID , $kuserId );
				}
				break;
			case extUvaServices::SEARCH_TYPE_PARTNER_CLIPS:
				// search all partner clips - no special criteria to add
				break;
			default:
				throw new Exception("Search type provided is not valid");
				return false;
		}

		if ($shouldSearch) {

			$c->addAnd ( entryPeer::PARTNER_ID , self::$partner_id );
			$c->addAnd ( entryPeer::MEDIA_TYPE , $media_type );
			$c->addAnd ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP );

			// support search text with wildcards
			$this->addSearchTextCriteria($c, $searchText);

			$c->setLimit( $pageSize );
			$c->setOffset( $page * $pageSize );

			$results = entryPeer::doSelect($c);
			$numOfResults = $c->getSphinxRecordsCount();

			foreach ( $results as $entry ) {
				$object = array (
					"id"          => $entry->getId(),
					"url"         => $entry->getDataUrl(),
					"tags"        => $entry->getTags(),
					"title"       => $entry->getName(),
					"description" => $entry->getTags()
				);

				$thumbnail = $entry->getThumbnailUrl();
				if ($thumbnail) {
					$object["thumb"] = $entry->getThumbnailUrl() ;
				}

				$objects[] = $object;
			}
		}

		return array('status' => $status, 'message' => $message, 'objects' => $objects, "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}



	private function addSearchTextCriteria(Criteria &$c, $searchText)
	{
		// if wildcard are used, entry title will be searched using SQL LIKE, and entry tags will be searched using SQL REGEXP (no other option)
		// this is a bit strange and messy, but i think that overall it gives better perfomance since LIKE is faster than REGEXP

		$searchWildcard = '*';
		$titleWildcard  = '%';
		$tagsWildcard   = '([^,]*)';
		$tempWildcard   = '@@@@';

		$count = 0;
		// temporarly escape the search wildcard
		$tempSearchText = str_replace($searchWildcard, $tempWildcard, $searchText, $count);

		// add slashes to all other wildcards, in regard to the sql statement they will be used in next (like / regexp)
		$titleSearchText = addcslashes($tempSearchText, '$_[]^');
		$tagsSearchText  = preg_quote ($tempSearchText,'\\');

		// change the temp escape wildcard to the translated wildcard that will be used in the expression
		$titleSearchText = str_replace( $tempWildcard, $titleWildcard, $titleSearchText);
		$tagsSearchText  = str_replace( $tempWildcard, $tagsWildcard,  $tagsSearchText);

		if ($count) {
			// wildcards should be used
			$titleCriterion = $c->getNewCriterion(entryPeer::NAME, $titleSearchText, Criteria::LIKE);
			$tagsCriterion  = $c->getNewCriterion(entryPeer::TAGS, entryPeer::TAGS.' REGEXP "[[:<:]](' . $tagsSearchText . ')[[:>:]]"', Criteria::CUSTOM);
			$titleCriterion->addOr($tagsCriterion);
			$c->addAnd($titleCriterion);
		}
		else {
			// no wildcards - do normal search
			$keywords_array = mySearchUtils::getKeywordsFromStr ( $searchText );
			$filter = new entryFilter();
			$filter->setPartnerSearchScope(self::$partner_id);
			$filter->addSearchMatchToCriteria($c, $keywords_array, entry::getSearchableColumnName() );
		}

	}



	private function findKuserId ()
	{
		if (defined("KALTURA_API_V3")) {
			$kuser = kuserPeer::getKuserByPartnerAndUid(self::$partner_id, self::$puser_id);
			$kuser_id = $kuser->getId();
		}
		else {
			$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid ( self::$partner_id , self::$subp_id, self::$puser_id , true );
			if ( ! $puser_kuser ) {
				// very bad - does not exist in system
				$kuser_id = false;
			}
			else {
				$kuser = $puser_kuser->getKuser();
				if ( !$kuser ) {
					$kuser_id = false;
				}
				else {
					$kuser_id = $kuser->getId();
				}
			}
		}
		return $kuser_id;
	}


}