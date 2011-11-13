<?php
/*
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// ===================================================================================================
*/

?>
<?php
	class Config
	{
		public static function getNewListVals()
		{
			$valueCount = rand(1,6);
			$result = array();
			for ($i = 0; $i < $valueCount; $i++)
				$result[] = "new_$i";
				
			return $result;
		}
		
		/**
		 * @return array
		 */
		public static function schemaArray ()
		{
			$result = array();
			
			$fieldCount = rand(1,10);
			for ($i = 0; $i < $fieldCount; $i++)
			{
				$result[] = self::getRandomField("id$i", "Field$i");
			}
						 
			return $result;
		}
	
		public static function getRandomField($id, $name)
		{
			$fieldType = rand(1,4);
			switch($fieldType)
			{
			case 1:
				return new MetadataTextField($id, $name);
				
			case 2:
				return new  MetadataDateField($id, $name);

			case 3:
				return new  MetadataListField($id, $name);

			case 4:
				return new  MetadataEntryListField($id, $name);
			}
		}
	}
	
abstract class MetadataField
{
	const UNBOUNDED = "unbounded";
	
	/**
	 * @var string
	 */
	public $id;
	/**
	 *  @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $minOccurs;
	/**
	 * @var string
	 */
	public $maxOccurs;
	/**
	 * @var boolean
	 */
	public $searchable;	
	/**
	 * @var string
	 */
	public $label;
	 
	public function __construct($id, $name)
	{
		$this->id = $id;
		$this->name = $name;
		$this->minOccurs = 0;
		$this->maxOccurs = rand(0, 1) ? self::UNBOUNDED : '1';
		$this->searchable = 'true';
		$this->label = strtolower($name);
	}
	
	protected function getAnnotation()
	{
		return 
		"<xsd:annotation>
			<xsd:documentation></xsd:documentation>
			<xsd:appinfo>
				<label>{$this->label}</label>
				<key>{$this->label}</key>
				<searchable>{$this->searchable}</searchable>
				<timeControl>false</timeControl>
				<description></description>
			</xsd:appinfo>
        </xsd:annotation>";
	}
	
	protected function getSingleValueXML($value)
	{
		return "<{$this->name}>{$value}</{$this->name}>";
	}
	
	protected function getRandomValueCount()
	{
		$randMin = $this->minOccurs;
		if ($this->maxOccurs == self::UNBOUNDED)
		{
			$randMax = 5;
		}
		else 
		{
			$randMax = $this->maxOccurs;
		}
		
		return rand($randMin, $randMax);
	}

	protected function getBasicElementAttributes()
	{
		return "id='{$this->id}' name='{$this->name}' minOccurs='{$this->minOccurs}' maxOccurs='{$this->maxOccurs}'";
	}
	
	/**
	 * 
	 * @return string
	 */
	abstract public function getXSD ();

	/**
	 * 
	 * @return string
	 */
	abstract public function getXML ();
}

class MetadataTextField extends MetadataField
{	
	const TYPE = "textType";
	
	public function __construct($id, $name)
	{
		parent::__construct($id, $name);
	}
	
	public function getXSD()
	{
		$xsd = "<xsd:element " . $this->getBasicElementAttributes() . " type='" . self::TYPE . "'>
		".$this->getAnnotation()."
        </xsd:element>";
		
		return $xsd;
	}
	
	public function getXML()
	{
		$howMany = $this->getRandomValueCount();
		
		$xml = "";
		for ($i=0; $i<$howMany; $i++)
		{
			$xml .= $this->getSingleValueXML("val_". rand(0,24));
		}
		
		return $xml;
	}
}

class MetadataDateField extends MetadataField
{
	const TYPE = "dateType";
	
	public function __construct($id, $name)
	{
		parent::__construct($id, $name);
	}
	
	public function getXSD ()
	{
		$xsd = "<xsd:element " . $this->getBasicElementAttributes() . " type='" . self::TYPE . "'>
		".$this->getAnnotation()."
        </xsd:element>";
		return $xsd;
	}
	
	public function getXML ()
	{
		$howMany = $this->getRandomValueCount();

		$xml = "";
		for ($i=0; $i<$howMany; $i++)
		{
			$xml .= $this->getSingleValueXML(rand(0,24));
		}
		
		return $xml;
	}
}

class MetadataListField extends MetadataField
{
	const TYPE = "listType";
	/**
	 * @var array
	 */
	public $valueList;
	
	public function __construct($id, $name)
	{
		parent::__construct($id, $name);
		
		$valueCount = rand(1,6);
		$this->valueList = array();
		for ($i = 0; $i < $valueCount; $i++)
			$this->valueList[] = "val_$i";
	}
	
	public function getXSD()
	{
		$xsd="<xsd:element " . $this->getBasicElementAttributes() . ">
		".$this->getAnnotation()."
          <xsd:simpleType>
            <xsd:restriction base='" . self::TYPE . "'>";
		
		foreach ($this->valueList as $value)
		{
             $xsd.= "<xsd:enumeration value='$value'/>";

		}
        $xsd.="</xsd:restriction>
          </xsd:simpleType>
        </xsd:element>";
        
        return $xsd;
	}
	
	public function getXML()
	{
		$xml = "";
        if ($this->maxOccurs == "1")
        {
        	$xml .=  $this->getSingleValueXML($this->valueList[rand(0, count($this->valueList)-1)]);
        }
        else
        {
        	foreach ($this->valueList as $value)
        	{
        		$choose = rand(0,1);
        		
        		if ($choose)
        		{
        			 $xml .= $this->getSingleValueXML($value);
        		}
        	}
        }
        return $xml;
	}
}

class MetadataEntryListField extends MetadataField
{
	const TYPE = "objectType";
	
	public function __construct($id, $name)
	{
		parent::__construct($id, $name);
	}
	
	public function getXSD()
	{
		$nameLS = strtolower($this->name);
		
		$xsd = "<xsd:element " . $this->getBasicElementAttributes() . " type='" . self::TYPE . "'>
		".$this->getAnnotation()."
        </xsd:element>";
		
		return $xsd;		
	}
	
	public function getXML ()
	{
		$howMany = $this->getRandomValueCount();

		$xml = "";
		for ($i=0; $i<$howMany; $i++)
		{
			$xml .= $this->getSingleValueXML(rand(0,24));
		}
		
		return $xml;
	}
}

