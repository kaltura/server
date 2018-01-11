<?php
/**
 * @package Core
 * @subpackage model.data
 */

/**
 * @package Core
 * @subpackage model.data
 */
class ktagword {
	const TAG_SEPARATOR = ",";
	const MAXIMUM_TAG_LENGTH = 100;

	static public function getTagsArray ( $tags_str )
	{
		$dummy = "";
		return self::tagsListFromParagraph ( $tags_str  , $dummy );
	}

	/**
	 * if $fixed_paragraph is a string longer than 0 - it will hold the fixed string after clearing the invalid parameters
	 */
	static public function tagsListFromParagraph($paragraph , &$fixed_paragraph)
	{
		$tagwords = explode( self::TAG_SEPARATOR, $paragraph);

		$validTagwords = array();

		foreach($tagwords as $tagword)
		{
			// first trim white spaces from the tags
			$tagword = strtolower  ( trim ( $tagword ) );
			if (strlen($tagword) >= 2)  // check only words with 2 chars or more
			{
				$fixed_tag = mb_strcut($tagword, 0, self::MAXIMUM_TAG_LENGTH, "UTF-8"); // make sure not to let strings longer than the size in the DB

				// remove duplicates
				if ( ! in_array ( $fixed_tag , $validTagwords ))
				{
					$validTagwords[] = $fixed_tag;
				}
			}
		}

		// check if the caller expects us to return the fixed
		if ( strlen ( $fixed_paragraph ) > 0 )
		{
			// when gluing the tags together - use the separator with " "
			$fixed_paragraph = implode( self::TAG_SEPARATOR . " "  , $validTagwords);
		}
		return $validTagwords;
	}

	static public function getTopTags ( $limit = 25 )
	{
		return array();
	}

	static public function getWeight($tagword)
	{
		return 0;
	}

	static public function updateTags($prevTags, $tags , $update_db = true  )
	{
		$fixed_tags = "bla";
		$tagwords = self::tagsListFromParagraph($tags , $fixed_tags );
		return $fixed_tags;
	}

	/* ---------------- admin_tags ---------------- */
	public static function getAdminTags ( $partner_id , $limit = 10000 , $force_get_from_db = false , $entry_id_to_exclude = null )
	{
		// deprecated - very heavy query for getting the unique tagwords from the entry table 
		return "";
	}
	
	public static function updateAdminTags ( entry $entry )
	{
		// be ready to update the admin_tags if the admin_tags OR the status was modified
		if ( $entry->isColumnModified( entryPeer::ADMIN_TAGS ) || $entry->isColumnModified( entryPeer::STATUS ) )
		{
			$partner_id = $entry->getPartnerId();

			$admin_tags_str = self::getAdminTags ( $partner_id , null , true , $entry->getId() );
			$admin_tags = explode ( self::TAG_SEPARATOR , $admin_tags_str ) ; 
			$admin_tags = self::mergeAdminTags( $admin_tags , $entry->getAdminTags() );
			$after_change = self::sortAndSetAdminTags ( $partner_id , $admin_tags );
		}
	}
	
	public static function fixAdminTags (  $tags )
	{
		$tag_arr = array();	
		$tag_arr = self::mergeAdminTags( $tag_arr , strtolower( $tags ));
		return implode ( self::TAG_SEPARATOR , $tag_arr );
	}
	
	private  static function mergeAdminTags ( $admin_tags , $new_admin_tags_str )
	{
		$tag_list = array ();
		
		$current_admin_tags = explode ( self::TAG_SEPARATOR , $new_admin_tags_str ) ;

		foreach ( $current_admin_tags as $tag )
		{ 
			$tag = strtolower(trim($tag)); // clear all white spaces from the beginning and end of each tag and move to lower case
			// add tags that are NOT empty - the fact that the key & value are set will prevent duplicates 
			if ( $tag ) $tag_list[$tag]=$tag; // make both the key & value poit to the tag
		}
		foreach ( $admin_tags as $tag )
		{ 
			$tag = strtolower(trim($tag)); // clear all white spaces from the beginning and end of each tag and move to lower case
			// add tags that are NOT empty - the fact that the key & value are set will prevent duplicates 
			if ( $tag ) $tag_list[$tag]=$tag; // make both the key & value poit to the tag
		}
		return $tag_list;
	}
	
	private static function sortAndSetAdminTags ( $partner_id , $admin_tags )
	{
		sort( $admin_tags , SORT_STRING );
		$admin_tags_str = implode ( self::TAG_SEPARATOR , $admin_tags );
		return $admin_tags_str;
	}
}

