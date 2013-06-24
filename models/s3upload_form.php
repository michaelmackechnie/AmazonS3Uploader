<?php

/*
 * This file is part of AmazonS3Uploader.
 * 
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * AmazonS3Uploader is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with AmazonS3Uploader.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of S3upload_form
 * 
 * @package		AmazonS3Uploader
 * @author		Peter Fox
 * @copyright	Copyright (c) 2013 Peter Fox.
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @link		https://github.com/peterfox/AmazonS3Uploader
 * @version 	Version 0.1
 * @filesource
 */
class S3upload_form extends CI_Model
{
				private static $TEXT_AREA = 'text_area';
				private static $FILE = 'file';
				
				private $form_extras;
				private $fields;
				private $fields_type;
				private $file_field;
				private $file_field_extras;
				private $submit_button;
				private $submit_value;
				private $submit_extras;
				private $bucket;
				
				public function __construct()
				{
								parent::__construct();
								
								$this->new_form();
				}
				
				private function new_form()
				{
								$this->form_extras = '';
								$this->fields = array();
								$this->file_field = self::$FILE;
								$this->file_field_extras = '';
								$this->submit_button = true;
								$this->submit_value = '';
								$this->submit_extras = '';
								$this->fields_type = array();
								$this->bucket = '';
				}
				
				public function set_bucket($bucket)
				{
								$this->bucket = $bucket;
				}
				
				public function set_form_extras($form_extras)
				{
								$form_extras = strlen($form_extras)>0 && substr($form_extras, 0, 1) !== ' ' ? ' '.$form_extras : $form_extras;
								$this->form_extras = $form_extras;
				}
				
				public function add_field($name, $value, $hidden = true)
				{
								$this->fields[$name] = $value;
								if(!$hidden)
								{
												$this->fields_type[$name] = 'input';
								} else {
												$this->fields_type[$name] = 'hidden';
								}
				}
				
				public function add_textarea($extra_attributes = '')
				{
								$this->file_field = self::$TEXT_AREA;
								$extra_attributes = strlen($extra_attributes)>0 && substr($extra_attributes, 0, 1) !== ' ' ? ' '.$extra_attributes : $extra_attributes;
								$this->file_field_extras = $extra_attributes;
								
				}
				
				public function add_file($extra_attributes = '')
				{
								$this->file_field = self::$FILE;
								$extra_attributes = strlen($extra_attributes)>0 && substr($extra_attributes, 0, 1) !== ' ' ? ' '.$extra_attributes : $extra_attributes;
								$this->file_field_extras = $extra_attributes;
				}
				
				public function add_submit_button($add_button = true, $name = 'submit', $extra_attributes = '')
				{
								$this->submit_button = $add_button;
								$this->submit_value = $name;
								$extra_attributes = strlen($extra_attributes)>0 && substr($extra_attributes, 0, 1) !== ' ' ? ' '.$extra_attributes : $extra_attributes;
								$this->submit_extras = $extra_attributes;
				}
				
				public function generate_form()
				{
								$output = '';
								
								$output .= "<form action=\"https://$this->bucket.s3.amazonaws.com/\" method=\"post\" enctype=\"multipart/form-data\"$this->form_extras >\n";
								
								foreach($this->fields as $key => $field)
								{
												$output .= "<input type=\"{$this->fields_type[$key]}\" name=\"$key\" value=\"$field\" />\n";
								}
								
								if($this->file_field === self::$FILE)
								{
												$output .= "<input type=\"file\" name=\"file\" value=\"\"$this->file_field_extras />\n";
								}
								else if ($this->file_field === self::$TEXT_AREA)
								{
												$output .= "<textarea name=\"file\"$this->file_field_extras >\n>";
												$output .= "</textarea>\n";
								}
								
								if($this->submit_button)
								{
												$output .= "<input type=\"submit\" value\"$this->submit_value\"$this->submit_extras />\n";
								}
								
								$output .= "</form>\n";
								
								$this->new_form();
								return $output;
				}
}