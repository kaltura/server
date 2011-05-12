<?php 
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage admin
 */
class Form_YoutubeApiProfileConfiguration extends Form_ProviderProfileConfiguration
{
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('YouTube Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		// General
		$this->addElement('text', 'username', array(
			'label'			=> 'YouTube Account:',
			'filters'		=> array('StringTrim'),
		));

		// General
		$this->addElement('text', 'password', array(
			'label'			=> 'YouTube Password:',
			'filters'		=> array('StringTrim'),
		));
								
//		$this->addMetadataProfile();
		
		$this->addDisplayGroup(
			array('username', 'password'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
				
		$this->addMetadataProfile(true);
		
		// taken from http://gdata.youtube.com/schemas/2007/categories.cat
		$youTubeCategories = array(
			'Film' => 'Film & Animation',
			'Autos' => 'Autos & Vehicles',
			'Music' => 'Music' ,
			'Animals' => 'Pets & Animals',
			'Sports' => 'Sports' ,
			'Travel' => 'Travel & Events',
			'Shortmov' => 'Short Movies',
			'Videoblog' => 'Videoblogging',
			'Games' => 'Gaming' ,
			'Comedy' => 'Comedy' ,
			'People' => 'People & Blogs',
			'News' => 'News & Politics',
			'Entertainment' => 'Entertainment',
			'Education' => 'Education',
			'Howto' => 'Howto & Style',
			'Nonprofit' => 'Nonprofits & Activism',
			'Tech' => 'Science & Technology',
			'Movies_Anime_animation' => 'Movies - Anime/Animation' ,
			'Movies' => 'Movies' ,
			'Movies_Comedy' => 'Movies - Comedy',
			'Movies_Documentary' => 'Movies - Documentary' ,
			'Movies_Action_adventure' => 'Movies - Action/Adventure' ,
			'Movies_Classics' => 'Movies - Classics',
			'Movies_Foreign' => 'Movies - Foreign',
			'Movies_Horror' => 'Movies - Horror',
			'Movies_Drama' => 'Movies - Drama',
			'Movies_Family' => 'Movies - Family',
			'Movies_Shorts' => 'Movies - Shorts',
			'Shows' => 'Shows' ,
			'Movies_Sci_fi_fantasy' => 'Movies - Sci-Fi/Fantasy' ,
			'Movies_Thriller' => 'Movies - Thriller',
			'Trailers' => 'Trailers',			
		);
		
		//  Metadata
		$this->addElement('select', 'default_category', array(
			'label' => 'Default Category:',
			'multioptions' => $youTubeCategories,
		));
		
		$this->addDisplayGroup(
			array('default_category', 'metadata_profile_id'), 
			'metadata',
			array('legend' => 'Metadata', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		// Community
		$this->addElement('select', 'allow_comments', array(
			'label' => 'Allow Comments:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
				'moderated' => 'moderated',
			)
		));
		
		$this->addElement('select', 'allow_embedding', array(
			'label' => 'Allow Embedding:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
			)
		));
		
		$this->addElement('select', 'allow_ratings', array(
			'label' => 'Allow Ratings:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
			)
		));
		
		$this->addElement('select', 'allow_responses', array(
			'label' => 'Allow Responses:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
				'moderated' => 'moderated',
			)
		));
		
		$this->addDisplayGroup(
			array('allow_comments', 'allow_embedding', 'allow_ratings', 'allow_responses'), 
			'community', 
			array('legend' => 'Community', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
}