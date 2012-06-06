<?php
# Copyright 2008 Amazon Technologies, Inc.  Licensed under the Apache License, Version 2.0 (the "License"); 
# you may not use this file except in compliance with the License. You may obtain a copy of the License at:
#
# http://aws.amazon.com/apache2.0
#
# This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and limitations under the License.

/************************************************************************
 * REQUIRED
 *
 * Access Key ID and Secret Access Key, obtained from:
 * http://aws.amazon.com
 ***********************************************************************/	
	define('AWS_ACCESS_KEY_ID', ''); // Enter your AWS Access Key ID
	define('AWS_SECRET_ACCESS_KEY', ''); // Enter your AWS Secret Access Key
	
	// Application specific variables
	define('IPN_URL', 'http://my.website.com/ipn.php'); // Enter the URL of your ipn.php script
	define('LOG_FILE', 'log.txt');  // Enter filename for log data.  This file will need write permissions.