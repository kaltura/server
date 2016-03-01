<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveEntryServerNode extends EntryServerNode{

	const OM_CLASS = 'LiveEntryServerNode';

	public function setStreams(KalturaLiveStreamParamsArray $v){$this->putInCustomData("streams", $v);}
	public function getStreams(){return $this->getFromCustomData("streams");}

}