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
 * Description of S3policy
 *
 * @package		AmazonS3Uploader
 * @author		Peter Fox
 * @copyright	Copyright (c) 2013 Peter Fox.
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @link		https://github.com/peterfox/AmazonS3Uploader
 * @version 	Version 0.1
 * @filesource
 */

class S3policy extends CI_Model
{

				const FILTER_STARTS_WITH = 'starts-with';
				const FILTER_EQUALS = 'eq';
				const FILTER_ENDS_WITH = 'ends-with';

				private $policy;

				public function __construct()
				{
								parent::__construct();

								$this->policy = $this->new_policy();
				}

				private function new_policy()
				{

								$this->policy =
											array(
													'conditions' => array()
								);
				}

				public function apply_bucket($bucket)
				{
								$this->policy['conditions'][]['bucket'] = $bucket;

								return $this;
				}

				public function apply_acl($acl = 'private')
				{
								$this->policy['conditions'][]['acl'] = $acl;

								return $this;
				}
				
				public function apply_meta_uuid($uuid)
				{
								$this->policy['conditions'][]['x-amz-meta-uuid'] = $uuid;

								return $this;
				}
				
				public function apply_meta_tags($tag = '')
				{
								$this->policy['conditions'][]['x-amz-meta-tag'] = $tag;

								return $this;
				}
				
				public function apply_meta_server_encryption($encryption = 'AES256')
				{
								$this->policy['conditions'][]['x-amz-meta-server-side-encryption'] = $encryption;

								return $this;
				}
				
				public function apply_meta_storage_class($redundancy = 'REDUCED_REDUNDANCY')
				{
								$this->policy['conditions'][]['x-amz-meta-storage-class'] = $redundancy;

								return $this;
				}

				public function apply_path_filter($path = '', $filter = 'starts-with')
				{				
								$this->policy['conditions'][] = array($filter, '$key', $path);

								return $this;
				}

				public function apply_content_type_filter($content_type = '', $filter = 'starts-with')
				{
								
								$this->policy['conditions'][] = array($filter, '$Content-Type', $content_type);

								return $this;
				}

				public function apply_content_type_filter_by_filename($filename, $filter = 'starts-with')
				{
								//load file helper for get_mime_by_extention function
								$this->load->helper('file');

								$mime = get_mime_by_extension($filename);

								$this->policy['conditions'][] = array($filter, '$Content-Type', $mime);

								return $this;
				}

				public function apply_success_action_redirection($redirection, $filter = 'eq')
				{
								$this->policy['conditions'][] = array($filter, '$success_action_redirect', $redirection);

								return $this;
				}
				
				public function apply_success_action_status($status = '201')
				{
								$this->policy['conditions'][]['success_action_status'] = (String)$status;

								return $this;
				}

				public function apply_content_length_filters($min = '0', $max = '5368709120')
				{
								$this->policy['conditions'][] = array('content-length-range', (String)$min, (String)$max);

								return $this;
				}

				public function apply_policy_expiration($expire_date = false)
				{
								//apply date helper for use of mdate function
								$this->load->helper('date');

								//if an expire date isn't set, set it to be 24 hours after the current time on the server
								$expire_date = $expire_date === false ? time() + 86400 : $expire_date;

								$this->policy['expiration'] = mdate('%Y-%m-%dT %H:%i:%sZ', $expire_date);

								return $this;
				}

				public function generate_policy()
				{
								$policy = $this->policy;
								$this->policy = $this->new_policy();
								
								print_r($policy);
								print "\n".json_encode($policy)."\n\n";
								return base64_encode(json_encode($policy));
				}

}