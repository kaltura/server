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
		$patterns = [
			self::CODE_BLOCKS_REGEX,          // Remove code block markers
			self::INLINE_CODE_REGEX,          // Inline code - keep content
			self::HEADERS_REGEX,              // Headers - keep content
			self::BOLD_ASTERISKS_REGEX,       // Bold asterisks - keep content
			self::ITALIC_ASTERISKS_REGEX,     // Italic asterisks - keep content
			self::BOLD_UNDERSCORES_REGEX,     // Bold underscores - keep content
			self::ITALIC_UNDERSCORES_REGEX,   // Italic underscores - keep content
			self::STRIKETHROUGH_REGEX,        // Strikethrough - keep content
			self::LINKS_REGEX,                // Links - keep text and URL
			self::REFERENCE_LINKS_REGEX,      // Reference links - keep text
			self::IMAGES_REGEX,               // Images - keep alt text
			self::HORIZONTAL_RULES_REGEX,     // Horizontal rules - remove
			self::BLOCKQUOTES_REGEX,          // Blockquotes - keep content
			self::UNORDERED_LISTS_REGEX,      // Unordered lists - keep content
			self::ORDERED_LISTS_REGEX,        // Ordered lists - keep content
			self::TABLE_PIPES_REGEX,          // Table pipes - replace with space
			self::TABLE_SEPARATORS_REGEX,     // Table separators - remove
			self::REFERENCE_DEFINITIONS_REGEX, // Reference definitions - remove
			self::HTML_ELEMENTS_REGEX,
			self::XML_TAGS_REGEX
		];

		$replacements = [
			'',                    // Remove code block markers
			'$1',                  // Keep inline code content
			'$1',                  // Keep header content
			'$1',                  // Keep bold content
			'$1',                  // Keep italic content
			'$1',                  // Keep strikethrough content
			'$1 $2',              // Keep link text and URL
			'$1',                  // Keep reference link text
			'$1',                  // Keep image alt text
			'',                    // Remove horizontal rules
			'$1',                  // Keep blockquote content
			'$1',                  // Keep list content
			'$1',                  // Keep list content
			' ',                   // Replace pipes with spaces
			'',                    // Remove table separators
			'',                    // Remove reference definitions
			'',                    // Remove HTML comments and tags
			'$1',                  // Keep XML tag names without angle brackets
		];
		$text = preg_replace($patterns, $replacements, $text);
		return strip_tags($text);
	}
}
