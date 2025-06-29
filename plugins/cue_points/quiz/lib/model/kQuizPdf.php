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
	const DEJAVU_FONT = 'dejaVuSansFont';
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
	const NATIVE_LANGUAGE_SCRIPT_STYLE_PREFIX = 'nativeLanguageScript';
	const RIGHT_2_LEFT_STYLE_PREFIX = 'right2Left';

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

	protected $languagesWithNativeScripts = [

		'ber-ma' => '/[\x{2D30}-\x{2D7F}]+/u', // Berber (Morocco) - Tifinagh script
		'hy' => '/[\x{0531}-\x{0556}\x{0561}-\x{0587}]+/u', // Armenian / Azerbaijani (Iran) - Armenian script
		'iu' => '/[\x{1400}-\x{167F}]+/u', // Inuktitut - Unified Canadian Aboriginal Syllabic
		'ka' => '/[\x{10A0}-\x{10FF}]+/u', // Georgian
		'lo' => '/[\x{0E80}-\x{0EFF}]+/u', // Lao
	];

	protected $rightToLeftLanguages = [
		'he' => '/\p{Hebrew}+/u', // Hebrew
		'ar' => '/\p{Arabic}+/u', // Arabic
	];

	protected $asianLanguages = [
		'ja' => '/\p{Hiragana}|\p{Katakana}|\p{Han}+/u', // Japanese - Chinese
	];

	protected $margins = [
		'left' => 10,
		'top' => 15,
		'right' => 10
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
		$styles[self::NOTO_STYLE_PREFIX.self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('NotoListWithAddLineBefore', self::NOTO_SANS_FONT,
			12, 'I', true);

		$styles[self::TITLE_STYLE] = new PdfStyle('Title', 'Arial', 14, 'BU', true, false, 'C');
		$styles[self::NOTO_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('NotoTitle', self::NOTO_SANS_FONT, 14, 'BU', true,
			false, 'C');
		$styles[self::ASIAN_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('AsianTitle', 'Arial', 14, 'U', true, false, 'C');

		$styles[self::NATIVE_LANGUAGE_SCRIPT_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('NativeLanguageScriptTitle', self::DEJAVU_FONT, 14, 'BU', true, false, 'C');
		$styles[self::NATIVE_LANGUAGE_SCRIPT_STYLE_PREFIX.self::INDENT_LIST_STYLE] = new PdfStyle('NativeLanguageScriptIndentList', self::DEJAVU_FONT, 12, '', false, false, 'L', 5);
		$styles[self::NATIVE_LANGUAGE_SCRIPT_STYLE_PREFIX.self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('NativeLanguageScriptListWithAddLineBefore', self::DEJAVU_FONT, 12, '', true);

		$styles[self::RIGHT_2_LEFT_STYLE_PREFIX.self::TITLE_STYLE] = new PdfStyle('Right2LeftTitle', self::DEJAVU_FONT, 14, 'BU', true, false, 'R', 0, '', 5, true);
		$styles[self::RIGHT_2_LEFT_STYLE_PREFIX.self::INDENT_LIST_STYLE] = new PdfStyle('Right2LeftIndentList', self::DEJAVU_FONT, 12, '', false, false, 'R', 5, '', 5, true);
		$styles[self::RIGHT_2_LEFT_STYLE_PREFIX.self::LIST_WITH_ADD_LINE_BEFORE_STYLE] = new PdfStyle('Right2LeftListWithAddLineBefore', self::DEJAVU_FONT, 12, '', true, false, 'R', 0, '', 5, true);

		$this->styles = $styles;
	}

	private function initPDF()
	{
		$this->pdf = new PdfGenerator('Thank You', 'Questionnaire', '','Questionnaire','Questionnaire', '');
		$this->pdf->SetMargins($this->margins['left'],$this->margins['top'],$this->margins['right']);
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
			$this->addListText($questNum, $question->getName(), $this->styles[$stylePrefix.self::LIST_WITH_ADD_LINE_BEFORE_STYLE]);
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
						$this->addListText($alphabet[$ansIdx], $text, $this->styles[$stylePrefix . self::INDENT_LIST_STYLE]);
						$ansIdx += 1;
					}
				}
			}
		}
	}

	protected function addListText($sign, $text, $style)
	{
		$text = $this->handleR2LText($text, $style);
		$this->pdf->addList($sign, $text, $style);
	}

	protected function handleR2LText($text, PdfStyle $stylePrefix)
	{
		if ($stylePrefix->getR2L())
		{
			$wantedIndentation = !is_null($stylePrefix->getX()) ? $stylePrefix->getX() : 0;
			$this->pdf->SetMargins($this->margins['left'],$this->margins['top'],$this->margins['right'] + $wantedIndentation);
			return $this->reverseSentence($text);
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

		if($this->detectLanguage($text, $this->asianLanguages))
		{
			return self::ASIAN_STYLE_PREFIX;
		}

		if($this->detectLanguage($text, $this->rightToLeftLanguages))
		{
			return self::RIGHT_2_LEFT_STYLE_PREFIX;
		}

		if($this->detectLanguage($text, $this->languagesWithNativeScripts))
		{
			return self::NATIVE_LANGUAGE_SCRIPT_STYLE_PREFIX;
		}

		return $stylePrefix;
	}

	public function submitDocument()
	{
		return $this->pdf->Submit();
	}

	protected function detectLanguage($text, $languagePatterns)
	{
		// Check if the text matches any language pattern
		foreach ($languagePatterns as $language => $pattern)
		{
			if (preg_match($pattern, $text))
			{
				return true;
			}
		}
		return false;
	}

	protected function reverseSentence($input)
	{
		$sentence = explode(' ', $input);

		// Check if the first and last elements are numeric - if so, save them
		$firstNumeric = is_numeric($sentence[0]) ? $sentence[0] : null;
		$lastNumeric = is_numeric(end($sentence)) ? end($sentence) : null;

		// Determine the start and end indices for slicing
		$noneNumStart = $firstNumeric !== null ? 1 : 0;
		$noneNumEnd = $lastNumeric !== null ? count($sentence) - 1 : count($sentence);

		// Reverse each none numeric word in the array
		foreach ($sentence as &$word)
		{
			$word = is_numeric($word) ? $word : $this->reverseSingleWord($word);
		}

		// Obtain the sub array that contains the middle section of the words (excluding first and last numeric words)
		$noneNumericMiddleSection = array_slice($sentence, $noneNumStart, $noneNumEnd - $noneNumStart);

		// Reverse the order of the array
		$reversedOrder = $this->prepareFinalReversedArray($firstNumeric, $lastNumeric, $noneNumericMiddleSection);

		// Implode the array into a new string
		return implode(' ', $reversedOrder);
	}

	protected function prepareFinalReversedArray($firstNumeric, $lastNumeric, $noneNumericMiddleSection)
	{
		$reversedOrder = array_reverse($noneNumericMiddleSection);

		// Concatenate the array while preserving numeric first and/or last cells
		$result = [];
		if ($firstNumeric !== null)
		{
			$result[] = $firstNumeric;
		}
		$result = array_merge($result, $reversedOrder);
		if ($lastNumeric !== null)
		{
			$result[] = $lastNumeric;
		}

		return $result;
	}

	protected function reverseSingleWord($input)
	{
		$length = mb_strlen($input, 'UTF-8');
		$reversed = '';
		for ($i = $length - 1; $i >= 0; $i--)
		{
			$reversed .= mb_substr($input, $i, 1, 'UTF-8');
		}
		return $reversed;
	}
}
