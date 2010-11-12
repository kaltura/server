<?php
class KalturaVersion
{
	/**
	 * @var int
	 */
	protected $major;
	
	/**
	 * @var int
	 */
	protected $minor;
	
	/**
	 * @var int
	 */
	protected $build;
	
	/**
	 * @var KalturaVersion
	 */
	protected $brokenCompatibilityVersion;

	public function __construct($major, $minor, $build, KalturaVersion $brokenCompatibilityVersion = null)
	{
		$this->major = $major;
		$this->minor = $minor;
		$this->build = $build;
		$this->brokenCompatibilityVersion = $brokenCompatibilityVersion;
	}
	
	/**
	 * @return string
	 */
	public function toString()
	{
		return "$this->major.$this->minor.$this->build";
	}
	
	/**
	 * @param KalturaVersion $version
	 * @return bool
	 */
	public function isCompatible(KalturaVersion $version)
	{
		if($version->getMajor() > $this->major)
			return false;
			
		if($version->getMajor() == $this->major)
		{
			if($version->getMinor() > $this->minor)
				return false;
				
			if($version->getMinor() == $this->minor && $version->getBuild() > $this->build)
				return false;
		}
			
		if(!$this->brokenCompatibilityVersion)
			return true;
		
		if($version->getMajor() < $this->brokenCompatibilityVersion->getMajor())
			return false;
			
		if($version->getMajor() == $this->brokenCompatibilityVersion->getMajor())
		{
			if($version->getMinor() < $this->brokenCompatibilityVersion->getMinor())
				return false;
				
			if($version->getMinor() == $this->brokenCompatibilityVersion->getMinor() && $version->getBuild() < $this->brokenCompatibilityVersion->getBuild())
				return false;
		}
			
		return true;
	}
	
	/**
	 * @return int major
	 */
	public function getMajor()
	{
		return $this->major;
	}

	/**
	 * @return int minor
	 */
	public function getMinor()
	{
		return $this->minor;
	}

	/**
	 * @return int build
	 */
	public function getBuild()
	{
		return $this->build;
	}

	
}