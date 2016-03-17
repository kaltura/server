<?php

/**
 * Created by IntelliJ IDEA.
 * User: Tali.Harash
 * Date: 9/3/2015
 * Time: 10:49 AM
 */
class kQuizPdf
{

    private $pdf;
    /**
     * @var boolean - is it required to set a footer in the PDF document
     */
    protected $isFooter = true;

    /**
     * @var boolean - is it required to set a header in the PDF document
     */
    protected $isHeader = true;

    //db entry id
    protected $entryId;

    protected $normalStyle;
    protected $indentListStyle;
    protected $listWithAddLineBeforeStyle;
    protected $indentedListWithAddLineBefore;
    protected $titleStyle;
    protected $heading1Style;
    protected $heading2Style;
    protected $heading3Style;
    protected $heading4Style;
    protected $heading5Style;
    protected $heading6Style;


    public function __construct($entryId)
    {
        $this->normalStyle = new PdfStyle('Normal', 'Times');
        $this->indentListStyle = new PdfStyle('IndentList', 'Times', 12, '', false, false, 'L', 5);
        $this->listWithAddLineBeforeStyle = new PdfStyle('ListWithAddLineBefore', 'Times', 12, 'I', true);
        $this->indentedListWithAddLineBefore = new PdfStyle('IndentListWithAddLineBefore', 'Times', 12, '',
                                                                                                true, false, 'L', 5);
        $this->titleStyle = new PdfStyle('Title', 'Arial', 14, 'BU', true, false, 'C');
        $this->heading1Style = new PdfStyle('Heading1', 'Times', 18);
        $this->heading2Style = new PdfStyle('Heading2', 'Times', 16);
        $this->heading3Style = new PdfStyle('Heading3', 'Times', 15);
        $this->heading4Style = new PdfStyle('Heading4', 'Times', 14);
        $this->heading5Style = new PdfStyle('Heading5', 'Times', 13);
        $this->heading6Style = new PdfStyle('Heading6', 'Times', 12);

        $this->entryId = $entryId;
        $this->pdf = new PdfGenerator('Thank You', 'Questionnaire', '','Questionnaire',
                                                    'Questionnaire', '');
        $this->pdf->Footer();
        $this->pdf->Header();
        $this->pdf->SetMargins(10,15,10);
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(true, 20);
        $this->pdf->SetY(30);
    }


    public function createQuestionPdf()
    {
        $dbEntry = entryPeer::retrieveByPK($this->entryId);
        $title = "Here are the questions from  [".$dbEntry->getName()."]";
        KalturaLog::debug("Questions from  [".$dbEntry->getName()."]");
        $this->pdf->addTitle($title, $this->titleStyle);
        $this->pdf->setOutFileName($dbEntry->getName());
        $questionType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION);
        $questions = CuePointPeer::retrieveByEntryId($this->entryId, array($questionType));
        $questNum = 0;
        foreach ($questions as $question)
        {
            $questNum +=1;
            $this->pdf->addList($questNum, $question->getName(), $this->listWithAddLineBeforeStyle);
            $this->pdf->addHeadline(6, "Optional Answers:", $this->heading6Style);
            $ansNum = 0;
            foreach ($question->getOptionalAnswers() as $optionalAnswer)
            {
                $ansNum +=1;
                $this->pdf->addList($ansNum, $optionalAnswer->getText(), $this->indentListStyle);
            }
        }
    }


    public function submitDocument()
    {
        return new kRendererPdfFile($this->pdf);
    }
}
