<?php

class myDebug
{
	private $m_str ;
	private $m_nl = "<br>";
	
	public function myDebug ( $nl = NULL )
	{
		if ( $nl != NULL ) setNewLine ( $nl );
	}
	
	public function setNewLine ( $nl )
	{
		$this->m_nl = $nl;
	}
	
	public function append ( $str )
	{
		$this->m_str .= " " . self::nowWithMilliseconds() . " " . $str . $this->m_nl; 
	}
	
	public function flush ()
	{
		echo $this->m_str;
	}
	
	private static function nowWithMilliseconds ( )
	{
		$time = ( microtime(true) );
		$milliseconds = (int)(($time - (int)$time) * 1000);  
		return strftime( "%d/%m %H:%M:%S." , time() ) . $milliseconds ;
	}
}
?>