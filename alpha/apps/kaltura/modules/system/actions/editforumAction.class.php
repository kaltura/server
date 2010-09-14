<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

class editforumAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();

		$this->error = null;
		$this->modified_fields = null;
		
		$post = new BBPost(); 
		$existing_post = $post;
		$this->post = $existing_post;
		
		$field_names = array ( "title" , "content" );
		baseObjectUtils::fillObjectFromRequest ( $_REQUEST , $post , "post_", null );
		
		if ( ! $post->getId() )
		{
			$this->error = "Cannot find ID to update";
			return;
		}
		$existing_post = BBPostPeer::retrieveByPK( $post->getId() );
		if ( ! $existing_post )
		{
			$this->error = "Cannot find post id [" . $post->getId() . "]" ;
			return;
		}

		$before_changes = $existing_post->copy();
//		baseObjectUtils::fillObjectFromObject( $field_names , $post , $existing_post , baseObjectUtils::CLONE_POLICY_PREFER_NEW ,null );
		if ( $post->getTitle() != null )  $existing_post->setTitle ( $post->getTitle() );
		if ( $post->getContent() != null  ) $existing_post->setContent ( $post->getContent() );
		
		
		$this->modified_fields = $existing_post->save();
		$this->post = $existing_post;
		$this->before_changes = $before_changes;
	}
}