<?php
require_once ( "myBaseObject.class.php");
require_once ( "model/kshow.class.php");

/**
 * TODO - think of how's best to work with these classes - $attach_policy and stuff !
 *
 */
abstract class kshowCustomData extends myBaseObject
{
	//const KSHOW_CUSTOM_DATA_FIELD = "custom_data";

	protected $m_kshow = NULL;

	// when this ctor is called - if the kshow is not NULL, initialize from it
	protected function __construct( kshow $kshow = NULL , $attach_policy = NULL )
	{
		if ( $kshow != NULL )
		{
			$this->m_kshow = $kshow;
			$this->deserializeFromString( $this->getCustomData());
		}

	}

	protected function attachToKshow ( kshow $kshow , $attach_policy )
	{
		$this->m_kshow = $kshow;
		$this->deserializeFromString( $this->getCustomData());
		
	}


	protected function updateKshow ()
	{
		$this->setCustomeData ( $this->serializeToString() );
	}

	private  function getCustomData ()
	{
		return $this->m_kshow->getCustomData();
	}

	private function setCustomData ( $value )
	{
		return $this->m_kshow->setCustomData( $value );
	}

}
?>