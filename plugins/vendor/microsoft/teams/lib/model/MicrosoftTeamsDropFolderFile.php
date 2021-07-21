<?php

/*
* @package plugins.MicrosoftTeamsDropFolder
* @subpackage model
*/
class MicrosoftTeamsDropFolderFile extends DropFolderFile
{
	const REMOTE_ID = 'remote_id';

	const OWNER_ID = 'owner_id';

	const ADDITIONAL_USER_IDS = 'additional_user_ids';

	const DESCRIPTION = 'description';

	const TARGET_CATEGORY_IDS = 'target_category_ids';

	const NAME = 'name';

	const CONTENT_URL = 'content_url';

	/**
	 * return string
	 */
	public function getRemoteId ()
	{
		return $this->getFromCustomData(self::REMOTE_ID);
	}

	/**
	 * @param string $v
	 */
	public function setRemoteId ($v)
	{
		$this->putInCustomData(self::REMOTE_ID, $v);
	}

	/**
	 * return string
	 */
	public function getOwnerId ()
	{
		return $this->getFromCustomData(self::OWNER_ID);
	}

	/**
	 * @param string $v
	 */
	public function setOwnerId ($v)
	{
		$this->putInCustomData(self::OWNER_ID, $v);
	}

	/**
	 * return string
	 */
	public function getName ()
	{
		return $this->getFromCustomData(self::NAME);
	}

	/**
	 * @param string $v
	 */
	public function setName ($v)
	{
		$this->putInCustomData(self::NAME, $v);
	}

	/**
	 * return string
	 */
	public function getDescription ()
	{
		return $this->getFromCustomData(self::DESCRIPTION);
	}

	/**
	 * @param string $v
	 */
	public function setDescription ($v)
	{
		$this->putInCustomData(self::DESCRIPTION, $v);
	}

	/**
	 * return string
	 */
	public function getAdditionalUserIds ()
	{
		return $this->getFromCustomData(self::ADDITIONAL_USER_IDS);
	}

	/**
	 * @param string $v
	 */
	public function setAdditionalUserIds ($v)
	{
		$this->putInCustomData(self::ADDITIONAL_USER_IDS, $v);
	}

	/**
	 * return string
	 */
	public function getTargetCategoryIds ()
	{
		return $this->getFromCustomData(self::TARGET_CATEGORY_IDS);
	}

	/**
	 * @param string $v
	 */
	public function setTargetCategoryIds ($v)
	{
		$this->putInCustomData(self::TARGET_CATEGORY_IDS, $v);
	}

	/**
	 * return string
	 */
	public function getContentUrl ()
	{
		return $this->getFromCustomData(self::CONTENT_URL);
	}

	/**
	 * @param string $v
	 */
	public function setContentUrl ($v)
	{
		$this->putInCustomData(self::CONTENT_URL, $v);
	}

	public function getFileUrl ()
	{
		return $this->getContentUrl();
	}

	public function getNameForParsing ()
	{
		return $this->getName();
	}
}