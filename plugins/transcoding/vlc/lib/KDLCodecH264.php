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
	public	$_me_range; 	/* "MErange controls the max range of the motion search. 
								For HEX and DIA, this is clamped to between 4 and 16, with a default of 16.
								For UMH and ESA, it can be increased beyond the default 16 to allow for 
								a wider-range motion search, which is useful on HD footage and for 
								high-motion footage. Note that for UMH and ESA, increasing MErange will 
								significantly slow down encoding
							 */
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
			
			break;
		}
		return true;
	}

}

?>