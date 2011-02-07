<?php

/**
 * Subclass for performing query and update operations on the 'comment' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class commentPeer extends BasecommentPeer
{
	/**
	 * This function returns a pager object holding the specified comments' entries
	 * sorted by a given sort order.
	 * each entry holds the kuser object of its host.
	 *
	 * @param int $subjectType = the type of the object the commens refers to
	 * @param int $subjectId = the id of the object the comments refers to
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getOrderedPager($commentType, $subjectId, $pageSize, $page)
	{
		$c = new Criteria();
		$c->add(commentPeer::COMMENT_TYPE, $commentType);
		$c->add(commentPeer::SUBJECT_ID, $subjectId);
		$c->addDescendingOrderByColumn(commentPeer::BASE_DATE);
		$c->addJoin(commentPeer::KUSER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
	    $pager = new sfPropelPager('comment', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinkuser');
	    $pager->setPeerCountMethod('doCountJoinkuser');
	    $pager->init();
			    
	    return $pager;
	}
	
	public static function getLastShoutout($kshowId)
	{
		$c = new Criteria();
		$c->add(commentPeer::COMMENT_TYPE, comment::COMMENT_TYPE_SHOUTOUT);
		$c->add(commentPeer::SUBJECT_ID, $kshowId);
		$c->addDescendingOrderByColumn(commentPeer::BASE_DATE);
		$c->setLimit(1);
		
		return self::doSelectOne($c);
	}
	
}
