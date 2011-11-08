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

		static $newListVals = array ('a','b','c');

		/**
		 * @return MetadataField
		 */
		public function getElementToAdd ()
		{
			return new MetadataTextField('id5', 'Field5', "1", "true");
		}
		
		/**
		 * 
		 * @return array
		 */
		public function schemaArray ()
		{
			$var = array(new MetadataTextField('id1', 'Field1', "1", "true"),
						 new MetadataTextField('id2', 'Field2', MetadataField::UNBOUNDED, "true"),
						 new MetadataDateField('id3', 'Field3', "1", "true"),
						 new MetadataListField('id4', 'Field4', MetadataField::UNBOUNDED, "true", array(1,2,3)),);
						 
			return $var;
		}
	
	}
	
class MetadataField
{
	const TYPE = "objectType";
	
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
	public $limited;
	/**
	 * @var boolean
	 */
	public $searchable;
	
	public $label;
	 
	public function __construct($id, $name, $limited, $searchable)
	{
		
		$this->id = $id;
		$this->name = $name;
		$this->limited = $limited;
		$this->searchable = $searchable;
		$this->label = strtolower($this->name);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getXSD ()
	{
		
	}
	/**
	 * 
	 * @return string
	 */
	public function getXML ()
	{
		
	}

}

class MetadataTextField extends MetadataField
{	
	const TYPE = "textType";
	
	public function __construct($id, $name, $limited, $searchable)
	{
		parent::__construct($id, $name, $limited, $searchable);
		
		
	}
	
	public function getXSD()
	{

		$xsd = "<xsd:element id='{$this->id}' name='{$this->name}' minOccurs='0' maxOccurs='{$this->limited}' type='textType'>
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>{$this->label}</label>
              <key>{$this->label}</key>
              <searchable>{$this->searchable}</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>";
		
		return $xsd;
	}
	
	public function getXML()
	{
		$xml = "";
		if ($this->limited == "1")
		{
			$xml = "<{$this->name}>". rand(0,24) ."</{$this->name}>";
		}
		else 
		{
			$howMany = rand (0,5);
			for ($i=0; $i<=$howMany; $i++)
			{
				$xml .= "<{$this->name}>". rand(0,24) ."</{$this->name}>";
			}
		}
		
		return $xml;
	}
	

}

class MetadataDateField extends MetadataField
{
	const TYPE = "dateType";
	
	public function __construct($id, $name, $limited, $searchable)
	{
		parent::__construct($id, $name, $limited, $searchable);

	}
	
	public function getXSD ()
	{
		
		$xsd = "<xsd:element id='{$this->id}' name='{$this->name}' minOccurs='0' maxOccurs='{$this->limited}' type='dateType'>
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>{$this->label}</label>
              <key>{$this->label}</key>
              <searchable>{$this->searchable}</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>";
		return $xsd;
	}
	
	public function getXML ()
	{
		$xml = "";
		if ($this->limited == "1")
		{
			$xml = "<{$this->name}>". rand(0,24) ."</{$this->name}>";
		}
		else 
		{
			$howMany = rand (0,5);
			for ($i=0; $i<=$howMany; $i++)
			{
				$xml .= "<{$this->name}>". rand(0,24) ."</{$this->name}>";
			}
		}
		
		return $xml;
	}
}

class MetadataListField extends MetadataField
{
	const TYPE = "objectType";
	/**
	 * @var array
	 */
	public $valueList;
	
	
	
	public function __construct($id, $name, $limited, $searchable, $valueList)
	{
		parent::__construct($id, $name, $limited, $searchable);
		
		$this->valueList = $valueList;

        
	}
	
	public function getXSD()
	{
		
		$xsd="<xsd:element id='{$this->id}' name='{$this->name}' minOccurs='0' maxOccurs='{$this->limited}'>
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>{$this->label}</label>
              <key>{$this->label}</key>
              <searchable>{$this->searchable}</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
          <xsd:simpleType>
            <xsd:restriction base='listType'>";
		
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
        if ($this->limited == "1")
        {
        	$xml.=  "<{$this->name}>" . $this->valueList[rand(0, count($this->valueList)-1)] . "</{$this->name}>";
        }
        else
        {
        	foreach ($this->valueList as $value)
        	{
        		$choose = rand(0,1);
        		
        		if ($choose)
        		{
        			 $xml .= "<{$this->name}>$value</{$this->name}>";
        		}
        		
        	}
        }
        return $xml;
	}
}

class MetadataEntryListField extends MetadataField
{
	const TYPE = "objectType";
	
	public function __construct($id, $name, $limited, $searchable)
	{
		parent::__construct($id, $name, $limited, $searchable);
		

		
	}
	
	public function getXSD()
	{
		$nameLS = strtolower($this->name);
		
		$xsd = "<xsd:element id='{$this->id}' name='{$this->name}' minOccurs='0' maxOccurs='{$this->limited}' type='objectType'>
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>{$this->label}</label>
              <key>{$this->label}</key>
              <searchable>{$this->searchable}</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>";
		
		return $xsd;
		
	}
	
	public function getXML ()
	{
		$xml = "";
		if ($this->limited == "1")
		{
			$xml = "<{$this->name}>". rand(0,24) ."</{$this->name}>";
		}
		else 
		{
			$howMany = rand (0,5);
			for ($i=0; $i<=$howMany; $i++)
			{
				$xml .= "<{$this->name}>". rand(0,24) ."</{$this->name}>";
			}
		}
		
		return $xml;
	}
}

