<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// Set your configs in groups to be loaded in through the library by the equiverlant of $this->load->library('Amazons3uploader', 'default');

$config['default']['aws_id'] = ''; // completely required
$config['default']['aws_key'] = ''; // completely required
$config['default']['bucket'] = ''; // completely required

$config['default']['filename'] = '${filename}'; // ${filename} is a wild card S3 uses to keep the original name on storing files.
$config['default']['path'] = ''; // shouldn't include a root forward slash e.g. '/test/' is wrong, use instead 'test/'
$config['default']['input_type'] = 'file'; // the default is as a file but it can also be set as a 'textarea' for raw text to be saved in an S3 Object

//$config['default']['input_type_extras'] = ''; // allows you to add extra attributes to the file input or textarea, do so as a string e.g. 'id="myInput"'
//$config['default']['form_extras'] = ''; // allows you to add extra attributes to the main form, do so as a string e.g. 'id="myForm"'
//$config['default']['submit_button_disabled'] = true; // can disable the submit button from being shown if needed
//$config['default']['submit_value'] = ''; // set the text of the submit button for you
//$config['default']['submit_extras'] = ''; // allows you to add extra attributes to the main form, do so as a string e.g. 'id="myButton"'


$config['default']['policy_expiration'] = time() + 86400; // sets the time in the typical epoach fashion, will set the timezone as UTC if it hasn't been set by the php.ini file already
$config['default']['acl'] = 'private'; // the acl for storing the file, typically you'll want private

//$config['default']['file_size_min'] = '0'; // number should be in bytes
//$config['default']['file_size_max'] = '5368709120'; // number should be in bytes max is 5GB ~ 5368709120bytes
//$config['default']['content_type'] = ''; // this one is complicated, it could be set as one field or an array, use the matcher field to say how you want it to match
//$config['default']['content_type_matcher'] = ''; // this should be a string for a singular match or an array that has matching keys to 'content_type', values should be either 'eq', 'starts-with', 'ends-with'

//$config['default']['success_action_status'] = '201'; //The HTTP Status code expected on a successful result, Default: 201
$config['default']['success_action_redirect'] = ''; //Should be a URI route for your current site path e.g. 'path/to/result/handler', leaving the item as a blank string goes the site index. if you wish to redirect to another site make sure http:// or https:// is applied to the full url