<?php
/**
 * Bumper data on entry
 *
 * @package plugins.Bumper
 * @subpackage model
 *
 */

class kBumper
{
	const BUMPER_DATA = 'bumperData';

	/**
	 *
	 * @var string
	 */
	protected $entryId;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}

	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	public static function saveBumperData( entry $dbEntry, $bumper, $dbBumper )
	{
		if($dbBumper)
		{
			$dbBumper = $bumper->toUpdatableObject($dbBumper);
		}
		elseif($bumper)
		{
			$dbBumper = $bumper->toInsertableObject();
		}
		else
		{
			$dbBumper = new kBumper();
		}
		$dbEntry->putInCustomData( self::BUMPER_DATA, $dbBumper);
		$dbEntry->save();
		return $dbBumper;
	}

	/**
	 * @param entry $entry
	 * @return kBumper
	 */
	public static function getBumperData( entry $entry )
	{
		return $entry->getFromCustomData( self::BUMPER_DATA );
	}

}
