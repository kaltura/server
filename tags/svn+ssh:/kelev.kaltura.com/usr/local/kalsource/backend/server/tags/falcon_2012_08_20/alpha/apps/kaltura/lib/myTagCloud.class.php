<?php

require_once("model/ktagword.class.php");

	/**
	 * Tag Cloud
	 *
	 * This class creates a tag cloud out of an array.
	 * Developed for www.15tags.com
	 * Free for personal and commercial use!
	 *
	 * @author	anty	mail@anty.at	www.anty.at
	 * @version	v1.0
	 */
	class myTagCloud {
		// tag-styles: from very tiny to very big
		var $a_tag_styles = array('tagCloud1', 'tagCloud2', 'tagCloud3', 'tagCloud4', 'tagCloud5', 'tagCloud6', 'tagCloud7');
		// how many tags do we want to display?
		var $max_shown_tags;
		// the tags
		var $a_tc_data;
		
		/**
		 * Construct
		 */
		function myTagCloud($max_shown_tags = 50) {
			$this->max_shown_tags = $max_shown_tags;
		}
		
		/**
		 * Saves the date for the tagcloud
		 *
		 * @param	array	$a_tc_data	An array with data. The keys are the actual tags, the values are how often they occure.
		 *								eg.: array('tag1' => 3, 'tag2' => 1, 'tag3' => 7);
		 * @return	bool				Always returns true
		 */
		function set_tagcloud_data($a_tc_data) {
			$this->a_tc_data = $a_tc_data;
			arsort($this->a_tc_data);
			
			// since we only want a specified number of tags, we strip all the tags, that do not often occure.
			$a_tags = array();
			reset($this->a_tc_data);
			$tag_count = count($this->a_tc_data);
			$i = 1;
			while ($i <= $tag_count && $i <= $this->max_shown_tags) {
				$a_tags[key($this->a_tc_data)] = current($this->a_tc_data);
				next($this->a_tc_data);
				$i++;
			}
			$this->a_tc_data = $a_tags;
			
			return true;
		}
		
		/**
		 * Create the Tagcloud
		 *
		 * @return	string				Returns the HTML code for the tagcloud
		 */
		function get_tagcloud() {
			if (count($this->a_tc_data) <= 0) {
				// no tags
				return '';
			}
			
			// calculate the range that lies between the the different tag sizes
			reset($this->a_tc_data);
			$count_high = current($this->a_tc_data);
			$count_low = end($this->a_tc_data);
			$range = ($count_high - $count_low) / (count($this->a_tag_styles) - 1); // ASSUME - can never be 0 !!
			
			// sort the tags alphabetically
			ksort($this->a_tc_data);
			
			
			$searchUrl = "/index.php/search?keywords=";
			
			// generate the html for the cloud
			if ($range > 0) {
				$b_first = true;
				$prev_search = '';

				foreach ($this->a_tc_data as $tag => $tagcount) {
					$s = '<li><a href="' . $searchUrl . $tag.'" class="' . $this->a_tag_styles[round(($tagcount - $count_low) / $range, 0)] . '">' . $tag . "</a></li>\n";
					if ($b_first) {
						$html_cloud = $s;
						$b_first = false;
					} else {
						$html_cloud .= $s;
					}
				}
			} else {
				// all tags are the same size
				$b_first = true;
				foreach ($this->a_tc_data as $tag => $tagcount) {
					$s = '<li><a href="' . $searchUrl . $tag.'" class="' . $this->a_tag_styles[round((count($this->a_tag_styles)-1) / 2, 0)] . '">' . $tag . "</a></li>\n";
					if ($b_first) {
						$html_cloud = $s;
						$b_first = false;
					} else {
						$html_cloud .= $s;
					}
				}
			}
			
			return $html_cloud;
		}

		/**
		 * renders a cloud from some text
		 */
		static public function render($tc_content)
		{
			// count all words and put them into an array
			$tc_a_tags = array();
			
			preg_match_all('/\\b(\\w+?)\\b/i', $tc_content, $regs, PREG_PATTERN_ORDER);
			foreach ($regs[0] as $tc_word) {
				$tc_word = strtolower(trim($tc_word));
				$tc_a_tags[$tc_word] = ktagword::getWeight($tc_word);
			}


			return self::renderImpl ( $tc_a_tags );
			/*
			 // now create the tagcloud:
			 // initialize the class, we want to see 20 different tags (if available)
			 $tc_tch = new myTagCloud(20);
			 // hand over the tags to the class
			 $tc_tch->set_tagcloud_data($tc_a_tags);
			 // request the tagcloud
			 $tc_tagcloud = $tc_tch->get_tagcloud();

			 // print the tagcloud on the page
			 return ($tc_tagcloud);
			 */
		}

		/**
		 * willl return a tagcloud from the top tags (ordered by count) 
		 */
		static public function renderTopTags ()
		{
			$tagword_count_list = ktagword::getTopTags();
			
			$tag_list = array ();
			foreach ( $tagword_count_list as $tagword_count  )
			{
				 $tag_list[] = $tagword_count->getTag();
			}
			
			return self::renderFromArray ( $tag_list );
				
		}
		
		/**
		 * renders a cloud from a string list 
		 */
		static private function renderFromArray($tc_array)
		{
			// count all words and put them into an array
			$tc_a_tags = array();
				
			foreach ($tc_array as $tc_word) {
				$tc_word = strtolower(trim($tc_word));
				$tc_a_tags[$tc_word] = ktagword::getWeight($tc_word);
			}


			return self::renderImpl ( $tc_a_tags );
		}
		
		static private function renderImpl ( $tc_a_tags )
		{
			// now create the tagcloud:
			// initialize the class, we want to see 20 different tags (if available)
			$tc_tch = new myTagCloud(20);
			// hand over the tags to the class
			$tc_tch->set_tagcloud_data($tc_a_tags);
			// request the tagcloud
			$tc_tagcloud = $tc_tch->get_tagcloud();
	
			// print the tagcloud on the page
			return ($tc_tagcloud);			
		}
		
		
		public static function getTagList ( $tag_str , $max_number_of_tags , $separator_char = ",")
		{
			$res = "";
			if (  empty ( $tag_str ) ) return $res;
			
			$tag_list = ktagword::getTagsArray ( $tag_str );
			$total_tag_count = min ( $max_number_of_tags , count ( $tag_list) );
			$displayed_tag_count = 0;
			foreach ( $tag_list as $tag ) 
			{
				if ( $displayed_tag_count >= $max_number_of_tags ) break;				
				if ( trim ( $tag) == "" ) continue; // don't print empty tags
				$displayed_tag_count++;
				$res .= "<li><a href='/index.php/search?keywords=".$tag."''>" . $tag . "</a>" . ( $total_tag_count > $displayed_tag_count ? "," : "" ) ."</li>\n";
			} 
			
			return $res;
		}
	}
	
	
