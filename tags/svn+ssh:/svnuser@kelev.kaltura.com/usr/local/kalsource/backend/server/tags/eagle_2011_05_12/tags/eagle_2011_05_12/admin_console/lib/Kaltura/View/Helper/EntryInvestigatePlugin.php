<?php
abstract class Kaltura_View_Helper_EntryInvestigatePlugin
{
	/**
	 * @param string $entryId
	 * @return array array of data to be used in the phtml template
	 */
	abstract public function getDataArray($entryId, $partnerId);
	
	/**
	 * @return string the path to the phtml templates folder
	 */
	abstract public function getTemplatePath();
	
	/**
	 * @return string the name of the phtml file
	 */
	abstract public function getPHTML();
}