<?php
/**
 * This class wraps some of Symfony's FormHelper (and ValidationHelper) functionality together with
 * with our object (and propel's) manipulations and naming conventions.
 *
 * TODO - remove the global version of the funcitons.
 * Leave only the static ones / object methods/
 *
 */
include_once( "baseObjectUtils.class.php");
include_once( "myBaseObject.class.php");
?>


<?php
// better version of the functions - the object holds state and make the calls much easier on the caller's side

class myFormHelper
{
	const OBJECT_TYPE_MY_BASE_OBJECT = 0;
	const OBJECT_TYPE_ORM_BASE_OBJECT = 1;

	const PREFIX_NAME_SEPARATOR = ".";

	private $m_current_object = NULL; // $kshow
	private $m_current_prefix = ""; // $field_prefix

	private $m_object_type;
	private $m_create_tr = true;
	private $m_should_echo = true;
	
	public function myFormHelper ( $obj  ,$prefix , $create_tr = true , $should_echo = true)
	{
		$this->m_current_object = $obj;
		$this->m_current_prefix = $prefix;
		$this->m_create_tr = $create_tr;
		$this->m_should_echo = $should_echo;
		
		if ( $obj instanceof myBaseObject )
		{
			$this->m_object_type = self::OBJECT_TYPE_MY_BASE_OBJECT;
		}
		else if ( $obj instanceof BaseObject )
		{
			$this->m_object_type = self::OBJECT_TYPE_ORM_BASE_OBJECT;
		}
	}

	public function setCurrentObject ( $obj )
	{
		$this->m_current_object = $obj;
	}

	public function setCurrentPrefix ( $prefix )
	{
		$this->m_current_prefix = $prefix;
	}


	public function printParam ( $mandatory , $lable , $param_name ,
	$input_type , $original_extra_params = NULL , $extra_params_for_select = NULL  , $should_echo = NULL )
	{
		if  ($should_echo == NULL )
		{
			$should_echo = $this->m_should_echo;
		}
		
		$res = "";
		if ( $this->m_object_type === self::OBJECT_TYPE_MY_BASE_OBJECT )
		{
			$p = $this->m_current_object->getParamFromObject( $param_name );
		}
		else if ( $this->m_object_type === self::OBJECT_TYPE_ORM_BASE_OBJECT )
		{
			$p = baseObjectUtils::getParamFromObject ( $this->m_current_object,  $param_name );
		}


		// use this so the style=font-size:8pt will always be appended to the original_extra_params
		$extra_params = array();//["style"] = "font-size:8pt";
		kArray::associativePush( $extra_params , $original_extra_params );

		if ( kString::isEmpty( $this->m_current_prefix ) )
		{
			$field_name = $param_name;
		}
		else
		{
			$field_name = $this->m_current_prefix . self::PREFIX_NAME_SEPARATOR . $param_name;
		}

		$error = form_error($param_name);

		if ( $this->m_create_tr )
		{
			if ( $error )
			{
				$res .=  "<tr><td colspan=2>" .$error . "</td></tr>";
			}
			if ( $input_type == "hidden" )
			{
				$res .=   "<tr><td></td>";
			}
			else
			{
				$res .=   " <tr><td valign=top><label for=\"". $field_name . "\">". ( $mandatory ? "*" : "" ) .$lable.":</label></td>";
			}
			$res .=   "<td sytle='font-size:8pt'>" ;
		}
		else
		{
			// do nothing - the format will be done externally 
		}
		
		if ( $input_type == "text" )
		{
			$res .=   input_tag ( $field_name, $p , $extra_params );
		}
		elseif ( $input_type == "hidden" )
		{
			$res .=   input_hidden_tag ( $field_name, $p , $extra_params );
		}
		elseif ( $input_type == "textarea" )
		{
			$res .=   textarea_tag( $field_name , $p , $extra_params );
		}
		elseif ( $input_type == "upload")
		{
			$res .= self::createUploadStructure ( $field_name , $p , NULL ,$extra_params , NULL , $should_echo );
			/*
			 echo tag('input', array_merge(array('name' => $field_name, 'value' => $p ,
				'id' => $field_name, 'READONLY' => '', 'oncomplete' => 'onComplete_'.str_replace('.', '_', $field_name).'()'), $extra_params));

				echo tag('input', array_merge(array('type' => 'button', 'value' => 'MyBrowse...',
				'onclick' => 'uploadBrowse(this)', 'uploadElement' => "$field_name"), $extra_params));

				echo tag('input', array_merge(array('type' => 'button', 'value' => 'Clear',
				'onclick' => 'javascript:{$(\''.$field_name.'\').value = \'\';}'), $extra_params));

				if ( ! kString::isEmpty ( $p ) )
				{
				echo "<img src='/images/file_exists.png' width='16' height='16'>";
				}
				*/
		}
		elseif ( $input_type == "file" )
		{
			$res .=   input_file_tag ( $field_name , $p , $extra_params );
		}
		elseif ( $input_type == "date" )
		{
			$extra_params["rich"] = "true";
			 $extra_params["style"] = "width:80";
			kArray::associativePush( $extra_params , $original_extra_params );
			
			// TODO - see how to format the date there and back from the DB format "dd/mm/yyyy" to "
			$converted_date = dateUtils::convertFromPhpDate( $p );
			// always append the default "rich=true" to the extra_params
			//$extra_params[] = 'rich=true' ;
			$res .=   input_date_tag ( $field_name ,$converted_date  , $extra_params );
		}
		elseif ( $input_type == "select" )
		{
			$more_extra_params = array ( "style" => "font-size:8pt");
			kArray::associativePush( $more_extra_params , $extra_params_for_select );
			$res .=   select_tag ( $field_name, options_for_select ( $original_extra_params , $p ) ,
			$more_extra_params ) ;
		}
		elseif ( $input_type == "radio" )
		{
			// assume the $lable holda the value of the radio 
				$res .=   radiobutton_tag( $field_name , $lable ,  $p == $lable );
		}
		elseif ( $input_type == "radiogroup" )
		{
			// assume the $extra_params hold a lable-value array
			foreach ( $extra_params as $lable => $name )
			{
				$value =  $extra_params[$lable];
				$res .=   $lable . " " . radiobutton_tag( $field_name , $value ,  $p == $value );
			}
		}
		elseif ( $input_type == "checkbox" )
		{
			$res .=    checkbox_tag( $field_name, "true" , $p == "true" , $extra_params );
		}
		elseif ( $input_type == "color" )
		{
			die ( "color is no longer supported !" );
//			$res .=    myColorPicker::getPickerHtml( $p , $field_name ); // TODO - pass on $extra_params ?
		}
		elseif ( $input_type == "country" )
		{
			$more_extra_params = array ( "style" => "font-size:8pt");
			kArray::associativePush( $more_extra_params , $extra_params_for_select );
			$res .=   select_country_tag( $field_name, $p , $more_extra_params ) ;
		}
		elseif ( $input_type == "language" )
		{
			$more_extra_params = array ( "style" => "font-size:8pt");
			kArray::associativePush( $more_extra_params , $extra_params_for_select );
			$res .=   select_language_tag( $field_name, $p , $more_extra_params ) ;
		}
		else
		{
			throw new Exception ( "Unknown input_type [" . $input_type . "]" );
		}

		if ( $this->m_create_tr )
		{
			$res .=   "</td></tr>\n";
		}

		if ( $should_echo )		echo $res;
		else return $res;
		
	}

	static public function createUploadStructure ( $field_name , $value , $url , $extra_params , 
		$oncomplete_method_name = NULL , $should_echo = true , $should_add_browse_button = true )
	{
		$res = "";
		if ( $oncomplete_method_name == NULL )
		{
			$oncomplete_method_name = 'onComplete_'.str_replace('.', '_', $field_name).'()';
		}
		//echo "<input value='$oncomplete_method_name'>";

		$extra_params["style"] = "font-size:8pt";
		$input_params =  array_merge( array('name' => $field_name, 'value' => $value , 'type' => 'hidden' ,
		'id' => $field_name, 'READONLY' => '', 'oncomplete' => $oncomplete_method_name ) , $extra_params);
		if ( $url != NULL )
		{
			$input_params ['url'] = $url ;
		}
			
		$status_element_id = $field_name . "__status";
		
		$res .=    tag('input', $input_params );
		
		if ( $should_add_browse_button )
		{
			$res .=    tag('input', array_merge(array('type' => 'button', 'value' => 'Browse...',
				'onclick' => 'requestUpload(this)', 'uploadElement' => "$field_name" ,'statusElement' => $status_element_id ), $extra_params));
		}	
		$res .= "&nbsp;&nbsp;";
		
		$res .= "<span style='font-size:8pt' id='$status_element_id' ></span>";
			
/*
		$res .=    tag('input', array_merge(array('type' => 'button', 'value' => 'Clear',
			'onclick' => 'javascript:{$(\''.$field_name.'\').value = \'\';}'), $extra_params));
*/
		// the status element will be used to display the status during upload.

		//		echo tag('input', array_merge(array( 'type' => 'text' , 'name' => $field_name . "_url", 'value' => "TO-BE-FILLED" ,
		//			'id' => $field_name . "_url" ), $extra_params));
/*
		if ( ! kString::isEmpty ( $value ) )
		{
			$res .=    "<img src='/images/file_exists.png' width='16' height='16'>";
		}
	*/	
		if ( $should_echo )	echo $res;
		else return $res;
		 
	}
}
?>
