<?php
/**
 * @package Scheduler
 * @subpackage Conversion.engines
 */
class KConversionEngineFfmpeg  extends KJobConversionEngine
{
	const FFMPEG = "ffmpeg";
	
	public function getName()
	{
		return self::FFMPEG;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::FFMPEG;
	}
	
	public function getCmd ()
	{
		return $this->engine_config->params->ffmpegCmd;
	}
	
	protected function getExecutionCommandAndConversionString ( KalturaConvertJobData $data )
	{
		$cmdLines =  parent::getExecutionCommandAndConversionString ($data);
			/*
			 * The code below handles the ffmpeg 0.10 and higher option to set up 'forced_key_frame'.
			 * The ffmpeg cmd-line should contain list of all forced kf's, this list might be up to 40Kb for 2hr videos.
			 * Since the cmd-lines are stored in db records (flavor_params_output), it would blow it up.
			 * The solution is to setup a placeholer w/duration and step, the full cmd-line is generated over here
			 * just before the activation.
			 * Sample:
			 *    	__forceKeyframes__462_2
			 *		stands for duration of 462 seconds, gop size 2 seconds
			 */
		foreach($cmdLines as $k=>$cmdLine){
			$kfPlcHldr = strstr($cmdLine->exec_cmd, KDLCmdlinePlaceholders::ForceKeyframes);
			if($kfPlcHldr!=false){ 
				$kfPlcHldr = explode(" ",$kfPlcHldr);	// 
				$kfPlcHldr = $kfPlcHldr[0];
				$kfPrms = substr($kfPlcHldr,strlen(KDLCmdlinePlaceholders::ForceKeyframes));
				$kfPrms = explode("_",$kfPrms);
				$forcedKF=null;
				for($t=0,$tr=0;$t<=$kfPrms[0]; $t+=$kfPrms[1], $tr+=round($kfPrms[1])){
					if(round($t)>$tr) {
						$t=$tr;
					}
					$forcedKF.=",".round($t,4);
				}
				$forcedKF[0] = ' ';
				$cmdLines[$k]->exec_cmd = str_replace ( 
						array($kfPlcHldr), 
						array($forcedKF),
						$cmdLine->exec_cmd);
			}
		}
		return $cmdLines;
	}
}
