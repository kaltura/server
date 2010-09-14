<?php
class kConversionEngineEncodingCom  extends kConversionEngineFlix
{
	const ENCODING_COM = "encoding_com";
	
	public function getName()
	{
		return self::ENCODING_COM;
	}

	public static function getCmd ()
	{
		return kConf::get ( "bin_path_encoding_com" );
	}
	
	protected function getExecutionCommandAndConversionString ( kConversionCommand $conv_cmd , $params_index )
	{
		list($exec_cmd, $conversion_string) = parent::getExecutionCommandAndConversionString($conv_cmd, $params_index);

		// replace the -i parameter with an http url for the archived file, all other parameters stays the same
		$i_pos = strpos($exec_cmd, "-i");
		$i_pos_end = $i_pos + 3;
		$i_value_end_pos = strpos($exec_cmd, " ", $i_pos_end);
		$input_file = substr($exec_cmd, $i_pos_end, $i_value_end_pos - $i_pos_end);
		
		$new_input_file = "http://".kConf::get("www_host")."/index.php/extwidget/raw/entry_id/".$conv_cmd->entry_id;
		
		$exec_cmd = str_replace($input_file, $new_input_file, $exec_cmd);
		$exec_cmd = str_replace(parent::getCmd(), $this->getCmd(), $exec_cmd);
		
		return array ($exec_cmd, $conversion_string);
	}
}
?>