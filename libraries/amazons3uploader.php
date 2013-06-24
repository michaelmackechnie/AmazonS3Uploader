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
 * Description of AmazonS3Uploader
 *
 * @package		AmazonS3Uploader
 * @author		Peter Fox
 * @copyright	Copyright (c) 2013 Peter Fox.
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @link		https://github.com/peterfox/AmazonS3Uploader
 * @version 	Version 0.1
 * @filesource
 */
class Amazons3uploader
{

				const PRIVATE_ACL = 'private';
				const PUBLIC_READ_ACL = 'public-read';
				const PUBLIC_WRITE_ACL = 'public-read-write';
				const AUTHENTICATED_READ_ACL = 'authenticated-read';
				const BUCKET_OWNER_READ_ACL = 'bucket-owner-read';
				const BUCKET_OWNER_FULL_CONTROL_ACL = 'bucket-owner-full-control';
				
				private $_config;
				
				public function __construct($params = 'default')
				{
								$CI = &get_instance();
		
								if(is_string($params))
								{
												
												$CI->load->config('s3uploader', true);
												$this->_config = $CI->config->item($params, 's3uploader');
								} 
								else
								{
												$this->_config = $params;
								}
				}

				public function get_results($non_found_defaults = FALSE)
				{
								$CI = &get_instance();
								
								$request_id = $CI->input->get_request_header('x-amz-request-id');
								$response_get = elements(array('bucket', 'key', 'etag'), $CI->input->get(), $non_found_defaults);
								
								return array_merge(array('requestId' => $request_id), $response_get);
				}
				
				public function create_form($output = TRUE)
				{
								$CI = &get_instance();
								
								$CI->load->model('s3policy');
								$CI->load->model('s3upload_form');
								$CI->load->helper('s3uploader');
								$CI->load->helper('array');
								$CI->load->helper('url');
								
								if(!ini_get('date.timezone'))
								{
											date_default_timezone_set('UTC');	
								}
								
								$config = elements(array(
										'aws_id', 
										'aws_key', 
										'bucket', 
										'filename', 
										'path', 
										'policy_expiration', 
										'acl', 
										'success_action_redirect', 
										'success_action_status', 
										'file_size_min', 
										'file_size_max',
										'content_type', 
										'content_type_matcher', 
										'input_type',
										'input_type_extras',
										'form_extras',
										'submit_button_disabled',
										'submit_value',
										'submit_extras',
										), $this->_config);
								
								
								$bucket = $config['bucket'];
								$aws_id = $config['aws_id'];
								$aws_key = $config['aws_key'];
								
								if($aws_id === FALSE OR $aws_key === FALSE OR $bucket === FALSE)
								{
												print_r($config);
												throw new Exception('Your config is missing items required for this library');
								}
								
								$CI->s3policy->apply_bucket($bucket);
								$CI->s3upload_form->set_bucket($bucket);

								$CI->s3upload_form->add_field('AWSAccessKeyId',$aws_id);
								
								$success_action_redirect = $config['success_action_redirect'];
								if(filter_var($success_action_redirect, FILTER_VALIDATE_URL))
								{
												$CI->s3policy->apply_success_action_redirection($success_action_redirect);
												$CI->s3upload_form->add_field('success_action_redirect', $success_action_redirect);
								}
								else
								{
												$success_action_redirect = base_url($success_action_redirect);
												$CI->s3policy->apply_success_action_redirection($success_action_redirect);
												$CI->s3upload_form->add_field('success_action_redirect', $success_action_redirect);
								}
								
								
								$filename = $config['filename'];

								if($filename === FALSE)
								{
												throw new Exception('Call to function '.$config['filename'].' failed.');	
								}
								
								$path = $config['path'];
								if(!substr($path, -strlen('/'))==='/')
								{
												$path.='/';
								}
								
								$full_path = $path.$filename;
								
								if(strstr($filename, '${filename}'))
								{
												$CI->s3policy->apply_path_filter($path, 'starts-with');
								}
								else
								{
												$CI->s3policy->apply_path_filter($full_path, 'eq');
								}
								
								$CI->s3upload_form->add_field('key',$full_path);
								
								$expire_time = $config['policy_expiration'];
								
								if($expire_time === FALSE)
								{
												$expire_time = time() + 86400;
								}
								
								$CI->s3policy->apply_policy_expiration($expire_time);
								
								$acl = $config['acl'];
								
								if($acl !== FALSE)
								{
												$CI->s3policy->apply_acl($acl);
												$CI->s3upload_form->add_field('acl', $acl);
								}
								
								$success_action_status = $config['success_action_status'];
								
								if($success_action_status !== FALSE)
								{
												$CI->s3policy->apply_success_action_status($success_action_status);
												$CI->s3upload_form->add_field('success_action_status', $success_action_status);
								}
								
								$file_size_min = $config['file_size_min'];
								$file_size_max = $config['file_size_max'];
								
								if($file_size_min !== FALSE && $file_size_max !== FALSE)
								{
												$CI->s3policy->apply_content_length_filters($file_size_min, $file_size_max);
								} 
								else if ($file_size_max !== FALSE)
								{
												$CI->s3policy->apply_content_length_filters("0", $file_size_max);
								}
								else if ($file_size_min !== FALSE)
								{
												$CI->s3policy->apply_content_length_filters($file_size_min, "5368709120");
								}
								
								$content_type = $config['content_type'];
								$content_type_matcher = $config['content_type_matcher'];
								
								//If the content type is set
								if($content_type !== FALSE)
								{
												//If both config items are arrays
												if(is_array($content_type) && is_array($content_type_matcher))
												{
																//Go through the config items getting the value and the index
																foreach($content_type as $index => $content_type_value)
																{
																				//looks up the index in the matcher array, if not found return starts-with as a default and then apply variables as a filter
																				$matcher = element($index, $content_type_matcher, 'starts-with');
																				$CI->s3policy->apply_content_type_filter($content_type_value, $matcher);
																}
												}
												//If content type is an array but the matcher is set apply to each filter the matcher
												elseif(is_array($content_type) && !is_array($content_type_matcher) && $content_type_matcher !== FALSE)
												{
																//loop through the array and apply the matcher to each filter
																foreach($content_type as $content_type_value)
																{
																				$CI->s3policy->apply_content_type_filter($content_type_value, $content_type_matcher);
																}
												}
												//If content type is an array but the matcher isn't set, apply the filter with a single parameter
												else if(is_array($content_type) && $content_type_matcher === FALSE)
												{
																foreach($content_type as $index => $content_type_value)
																{
																				$CI->s3policy->apply_content_type_filter($content_type_value);
																}
												}
												//If the matcher is set then use, otherwise just go with the single parameter
												else if($content_type_matcher !== FALSE)
												{
																$CI->s3policy->apply_content_type_filter($content_type, $content_type_matcher);
												}
												else
												{
															$CI->s3policy->apply_content_type_filter($content_type_value);
												}
								}
								
								$policy = $CI->s3policy->generate_policy();
								
								$CI->s3upload_form->add_field('policy', $policy);
								
								$CI->s3upload_form->add_field('signature', generate_s3_signature($policy, $aws_key));
								
								$form_extras = element('form_extras', $config, '');
								
								$CI->s3upload_form->set_form_extras($form_extras);
								
								$submit_disabled = $config['submit_button_disabled'];
								
								$submit_value = element('submit_value', $config, 'submit');
								$submit_extras = element('submit_extras', $config, '');
								
								if(!$submit_disabled)
								{
												$CI->s3upload_form->add_submit_button(!$submit_disabled, $submit_value, $submit_extras);
								}
								
								$input_type = $config['input_type'] === false ? 'file' : $config['input_type']; 
								$input_type_extras = element('input_type_extras', $config, '');
								
								if($input_type === 'textarea')
								{
												$CI->s3upload_form->add_textarea($input_type_extras);
								}
								else
								{
												$CI->s3upload_form->add_file($input_type_extras);
								}
								
								$form = $CI->s3upload_form->generate_form();
								
								if($output)
								{
												$CI->output->set_output($form);
								}
								else
								{
												return $form;
								}
				}

}