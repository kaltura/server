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
	const DEJAVU_FONT = 'DejaVuSansFont';
	const NOTO_SANS_FONT = 'NotoSansFont';
	const TIMES_FONT = 'Times';
	const NORMAL_STYLE = 'NormalStyle';
	const INDENT_LIST_STYLE = 'IndentListStyle';
	const LIST_WITH_ADD_LINE_BEFORE_STYLE = 'ListWithAddLineBeforeStyle';
	const INDENTED_LIST_WITH_ADD_LINE_BEFORE = 'IndentedListWithAddLineBefore';
	const TITLE_STYLE = 'TitleStyle';
	const HEADING6_STYLE = 'Heading6Style';
	const ASIAN_STYLE_PREFIX = 'Asian';
	const NOTO_STYLE_PREFIX = 'Noto';
	const NON_LATIN_STYLE_PREFIX = 'NonLatin';
	const RIGHT_2_LEFT_STYLE_PREFIX = 'Right2Left';

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

	protected $noneLatineLanguagePatterns = [

		'ber-ma' => '/[\x{2D30}-\x{2D7F}]+/u', // Berber (Morocco) - Tifinagh script
		'hy' => '/[\x{0531}-\x{0556}\x{0561}-\x{0587}]+/u', // Armenian
		'iu' => '/[\x{1400}-\x{167F}]+/u', // Inuktitut - Unified Canadian Aboriginal Syllabics
		'ka' => '/[\x{10A0}-\x{10FF}]+/u', // Georgian
		'lo' => '/[\x{0E80}-\x{0EFF}]+/u', // Lao
	];

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
		$styles[self::NOTO_STYLE_PREFIX.self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('NotoListWithAddLineBefore', self::NOTO_SANS_FONT, 12, 'I', true);

		$styles[self::TITLE_STYLE] = new PdfStyle('Title', 'Arial', 14, 'BU', true, false, 'C');
		$styles[self::NOTO_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('NotoTitle', self::NOTO_SANS_FONT, 14, 'BU', true, false, 'C');
		$styles[self::ASIAN_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('AsianTitle', 'Arial', 14, 'U', true, false, 'C');

		$styles[self::NON_LATIN_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('NonLatinTitle', self::DEJAVU_FONT, 14, 'BU', true, false, 'C');
		$styles[self::NON_LATIN_STYLE_PREFIX.self::INDENT_LIST_STYLE] = new PdfStyle('NonLatinIndentList', self::DEJAVU_FONT, 12, '', false, false, 'L', 5);
		$styles[self::NON_LATIN_STYLE_PREFIX.self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('NonLatinListWithAddLineBefore', self::DEJAVU_FONT, 12, '', true);

		$styles[self::RIGHT_2_LEFT_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('Right2LeftTitle', self::DEJAVU_FONT, 14, 'BU', true, false, 'R');
		$styles[self::RIGHT_2_LEFT_STYLE_PREFIX.self::INDENT_LIST_STYLE] = new PdfStyle('Right2LeftIndentList', self::DEJAVU_FONT, 12, '', false, false, 'R', 5);
		$styles[self::RIGHT_2_LEFT_STYLE_PREFIX.self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('Right2LeftListWithAddLineBefore', self::DEJAVU_FONT, 12, '', true, false, 'R');

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
		$this->pdf->AddFont(self::DEJAVU_FONT,'','DejaVuSans.ttf',true);
	}

	protected function reverseMultibyteString($input)
	{
		$length = mb_strlen($input, 'UTF-8');
		$reversed = '';
		for ($i = $length - 1; $i >= 0; $i--)
		{
			$reversed .= mb_substr($input, $i, 1, 'UTF-8');
		}
		return $reversed;
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
		$cuePointType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION);
		$questions = CuePointPeer::retrieveByEntryId($this->entryId, array($cuePointType));
		$questNum = 0;
		foreach ($questions as $question)
		{
			$questNum +=1;
			$stylePrefix = $this->getStylePrefix($question->getName());
			$questionName = $this->handleR2LText($question->getName(), $this->styles[$stylePrefix.self::LIST_WITH_ADD_LINE_BEFORE_STYLE]);
			$this->pdf->addList($questNum, $questionName, $this->styles[$stylePrefix.self::LIST_WITH_ADD_LINE_BEFORE_STYLE], $stylePrefix.self::LIST_WITH_ADD_LINE_BEFORE_STYLE);
			$alphabet = range('A', 'Z');
			$ansIdx = 0;
			if($question->getQuestionType() !== QuestionType::OPEN_QUESTION)
			{
				foreach ($question->getOptionalAnswers() as $optionalAnswer)
				{
					if ($ansIdx < sizeof($alphabet))
					{
						$text = $optionalAnswer->getText();
						$stylePrefix = $this->getStylePrefix($text);
						$text = $this->handleR2LText($text, $this->styles[$stylePrefix.self::INDENT_LIST_STYLE]);
						$this->pdf->addList($alphabet[$ansIdx], $text, $this->styles[$stylePrefix . self::INDENT_LIST_STYLE], $stylePrefix . self::INDENT_LIST_STYLE);
						$ansIdx += 1;
					}
				}
			}
		}
	}

	protected function handleR2LText($text, PdfStyle &$stylePrefix)
	{
		$styleName = $stylePrefix->getStyleName();
		$r2lStylePrefix = self::RIGHT_2_LEFT_STYLE_PREFIX;
		$wantedIndentation = 0;
		if (strpos($styleName, $r2lStylePrefix) !== false && $stylePrefix->getRowIndent())
		{
			$wantedIndentation = !is_null($stylePrefix->getX()) ? $stylePrefix->getX() : 0;
			$this->pdf->SetMargins(10,15,10 + $wantedIndentation);
			return $this->reverseMultibyteString($text);
		}
		return $text;
	}

	private function getStylePrefix($text)
	{
		$stylePrefix = self::NOTO_STYLE_PREFIX;
		if(is_null($text))
		{
			return $stylePrefix;
		}

		if(preg_match("/\p{Han}+/u", $text)) //contain chinese/japanese letters
		{
			$stylePrefix = self::ASIAN_STYLE_PREFIX;
		}

		if(preg_match("/\p{Hebrew}+/u", $text) ||
			preg_match("/\p{Arabic}+/u", $text) ||
			preg_match("/[\x{0531}-\x{0556}\x{0561}-\x{0587}]+/u", $text)) //contain chinese/japanese letters
		{
			$stylePrefix = self::RIGHT_2_LEFT_STYLE_PREFIX;
		}

		if($this->detectNoneLatinLanguage($text))
		{
			$stylePrefix = self::NON_LATIN_STYLE_PREFIX;
		}

		return $stylePrefix;
	}

	public function submitDocument()
	{
		return $this->pdf->Submit();
	}

	function detectNoneLatinLanguage($text) {
		// Check if the text matches any language pattern
		foreach ($this->noneLatineLanguagePatterns as $language => $pattern) {
			if (preg_match($pattern, $text)) {
				return true; // Return the language code if matched
			}
		}

		return false; // Return null if no match found
	}
}
