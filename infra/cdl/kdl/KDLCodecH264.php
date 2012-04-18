<?php

	/* ===========================
	 * KDLBaseCodec
	 */
abstract class KDLBaseCodec {
	
	public function __construct(KDLVideoData $vidObj=null){
		if(isset($vidObj)){
			$this->Evaluate($vidObj);
		}
	}
	abstract public function Evaluate(KDLVideoData $vidObj);
}
	
	/* ===========================
	 * KDLCodecH264
	 */
class KDLCodecH264 extends KDLBaseCodec{
	public	$_crf;			/*	"Constant quality mode (also known as constant ratefactor). 
								Bitrate corresponds approximately to that of constant quantizer, 
								but gives better quality overall at little speed cost."
								default=23
								For presentation-style - 10
							*/
	public 	$_refs;			/*	reference frames*/
	public	$_subq=2;		/* subpixel estimation complexity. Higher numbers are better */
	public	$_coder=0;

	public 	$_qcomp=0.6; 
	public 	$_qmin=10;
	public 	$_qmax=50;
	public 	$_qdiff=4;
	
	public	$_bframes=3;	/* B-Frames */

	public	$_b_adapt;		/* "x264, by default, adaptively decides through a low-resolution lookahead 
								the best number of B-frames to use. 
								It is possible to disable this adaptivity; this is not recommended. 
								Recommended default: 1"
							*/
	public	$_b_pyramid;	/* "it increases the DPB (decoding picture buffer) size required
								for playback, so when encoding for hardware, disable"
								to disable for mobiles
							*/
	public	$_weight_b=1; 	/* dragonfly - not set
								x264 recommendation - no cost -> always enabled
							*/
	public	$_threads;
	public	$_partitions;
	public	$_level;		/* Represents minimal required decoder capability - frmSz/frmRt/br*/
	public	$_global_header=1;/*"is used to force ffmpeg to spit out some important audio specifications"
								Important for akmi-hd/hls. somehow related with vglobal/aglobal options
								that i do not use here.
							*/
	public	$_trellis = 1;	/* "The main decision made in quantization is which coefficients 
								to round up and which to round down. Trellis chooses the optimal 
								rounding choices for the maximum rate-distortion score, 
								to maximize PSNR relative to bitrate."
								BP does not support it.
							*/
	public	$_chroma_me=1;	/* "Normally, motion estimation works off both the luma and 
								chroma planes."
								can be turned on to gain speed. relevant for mencoder
							*/
	public	$_dct8x8;		/* "the only reason to disable it is when one needs support 
								on a device not compatible with High Profile."
							*/
	public	$_fastskip=0;	/* "By default, x264 will skip macroblocks in P-frames that 
								don't appear to	have changed enough between two frames 
								to justify encoding the difference. This considerably speeds
								 up encoding. However, for a slight quality boost, 
								P-skip can be disabled."
								To turm on for 'presentation' assets
							*/
	public	$_mixed_refs;	/* "boosts quality with little speed impact. 
								It should generally be used, though it 
								obviously has no effect with only one reference frame."
							*/
	
	public	$_me="hex";
	public	$_loop;
	public	$_mv4;
	public	$_cmp;
	public	$_me_range; 
	public	$_keyint_min;	/* "Minimum GOP length, the minimum distance between I-frames. 
								Recommended default: 25"
								should match gop.
							*/
	public	$_sc_threshold; /* "Adjusts the sensitivity of x264's scenecut detection. Rarely needs to be adjusted. 
								Recommended default: 40"
							*/
	public	$_i_qfactor; 
	public	$_bt; 
	public	$_maxrate; 
	public	$_bufsize; 
	public	$_rc_eq;

	/* none h264 */
	public	$_sws;			/*	0 (Fast bilinear), 
								1 (Bilinear), 
								2 (Bicubic (good quality)), 
								3 (Experimental), 
								4 (Nearest neighbour (bad quality)), 
								5 (Area), 
								6 (Luma bicubic / chroma bilinear), 
								7 (Gauss), 
								8 (SincR), 
								9 (Lanczos), 
								10 (Bicubic spline)
							*/
	
	public	$_async; 
	public	$_vsync;
	
	public	$_vidBr;
	
	/* ----------------------
	 * Evaluate(KDLFlavor $target)
	 */
	public function Evaluate(KDLVideoData $vidObj){

		
			/*
			 * From Eagle and on, the H264 should be generated to match Akami HD constarints 
			 * for Apple HLS/adaptive playbck:
			 * - aligned key frames across all bitrates
			 * - same frame rate across all bitrates
			 * 
			 * '_h264ForMobile' flag rules the generation mode 
			 */
		$h264ForMobile = 0;
		if(property_exists($vidObj,"_h264ForMobile")) {
			$h264ForMobile = $vidObj->_h264ForMobile;
		}
			/*
			 * Check for 'presentation-style' video mode
			 */
		$presentationStyleMode = 0;
		if(isset($vidObj->_bitRate)){
			$this->_vidBr = $vidObj->_bitRate;
			if($vidObj->_bitRate<KDLConstants::LowBitrateThresHold) {
				$presentationStyleMode=1;
				$this->_crf=10;
			}
		}
		
/*
			$this->_vidId = $target->_video->_id;
			$this->_vidBr = $target->_video->_bitRate;
			$this->_vidWid = $target->_video->_width;
			$this->_vidHgt = $target->_video->_height;
			$this->_vidFr = $target->_video->_frameRate;
			$this->_vidGop = $target->_video->_gop;
			$this->_vid2pass = $target->_isTwoPass;
			$this->_vidRotation = $target->_video->_rotation;
			$this->_vidScanType = $target->_video->_scanType;

ffmp - -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 800k -maxrate 1200k -bufsize 1200k -rc_eq 'blurCplx^(1-qComp)' -level 30 -async 2 -vsync 2 
 */
		$h264params=null;
		switch($vidObj->_id) {
		case KDLVideoTarget::H264:
			$this->_refs = 2;
			$this->_coder = 0;
			$this->_subq = 2;
			$this->_bframes=3;
			$this->_b_pyramid;
			$this->_weight_b;
			$this->_threads="auto";
			$this->_partitions;
			$this->_dct8x8=null;

			if($h264ForMobile) {
				$this->forMobile($vidObj);
			}
			break;
		case KDLVideoTarget::H264B:
			$this->_refs = 6; // ffm - 2
			$this->_coder = 0;
			$this->_sws = 9; // ffm - none
			$this->_subq = 2;
			$this->_bframes=0;
			$this->_b_pyramid;
			$this->_weight_b;
			$this->_threads="auto";
			$this->_level;
			$this->_partitions;  // ffm - none/default
			$this->_global_header=1;
			$this->_trellis = 1;
			$this->_chroma_me;
			$this->_me="hex";
			$this->_dct8x8=null;

			if($h264ForMobile) {
				$this->forMobile($vidObj);
			}
			break;
		case KDLVideoTarget::H264M:
			$this->_refs = 6;// ffm - 2
			$this->_coder = 1;
			$this->_sws = 9; // ffm - none
			$this->_subq = 5;
			$this->_bframes=3;
			$this->_b_pyramid;
			$this->_weight_b;
			$this->_threads="auto";
			$this->_level;
			$this->_partitions="all";  // ffm - none/default
			$this->_global_header=1;
			$this->_trellis = 1;
			$this->_chroma_me;
			$this->_me="hex";
			$this->_dct8x8=null;

			if($h264ForMobile) {
				$this->forMobile($vidObj);
			}
			break;
		case KDLVideoTarget::H264H:				
			$this->_refs = 6;// ffm - 2
			$this->_coder = 1;
			$this->_sws = 9; // ffm - none
			$this->_subq = 7;
			$this->_bframes=16; //ffm - 16
			$this->_b_adapt=1;
			$this->_b_pyramid=1;
			$this->_weight_b=1; // ffmpeg - wpred
			$this->_threads="auto";
			$this->_level;
			$this->_partitions="p8x8,b8x8,i8x8,i4x4";  // ffm - none/default
			$this->_global_header=1;
			$this->_trellis = 1;
			$this->_chroma_me;
			$this->_me="umh";
			$this->_dct8x8=1;
			$this->_fastskip=1;
			$this->_mixed_refs=1;
			
			if($h264ForMobile) {
				$this->_cmp = 256;
				$this->_loop=1;
				$this->_mv4=1;
				$this->_trellis = 1;
				$this->_me_range = 16;
				if(isset($vidObj->_gop))
					$this->_keyint_min = $vidObj->_gop;
				else 
					$this->_keyint_min = 25; 	//should match gop
				$this->_sc_threshold = 40; 	// x264 recommendation
				$this->_i_qfactor = 0.71;
				$this->_rc_eq = '\'blurCplx^(1-qComp)\'';
				$this->_vsync = 2;
				$this->_async = 2;
				
/*
 * Following 'mobile-compliant' restrictions where removed from High Profile settings
 
				$this->_refs = 1;
				$this->_partitions="p8x8,b8x8,i4x4";
				$this->_maxrate = 1200;		//should match vidBr
				$this->_bufsize = 1200;		//should match vidBr ??? "Depends on the profile level of the video being encoded. Set only if you're encoding for a hardware device"
				if(!isset($vidObj->_bt) && !isset($vidObj->_cbr)) {
					$this->_bt = 800;			// bit rate tolleranceto be relative to vidBr
				}
				$this->_level = 30; 		// to match iPhone processing constraints
				$this->_bframes=0;

 				$this->_b_pyramid = null;
				$this->_mixed_refs= null;
				$this->_dct8x8=null;
				$this->_b_adapt=null;
	*/
			}
			break;
		}
		return true;
	}

	/* ----------------------
	 * forMobile
	 */
	private function forMobile(KDLVideoData $vidObj){
//ffmp - -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 
//-me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 800k 
//-maxrate 1200k - 1200k -rc_eq 'blurCplx^(1-qComp)' -level 30 -async 2 -vsync 2 
		$this->_cmp = 256;
		$this->_partitions="p8x8,b8x8,i4x4";
		$this->_loop=1;
		$this->_mv4=1;
		$this->_trellis = 1;
		$this->_refs = 1;
		$this->_me_range = 16;
		if(isset($vidObj->_gop))
			$this->_keyint_min = $vidObj->_gop;
		else 
			$this->_keyint_min = 25; 	//should match gop
		$this->_sc_threshold = 40; 	// x264 recommendation
		$this->_i_qfactor = 0.71;
		$this->_bt = 800;			// bit rate tolerance to be relative to vidBr
		if(isset($this->_vidBr))
			$this->_maxrate = $this->_vidBr;		//should match vidBr
		else
			$this->_maxrate = 1200;		//should match vidBr
		$this->_bufsize = 1200;		//should match vidBr ??? "Depends on the profile level of the video being encoded. Set only if you're encoding for a hardware device"
		$this->_rc_eq = '\'blurCplx^(1-qComp)\'';
		$this->_level = 30; 		// to match iPhone processing constraints
		$this->_vsync = 2;
		$this->_async = 2;
		
		$this->_b_pyramid = null;
		$this->_mixed_refs= null;
		$this->_dct8x8=null;
		$this->_bframes=0;
		$this->_b_adapt=null;
	}
	
	/* ----------------------
	 * FFmpeg
	 */
	public function FFmpeg()
	{
// main=" libx264 -subq 5".$ffQsettings." -coder 1 -refs 2";
// High=" libx264 -subq 7".$ffQsettings." -bf 16 -coder 1 -refs 6 -flags2 +bpyramid+wpred+mixed_refs+dct8x8+fastpskip";
		$params = " libx264";
		if(isset($this->_subq)) 	$params.=" -subq $this->_subq";
		$params.= " -qcomp $this->_qcomp -qmin $this->_qmin -qmax $this->_qmax -qdiff $this->_qdiff";
		if(isset($this->_bframes)) 	$params.=" -bf $this->_bframes";
		if(isset($this->_coder)) 	$params.=" -coder $this->_coder";
		if(isset($this->_refs)) 	$params.=" -refs $this->_refs";
		if(isset($this->_crf)) 		$params.=" -crf $this->_crf";
		if(isset($this->_b_adapt))	$params.=" -b_strategy $this->_b_adapt";
		
		if(isset($this->_partitions)){
			$partArr = explode(",",$this->_partitions);
			$partitions = null;
			foreach ($partArr as $p) {
				switch($p){
				case "all":
					$partitions.="+partp8x8+partp4x4+partb8x8+parti8x8+parti4x4";
					break;
				case "p8x8":
					$partitions.="+partp8x8";
					break;
				case "p4x4":
					$partitions.="+partp4x4";
					break;
				case "b8x8":
					$partitions.="+partb8x8";
					break;
				case "i8x8":
					$partitions.="+parti8x8";
					break;
				case "i4x4":
					$partitions.="+parti4x4";
					break;
				}
			}
			if(isset($partitions))	$params.=" -partitions $partitions";
		}
// ffmpeg -i <in file> -f mpegts -acodec libmp3lame -ar 48000 -ab 64k -s 320×240 -vcodec libx264 -b 96k 
// -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 5 -trellis 1 -refs 1 -coder 0 
// -me_range 16 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -bt 200k 
// -maxrate 96k -bufsize 96k -rc_eq 'blurCplx^(1-qComp)' 
// -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -level 30 -aspect 320:240 -g 30 -async 2 
		if(isset($this->_trellis))		$params.= " -trellis $this->_trellis";
		if(isset($this->_keyint_min))	$params.= " -keyint_min $this->_keyint_min";
		if(isset($this->_me))			$params.= " -me_method $this->_me";
		if(isset($this->_me_range))		$params.= " -me_range $this->_me_range";
		if(isset($this->_sc_threshold))	$params.= " -sc_threshold $this->_sc_threshold";
		if(isset($this->_i_qfactor))	$params.= " -i_qfactor $this->_i_qfactor";
		if(isset($this->_bt))			$params.= " -bt $this->_bt"."k";
		if(isset($this->_maxrate))		$params.= " -maxrate $this->_maxrate"."k";
		if(isset($this->_bufsize))		$params.= " -bufsize $this->_bufsize"."k";
		if(isset($this->_rc_eq))		$params.= " -rc_eq $this->_rc_eq";
		if(isset($this->_level))		$params.= " -level $this->_level";
		
		$flags=null;
		{
			if(isset($this->_loop)) {
				if($this->_loop>0) $flags.= "+loop";
				else $flags.= "-loop";
			}
			if(isset($this->_mv4)) {
				if($this->_mv4>0) $flags.= "+mv4";
				else $flags.= "-mv4";
			}
			if(isset($this->_global_header)) {
				if($this->_global_header>0) $flags.= "+global_header";
				else $flags.= "-global_header";
			}
			
			if(isset($flags))	$params.=" -flags $flags";
		}
		
		$flags2=null;
		{
			if(isset($this->_b_pyramid)) 	$flags2.= "+bpyramid";
			if(isset($this->_weight_b))		$flags2.= "+wpred";
			if(isset($this->_mixed_refs))	$flags2.= "+mixed_refs";
			if(isset($this->_dct8x8))		$flags2.= "+dct8x8";
			if(isset($this->_fastpskip)) {
				if($this->_fastpskip>0) $flags2.= "+fastpskip";
				else $flags2.= "-fastpskip";
			}
			
			if(isset($flags2))	$params.=" -flags2 $flags2";
		}
		
		if(isset($this->_vsync))		$params.= " -vsync $this->_vsync";
		if(isset($this->_async))		$params.= " -async $this->_async";
		
		return $params;
	} 
	
	/* ----------------------
	 * Mencoder
	 */
	public function Mencoder()
	{
/*
						$h264params = $h264params." -ovc x264 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= "bitrate=".$this->_vidBr;
							$h264params .= ":";
							if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
								$h264params .= "crf=30:";
							}
						}
						$h264params .= "subq=2:8x8dct:frameref=2:bframes=3:b_pyramid=1:weight_b:threads=auto";
*/
/*
						$h264params = $h264params." -ovc x264 -sws 9 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= " bitrate=".$this->_vidBr;
							$h264params .= ":";
							if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
								$h264params .= "crf=30:";
							}
						}
						$h264params .= "subq=2:frameref=6:bframes=0:threads=auto:nocabac:level_idc=30:
						global_header:partitions=all:trellis=1:chroma_me:me=umh";

 */
		$params = " -ovc x264";
		if(isset($this->_sws))		$params.= " -sws $this->_sws";

		$encopts = " qcomp=$this->_qcomp:qpmin=$this->_qmin:qpmax=$this->_qmax:qpstep=$this->_qdiff:";
		{
			if(isset($this->_vidBr)) {
				$encopts.= "bitrate=$this->_vidBr:";
				if(isset($this->_crf))	$encopts.= "crf=30:";
			}
			if(isset($this->_subq))			$encopts.= "subq=$this->_subq:";
			if(isset($this->_refs))			$encopts.= "frameref=$this->_refs:";
			if(isset($this->_bframes))		$encopts.= "bframes=$this->_bframes:";
if(isset($this->_b_adapt)) $encopts.= "b_adapt=$this->_b_adapt:";
			if(isset($this->_b_pyramid))	$encopts.= "b_pyramid=1:";
			if(isset($this->_weight_b))		$encopts.= "weight_b=1:";
			if(isset($this->_threads))		$encopts.= "threads=$this->_threads:";
			if(isset($this->_coder) && $this->_coder==0) $encopts.= "nocabac:";
			if(isset($this->_level))		$encopts.= "level_idc=$this->_level:";
			if(isset($this->_global_header))$encopts.= "global_header:";
			if(isset($this->_dct8x8))		$encopts.= "8x8dct:";
			if(isset($this->_trellis))		$encopts.= "trellis=$this->_trellis:";
			if(isset($this->_chroma_me))	$encopts.= "chroma_me=$this->_chroma_me:";

			if(isset($this->_me))			$encopts.= "me=$this->_me:";
			if(isset($this->_keyint_min))	$encopts.= "keyint_min=$this->_keyint_min:";
			if(isset($this->_me_range))		$encopts.= "me_range=$this->_me_range:";
			if(isset($this->_sc_threshold))	$encopts.= "scenecut=$this->_sc_threshold:";
			if(isset($this->_i_qfactor))	$encopts.= "ipratio=$this->_i_qfactor:";
			if(isset($this->_bt))			$encopts.= "ratetol=$this->_bt:";
			if(isset($this->_maxrate))		$encopts.= "vbv-maxrate=$this->_maxrate:";
			if(isset($this->_bufsize))		$encopts.= "vbv-bufsize=$this->_bufsize:";
//			if(isset($this->_rc_eq))		$encopts.= " -rc_eq $this->_rc_eq";
			
			if(isset($this->_partitions)){
				$partArr = explode(",",$this->_partitions);
				$partitions = null;
				foreach ($partArr as $p) {
					switch($p){
					case "all":
						$partitions.="all";
						break;
					case "p8x8":
						$partitions.="+p8x8";
						break;
					case "p4x4":
						$partitions.="+p4x4";
						break;
					case "b8x8":
						$partitions.="+b8x8";
						break;
					case "i8x8":
						$partitions.="+i8x8";
						break;
					case "i4x4":
						$partitions.="+i4x4";
						break;
					}
				}
				if(isset($partitions))	$encopts.="partitions=$partitions:";
			}
			
			if(isset($encopts))	{
				$encopts = rtrim($encopts,":");
				$params.= " -x264encopts $encopts";
			}
		}
		
		
		return $params;
	}
}

?>