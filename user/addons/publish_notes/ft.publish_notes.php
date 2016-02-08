<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
    This file is part of Publish Notes add-on for ExpressionEngine.

    Publish Notes is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Publish Notes is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    Read the terms of the GNU General Public License
    at <http://www.gnu.org/licenses/>.

    Copyright 2011-2015 Derek Hogue
*/

include(PATH_THIRD.'/publish_notes/config.php');

class Publish_notes_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'Publish Notes',
		'version'	=> PUBLISH_NOTES_VERSION
	);
	
	var $display_styles;

	function __construct()
	{
		ee()->lang->loadfile('publish_notes');
		
		$this->display_styles = array(
			'warn' => lang('warning'),
			'issue' => lang('issue'),
			'success' => lang('success')
		);
	}


	function accepts_content_type($name)
	{
		return ($name == 'channel');
	}
	

	function display_settings($data)
	{	
		ee()->load->model('addons_model');
		$format_options = ee()->addons_model->get_plugin_formatting(TRUE);
				
		$settings = array(
			'publish_notes' => array(
				'label' => $this->info['name'],
				'group' => 'publish_notes',
				'settings' => array(
					array(
						'title' => 'display_style',
						'desc' => '',
						'fields' => array(
							'display_style' => array(
								'type' => 'select',
								'choices' => $this->display_styles,
								'value' => (isset($data['display_style'])) ? $data['display_style'] : 'warn'
							)
						)
					),
					array(
						'title' => 'formatting',
						'fields' => array(
							'formatting' => array(
								'type' => 'select',
								'choices' => $format_options,
								'value' => isset($data['formatting']) ? $data['formatting'] : 'xhtml',
							)
						)
					)
				)
			)
		);
		return $settings;
	}
	

	function save_settings($data)
	{
		return array(
			'display_style' => ee('Request')->post('display_style'),
			'formatting' => ee('Request')->post('formatting'),
			'field_fmt' => 'none',
			'field_show_fmt' => 'n',
		);
	}


	function save($data)
	{
		return null;
	}


	function display_field($data)
	{
		ee()->cp->load_package_css('publish');
		ee()->cp->load_package_js('publish');
		
		ee()->load->library('typography');
		ee()->typography->initialize();
		
		$notes = ee()->typography->parse_type(
			$this->settings['field_instructions'],
			array(
				'text_format' => $this->settings['formatting'],
				'html_format' => 'all',
				'auto_links' => 'n',
				'allow_img_url' => 'y'
			)
		);
		$r = '<div class="publish-notes-field alert inline '.$this->settings['display_style'].'" style="display:none;">'.$notes.'</div>';
		
		return $r;
	}


	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $data;
	}

	
	function update($current = '')
	{
		if($current == $this->info['version'])
		{
			return FALSE;
		}
		return TRUE;
	}	

}