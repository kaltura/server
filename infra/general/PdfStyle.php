<?php

/**
 * Created by IntelliJ IDEA.
 * User: Tali.Harash
 * Date: 9/10/2015
 * Time: 11:41 AM
 */
class PdfStyle
{

    private $styleName='';
    private $fontName='';
    private $fontSize=12;
    private $fontStyle='';
    private $isAddLineBefore=false;
    private $isAddLineAfter = false;
    private $fColor = ''; //currently not supported
    private $rowHeight = 5;
    private $rowIndent = 'L';
    private $x = 0; //set the beginig of the line

    public function __construct($sName, $fName, $fSize=12, $fStyle='', $addLineBefore=false, $addLineAfter=false, $rowI = 'L',
                                $xx=0, $fColor='', $rowH=5)
    {
        $this->styleName = $sName;
        $this->fontName = $fName;
        $this->fontSize = $fSize;
        $this->fontStyle = $fStyle;
        $this->isAddLineAfter = $addLineAfter;
        $this->isAddLineBefore = $addLineBefore;
        $this->fColor = $fColor;
        $this->rowHeight = $rowH;
        $this->rowIndent = $rowI;
        $this->x = $xx;
    }

    public function getStyleName()
    {
        return $this->styleName;
    }

    public function getFontName()
    {
        return $this->fontName;
    }

    public function getFontSize()
    {
        return $this->fontSize;
    }

    public function getFontStyle()
    {
        return $this->fontStyle;
    }

    public function isAddLineAfter()
    {
        return $this->isAddLineAfter;
    }

    public function isAddLineBefore()
    {
        return $this->isAddLineBefore;
    }

    public function getRowHeight()
    {
        return $this->rowHeight;
    }

    public function getRowIndent()
    {
        return $this->rowIndent;
    }

    public function getX()
    {
        return $this->x;
    }
}