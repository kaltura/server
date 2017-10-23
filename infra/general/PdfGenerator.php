<?php

require('../../vendor/fpdf/fpdf.php');

/**
 * Created by IntelliJ IDEA.
 * User: Tali.Harash
 * Date: 8/27/2015
 * Time: 11:00 AM
 */
class PdfGenerator extends FPDF
{

    private $heading = '';
    private $auth = '';
    private $subj = '';
    private $fName = '';
    private $creat = '';
    private $signature ='';
    private $requiredSkipSize = 60;
    private $minSkipSize = 10;
    private $pageHieght = 260;
    private $docHeightThreshold = 200;
    private $outputFileName="outQuizToBrowse";

    /**
     * @param
     * @param sig - string - document signature
     * @param head - string - document title
     * @param ath - string - document author
     * @param sbj - string - document subject
     * @param name - string - document name
     * @param creat - string - document creator
     * @param string $orientation
     * @param string $unit
     * @param string $size - page size
     */
    public function __construct($sig='', $head='', $ath='', $sbj='', $name='', $creat='', $orientation='P',
                                $unit='mm', $size='A4')
    {

        parent::__construct($orientation,$unit,$size);

        if (isset($sig) && is_string($sig))
        {
            $this->signature = $sig;
        }

        $this->heading = $head;
        $this->auth = $ath;
        $this->subj = $sbj;
        $this->fName = $name;
        $this->creat = $creat;

        if (strlen($this->heading)> 0)
        {
            $this->SetTitle($this->heading);
        }

        if (strlen($this->creat) > 0)
        {
            $this->SetCreator($this->creat);
        }

        if (strlen($this->auth) > 0)
        {
            $this->SetAuthor($this->auth);
        }

        if (strlen($this->subj) > 0)
        {
            $this->SetSubject($this->subj);
        }
    }

    public function setOutFileName($name)
    {
        $this->outputFileName=$name;
    }

    public function Header()
    {
        // set the font to be ariel
        $this->SetFont('Arial','',8);
        $this->SetY(10);
        $this->Cell(0, 0, $this->heading, 0, 0, 'C');
        $this->Line(10, 15, 200, 15);
        //set the 'y' so that the rest of the document will be written under the header
        $this->SetY(20);
    }


    public function Footer()
    {
        if ($this->PageNo()>0)
        {
            $this->Line(10, 280, 200, 280);

            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial', 'I', 8);
            // Page number
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    /**
     * @param string $str - add title for the document
     * @param PdfStyle $style
     */
    public function addTitle($str, PdfStyle $style)
    {
        if (!isset ($str) || (!is_string($str)))
        {
            return;
        }
        $this->addText($str, $style);
    }

    /**
     * addList - add text in a list format
     * @param string $sign - The sign that will be printed at the beginig of each paragraph in the text.
     * @param string $text - the question that should be printed
     * @param PdfStyle $style
     */
    public function addList($sign, $text, PdfStyle $style)
    {
        if (!isset($text) || !strlen($text) > 0)
        {
            return;
        }

        if (is_numeric($sign))
        {
            $text = $sign.". ".$text;
        }
        else
        {
            $text = $sign." ".$text;
        }

        $this->addText($text, $style);
    }

    /**
     * @param $string
     */
    public function addParagraph($string)
    {
        if (!isset ($string) || (!is_string($string)))
        {
            return;
        }
        $style = new PdfStyle('Normal', 'Times', 12, '', true, false, '');
        $this->addText($string, $style);
    }


    /**
     * @param $level - headline level
     * @param $text - string - the headline text
     * @param PdfStyle $style
     */
    public function addHeadline($level='1', $text, PdfStyle $style)
    {
        if (!isset ($text) || (!is_string($text)))
        {
            return;
        }
        $this->addText($text, $style);
    }

    private function addSignature()
    {
        $skipSize = 0;
        $currentY = $this->GetY();

        if ($currentY ==$this->pageHieght)
        {
            $skipSize = $this->minSkipSize;
        }
        if ($currentY < $this->pageHieght && $currentY <= $this->docHeightThreshold)
        {
            $skipSize = $this->pageHieght - $currentY;
        }
        if ($currentY < $this->pageHieght && $currentY > $this->docHeightThreshold)
        {
            $skipSize = $this->requiredSkipSize - $this->pageHieght + $currentY;
        }
        $this->Ln($skipSize);
        $this->SetFont('Times','',12);
        $this->MultiCell(0, 5, $this->signature, 0, 'L');
    }

    private function addText($text, PdfStyle $style)
    {
        if (strlen($text)> 0)
        {
            if ($style->isAddLineBefore())
            {
                $this->Ln();
            }
            $this->SetFont($style->getFontName(), $style->getFontStyle(), $style->getFontSize());

            $temp = $this->GetX();
            if ($style->getX()>0)
            {
                $temp2 = $temp + $style->getX() ;
                $this->SetX($temp2);
            }
            $this->MultiCell(0, $style->getRowHeight(),$text, 0, $style->getRowIndent());
            if ($style->getX()>0)
            {
                $this->SetX($temp);
            }
                if ($style->isAddLineAfter())
            {
                $this->Ln();
            }
        }
    }



    public function Submit()
    {
        if (strlen($this->signature) > 0)
        {
            $this->addSignature();
        }
        return ($this->Output($this->outputFileName.'.pdf','S'));
    }

}
