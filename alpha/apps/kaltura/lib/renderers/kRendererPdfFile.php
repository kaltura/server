<?php
require_once(dirname(__file__) . '/kRendererBase.php');

/**
 * Created by IntelliJ IDEA.
 * User: Tali.Harash
 * Date: 9/7/2015
 * Time: 2:54 PM
 */
class kRendererPdfFile implements kRendererBase
{


    private $pdfObj;

    public function __construct(PdfGenerator $obj)
    //public function __construct($obj)
    {
        $this->pdfObj = $obj;
    }

    public function validate()
    {
        return true;
    }

    public function output()
    {
        try
        {
            KalturaLog::debug("PDF::: kQuizPdf output");
            echo $this->pdfObj->Submit();
            //return $this->pdfObj->Submit();
            //echo($this->pdfObj);

            KExternalErrors::dieGracefully();
        }
        catch (KalturaAPIException $e)
        {
            KalturaLog::debug("PDF::: API Exception message ".$e->getMessage());
            KalturaLog::debug("PDF::: API Exception Trace:  ".$e->getTrace());

        }
    }


}