<?php
class myCsvWrapper
{
	const CELL_SEPARATOR = ",";
	const NEW_LINE_SEPARATOR = "\n";
	
	private $data = "" ;
	private $new_line = true;
	
	public function formatDate ( $timestamp )
	{
		return date ( "l, F j, Y" , $timestamp );
	}
	
	public function add ( $obj )
	{
		$args = func_get_args();
		foreach ( $args as $arg )
		{
			$this->addImpl ( $arg );
		}
	}
	
	private function addImpl ( $obj )
	{
		if ( is_array ( $obj ) )
		{
			foreach ( $obj as $elem )
			{
				$this->addImpl ( $elem );
			}
		}
		else
		{
			if ( !$this->new_line ) $this->data .= self::CELL_SEPARATOR;
			if ( strpos ( $this->data , self::CELL_SEPARATOR ) !== null )
				$this->data .= '"' . $obj . '"';
			else
				$this->data .= $obj;				
		}
		
		$this->new_line = false;		
	}
	
	public function addNewLine ( $obj )
	{
		$args = func_get_args();
		foreach ( $args as $arg )
		{
			$this->addImpl ( $arg );
		}
		$this->data .= self::NEW_LINE_SEPARATOR;
		$this->new_line = true;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	public function clearData()
	{
		$this->data = "";
	}
}
?>