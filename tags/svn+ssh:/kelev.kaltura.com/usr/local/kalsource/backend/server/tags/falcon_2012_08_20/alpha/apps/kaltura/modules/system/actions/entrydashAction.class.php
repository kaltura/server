<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class entrydashAction extends kalturaSystemAction
{
	/**
	 * Will list out all kind of perspectives of entries
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		$conversion_count = $this->getRequestParameter( "conv_count" , 10 );
		if ( $conversion_count > 40 ) $conversion_count=40;
		$c = new Criteria();
		$c->addDescendingOrderByColumn( conversionPeer::ID );
		$c->setLimit( $conversion_count );
		$this->conversions = conversionPeer::doSelect ( $c );
		$this->conversion_count = $conversion_count;
/*		
		$conv = new conversion();
		$conv->setId(6);
		$conv->setinFileName ( "Dasd");
		$conv->setStatus ( 1 );
		$conv->setCreatedAt ( 1234545 );
		$convs = array ( $conv );
		$this->conversions = $convs;
	*/	
		$import_count = $this->getRequestParameter( "impo_count" , 10 );
		if ( $import_count > 40 ) $import_count=40;
		$c = new Criteria();
		$c->addDescendingOrderByColumn( BatchJobPeer::ID );
		$c->setLimit( $import_count );
		$this->imports = BatchJobPeer::doSelect( $c );
		$this->import_count = $import_count;
	/*	
		$batch= new BatchJob();
		$batch->setId ( 4 );
		$batch->setData ( 'a:3:{s:7:"entryId";i:11077;s:9:"sourceUrl";s:84:"http://youtube.com/get_video?video_id=KDko2MFvY-s&t=OEgsToPDskKuVircfOJDjTh4FENGwQ0g";s:8:"destFile";s:36:"/web//content/imports/data/11077.flv";}' );
		$imports = array( $batch );
		
		$this->imports = $imports;
		*/
	}
}
?>