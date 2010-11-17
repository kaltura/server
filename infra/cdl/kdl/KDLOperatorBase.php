<?php
	/* ===========================
	 * KDLOperatorBase
	 */
abstract class KDLOperatorBase {
	
	protected	$_id;
	protected	$_name;
	protected	$_sourceBlacklist = array();
	protected	$_targetBlacklist = array();
	

	abstract public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target);
	
	public function GenerateConfigData(KDLFlavor $design, KDLFlavor $target)
	{
		return null;
	}
	
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
		kLog::log("KDLOperatorBase::__construct: id($id), name($name), sourceBlacklist(".print_r($sourceBlacklist,true)."), targetBlacklist(".print_r($targetBlacklist,true).")");
    	$this->_id=$id;
		$this->_name=$name;
		$this->_sourceBlacklist = $sourceBlacklist;
		$this->_targetBlacklist = $targetBlacklist; 	
    }

    /**
     * @param KDLFlavor $target
     * @return string configuration to be saved as file
     */
    public function getConfigFile(KDLFlavor $target)
    {
    	return null;
    }
    
    /* ---------------------------
	 * CheckConstraints
	 */
    public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
			/*
			 * Source Blacklist processing
			 */
		if($this->_sourceBlacklist){
			$medSetSec=$this->checkBlackList($this->_sourceBlacklist, $source, $errors, $warnings);
			if(!is_null($medSetSec)){
				return true;
			}
		}
		
			/*
			 * Target Blacklist processing
			 */
		if($this->_targetBlacklist){
			$medSetSec=$this->checkBlackList($this->_targetBlacklist, $target, $errors, $warnings);
			if(!is_null($medSetSec)){
				return true;
			}
		}
		return false;
	}
	
    /* ---------------------------
	 * Blacklist processing
	 */
	public function checkBlackList($blackList, $mediaSet, array &$errors=null, array &$warnings=null)
	{
		if(!is_null($blackList)) {
			foreach ($blackList as $keyPart => $subBlackList){
				$sourcePart = null;
				switch($keyPart){
				case KDLConstants::ContainerIndex;
					$sourcePart = $mediaSet->_container;
					break;
				case KDLConstants::VideoIndex;
					$sourcePart = $mediaSet->_video;
					break;
				case KDLConstants::AudioIndex;
					$sourcePart = $mediaSet->_audio;
					break;
				default:
					continue;
				}
				if($sourcePart && is_array($subBlackList)
				&& (in_array($sourcePart->_id, $subBlackList)
				|| in_array($sourcePart->_format, $subBlackList))) {
					$warnings[$keyPart][] = 
						KDLWarnings::ToString(KDLWarnings::TranscoderFormat, $this->_id, ($sourcePart->_id."/".$sourcePart->_format));
					return $sourcePart;
				}
			}
		}
		return null;
	}

	/**
	 * @return the $_id
	 */
	public function get_id() {
		return $this->_id;
	}
	/**
	 * @param $_targetBlacklist the $_targetBlacklist to set
	 */
	public function set_targetBlacklist($_targetBlacklist) {
		$this->_targetBlacklist = $_targetBlacklist;
	}

	/**
	 * @param $_sourceBlacklist the $_sourceBlacklist to set
	 */
	public function set_sourceBlacklist($_sourceBlacklist) {
		$this->_sourceBlacklist = $_sourceBlacklist;
	}

	/**
	 * @return the $_targetBlacklist
	 */
	public function get_targetBlacklist() {
		return $this->_targetBlacklist;
	}

	/**
	 * @return the $_sourceBlacklist
	 */
	public function get_sourceBlacklist() {
		return $this->_sourceBlacklist;
	}

	/**
	 * @param $_name the $_name to set
	 */
	public function set_name($_name) {
		$this->_name = $_name;
	}

	/**
	 * @param $_id the $_id to set
	 */
	public function set_id($_id) {
		$this->_id = $_id;
	}

	/**
	 * @return the $_name
	 */
	public function get_name() {
		return $this->_name;
	}
}

	/* ===========================
	 * KDLOperationParams
	 */
class KDLOperationParams {
	public function KDLOperationParams($id,$ex=null, $cmd=null, $cfg=null){
		$this->_id=$id;
		$this->_extra=$ex;
		$this->_cmd=$cmd;
		$this->_cfg=$cfg;
	}
	public $_id=null;
	public $_extra=null;
	public $_cmd=null;
	public $_cfg=null;
	public $_engine=null;
	
		/* ---------------------------
		 * ToString
		 */
	public function ToString(){
		$rvStr=null;
		if($this->_id)
			$rvStr = "tr:".$this->_id;
		
		if($this->_extra)
			$rvStr.=",ex:".$this->_extra;
		
		if(property_exists($this,"_cmd") && $this->_cmd)
			$rvStr.=",cmd:".$this->_cmd;
		
		if(property_exists($this,"_cfg") && $this->_cfg)
			$rvStr.=",cfg:".$this->_cfg;
			
		return $rvStr;
	}
	
		/* ---------------------------
		 * SearchInArray($trId, array $transObjArr)
		 */
	public static function SearchInArray($trId, array $transObjArr)
	{
		foreach ($transObjArr as $key=>$trPrm) {
			if($trPrm->_id==$trId){
				return $key;
			}
		}
		return null;
	}
	
};


