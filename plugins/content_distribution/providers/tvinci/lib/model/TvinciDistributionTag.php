<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage model
 */
class TvinciDistributionTag extends BaseObject
{

	protected $tagname;
	protected $extension;
	protected $protocol;
	protected $format;
	protected $filename;
	protected $ppvmodule;

	/**
	 * @return mixed
	 */
	public function getTagname()
	{
		return $this->tagname;
	}

	/**
	 * @param mixed $tagname
	 */
	public function setTagname($tagname)
	{
		$this->tagname = $tagname;
	}

	/**
	 * @return mixed
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * @param mixed $extension
	 */
	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

	/**
	 * @return mixed
	 */
	public function getProtocol()
	{
		return $this->protocol;
	}

	/**
	 * @param mixed $protocol
	 */
	public function setProtocol($protocol)
	{
		$this->protocol = $protocol;
	}

	/**
	 * @return mixed
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * @param mixed $format
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @return mixed
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * @param mixed $filename
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * @return mixed
	 */
	public function getPpvmodule()
	{
		return $this->ppvmodule;
	}

	/**
	 * @param mixed $ppvmodule
	 */
	public function setPpvmodule($ppvmodule)
	{
		$this->ppvmodule = $ppvmodule;
	}
	
	
	
}
