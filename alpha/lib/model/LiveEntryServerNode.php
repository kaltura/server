<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveEntryServerNode extends EntryServerNode{

	const OM_CLASS = 'LiveEntryServerNode';

	public function setBitrate($v){ $this->putInCustomData("Bitrate", $v);}
	public function getBitrate(){ return $this->getFromCustomData("Bitrate");}
	
	public function setFlavorId($v){ $this->putInCustomData("FlavorId", $v);}
	public function getFlavorId(){ return $this->getFromCustomData("FlavorId");}

	public function setWidth($v){ $this->putInCustomData("Width", $v);}
	public function getWidth(){ return $this->getFromCustomData("Width");}

	public function setHeight($v){ $this->putInCustomData("Height", $v);}
	public function getHeight(){ return $this->getFromCustomData("Height");}

	public function setCodec($v){ $this->putInCustomData("Codec", $v);}
	public function getCodec(){ return $this->getFromCustomData("Codec");}

	public function setPattern($v){ $this->putInCustomData("Pattern", $v);}
	public function getPattern(){ return $this->getFromCustomData("Pattern");}

}