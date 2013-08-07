<?php
class WebexFile
{
	/**
	 * @var int
	 */
	protected $size;
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var string
	 */
	protected $path;
	
	/**
	 * @return int $size
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return string $name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string $path
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param int $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}
}
