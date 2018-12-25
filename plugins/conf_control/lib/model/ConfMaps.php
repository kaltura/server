<?php


/**
 * Skeleton subclass for representing a row from the 'conf_maps' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class ConfMaps extends BaseConfMaps
{
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;


	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{
		return parent::getCreatedAt('Y-m-d H:i:s');
	}


	/**
	 * @param ConfMaps $exstingMap
	 */
	public function addNewMapVersion(ConfMaps $exstingMap, $content)
	{
		$this->setMapName($exstingMap->getMapName());
		$this->setHostName($exstingMap->getHostName());
		$this->setVersion($exstingMap->getVersion() + 1);
		$this->setContent($content);
		$this->setRemarks(kCurrentContext::$ks);
		$this->setStatus($exstingMap->getStatus());
		$this->save();
	} // ConfMaps
}

