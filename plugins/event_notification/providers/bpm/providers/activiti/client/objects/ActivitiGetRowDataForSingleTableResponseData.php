<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetRowDataForSingleTableResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'tASK_ID_' => 'string',
			'nAME_' => 'string',
			'rEV_' => 'int',
			'tEXT_' => 'string',
			'lONG_' => 'int',
			'iD_' => 'string',
			'tYPE_' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $tASK_ID_;

	/**
	 * @var string
	 */
	protected $nAME_;

	/**
	 * @var int
	 */
	protected $rEV_;

	/**
	 * @var string
	 */
	protected $tEXT_;

	/**
	 * @var int
	 */
	protected $lONG_;

	/**
	 * @var string
	 */
	protected $iD_;

	/**
	 * @var string
	 */
	protected $tYPE_;

	/**
	 * @return string
	 */
	public function getTaskId()
	{
		return $this->tASK_ID_;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->nAME_;
	}

	/**
	 * @return int
	 */
	public function getRev()
	{
		return $this->rEV_;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->tEXT_;
	}

	/**
	 * @return int
	 */
	public function getLong()
	{
		return $this->lONG_;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->iD_;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->tYPE_;
	}

}

