<?php
/**
 * @package infra
 * @subpackage Media
 */
class kPatchSwf
{
	const SWF_TAG_DefineBinaryData = 87;
	
	const KALTURA_EMBED_SIGNATURE = "KALTURA_EMBEDDED_DATA";
	
	static $TAG_NAMES = array(
		0 => "End",
		1 => "ShowFrame",
		2 => "DefineShape",
		4 => "PlaceObject",
		5 => "RemoveObject",
		6 => "DefineBits",
		7 => "DefineButton",
		8 => "JPEGTables",
		9 => "SetBackgroundColor",
		10 => "DefineFont",
		11 => "DefineText",
		12 => "DoAction",
		13 => "DefineFontInfo",
		14 => "DefineSound",
		15 => "StartSound",
		17 => "DefineButtonSound",
		18 => "SoundStreamHead",
		19 => "SoundStreamBlock",
		20 => "DefineBitsLossless",
		21 => "DefineBitsJPEG2",
		22 => "DefineShape2",
		23 => "DefineButtonCxform",
		24 => "Protect",
		26 => "PlaceObject2",
		28 => "RemoveObject2",
		32 => "DefineShape3",
		33 => "DefineText2",
		34 => "DefineButton2",
		35 => "DefineBitsJPEG3",
		36 => "DefineBitsLossless2",
		37 => "DefineEditText",
		39 => "DefineSprite",
		43 => "FrameLabel",
		45 => "SoundStreamHead2",
		46 => "DefineMorphShape",
		48 => "DefineFont2",
		56 => "ExportAssets",
		57 => "ImportAssets",
		58 => "EnableDebugger",
		59 => "DoInitAction",
		60 => "DefineVideoStream",
		61 => "VideoFrame",
		62 => "DefineFontInfo2",
		64 => "EnableDebugger2",
		65 => "ScriptLimits",
		66 => "SetTabIndex",
		69 => "FileAttributes",
		70 => "PlaceObject3",
		71 => "ImportAssets2",
		73 => "DefineFontAlignZones",
		74 => "CSMTextSettings",
		75 => "DefineFont3",
		76 => "SymbolClass",
		77 => "Metadata",
		78 => "DefineScalingGrid",
		82 => "DoABC",
		83 => "DefineShape4",
		84 => "DefineMorphShape2",
		86 => "DefineSceneAndFrameLabelData",
		87 => "DefineBinaryData",
		88 => "DefineFontName",
		89 => "StartSound2"
	);
	
	private $header;
	private $swfdata;
	private $pos;
	
	public function kPatchSwf($swf, $signature = self::KALTURA_EMBED_SIGNATURE)
	{
		$this->header = substr($swf, 0, 8);
		$zdata = substr($swf, 8, strlen($swf) - 8);
		$this->swfdata = @gzuncompress($zdata);
		$this->reset();
		$this->signature = "\0\0\0\0$signature";
	}
	
	private function reset()
	{
		$this->pos = 0xd;
	}
	
	public function patch($data)
	{
		$this->reset();
		$swfdata = $this->swfdata;
		
		while(list($tag_start_pos, $tag_type, $tag_header_len, $tag_len) = $this->getNextTag())
		{
			if ($tag_type == self::SWF_TAG_DefineBinaryData)
			{
				$signature = substr($swfdata, $tag_start_pos + $tag_header_len + 2 + 4, strlen($this->signature));
				if ($signature != $this->signature)
					continue;
					
				$tag_prefix = substr($swfdata, $tag_start_pos + $tag_header_len, 6);
				
				$data_len = strlen($tag_prefix) + strlen($data);
				if ($data_len < 63)
				{
					$record_header = pack("v1", (self::SWF_TAG_DefineBinaryData << 6) + $data_len);
				}
				else
				{
					$record_header = pack("v1V1", (self::SWF_TAG_DefineBinaryData << 6) + 0x3f, $data_len);
				}
				
				$new_tag = $record_header.$tag_prefix.$data;
				
				$new_swfdata = substr($swfdata, 0, $tag_start_pos).
					$new_tag.
					substr($swfdata, $this->pos);

				$header = substr($this->header, 0, 4).pack("V1", 8 + strlen($new_swfdata));
				$zdata = gzcompress($new_swfdata, 9);

				return $header . $zdata;
			}
		}
	}
	
	private function getNextTag()
	{
		$swfdata = $this->swfdata;
		
		$pos = $this->pos;

		if($pos < strlen($swfdata))
		{
			$tag_start_pos = $pos;
			$record_header = unpack("v1", $swfdata[$pos++].$swfdata[$pos++]);
			$tag_type = $record_header[1] >> 6;
			$tag_len = $record_header[1] & 0x3f;
			$tag_header_len = 2;
			if ($tag_len == 0x3f)
			{
				$tag_header_len = 6;
				$tag_len = unpack("V1", $swfdata[$pos++].$swfdata[$pos++].$swfdata[$pos++].$swfdata[$pos++]);
				$tag_len = $tag_len[1];
			}
			
			$this->pos = $pos + $tag_len;
			
			return array($tag_start_pos, $tag_type, $tag_header_len, $tag_len);
		}
		
		return false;
	}
	
	public function dump()
	{
		$this->reset();
		
		while(list($tag_start_pos, $tag_type, $tag_header_len, $tag_len) = $this->getNextTag())
		{
			printf("%06x %3d %-20s : length: (hdr: %2d) %04x\n", $tag_start_pos, $tag_type, @self::$TAG_NAMES[$tag_type], $tag_header_len, $tag_len);
		}
	}
};

