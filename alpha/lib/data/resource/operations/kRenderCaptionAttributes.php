<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kRenderCaptionAttributes extends kCaptionAttributes
{
	/**
	 * @var string
	 */
	private $captionFileUrl;

	/**
	 * @var string
	 */
	private $fontName;

	/**
	 * @var int
	 */
	private $fontSize;

	/**
	 * @var string
	 */
	private $fontStyle;

	/**
	 * @var string
	 */
	private $primaryColour;

	/**
	 * @var kBorderStyle
	 */
	private $borderStyle;

	/**
	 * @var string
	 */
	private $backColour;

	/**
	 * @var string
	 */
	private $outlineColour;

	/**
	 * @var int
	 */
	private $shadow;

	/**
	 * @var bool
	 */
	private $bold;

	/**
	 * @var bool
	 */
	private $italic;

	/**
	 * @var bool
	 */
	private $underline;

	/**
	 * @var kCaptionsAlignment
	 */
	private $alignment;

	/**
	 * @var string
	 */
	private $captionAssetId;

	/**
	 * @return string
	 */
	public function getFontName()
	{
		return $this->fontName;
	}

	/**
	 * @param $fontName string
	 */
	public function setFontName($fontName)
	{
		$this->fontName = $fontName;
	}

	/**
	 * @return int
	 */
	public function getFontSize()
	{
		return $this->fontSize;
	}

	/**
	 * @param $fontSize int
	 */
	public function setFontSize($fontSize)
	{
		$this->fontSize = $fontSize;
	}

	/**
	 * @return string
	 */
	public function getFontStyle()
	{
		return $this->fontStyle;
	}

	/**
	 * @param $fontStyle string
	 */
	public function setFontStyle($fontStyle)
	{
		$this->fontStyle = $fontStyle;
	}

	/**
	 * @return string
	 */
	public function getPrimaryColour()
	{
		return $this->primaryColour;
	}

	/**
	 * @param $primaryColour string
	 */
	public function setPrimaryColour($primaryColour)
	{
		$this->primaryColour = $primaryColour;
	}

	/**
	 * @return kBorderStyle
	 */
	public function getBorderStyle()
	{
		return $this->borderStyle;
	}

	/**
	 * @param $borderStyle kBorderStyle
	 */
	public function setBorderStyle($borderStyle)
	{
		$this->borderStyle = $borderStyle;
	}

	/**
	 * @return string
	 */
	public function getBackColour()
	{
		return $this->backColour;
	}

	/**
	 * @param $backColour string
	 */
	public function setBackColour($backColour)
	{
		$this->backColour = $backColour;
	}

	/**
	 * @return string
	 */
	public function getOutlineColour()
	{
		return $this->outlineColour;
	}

	/**
	 * @param $outlineColour string
	 */
	public function setOutlineColour($outlineColour)
	{
		$this->outlineColour = $outlineColour;
	}

	/**
	 * @return int
	 */
	public function getShadow()
	{
		return $this->shadow;
	}

	/**
	 * @param $shadow string
	 */
	public function setShadow($shadow)
	{
		$this->shadow = $shadow;
	}

	/**
	 * @return boolean
	 */
	public function getBold()
	{
		return $this->bold;
	}

	/**
	 * @param $bold boolean
	 */
	public function setBold($bold)
	{
		$this->bold = $bold;
	}

	/**
	 * @return boolean
	 */
	public function getItalic()
	{
		return $this->italic;
	}

	/**
	 * @param $italic boolean
	 */
	public function setItalic($italic)
	{
		$this->italic = $italic;
	}

	/**
	 * @return boolean
	 */
	public function getUnderline()
	{
		return $this->underline;
	}

	/**
	 * @param $underline boolean
	 */
	public function setUnderline($underline)
	{
		$this->underline = $underline;
	}

	/**
	 * @return kCaptionsAlignment
	 */
	public function getAlignment()
	{
		return $this->alignment;
	}

	/**
	 * @param kCaptionsAlignment $alignment
	 */
	public function setAlignment($alignment)
	{
		$this->alignment = $alignment;
	}

	/**
	 * @return string
	 */
	public function getCaptionAssetId()
	{
		return $this->captionAssetId;
	}

	/**
	 * @param string $captionAssetId
	 */
	public function setCaptionAssetId($captionAssetId)
	{
		$this->captionAssetId = $captionAssetId;
	}

	/**
	 * @return string
	 */
	public function getCaptionFileUrl()
	{
		return $this->captionFileUrl;
	}

	/**
	 * @param string $captionFileUrl
	 */
	public function setCaptionFileUrl($captionFileUrl)
	{
		$this->captionFileUrl = $captionFileUrl;
	}

	public function toArray()
	{
		return array(
			'fontName' => $this->fontName,
			'fontSize' => $this->fontSize,
			'fontStyle' => $this->fontStyle,
			'primaryColour' => $this->primaryColour,
			'borderStyle' => $this->borderStyle,
			'backColour' => $this->backColour,
			'outlineColour' => $this->outlineColour,
			'shadow' => $this->shadow,
			'bold' => $this->bold,
			'italic' => $this->italic,
			'underline' => $this->underline,
			'alignment' => $this->alignment,
			'captionAssetId' => $this->captionAssetId
		);
	}

	public function getApiType()
	{
		return 'KalturaRenderCaptionAttributes';
	}
}
