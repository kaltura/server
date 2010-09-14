<?php

/**
 * Subclass for representing a row from the 'bb_post' table.
 *
 * 
 *
 * @package lib.model
 */ 
class BBPost extends BaseBBPost
{
	
 public function getNbReplies()
 {
 	$c = new Criteria();
 	$c->add(BBPostPeer::PARENT_ID, $this->getId());
 	return BBPostPeer::doCount($c);
 }

 public function getNbViews()
 {

 }

 public function getLatestReply()
 {
 	$c = new Criteria();
 	$c->add(BBPostPeer::PARENT_ID, $this->getId());
 	$c->addJoin(kuserPeer::ID, BBPostPeer::KUSER_ID, Criteria::LEFT_JOIN);
 	$c->addDescendingOrderByColumn(BBPostPeer::CREATED_AT);
 	$c->setLimit(1);
 	$post = BBPostPeer::doSelectJoinkuser($c);
 	if( $post ) return $post[0]; else return NULL;
 }

	public function getFormattedCreatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getCreatedAt' , $format );
	}
	
	public function save( $con = null )
	{
		// first let's make sure that we don't reurse, our special handling is only for new objects.
		if( !$this->isNew() ) return parent::save();
		
		if( $this->getParentId() == NULL ) $this->setParentId( 0 );
		
		$forum = BBForumPeer::retrieveByPK( $this->getForumId() );
		$forum->setPostCount( $forum->getPostCount() + 1 );
			
		if( $this->getParentId() == 0  ) // this is a new thread
		{
			// update the forum data, increasing the thread count
			$forum->setThreadCount( $forum->getThreadCount() + 1 );
			
			// set basic info for this post
			$this->setNodeLevel( 0 );
			$this->setNodeId( strval( $forum->getPostCount() + 1 )  );
			$this->setNumChildern( 0 );
			$this->setLastChild( 0 );
			
		} else // this is a reply
		{
			
			// update the forum data, without increasing thread count
			
			// chage post data for the parent post
			$parentpost = BBPostPeer::retrieveByPK( $this->parent_id );
			$this->setNodeId( strval($parentpost->getNodeId()).'.'. strval( $parentpost->getLastChild() + 1 ) );
			$parentpost->setLastChild( $parentpost->getLastChild() + 1 );
			$parentpost->setNumChildern( $parentpost->getNumChildern() + 1 );
			$this->setNodeLevel( $parentpost->getNodeLevel() + 1 );
			$this->setNumChildern( 0 );
			$this->setLastChild( 0 );
			
			$parentpost->save();
		}
		
		parent::save( $con );
		
		// now that we have the new id, let the forum know what the last post was, so that it can be displayed in the forum defView
		$forum->setLastPost( $this->getId() );
		$forum->save();
		
	}

}

