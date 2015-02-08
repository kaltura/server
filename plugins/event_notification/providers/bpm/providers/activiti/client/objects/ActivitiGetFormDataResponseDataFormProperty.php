<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');
require_once(__DIR__ . '/ActivitiGetFormDataResponseDataFormPropertyEnumValue.php');
	

class ActivitiGetFormDataResponseDataFormProperty extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'name' => 'string',
			'type' => 'string',
			'value' => '',
			'readable' => 'boolean',
			'writable' => 'boolean',
			'required' => 'boolean',
			'datePattern' => '',
			'enumValues' => 'array<ActivitiGetFormDataResponseDataFormPropertyEnumValue>',
		));
	}
	
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var 
	 */
	protected $value;

	/**
	 * @var boolean
	 */
	protected $readable;

	/**
	 * @var boolean
	 */
	protected $writable;

	/**
	 * @var boolean
	 */
	protected $required;

	/**
	 * @var 
	 */
	protected $datePattern;

	/**
	 * @var array<ActivitiGetFormDataResponseDataFormPropertyEnumValue>
	 */
	protected $enumValues;

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return 
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return boolean
	 */
	public function getReadable()
	{
		return $this->readable;
	}

	/**
	 * @return boolean
	 */
	public function getWritable()
	{
		return $this->writable;
	}

	/**
	 * @return boolean
	 */
	public function getRequired()
	{
		return $this->required;
	}

	/**
	 * @return 
	 */
	public function getDatepattern()
	{
		return $this->datePattern;
	}

	/**
	 * @return array<ActivitiGetFormDataResponseDataFormPropertyEnumValue>
	 */
	public function getEnumvalues()
	{
		return $this->enumValues;
	}

}

