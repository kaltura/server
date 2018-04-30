<?php
 
/*****************************
 * Includes & Globals
 */
ini_set("memory_limit","512M");
	
	/********************
	 * KBaseFFmpegFilterObject
	 */
	abstract class KBaseFFmpegFilterObject {
		public $id = 0;
		public $name = null;
		public $labelIn = array();
		public $labelOut = null;
		
		public $_string = null;
		
		/********************
		 * Parse
		 */
		abstract public function Parse($filterStr);
		 
	}

	/********************
	 * KBaseFFmpegFilterObject
	 */
	class KFFmpegFilter extends KBaseFFmpegFilterObject{
		public $delim 	= null;
		
		public $_chain = null;
		
		/********************
		 * 
		 */
		public function __construct($delim = ':') 
		{
			$this->delim = $delim;
		}
		
		/********************
		 * Parse
		 */
		public function Parse($filterStr)
		{
			KalturaLog::log("Filter:$filterStr");
			$this->_string = $filterStr;

			$paramStr = $filterStr;
				/*
				 * Retrieve leading labelIn's
				 */
			while($paramStr[0]=='[' && preg_match('~\[(.*?)]~', $paramStr, $matched)==1) {
				$this->labelIn[] = $matched[1];
				$paramStr = substr($paramStr, strlen($matched[0]));
				KalturaLog::log("cleaned labelIn:$paramStr");
			}
				/*
				 * Retrieve leading labelout
				 */
			if(preg_match('~\[(.*?)]~', $paramStr, $matched)==1){
				$this->labelOut = $matched[1];
				$paramStr = str_replace($matched[0], "", $paramStr);
				KalturaLog::log("cleaned labelOut:$paramStr");
			}		
				/*
				 * Retrieve filter name
				 */
			preg_match('/^([\w]+)/i', $paramStr, $matched); // get name
			$paramStr = substr($paramStr, strlen($matched[0]));
			KalturaLog::log("cleaned name:$paramStr");
			if(!isset($this->name)){
				$this->name = $matched[1];
			}
				/*
				 * If no other data - get out
				 */
			if($paramStr[0]!='=') {
				KalturaLog::log(print_r($this,1));
				return true;
			}
				/*
				 * Handle params's fileds
				 */
			$paramStr = substr($paramStr, 1);
			KalturaLog::log("fields:$paramStr");
			$this->_paramStr = $paramStr;
				/*
				 * Scan through additional param field
				 */
			$paramObj = new stdClass();
			if(isset($this->delim) && strlen($this->delim)>0){
				$fieldArr = explode($this->delim, $paramStr);
				foreach($fieldArr as $fieldStr) {
					$auxArr = explode('=',$fieldStr);
					KalturaLog::log("Field:$fieldStr");
						/*
						 * Single filter param means that it is a 'compact'/non-named syntax,
						 * therefore no need to scan further
						 */
					if(count($auxArr)==1) {
						$field = $this->name;
						$this->$field = $paramStr;
						break;
					}
					else {
						$field = $auxArr[0];
						$this->$field = $auxArr[1];
					}
				}
			}

			return true;
		}
	

	}
	
	/********************
	 * KFFmpegFilterChain
	 */
	class KFFmpegFilterChain extends KBaseFFmpegFilterObject{
		public $entities  = array();
		
		/********************
		 * Parse
		 */
		public function Parse($filterChainStr)
		{
			KalturaLog::log("FilterChain:$filterChainStr");
			$filterArr = explode(',',$filterChainStr);
			$this->_string = $filterChainStr;
			$filters = array();
			$subsId = null;
			$subsFilters = array();
			$auxFilterStr = null;
			foreach($filterArr as $filterStr) {
				if(substr( $filterStr, -1)=='\\'){
					$auxFilterStr.= $filterStr.',';
					continue;
				}
				else if(isset($auxFilterStr)){
					$filterStr = $auxFilterStr.$filterStr;
					$auxFilterStr = null;
					$filter = new KFFmpegFilter("");
				}
				else {
					$filter = new KFFmpegFilter();
				}
				$filter->Parse($filterStr);
				$filter->_chain = $this;
				$filter->id = count($filters);
				$filters[] = $filter;
			}
			$this->entities = $filters;
			$this->labelIn = $filters[0]->labelIn;
			$this->labelOut = $filters[count($filters)-1]->labelOut;
			return true;
		}

		/********************
		 * FindEntityByLabelIn
		 */
		public function FindEntityByLabelIn($labelIn)
		{
			KalturaLog::log("labelIn:$labelIn");
			return $this->LoopEntities($this,'iterFuncLabelIn', $labelIn);
		}
		
		/********************
		 * iterFuncLabelIn
		 */
		protected function iterFuncLabelIn($entity, $labelIn)
		{
			KalturaLog::log("labelIn:$labelIn ".$this->_string);
			foreach($entity->labelIn as $lbl) {
				if($lbl==$labelIn) {
					return $entity;
				}
			}
			return null;
		}
		
		/********************
		 * LoopEntities
		 */
		public function LoopEntities($obj, $funcName, $var)
		{
			KalturaLog::log("funcName:$funcName");
			foreach($this->entities as $entity) {
				if(isset($obj))
					$found = $obj->$funcName($entity, $var);
				else
					$found = $funcName($entity, $var);
				if($found===null)
					continue;
				return $found;
			}
			return null;
		}
	}
	
	/********************
	 * 
	 */
	class KFFmpegFilterGraph extends KFFmpegFilterChain{
		
		/********************
		 * Parse
		 */
		public function Parse($filterGraphStr)
		{
			KalturaLog::log("Graph:$filterGraphStr");
			$filterChainArr = explode(';',$filterGraphStr);
			$this->_string = $filterGraphStr;
			foreach($filterChainArr as $filterChainStr) {
				$filterChain = new KFFmpegFilterChain();
				$filterChain->Parse($filterChainStr);
				$filterChain->id = count($this->entities);
				$this->entities[] = $filterChain;
			}
			return true;
		}
		
		/********************
		 * CompoundString
		 */
		public function CompoundString(&$lastLabelOut)
		{
			$str = null;
			$chainArr = array();
			foreach($this->entities as $chain) {
				$chainStr = null;
				$filterArr = array();
				foreach($chain->entities as $filter){
					$filterStr = null;
					if(isset($filter->labelIn)){
						foreach($filter->labelIn as $labelIn) {
							$filterStr.="[$labelIn]";
						}
					}
					$filterStr.= $filter->name;
					$auxStr = $filter->name;
					if(isset($filter->$auxStr)){
						$filterStr.= ("=".$filter->$auxStr);
					}
					$fieldArr = array();
					foreach($filter as $key=>$val) {
						if(in_array($key,array("name","delim","labelIn","labelOut","_string","_paramStr","_chain","id",$filter->name)))
							continue;
						$fieldArr[] = "$key=$val";
					}
					if($filter->delim=="")
						$filterStr.= "=".$filter->_paramStr;
					else if(count($fieldArr)>0)
						$filterStr.= "=".implode($filter->delim,$fieldArr);
					
					if(isset($filter->labelOut)){
						$lastLabelOut ="[$filter->labelOut]";
						$filterStr.= $lastLabelOut;
					}
					else
						$lastLabelOut = "";
					$filterArr[] = $filterStr;
				}
				$chainStr.= implode(',',$filterArr);
				$chainArr[] = $chainStr;
			}
			$str = implode(';',$chainArr);
			KalturaLog::log("filterGraph string:$str");
			return $str;
		}
		
		/********************
		 * RemoveChain
		 */
		public function RemoveChain($chain)
		{
			$chainsToRemove = array();
			$chainsToRemove[] = $chain;
			
			unset($this->entities[$chain->id]);
			if(count($this->entities)==0)
				return true;
			
				/*
				 * If no 'labelOut' - leave
				 * Note: this usecase should be handled too
				 */
			if(!isset($chain->labelOut)){
				return true;
			}
			$labelOut = $chain->labelOut;
				/*
				 * LabelOut should be in the graph, otherwise - error
				 */
			$chain = $this->FindEntityByLabelIn($labelOut);
			if(!isset($chain)){
				return false;
			}

			$chainsToRemove[] = $chain;
			unset($this->entities[$chain->id]);
			
			$lastLabelIn = $chainsToRemove[count($chainsToRemove)-1]->labelIn;

				/*
				 * Find the external input label, in order to switchh the labels of the removed chains
				 */
			for($idx0=0; $idx0<count($chainsToRemove)-1; $idx0++) {
				foreach($lastLabelIn as $label) {
					if($label!=$chainsToRemove[$idx0]->labelOut){
						$externalLabel = $label;
						break;
					}
				}
				if(isset($externalLabel))
					break;
			}

			$chain = end($chainsToRemove);
			$toChangeLabel = $chain->labelOut;

				/*
				 * Fix chain/filter input label
				 */
			$chain = $this->FindEntityByLabelIn($toChangeLabel);
			if(!isset($chain)){
				return false;
			}
			$filter = $chain->FindEntityByLabelIn($toChangeLabel);
			if(!isset($filter)){
				return false;
			}
			$filter->labelIn = array_replace($filter->labelIn, array($toChangeLabel), array($externalLabel));
			$this->entities[$chain->id]->labelIn = $filter->labelIn;
			$this->entities[$chain->id]->entities[$filter->id]->labelIn = $filter->labelIn;
			
				/*
				 * Fix the finishing chain/filter - they should not have 'labelOut'
				 */
			$chain = end($this->entities);
			$this->entities[$chain->id]->labelOut = null;
			$filter = end($this->entities[$chain->id]->entities);
			$this->entities[$chain->id]->entities[$filter->id]->labelOut = null;
			
			return true;
		}

		/********************
		 * LoopFilters
		 */
		public function LoopFilters($obj, $funcName, $var)
		{
			KalturaLog::log("funcName:$funcName");
			foreach($this->entities as $entity) {
				$found = $entity->LoopEntities($obj, $funcName, $var);
				if($found===null)
					continue;
				return $found;
			}
			return null;
		}
		
		/********************
		 * LoopFilters
		 */
		public static function iterFuncClone($chain, $obj)
		{
			$chn = clone $chain;
			$chn->entities = array();
			foreach($chain->entities as $idx=>$entity){
				$chn->entities[$idx] = clone $entity;
			}
			$obj->entities[] = $chn;
			KalturaLog::log("Name:".$entity->name);
			return null;
		}

	}
	
