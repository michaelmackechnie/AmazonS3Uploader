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
 * @package		AmazonS3Uploader
 * @author		Peter Fox
 * @copyright	Copyright (c) 2013 Peter Fox.
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @link		https://github.com/peterfox/AmazonS3Uploader
 * @version 	Version 0.1
 * @filesource
 */

if (!function_exists('generate_s3_signature'))
{

				function generate_s3_signature($policy, $aws_key)
				{
								//take the already base64 encoded policy and then hash it using the aws key as a salt, then again base64 encode it
								return base64_encode(hash_hmac("sha1", $policy, $aws_key, TRUE));
				}

}