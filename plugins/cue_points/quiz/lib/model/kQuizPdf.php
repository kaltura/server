<?php

/**
 * Created by IntelliJ IDEA.
 * User: Tali.Harash
 * Date: 9/3/2015
 * Time: 10:49 AM
 */
class kQuizPdf
{
	const ASIAN_FONT = 'AsianFont';
	const NOTO_SANS_FONT = 'notoSansFont';
	const TIMES_FONT = 'Times';
	const NORMAL_STYLE = 'normalStyle';
	const INDENT_LIST_STYLE = 'indentListStyle';
	const LIST_WITH_ADD_LINE_BEFORE_STYLE = 'listWithAddLineBeforeStyle';
	const INDENTED_LIST_WITH_ADD_LINE_BEFORE = 'indentedListWithAddLineBefore';
	const TITLE_STYLE = 'titleStyle';
	const HEADING6_STYLE = 'heading6Style';
	const ASIAN_STYLE_PREFIX = 'asian';
	const NOTO_STYLE_PREFIX = 'noto';

	/**
	 * @var PdfGenerator
	 */
	private $pdf;

	private $styles;
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

	public function __construct($entryId)
	{
		$this->entryId = $entryId;
		$this->initPDF();
		$this->initFonts();
		$this->initStyles();
	}

	private function initStyles()
	{
		$styles = array();
		$styles[self::NORMAL_STYLE] = new PdfStyle('Normal', self::TIMES_FONT);
		$styles[self::HEADING6_STYLE] = new PdfStyle('Heading6', 'Times', 12);
		$styles[self::INDENTED_LIST_WITH_ADD_LINE_BEFORE] = new PdfStyle('IndentListWithAddLineBefore', self::TIMES_FONT, 12, '',
			true, false, 'L', 5);

		$styles[self::INDENT_LIST_STYLE] = new PdfStyle('IndentList', self::TIMES_FONT, 12, '', false, false, 'L', 5);
		$styles[self::ASIAN_STYLE_PREFIX.self::INDENT_LIST_STYLE] = new PdfStyle('AsianIndentList', self::ASIAN_FONT,12, '', false, false, 'L', 5);
		$styles[self::NOTO_STYLE_PREFIX.self::INDENT_LIST_STYLE] = new PdfStyle('NotoIndentList', self::NOTO_SANS_FONT,12, '', false, false, 'L', 5);

		$styles[self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('ListWithAddLineBefore', self::TIMES_FONT, 12, 'I', true);
		$styles[self::ASIAN_STYLE_PREFIX.self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('AsianListWithAddLineBefore', self::ASIAN_FONT, 12,
			'', true);
		$styles[self::NOTO_STYLE_PREFIX.self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('NotoListWithAddLineBefore', self::NOTO_SANS_FONT,
			12, 'I', true);

		$styles[self::TITLE_STYLE] = new PdfStyle('Title', 'Arial', 14, 'BU', true, false, 'C');
		$styles[self::NOTO_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('NotoTitle', self::NOTO_SANS_FONT, 14, 'BU', true,
			false, 'C');
		$styles[self::ASIAN_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('AsianTitle', 'Arial', 14, 'U', true, false, 'C');

		$this->styles = $styles;
	}

	private function initPDF()
	{
		$this->pdf = new PdfGenerator('Thank You', 'Questionnaire', '','Questionnaire','Questionnaire', '');
		$this->pdf->SetMargins(10,15,10);
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage();
		$this->pdf->SetAutoPageBreak(true, 20);
		$this->pdf->SetY(30);
	}

	private function initFonts()
	{
		$this->pdf->AddFont(self::ASIAN_FONT,'','VL-PGothic-Regular.ttf',true);
		$this->pdf->AddFont(self::NOTO_SANS_FONT,'','NotoSans-Regular.ttf',true);
		$this->pdf->AddFont(self::NOTO_SANS_FONT,'B','NotoSans-Bold.ttf',true);
		$this->pdf->AddFont(self::NOTO_SANS_FONT,'BI','NotoSans-BoldItalic.ttf',true);
		$this->pdf->AddFont(self::NOTO_SANS_FONT,'I','NotoSans-Italic.ttf',true);
	}

	public function createQuestionPdf()
	{
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		$entryName = $dbEntry->getName();
		$title = "Here are the questions from  [$entryName]";
		KalturaLog::debug("Questions from  [$entryName]");
		$stylePrefix = $this->getStylePrefix($title);
		$this->pdf->addTitle($title, $this->styles[$stylePrefix.self::TITLE_STYLE]);
		$this->pdf->setOutFileName($dbEntry->getName());
		$questionType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION);
		$questions = CuePointPeer::retrieveByEntryId($this->entryId, array($questionType));
		$questNum = 0;
		foreach ($questions as $question)
		{
			$questNum +=1;
			$stylePrefix = $this->getStylePrefix($question->getName());
			$this->pdf->addList($questNum, $question->getName(), $this->styles[$stylePrefix.self::LIST_WITH_ADD_LINE_BEFORE_STYLE]);
			$this->pdf->addHeadline(6, "Optional Answers:", $this->styles[self::HEADING6_STYLE]);
			$ansNum = 0;
			foreach ($question->getOptionalAnswers() as $optionalAnswer)
			{
				$ansNum +=1;
				$text = $optionalAnswer->getText();
				$stylePrefix = $this->getStylePrefix($text);
				$this->pdf->addList($ansNum, $text, $this->styles[$stylePrefix.self::INDENT_LIST_STYLE]);
			}
		}
	}

	private function getStylePrefix($text)
	{
		$stylePrefix = self::NOTO_STYLE_PREFIX;
		if(preg_match("/\p{Han}+/u", $text)) //contain chinese/japanese letters
			$stylePrefix = self::ASIAN_STYLE_PREFIX;
		
		return $stylePrefix;
	}

	public function submitDocument()
	{
		return $this->pdf->Submit();
	}
}
