<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');
require_once(__DIR__ . '/ActivitiGetRowDataForSingleTableResponseData.php');
	

class ActivitiGetRowDataForSingleTableResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'total' => 'int',
			'start' => 'int',
			'sort' => '',
			'order' => '',
			'size' => 'int',
			'data' => 'array<ActivitiGetRowDataForSingleTableResponseData>',
		));
	}
	
	/**
	 * @var int
	 */
	protected $total;

	/**
	 * @var int
	 */
	protected $start;

	/**
	 * @var 
	 */
	protected $sort;

	/**
	 * @var 
	 */
	protected $order;

	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @var array<ActivitiGetRowDataForSingleTableResponseData>
	 */
	protected $data;

	/**
	 * @return int
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * @return int
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * @return 
	 */
	public function getSort()
	{
		return $this->sort;
	}

	/**
	 * @return 
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return array<ActivitiGetRowDataForSingleTableResponseData>
	 */
	public function getData()
	{
		return $this->data;
	}

}

