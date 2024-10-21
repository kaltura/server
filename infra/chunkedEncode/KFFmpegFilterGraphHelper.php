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
	 * KFFmpegFilter
	 */
	class KFFmpegFilter extends KBaseFFmpegFilterObject{
		public $delim 	= null;		// Filter fields/params delimiter
		
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
	
		/********************
		 * CompoundString
		 */
		public function CompoundString(&$lastLabelOut)
		{
			$compoundStr = null;
			if(isset($this->labelIn)){
				foreach($this->labelIn as $labelIn) {
					$compoundStr.="[$labelIn]";
				}
			}
			$compoundStr.= $this->name;
			$auxStr = $this->name;
			if(isset($this->$auxStr)){
				$compoundStr.= ("=".$this->$auxStr);
			}
			$fieldArr = array();
			foreach($this as $key=>$val) {
				if(in_array($key,array("name","delim","labelIn","labelOut","_string","_paramStr","_chain","id",$this->name)))
					continue;
				$fieldArr[] = "$key=$val";
			}
			if($this->delim=="")
				$compoundStr.= "=".$this->_paramStr;
			else if(count($fieldArr)>0)
				$compoundStr.= "=".implode($this->delim,$fieldArr);
			
			if(isset($this->labelOut)){
				$lastLabelOut ="[$this->labelOut]";
				$compoundStr.= $lastLabelOut;
			}
			else
				$lastLabelOut = "";
			KalturaLog::log("filter string:$compoundStr");
			return $compoundStr;
		}
	}
	
	/********************
	 * KFFmpegFilterChain
	 */
	class KFFmpegFilterChain extends KFFmpegFilter{
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
		 * FindEntityByName
		 */
		public function FindEntityByName($filterName)
		{
			KalturaLog::log("filterName:$filterName,".count($this->entities));
			foreach($this->entities as $entity) {
				KalturaLog::log("entity:$entity->name, $entity->id");
				if($entity->name==$filterName)
					return $entity;
				else if(isset($entity->entities)) {
					$found= $entity->FindEntityByName($filterName);
					if(isset($found))
						return $found;
				}
			}
			return null;
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
		
		/********************
		 * RemoveEntity
		 */
		public function RemoveEntity($entity)
		{
			KalturaLog::log("entity: id:$entity->id), name:$entity->name");

			$chain = $entity->_chain;
				// If there is prev entity in the chain,
				//  adjust it (labelOut and entity string), 
			if($entity->id>0){
				$prev = $chain->entities[$entity->id-1];
				$prev->labelOut = $entity->labelOut;
				$chain->entities[$entity->id-1]->_string = $prev->CompoundString($stam);
			}
				// Remove the entity from the chain and update the chain string
			unset($chain->entities[$entity->id]);
			$chain->_string = $chain->CompoundString($stam);
				// If there are no more entities in the chain, remove it from the graph
			if(count($this->entities[$chain->id]->entities)==0) {
					// If there is prev chain in the graph,
					//  adjust it (labelOut and chain string), 
				if($chain->id>0){
					$prev = $this->entities[$chain->id-1];
					$prev->labelOut = $entity->labelOut;
					$lastFilter = end($prev->entities);
					$lastFilter->labelOut = $entity->labelOut;
					$lastFilter->_string = $lastFilter->CompoundString($stam);
					$prev->_string = $prev->CompoundString($stam);
				}
				unset($this->entities[$chain->id]);
			}
				// Adjust the filter graph string
			$this->_string = $this->CompoundString($stam);
			KalturaLog::log($this->_string);
			return true;
		}
		
		/********************
		 * CompoundString
		 */
		public function CompoundString(&$lastLabelOut)
		{
			$filterArr = array();
			foreach($this->entities as $filter){
				$filterStr = $filter->CompoundString($lastLabelOut);
				$filterArr[] = $filterStr;
			}
			$compoundStr = implode(',',$filterArr);
			KalturaLog::log("filterChain string:$compoundStr");
			return $compoundStr;
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
			if(count($this->entities)>0) {
				$this->labelIn = $this->entities[0]->labelIn;
				$this->labelOut = $filterChain->labelOut;
			}
			return true;
		}
		
		/********************
		 * CompoundString
		 */
		public function CompoundString(&$lastLabelOut)
		{
			$chainArr = array();
			foreach($this->entities as $chain) {
				$chainStr = null;
				$filterArr = array();
				$chainStr = $chain->CompoundString($lastLabelOut);
				$chainArr[] = $chainStr;
			}
			$compoundStr = implode(';',$chainArr);
			KalturaLog::log("filterGraph string:$compoundStr");
			return $compoundStr;
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
		 * iterFuncClone
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
	
