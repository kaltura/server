<?php

/**
 * Class Form_NewFlavorParam
 */
class Form_NewFlavorParam extends Infra_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
       // $this->setDecorators(array(
       ///     'FormElements',
       //     'Form',
       //     array('HtmlTag', array('tag' => 'fieldset'))
       // ));

        $this->setAttrib('class', 'inline-form');
        $this->addElement('text', 'name', array(
            'label' => 'Name:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(array('validator' => 'Regex',
                'options'=> array('pattern' => '/^[-()\/\_A-Za-z0-9, ]+$/')))
        ));

        $this->addElement('text', 'partner_id', array(
            'label' => 'Partner ID (Not Zero):',
            'required' => true,
            'filters' => array('Int'),
            'validators' => array('Int')
        ));

        $this->addElement('text', 'video_bitrate', array(
            'label' => 'Video bitrate:',
            'required' => true,
            'filters' => array('Int'),
            'validators' => array('Int')
        ));

        $this->addElement('text', 'audio_bitrate', array(
            'label' => 'Audio bitrate:',
            'required' => true,
            'filters' => array('Int'),
            'validators' => array('Int')
        ));

        $this->addElement('text', 'video_height', array(
            'label' => 'Video height:',
            'required' => true,
            'filters' => array('Int'),
            'validators' => array('Int')
        ));

        $this->addElement('text', 'video_width', array(
            'label' => 'Video width:',
            'required' => true,
            'filters' => array('Int'),
            'validators' => array('Int')
        ));

        $this->addElement('select', 'two_pass', array(
            'label' => 'Two pass encoding:',
            'required' => true,
            'multiOptions' => array('true' => 'true', 'false' => 'false')
        ));





        $this->addElement('text', 'description', array(
            'label' => 'Description:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(array('validator' => 'Regex',
                'options'=> array('pattern' => '/^[-+:_^\'.\/\|=()A-Za-z0-9, ]+$/')))
        ));

        $this->addElement('text', 'extra_params', array(
            'label' => 'Extra engine params (optional):',
            'filters' => array('StringTrim')
        ));

        $element_array = array(new Kaltura_Form_Element_EnumSelect('video_codec', array('enum' => 'Kaltura_Client_Enum_VideoCodec')),
            new Kaltura_Form_Element_EnumSelect('audio_codec', array('enum' => 'Kaltura_Client_Enum_AudioCodec')),
            new Kaltura_Form_Element_EnumSelect('container_format', array('enum' => 'Kaltura_Client_Enum_ContainerFormat')));

        $element_array[0]->setLabel('Video Codec:');

        $element_array[1]->setLabel('Audio Codec:');

        $element_array[2]->setLabel('Container Format:');

        $this->addElements($element_array);

        // Add the submit button
        $this->addElement('button', 'submit', array(
            'type' => 'submit',
            'ignore' => true,
            'label' => 'Add',
            'decorators' => array('ViewHelper')
        ));

        $this->addDisplayGroup(array('partner_id',
                'name',
                'description',
                'video_codec',
                'audio_codec',
                'container_format',
                'video_bitrate',
                'audio_bitrate',
                'video_height',
                'video_width',
                'two_pass',
                'extra_params',
                'submit'),
            'flavor_info', array(
                'decorators' => array(
#                   'Description',
                    'FormElements',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
                    array('Fieldset'),
                ),
                'legend' => 'Flavor properties',
            ));


    }
} 