<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');
require_once(__DIR__ . '/ActivitiListOfProcessInstancesResponseData.php');
	

class ActivitiListOfProcessInstancesResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'data' => 'array<ActivitiListOfProcessInstancesResponseData>',
			'total' => 'int',
			'start' => 'int',
			'sort' => 'string',
			'order' => 'string',
			'size' => 'int',
		));
	}
	
	/**
	 * @var array<ActivitiListOfProcessInstancesResponseData>
	 */
	protected $data;

	/**
	 * @var int
	 */
	protected $total;

	/**
	 * @var int
	 */
	protected $start;

	/**
	 * @var string
	 */
	protected $sort;

	/**
	 * @var string
	 */
	protected $order;

	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @return array<ActivitiListOfProcessInstancesResponseData>
	 */
	public function getData()
	{
		return $this->data;
	}

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
	 * @return string
	 */
	public function getSort()
	{
		return $this->sort;
	}

	/**
	 * @return string
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

}

