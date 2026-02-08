<?php

class mdAttachmentContentManager extends kAttachmentContentManager
{
	const PAGE_BREAK_REGEX = '/\s*<!--\s*PAGE_BREAK\s*-->\s*/i';
	const CODE_BLOCKS_REGEX = '/```[a-zA-Z]*/';
	const INLINE_CODE_REGEX = '/`([^`]+)`/';
	const HEADERS_REGEX = '/^#+\s*(.*)$/m';
	const BOLD_ASTERISKS_REGEX = '/\*\*([^*]+)\*\*/';
	const ITALIC_ASTERISKS_REGEX = '/\*([^*]+)\*/';
	const BOLD_UNDERSCORES_REGEX = '/__([^_]+)__/';
	const ITALIC_UNDERSCORES_REGEX = '/\b_([^_]+)_\b/';
	const STRIKETHROUGH_REGEX = '/~~([^~]+)~~/';
	const LINKS_REGEX = '/\[([^\]]+)\]\(([^)]+)\)/';
	const REFERENCE_LINKS_REGEX = '/\[([^\]]+)\]\[[^\]]*\]/';
	const IMAGES_REGEX = '/!\[([^\]]*)\]\([^)]+\)/';
	const HORIZONTAL_RULES_REGEX = '/^[-*]{3,}$/m';
	const BLOCKQUOTES_REGEX = '/^>\s*(.*)$/m';
	const UNORDERED_LISTS_REGEX = '/^\s*[-*+]\s+(.*)$/m';
	const ORDERED_LISTS_REGEX = '/^\s*\d+\.\s+(.*)$/m';
	const TABLE_PIPES_REGEX = '/\|/';
	const TABLE_SEPARATORS_REGEX = '/^\s*[-:\s]+\s*$/m';
	const REFERENCE_DEFINITIONS_REGEX = '/^\[[^\]]+\]:\s*.*$/m';
	const HTML_ELEMENTS_REGEX = '/<!--.*?-->|<(?:html|head|body|div|span|p|br|hr|h[1-6]|ul|ol|li|dl|dt|dd|table|thead|tbody|tr|td|th|form|input|button|select|option|img|a|strong|em|b|i|u)(?:\s[^>]*)?>|<\/(?:html|head|body|div|span|p|br|hr|h[1-6]|ul|ol|li|dl|dt|dd|table|thead|tbody|tr|td|th|form|input|button|select|option|img|a|strong|em|b|i|u)>/is'; // Remove HTML comments and tags
	const XML_TAGS_REGEX = '/&lt;(\/?[A-Za-z][A-Za-z0-9_]*?)&gt;/';



	/**
	 * @return mdAttachmentContentManager
	 */
	public static function get()
	{
		return new mdAttachmentContentManager();
	}

	public function parse($content)
	{
		$itemsData = $this->parseMarkdown($content);
		return $itemsData;
	}

	public function parseMarkdown($content)
	{
		if (empty($content))
		{
			return array();
		}

		$pages = preg_split(self::PAGE_BREAK_REGEX, $content);
		$itemsData = array();
		foreach ($pages as $page)
		{
			$strippedPage = $this->stripMarkdown($page);

			if (!empty($strippedPage))
			{
				$itemsData[] = array(
					'content' => array(array('text' => $strippedPage)),
				);
			}
		}
		return $itemsData;
	}

	public function stripMarkdown($text)
	{
		$patternReplacements = [
			self::CODE_BLOCKS_REGEX          => '',      // Remove code block markers
			self::INLINE_CODE_REGEX          => '$1',    // Inline code - keep content
			self::HEADERS_REGEX              => '$1',    // Headers - keep content
			self::BOLD_ASTERISKS_REGEX       => '$1',    // Bold asterisks - keep content
			self::ITALIC_ASTERISKS_REGEX     => '$1',    // Italic asterisks - keep content
			self::BOLD_UNDERSCORES_REGEX     => '$1',    // Bold underscores - keep content
			self::ITALIC_UNDERSCORES_REGEX   => '$1',    // Italic underscores - keep content
			self::STRIKETHROUGH_REGEX        => '$1',    // Strikethrough - keep content
			self::LINKS_REGEX                => '$1 $2', // Links - keep text and URL
			self::REFERENCE_LINKS_REGEX      => '$1',    // Reference links - keep text
			self::IMAGES_REGEX               => '$1',    // Images - keep alt text
			self::HORIZONTAL_RULES_REGEX     => '',      // Horizontal rules - remove
			self::BLOCKQUOTES_REGEX          => '$1',    // Blockquotes - keep content
			self::UNORDERED_LISTS_REGEX      => '$1',    // Unordered lists - keep content
			self::ORDERED_LISTS_REGEX        => '$1',    // Ordered lists - keep content
			self::TABLE_PIPES_REGEX          => ' ',     // Table pipes - replace with space
			self::TABLE_SEPARATORS_REGEX     => '',      // Table separators - remove
			self::REFERENCE_DEFINITIONS_REGEX => '',     // Reference definitions - remove
			self::HTML_ELEMENTS_REGEX        => '',      // Remove HTML comments and tags
			self::XML_TAGS_REGEX             => '$1',    // Keep XML tag names without angle brackets
		];

		$text = preg_replace(array_keys($patternReplacements), array_values($patternReplacements), $text);
		return strip_tags($text);
	}
}
