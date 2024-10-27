<?php
// This was Amazonfeed 2.0.1, forked as AmazonPress starting from ver 8.6
// Improved by: Tom Makela - http://cj7.co.uk/
// Version - 9.1.1 - New APi, New countries 
// Version - 9.01 -  includes all Amazon partner sites .com, Canada, Germany, Japan and France
// Version - 8.6 -  includes new Amazon partner sites China, Spain and Italy, new Amazon API
// Updates and this download can be found at: http://cj7.co.uk/amazonpress-free-wp-plugin/
// Wordpres repository and this download can be found at: http://wordpress.org/extend/plugins/amazonpress/
// Original source by Warkior -  http://www.warkensoft.com/php-downloads/amazonfeed-wordpress-plugin/

if(!class_exists("AmazonPress")) {
class AmazonPress {

	var $options;
	var $params;
	var $live 			= false;
	var $done_request	= false;
	var $admin_notices	= array();
	var $debug_mode		= 'off';
	var $debug_lines	= 100;
	var $debug_visible	= false;
	var $xmlData;
	var $table_cache 	= "";
	var $table_options 	= "";
	var $table_log	 	= "";
	var $table_products = "";
	var $AllowTip 		= "yes";
	var $version		= "9.1.1";
	var $validate		= array();

	function AmazonPress($new_params = array()) {

		global $wpdb;

		$this->table_options 	= $wpdb->prefix . "amazonpress_options";
		$this->table_log 		= $wpdb->prefix . "amazonpress_log";
		$this->table_cache 		= $wpdb->prefix . "amazonpress_cache";
		$this->table_products	= $wpdb->prefix . "amazonpress_products";

		$this->options = get_option('amazonPressOptions');
		$this->setValidation();
		
		if(defined('AUTH_KEY'))
			$this->encKey = AUTH_KEY;
		else
			$this->encKey = ABSPATH . 'fwa CGkK-5{ao[XYn7hq7wLvDMN#^PSIZ)$19lPI+UpH51vD|gYe%9s)j#5E-.lu';

		$this->params = array(
			'Operation' => 'ItemSearch',
			'SearchIndex' => 'Books',
			'ResponseGroup' => 'Medium,Images'
		);
		
		if( isset($this->options['ServicePath'])
		AND isset($this->options['AWSAccessKeyId'])
		AND (
			isset($this->options['AWSSecretAccessKeyId'])
			OR time() < 1250294400
			)
		AND isset($this->options['AssociateTag'])
		AND isset($this->options['DefaultTags'])
		AND isset($this->options['DefaultSearchField'])
		AND isset($this->options['MaxResults'])
		AND isset($this->options['Version']) 
		AND function_exists('simplexml_load_string') )
		{
			$this->live = true;
		}

	}
	
	function setValidation()
	{
		$this->validate['SortBy'] = array(
			'random' => true,
			'salesrank' => true,
			'-salesrank' => true,
			'listprice' => true,
			'-listprice' => true
		);
		
		$this->validate['DisplayPosition'] = array(
			'0' => true,
			'1' => true
		);
				
	}
	
	function checkInstall($autoInstall = false)
	{
		if(!isset($this->options['Version']) OR $this->options['Version'] < $this->version) 
		{
			if($autoInstall) return($this->doInstall());
			else 
			{
				if(!strpos($_SERVER['REQUEST_URI'], 'amazonpress/php/amazonpress.class.php'))
				$this->admin_notices[] = 'AmazonPress is almost ready.<br/>Please visit the management page <a href="tools.php?page=amazonpress/php/amazonpress.class.php" target="_top">"(Tools &gt; AmazonPress)"</a> to complete the ' .
				'installation process and enter your Amazon API credentials. <br/> <br/>
				If you do not have one you can get one quite easy from: <br/>
				US: <a href="http://affiliate-program.amazon.com/?_encoding=UTF8&tag=xxxxxxxus-20" target="_new">"Amazon Affiliate USA"</a><br/>
				UK: <a href="http://affiliate-program.amazon.co.uk/?_encoding=UTF8&tag=xxxxxxxuk-21" target="_new">"Amazon Affiliate United Kingdom"</a><br/>
				Canada: <a href="http://associates.amazon.ca/?_encoding=UTF8&tag=xxxxxxxca-20" target="_new">"Amazon Affiliate Canada"</a><br/>
				China: <a href="http://associates.amazon.cn/?_encoding=UTF8&tag=xxxxxxxcn-23" target="_new">"Amazon Affiliate China"</a><br/>
				Japan: <a href="http://affiliate.amazon.co.jp/?_encoding=UTF8&tag=xxxxxxxjp-22" target="_new">"Amazon Affiliate Japan"</a><br/>
				Germany: <a href="http://partnernet.amazon.de/?_encoding=UTF8&tag=xxxxxxxde-21" target="_new">"Amazon Affiliate Deutchland"</a><br/>
				Italy: <a href="http://programma-affiliazione.amazon.it/?_encoding=UTF8&tag=xxxxxxxit-21" target="_new">"Amazon Affiliate Italy"</a><br/>
				France: <a href="http://partenaires.amazon.fr/?_encoding=UTF8&tag=xxxxxxxfr-21" target="_new">"Amazon Affiliate France"</a><br/>
				Spain: <a href="http://afiliados.amazon.es/?_encoding=UTF8&tag=xxxxxxxes-21" target="_new">"Amazon Affiliate Spain"</a><br/>';
				return(false);
			}
		}
		else
			return(true);
	}

	function unInstall()
	{
		global $wpdb;
		$sql = "DROP TABLE `" . $this->table_cache . "`;";
		$wpdb->query($sql);
		
		$sql = "DROP TABLE `" . $this->table_log . "`;";
		$wpdb->query($sql);

		$sql = "DROP TABLE `" . $this->table_products . "`;";
		$wpdb->query($sql);
		
		delete_option('amazonPressOptions');
	}
	
	function doInstall()
	{
		global $wpdb;
		
		if(!function_exists('simplexml_load_string')) {
			$this->admin_alert("WARNING: AmazonPress currently only works on servers running PHP v 5.x or higher.");	
			return(false);
		}
		
		if(!isset($this->options['Version'])) {

			$this->admin_alert("Previous installation not found.  Installing necessary tables now.");

			$sql = "CREATE TABLE IF NOT EXISTS `" . $this->table_cache . "` (
			  `keyword` varchar(255) NOT NULL,
			  `timestamp` bigint(20) unsigned zerofill NOT NULL,
			  `data` longblob NOT NULL,
			  `blocked` blob NOT NULL,
			  PRIMARY KEY  (`keyword`)
			) ENGINE = MYISAM ;";
			$result = $wpdb->query($sql);
			if($result === false) {
				$this->admin_alert("Failed to create table `" . $this->table_cache . "`.");
				return(false);
			}

			$sql = "CREATE TABLE IF NOT EXISTS `" . $this->table_log . "` (
			  `id` bigint(20) NOT NULL auto_increment,
			  `timestamp` bigint(20) unsigned zerofill NOT NULL,
			  `message` text NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `timestamp` (`timestamp`)
			) ENGINE=MyISAM ;";
			$result = $wpdb->query($sql);
			if($result === false) {
				$this->admin_alert("Failed to create table `" . $this->table_log . "`.");
				return(false);
			}

			$sql = "CREATE TABLE IF NOT EXISTS `" . $this->table_products . "` (
			  `id` bigint(20) unsigned zerofill NOT NULL auto_increment,
			  `cache_id` varchar(255) NOT NULL,
			  `data` longblob NOT NULL,
			  `asin` varchar(255) NOT NULL,
			  `blocked` tinyint(1) NOT NULL,
			  `sticky` tinyint(1) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM;";
			$result = $wpdb->query($sql);
			if($result === false) {
				$this->admin_alert("Failed to create table `" . $this->table_products . "`.");
				return(false);
			}
			
			$this->options = array(
				'Locale' 			=> 'United States',
				'LocaleTipTag' 		=> 'xxxxxxxus-20',
				'ServicePath' 		=> 'http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService',
				'AWSAccessKeyId' 	=> '',
				'AssociateTag' 		=> 'xxxxxxxus-20',
				'SearchFrom' 		=> 'categories',
				'DefaultTags'		=> 'Amazon',
				'ShowOnPosts'		=> true,
				'ShowOnPages'		=> false,
				'ShowOnHome'		=> true,
				'ShowOnCategories'	=> true,
				'ShowOnTags'		=> false,
				'ShowOnSearch'		=> true,
				'TitleText' 		=> '<h3>Related Reading:</h3>',
				'DefaultSearchField'=> 'Keywords',
				'MaxResults' 		=> 3,
				'ShowText' 			=> true,
				'ShowImages' 		=> true,
				'CacheExpiry' 		=> 60*24,
				'AllowTip' 			=> true,
				'StyleSheet'		=> 'style.css',
				'WidgetOptions'		=> array(
				'Title'				=> false,
				'DefaultTags'		=> false),
				'Version'	 		=> $this->version
			);
			update_option('amazonPressOptions', $this->options);
		}
		elseif($this->options['Version'] < $this->version)
		{
			$this->admin_alert("Plugin files version do not match installed version.  Running upgrade scripts now.");
			if($this->options['AllowTip'] != true) $this->admin_alert("I notice you don't have the 'Tip' option enabled.  If you'd like to help " .
					"support the ongoing development of this plugin, you can find that option under the 'Options-&gt;Display' tab.  " .
					"Enabling the tip option is a nice way you could say thank you.");
			
			if($this->options['Version'] < '1.1')
			{
				$this->options['Version'] = '1.1';
				$this->options['ShowOnPosts'] = true;
				$this->options['ShowOnPages'] = true;
				$this->options['ShowOnHome'] = true;
				$this->options['ShowOnCategories'] = true;
				$this->options['ShowOnTags'] = true;
				$this->options['ShowOnSearch'] = true;
				
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. 1.1  This version allows you to limit display of products on various categories of blog pages.  Some CSS tweaks were also added.");
			}
			
			if($this->options['Version'] < '1.2')
			{
				$this->options['Version'] = '1.2';
	
				$this->options['Locale'] 		= 'United States';
				$this->options['LocaleTipTag'] 	= 'xxxxxxxus-20';
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. 1.2  This version allows you to select the locale from where you wish your products to be selected.  Please be aware that in order to collect referral rewards, your associate account must be registered in the same locale as you are using to pull products from.");
			}
			
			if($this->options['Version'] < '1.3')
			{

				$this->options['Version'] = '1.3';
				if(!$this->options['SearchIndex']) $this->options['SearchIndex'] = $this->params['SearchIndex'];
	
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. 1.3 -- Tired of promoting just books? This version gives you access to promote many different types of products from Amazon.  Simply check off the products you would like to promote in the Items to Feature field below.");
			}
			
			if($this->options['Version'] < '1.4')
			{

				$this->options['Version'] = '1.4';
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. 1.4 -- Updated to support new Amazon security protocols.  You will need to enter your AWS Secret Access Key in the appropriate field below.");
			}
			
			if($this->options['Version'] < '1.5')
			{

				$this->options['Version'] = '1.5';
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. 1.5 -- Minor bug fixes and tweaks to support a wider range of Amazon keys.");
			}
			
			if($this->options['Version'] < '1.6')
			{

				$this->options['Version'] = '1.6';
				$this->options['StyleSheet'] = 'style.css';
				$this->options['ShowText'] = true;
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. 1.6<br/>" .
						"Minor bug fixes when zero results are returned.<br/>" .
						"Better admin screen organization.<br/>" .
						"Upgraded admin security.<br/>" .
						"Allowed display of only images or only text.<br/>" .
						"Allowed for custom stylesheets.<br/><br/>" .
						"NOTE: If products are not showing on your blog, you may need to clear the built-in product cache.");
			}
			
			if($this->options['Version'] < '1.7')
			{

				$this->options['Version'] = '1.7';
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. 1.7<br/>" .
						"Expanded debugging and error control.<br/>" .
						"Allowed display of cached Amazon data in admin console.");
			}
			
			if($this->options['Version'] < '1.8')
			{

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->table_log . "` (
				  `id` bigint(20) NOT NULL auto_increment,
				  `timestamp` bigint(20) unsigned zerofill NOT NULL,
				  `message` text NOT NULL,
				  PRIMARY KEY  (`id`),
				  KEY `timestamp` (`timestamp`)
				) ENGINE=MyISAM ;";
				$result = $wpdb->query($sql);
				if($result === false) {
					$this->admin_alert("Failed to create table `" . $this->table_log . "`.");
					return(false);
				}
				
				$this->options['WidgetOptions'] = array(
						'Title'				=> false,
						'DefaultTags'	=> false);
				
				
				$this->options['ImageSize'] = '';

				$this->options['Version'] = '1.8';
				unset($this->options['error_log']);
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. 1.8<br/>" .
						"Added sidebar Widget controls.<br/>" .
						"Enabled display of product descriptions.<br/>" .
						"Updated display CSS for better theme compatibility.<br/>" .
						"Expanded debugging and error control.<br/>" .
						"");
			}
			
			if($this->options['Version'] < '1.9')
			{
				$this->options['SortBy'] = 'random';
				$this->options['DisplayPosition'] = '0';
				
				$sql = "ALTER TABLE `" . $this->table_cache . "` ADD `blocked` BLOB NOT NULL";
				$result = $wpdb->query($sql);
				if($result === false) {
					$this->admin_alert("Failed to create table `" . $this->table_log . "`.");
					return(false);
				}
				
				$this->options['Version'] = '1.9';
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. " . $this->options['Version'] . "<br/>" .
						"Added ability to sort results on the whole blog, as well as on individual posts.<br/>" .
						"Added ability to chose display position relative to page/post content.<br/>" .
						"Reworked product caching for more robust storage and more advanced potential future features.<br/>" .
						"Added ability to block specific products from being displayed.<br/>" .
						"");
			}
			
			if($this->options['Version'] < '2.0')
			{
				$this->options['Version'] = '2.0';
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. " . $this->options['Version'] . "<br/>" .
						"Removed Amazon products from appearing in blog feeds in order to better comply with Amazon regulations.<br/>" .
						"");
			}

			if($this->options['Version'] < '9.1')
			{
				$this->options['Version'] = '9.1';
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. " . $this->options['Version'] . "<br/>" .
						"New Amazon countries added to the list.<br/>" .
						"Germany, France, Japan.<br/>" .
						"<br/>" .
						"");
			}
			
			if($this->options['Version'] < '9.1.1')
			{
				$this->options['Version'] = '9.1.1';
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Plugin upgraded to v. " . $this->options['Version'] . "<br/>" .
						"Updated Amazon API calls.<br/>" .
						"New Amazon countries added to the list. China, Italy, Spain.<br/>" .
						"Thanks for letting the tip function be active. I am not making a fortune, but it has motivated me to do this update.<br/>" .
						"");
			}
			
			return(true);
		}
	}
	
	function wp_admin_notices()
	{
		foreach($this->admin_notices as $msg)
		{
			$this->admin_alert($msg);
			$this->debug($msg);
		}
	}

	function admin_alert($msg = '', $log = true)
	{
		if($msg) echo "<div class='updated' id='amazonPressAlert'><p><strong>$msg</strong></p></div>";
		if($log) $this->debug($msg);
	}
	
	function error_handler($errno, $errstr, $errfile=false, $errline=false)
	{
		$msg = 'PHP Error: ' . $errstr;
		
		switch($errno)
		{
			case E_ERROR:
			case E_USER_ERROR:
				$this->debug($msg);
				die();
			break;
			
			case E_WARNING:
			case E_USER_WARNING:
				$this->debug($msg);
			break;
			
			default:
			break;
		}
	}
		
	function log_error($msg = '')
	{
		if(!isset($this->options['Version'])) return;
		
		if($this->options['Version'] < '1.8')
		{
			if(!is_array($this->options['error_log'])) $this->options['error_log'] = array();
			array_unshift($this->options['error_log'], date('F j, Y, g:i a ') . $msg);
			if(count($this->options['error_log']) > 100)
			{
				array_pop($this->options['error_log']);
			}
			update_option('amazonPressOptions', $this->options);
		}
		else
		{
			global $wpdb;
			$sql = "INSERT INTO `" . $this->table_log . "` (`timestamp`, `message`) " .
					"VALUES (" .
					"'" . time() . "', " .
					"'" . addslashes($msg) . "');";
			$wpdb->query($sql);
		}
	}
	
	function show_error($msg = '')
	{
		if($this->debug_visible == true)
		{
			echo "\n<!-- AmazonPress Debugging Message --><pre>$msg</pre>";
		}
	}

	function debug($msg = '', $level = '1')
	{
		
		switch($this->debug_mode)
		{
			case 'basic':
				if($level <= '1') {
					$this->log_error($msg);
					if($this->debug_visible == true) $this->show_error($msg);
				}
			break;
			
			case 'all':
				if($level <= '2') {
					$this->log_error($msg);
					if($this->debug_visible == true) $this->show_error($msg);
				}
			break;
			
			default:
			break;
		}
			
	}

	function getpath($path, $username = false, $password = false)
	{
		$this->debug("Using built-in getpath function to load data.  Slower, but should work.");
		
		if(false !== $username AND false !== $password)
			$match = "^([a-z]{2,10})\://" . $username . "\:" . $password . "([a-z0-9\.\-]+)/?([^\?]*)(.*)$";
		else
			$match = "^([a-z]{2,10})\://([a-z0-9\.\-]+)(/?[^\?]*)(.*)$";
	
		if(!eregi($match, $path, $regs)) {
			return(false);
		}
		else {
			list($path, $protocol, $hostname, $request, $query) = $regs;
	
			switch(strtoupper($protocol))
			{
				case "HTTPS":
					$port = 443;
					break;
	
				case "FTP":
					$port = 21;
					break;
	
				default:
					$port = 80;
					break;
			}
		}
	
		$fp = fsockopen($hostname, $port, $errno, $errstr, 10);
		if (!$fp) {
		    echo "$errstr ($errno)<br />\n";
		    return(false);
		} else {
		    $out = "GET " . $request . $query . " HTTP/1.0\r\n";
		    $out .= "Host: $hostname\r\n";
		    $out .= "Connection: Close\r\n\r\n";
	
		    fwrite($fp, $out);
		    $data = '';
		    while (!feof($fp)) {
		        $data .= fgets($fp);
		    }
		    fclose($fp);
	
		    $data_start = strpos($data, "\r\n\r\n");
	
		    $header = substr($data, 0, $data_start);
		    $body = substr($data, $data_start + 4, strlen($data));
	
			$regs = "";
			if(eregi("[\r\n]+Location\: *([^\r\n]+)", $header, $regs) AND eregi("HTTP/[0-9]*\.[0-9]*[ ]*3[0-9]{2}", $header))
			{
				$location = $regs[1];
				return($this->getpath($location));
			}
			else
			{
				return($body);
			}
		}
	}


	function request_data($new_params = array())
	{
		if($this->done_request == true) {
			$this->debug("Do not run request for '" . implode(",", $new_params) . "' in order to comply " .
					"with Amazon speed limit regulations");
			return(false);
		} 
		
		if($this->options['SearchIndex']) $this->params['SearchIndex'] = $this->options['SearchIndex'];
		
		$params = array_merge($this->params, $new_params);

		if(eregi('ecs\.amazonaws\.([^/]+)/', $this->options['ServicePath'], $regs))
		{
			$region = $regs[1];
		}

		
		$pubKey = $this->options['AWSAccessKeyId'];
		$priKey = $this->options['AWSSecretAccessKeyId'];
		$request['AssociateTag'] = $this->options['AssociateTag'];
		
		foreach($params as $key=>$param)
		{
			if($param != "")
			{
				$request[$key] = $param;
			}
		}
		
		$xml_data = $this->aws_signed_request($region, $request, $pubKey, $priKey); 
		$this->done_request = true;
		if($xml_data) return($xml_data);
	}

	function load($keyword)
	{
		global $wpdb;
		$keyword = addslashes($keyword);
		$sql = "SELECT * FROM " . $this->table_cache . " WHERE `keyword` = '" . $keyword . "' LIMIT 0,1";
		$data = $wpdb->get_row($sql, ARRAY_A);

		if($data !== false AND $data['keyword'] != "")
		{
			$data['keyword'] = stripslashes($data['keyword']);
			$data['data'] = stripslashes($data['data']);

			return($data);
		}
	}
	
	function save($keyword, $xml)
	{
		global $wpdb;

		$keyword = trim(addslashes($keyword));
		$data = trim(addslashes($xml));
		$timestamp = time() + ($this->options['CacheExpiry']*60);

		$sql = "SELECT * FROM " . $this->table_cache . " WHERE `keyword` = '" . $keyword . "' LIMIT 0,1";
		$existing_data = $wpdb->get_row($sql, ARRAY_A);

		if($existing_data['keyword'] != "")
			$sql = "UPDATE " . $this->table_cache . " SET `timestamp` = '$timestamp', `data` = '$data' WHERE `keyword` = '" . $keyword . "' LIMIT 1;";
		else
			$sql = "INSERT INTO " . $this->table_cache . " (`keyword`, `timestamp`, `data`) VALUES ('$keyword', '$timestamp', '$data');";

		$results = $wpdb->query($sql);
		return;
	}
	
	function block($keyword, $asin)
	{
		global $wpdb;
		
		$keyword = trim(addslashes($keyword));
		$asin = trim(addslashes($asin));
		
		$sql = "SELECT * FROM " . $this->table_cache . " WHERE `keyword` = '" . $keyword . "' LIMIT 0,1";
		$existing_data = $wpdb->get_row($sql, ARRAY_A);

		if($existing_data['keyword'] != "")
		{
			$blocked = stripslashes($existing_data['blocked']);
			if(trim($blocked) != "") $blocked = explode('|', $blocked);
			else $blocked = array();
			$key = array_search($asin, $blocked);
			if($key === false)
			{
				$blocked[] = $asin;
			}
			$blocked = implode('|', $blocked);
			$blocked = addslashes($blocked);
				 
			$sql = "UPDATE " . $this->table_cache . " SET `blocked`='$blocked' WHERE `keyword` = '" . $keyword . "' LIMIT 1;";
			$wpdb->query($sql);
		}
		else
			$this->admin_alert("Unable to block this product.  The keyword doesn't exist in the cache.");

		return;
	}
	
	function unblock($keyword, $asin)
	{
		global $wpdb;
		
		$keyword = trim(addslashes($keyword));
		$asin = trim(addslashes($asin));
		
		$sql = "SELECT * FROM " . $this->table_cache . " WHERE `keyword` = '" . $keyword . "' LIMIT 0,1";
		$existing_data = $wpdb->get_row($sql, ARRAY_A);

		if($existing_data['keyword'] != "")
		{
			$blocked = stripslashes($existing_data['blocked']);
			if(trim($blocked) != "") $blocked = explode('|', $blocked);
			else $blocked = array();
			$key = array_search($asin, $blocked);
			if(false !== $key)
				unset($blocked[$key]);
			if(count($blocked) > 0) $blocked = implode('|', $blocked);
			else $blocked = '';
			$blocked = addslashes($blocked);
				 
			$sql = "UPDATE " . $this->table_cache . " SET `blocked`='$blocked' WHERE `keyword` = '" . $keyword . "' LIMIT 1;";
			$wpdb->query($sql);
		}
		else
			$this->admin_alert("Unable to block this product.  The keyword doesn't exist in the cache.");

		return;
	}


	function search($search_keywords, $searchResults=false, $searchField=false, $searchIndex=false, $options = array())
	{
		global $wpdb;

		$this->debug("Searching For: '$search_keywords'", 2);

		if($searchResults == false) $searchResults = $this->options['MaxResults'];
		if($searchField == false) $searchField = $this->options['DefaultSearchField'];
		if($searchIndex == false) $searchIndex = $this->params['SearchIndex'];

		$keywords = explode(",", $search_keywords);
		$tmp_items = array();

		foreach($keywords as $word)
		{
			if(trim($word) != "")
			{
				$xml_data = false;
				$data = false;
				$data = $this->load(trim($word));
				$blocked = array();
				
				if($data['data'])
				{
					$blocked = stripslashes($data['blocked']);
					if(trim($blocked) != "") $blocked = explode('|', $blocked);
					else $blocked = array();
				}
				
				if($data['data'] != "" AND ($data['timestamp'] > time() OR $this->done_request == true)) {
					$this->debug("Loading from memory for keyword: '$word'.", 2);
					$xml_data = $data['data'];
					
					$memCached = true;
				}
				elseif(!$this->done_request)
				{
					$new_params = array(
						$searchField => urlencode(trim($word))
					);
					
					$this->debug("Sending request to amazon for keyword: '$word'.");
					$xml_data = $this->request_data($new_params);
					
					if($xml_data)
					{
						libxml_use_internal_errors(true);
						$xml = simplexml_load_string($xml_data);

						if($xml)
						{
							$this->debug("Received XML data from Amazon for word '$word'.  Saving to cache.", 2);
							$this->save($word, $xml_data);
							$memCached = false;
						}
						else
						{
							libxml_clear_errors();
							$this->debug("Unable to properly process XML data received for word '$word'.  Attempting to use pre-cached data if available.");
							$xml_data = $data['data'];
							$memCached = true;
						}
					}
					else
						$this->debug("Failed to receive any valid XML from Amazon for word '$word'.");
					
				}
				else
				{
					$this->debug("Skipped searching for word '$word' because we should only send one request to Amazon per page load.  Right now, we're done.", 2);
				}
				
				
				
				if($xml_data)
				{
					$xml = simplexml_load_string($xml_data);
					if(isset($xml))
					{
						$items = $xml->Items->Item;
						
						if(count($items) > 0)
						{
							$counter = 0;
							foreach($xml->Items->Item as $item) {
								if(array_search($item->ASIN, $blocked) === false)
									$tmp_items[] = $item;
							}
						}
						elseif(isset($xml->Items->Request->Errors))
						{
							if(!$memCached)
							{
								$this->debug("Error returned from Amazon for word '$word'.");
								$this->debug($xml->Items->Request->Errors->Error->Message);
							}
							else
							{
								$this->debug("Detected previously cached errors for this word '$word' from Amazon.");
								$this->debug($xml->Items->Request->Errors->Error->Message);
							}
						}
						else
						{
							$this->debug("No results or errors were returned for word '$word'.");
						}
					}
					else
						$this->debug("Unable to properly load XML string for word '$word'.");
				}
				else
					$this->debug("No XML data detected for word '$word'.", 2);
			}
		}
		
		if(!$tmp_items) {
			$this->debug('No results found related to the keyword(s): ' . $search_keywords);
			return(false);
		}

		$items = array();
		$counter = 0;
		
		if(!isset($options['SortBy'])) $options['SortBy'] = '';
		
		switch($options['SortBy'])
		{
			case 'salesrank':
				foreach($tmp_items as $tmp_item)
				{
					$id = (int) $tmp_item->SalesRank;
					if($id)
					{
						$items[$id] = $tmp_item;
					}
				}
				ksort($items);
				$items = array_slice($items, 0, $searchResults);
			break;
			
			case '-salesrank':
				foreach($tmp_items as $tmp_item)
				{
					$id = (int) $tmp_item->SalesRank;
					if($id)
					{
						$items[$id] = $tmp_item;
					}
				}
				krsort($items);
				$items = array_slice($items, 0, $searchResults);
			break;
			
			case 'listprice':
				foreach($tmp_items as $tmp_item)
				{
					if($tmp_item->OfferSummary->LowestNewPrice->Amount)
						$id = (int) $tmp_item->OfferSummary->LowestNewPrice->Amount;
					else
						$id = (int) $tmp_item->ItemAttributes->ListPrice->Amount;
					
					
					
					if($id)
					{
						$items[$id] = $tmp_item;
					}
				}
				ksort($items);
				$items = array_slice($items, 0, $searchResults);
			break;
			
			case '-listprice':
				foreach($tmp_items as $tmp_item)
				{
					if($tmp_item->OfferSummary->LowestNewPrice->Amount)
						$id = (int) $tmp_item->OfferSummary->LowestNewPrice->Amount;
					else
						$id = (int) $tmp_item->ItemAttributes->ListPrice->Amount;
					
					
					
					if($id)
					{
						$items[$id] = $tmp_item;
					}
				}
				krsort($items);
				$items = array_slice($items, 0, $searchResults);
			break;
			
			case 'random':
			default:
				while($counter++ < count($tmp_items)*2)
				{
					$rand = rand(0, count($tmp_items)-1);
					$tmp_item = $tmp_items[$rand];
					$id = $tmp_item->ASIN;
		
					if(!$items["$id"]) $items["$id"] = $tmp_item;
					if(count($items) >= $searchResults) break;
				}
			break;
		}

		return($items);
	}

	function display($keywords, $echo = true, $params = array())
	{
		$options = $this->options;
		
		if(!isset($params['class'])) $params['class'] = 'amazonpress';
		
		foreach($params as $key=>$value)
		{
			$options[$key] = $value;
		}
		
		$old_error_handler = set_error_handler(array($this, 'error_handler'));
		if($old_error_handler === false) $this->debug('Unable to set custom error handler.');
		
		$items = $this->search($keywords, false, false, false, $options);
		if($items) $numBooks = count($items);
		
		if($numBooks == 0 AND $keywords != $options['DefaultTags'])
		{
			$this->debug("No items found for keywords '$keywords'.  Searching with default tags: '" . $options['DefaultTags'] . "'");
			$items = $this->search($options['DefaultTags'], false, false, false, $options);
			$numBooks = count($items);
			
			$this->debug("Number of Items: $numBooks", 2);
		}
		
		$result = '';

		if($numBooks > 0)
		{
			if($options['AllowTip'] == true)
			{
				if($numBooks == 1) 
					$tip_random_number = rand(1, 5);
				else
					$tip_random_number = rand(1, $numBooks);
				
			}
			
			$result = "<div class='" . $params['class'] . "'>" . stripslashes($options['TitleText']) . "\n";

			$counter = 0;
			if(is_array($items)) foreach($items as $item)
			{
				$result .= "<div class='product'>";
				
				$counter++;
				
				$title = $item->ItemAttributes->Title;
				$desc = trim($item->EditorialReviews->EditorialReview[0]->Content);
				$link = urldecode($item->DetailPageURL);
				$link_target = $options['LinkTarget'];

				if(isset($options['ImageSize'])) {
					if($options['ImageSize'] == '1')
						$image = $item->SmallImage->URL;
					elseif($options['ImageSize'] == '2')
						$image = $item->MediumImage->URL;
					elseif($options['ImageSize'] == '3')
						$image = $item->LargeImage->URL;
					else
						$image = '';
						
				}
				elseif($options['ShowImages'])
				{
					$image = $item->SmallImage->URL;
				}
				else
				{
					$image = '';
				}
				
				if($desc AND $options['ShowDesc'] == '1')
				{
					$desc = strip_tags($desc, '<p><br>');
					if(strlen($desc) > 300)
					{
						$desc = substr($desc, 0, 150) . "... <a class='readmorelink' href='$link' target='$link_target'>Read More &gt;</a>";
					}
				}
				elseif($desc AND $options['ShowDesc'] == '2')
				{
					$desc = strip_tags($desc, '<p><br><strong><ol><ul><li><b><blockquote><h1><h2><h3><h4><h5><h6>');
				}
				else
				{
					$desc = '';
				}
				
				if($options['AllowTip'] == true AND $counter == $tip_random_number)
				{
					$link = str_replace($options['AssociateTag'], $options['LocaleTipTag'], $link);
				}

				if(trim($image) != "") $image_html = "<img src='$image' class='amazonpress-product-image' " .
						"alt='" . htmlentities($title, ENT_QUOTES) . "' " .
						"title='" . htmlentities($title, ENT_QUOTES) . "' />";
				else $image_html = "";

				if($options['ShowText'] AND trim($title) != "") $title_html = "<span class='amazonpress-product-title'>$title</span>";
				else $title_html = "";

				if($options['ShowDesc'] AND trim($desc) != "") $desc_html = "<span class='amazonpress-product-desc'>$desc</span>";
				else $desc_html = "";


				$result .= "<a href='$link' target='$link_target' rel='nofollow'>" . $image_html . $title_html . "</a>$desc_html\n";
				$result .= "</div>";
				
				if($counter >= $options['MaxResults']) break;
			}

			$result .= "</div>";
		}
		if($old_error_handler !== false) restore_error_handler();
		if($echo) echo $result;
		else return($result);

	}

	function wp_head()
	{
		if(!isset($this->options['StyleSheet'])) $this->options['StyleSheet'] = 'style.css';
		
		if(file_exists($this->basePath . '/css/' . $this->options['StyleSheet']))
		{
			$csspath = $this->urlPath . '/css/' . $this->options['StyleSheet'];
			?>
			<link rel='stylesheet' href='<?php echo $csspath; ?>' type='text/css' media='all' />
			<?php
		}
	}
	
	function admin_head()
	{
		$csspath = $this->urlPath . '/html/admin_styles.css';
		?>
		<link rel='stylesheet' href='<?php echo $csspath; ?>' type='text/css' media='all' />
		<?php
	}

	function wp_content($content='')
	{
		global $post;
		
		if(is_feed()) return($content);

		$params = array();
		
		$custom_keywords = get_post_meta($post->ID, '_amazonpress_keywords', true);
		
		if($this->options['Version'] > '1.0')
		{
			if(is_single() AND !$this->options['ShowOnPosts']) return($content);
			if(is_page() AND !$this->options['ShowOnPages']) return($content);
			if((is_home()) AND !$this->options['ShowOnHome']) return($content);
			if(is_category() AND !$this->options['ShowOnCategories']) return($content);
			if(is_tag() AND !$this->options['ShowOnTags']) return($content);
			if(is_search() AND !$this->options['ShowOnSearch']) return($content);
			if(function_exists('is_front_page')) if((is_front_page()) AND !$this->options['ShowOnHome']) return($content);
			
		}
		
		if( get_post_meta($post->ID, '_amazonpress_disabled', true) == "true" ) return($content);

		if(!$custom_keywords)
		{
			if($this->options['SearchFrom'] == "categories")
				$tags = get_the_category();
			else
				$tags = get_the_tags();

			if(count($tags) == 0 OR $tags == "") {
				$keywords = $this->options['DefaultTags'];
			}
			else
			{
				foreach($tags as $tag)
					$search_string[] = $tag->name;
				$keywords = implode(', ', $search_string);
			}
		}
		else
			$keywords = $custom_keywords;
		
		if( get_post_meta($post->ID, '_amazonpress_sortby', true) != '' AND get_post_meta($post->ID, '_amazonpress_sortby', true) != 'default' ) $params['SortBy'] = get_post_meta($post->ID, '_amazonpress_sortby', true);

		$result = $this->display($keywords, false, $params);

		if($this->options['DisplayPosition'] == '1')
			$content = "$result\n$content";
		else
			$content = "$content\n$result";

		return($content);
	}

	function wp_admin_init()
	{
		global $wp_version;
		
		add_management_page('AmazonPress Management', 'AmazonPress', 7, __FILE__, array(&$this, 'wp_admin_controller'));

		$this->checkInstall(false);

		if(function_exists('wp_enqueue_script'))
		{
			wp_enqueue_script('jQuery'); 
		}
	}

	function wp_admin_controller()
	{
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Access Denied'));
		
		$this->checkInstall(true);
		
		if(isset($_GET['amazonPressAdminPage']))
		{
			$action = $_GET['amazonPressAdminPage'];
			$this->adminurl = substr($_SERVER['REQUEST_URI'], 0, stripos($_SERVER['REQUEST_URI'], '&amazonPressAdminPage'));
		}
		else
		{
			$action = '';
			$this->adminurl = $_SERVER['REQUEST_URI'];
		}
			
		switch($action)
		{
			case 'message_log':
				$this->wp_admin_errors();
			break;
			
			case 'view_cache':
				$this->wp_admin_cache();
			break;
			
			case 'options':
				$this->wp_admin_options();
			break;
			
			case 'dashboard':
			default:
				$this->wp_admin_dashboard();
			break;
		}
	}
	
	function wp_admin_dashboard()
	{
		$homePath = $this->adminurl;
		include($this->basePath . "/html/dashboard.php");
	}
	
	function wp_admin_options()
	{
		$styles_folder = $this->basePath . '/css';
		$d = dir($styles_folder);
		while (false !== ($entry = $d->read())) {
			if(is_file($this->basePath . '/css/' . $entry))
			{
				$stylesheets[] = trim($entry);
			}
		}
		$d->close();
		
		
		if($_POST) {
			$post_errors = false;

			if(isset($_POST['Locale']) AND $_POST['Locale'] != $this->options['Locale'])
			{
				$_POST['ClearCacheNowv'] = 'yes';
				$this->admin_alert("Locale change detected.");
				
				switch($_POST['Locale'])
				{
					case "Canada":
						$this->options['Locale'] 		= 'Canada';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.ca/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'xxxxxxxca-20';
					break;
										
					case "China":
						$this->options['Locale'] 		= 'China';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.cn/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'xxxxxxxcn-23';
					break;

					case "Italy":
						$this->options['Locale'] 		= 'Italy';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.it/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'xxxxxxxit-21';
					break;
					
					case "Spain":
						$this->options['Locale'] 		= 'Spain';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.es/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'xxxxxxxes-21';
					break;					
					
					case "United Kingdom":
						$this->options['Locale'] 		= 'United Kingdom';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.co.uk/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'xxxxxxxuk-21';
					break;

					case "France":
						$this->options['Locale'] 		= 'France';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.fr/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'xxxxxxxfr-21';
					break;
					
					case "Germany":
						$this->options['Locale'] 		= 'Germany';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.de/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'xxxxxxxus-21';
					break;
					
					case "Japan":
						$this->options['Locale'] 		= 'Japan';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.co.jp/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'xxxxxxxjp-22';
					break;
					
					case "United States":
					default:
						$this->options['Locale'] 		= 'United States';
						$this->options['ServicePath'] 	= 'http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService';
						$this->options['LocaleTipTag'] 	= 'CJ7COUK-20';
					break;
				}
			}

			if(isset($_POST['AWSAccessKeyId']) AND eregi("^.+$", $_POST['AWSAccessKeyId']))
				$this->options['AWSAccessKeyId'] = trim($_POST['AWSAccessKeyId']);
			else
			{
				$this->admin_alert("The AWS Access Key you entered was improperly formatted.");
				$post_errors = true;
			}

			if(isset($_POST['AWSSecretAccessKeyId']) AND $_POST['AWSSecretAccessKeyId'] != '************************')
			{
				if(eregi("^.+$", $_POST['AWSSecretAccessKeyId']))
					$this->options['AWSSecretAccessKeyId'] = $this->encrypt(trim($_POST['AWSSecretAccessKeyId']));
				else
				{
					$this->admin_alert("The AWS Secret Access Key you entered was improperly formatted.");
					$post_errors = true;
				}
			}

			if(isset($_POST['AssociateTag']) AND eregi("^.+$", $_POST['AssociateTag']))
				$this->options['AssociateTag'] = trim($_POST['AssociateTag']);
			else
			{
				$this->admin_alert("The Associate Tag you entered was improperly formatted.");
				$post_errors = true;
			}

			if(isset($_POST['SearchFrom']) AND eregi("^[a-z]+$", $_POST['SearchFrom']) AND $_POST['SearchFrom'] == 'tags')
				$this->options['SearchFrom'] = 'tags';
			else
			{
				$this->options['SearchFrom'] = 'categories';
			}

			if(isset($_POST['ShowOnPosts']) AND eregi("^[a-z0-9\-]+$", $_POST['ShowOnPosts']) AND $_POST['ShowOnPosts'] == 'yes')
				$this->options['ShowOnPosts'] = true;
			else
			{
				$this->options['ShowOnPosts'] = false;
			}

			if(isset($_POST['ShowOnPages']) AND eregi("^[a-z0-9\-]+$", $_POST['ShowOnPages']) AND $_POST['ShowOnPages'] == 'yes')
				$this->options['ShowOnPages'] = true;
			else
			{
				$this->options['ShowOnPages'] = false;
			}

			if(isset($_POST['ShowOnHome']) AND eregi("^[a-z0-9\-]+$", $_POST['ShowOnHome']) AND $_POST['ShowOnHome'] == 'yes')
				$this->options['ShowOnHome'] = true;
			else
			{
				$this->options['ShowOnHome'] = false;
			}

			if(isset($_POST['ShowOnCategories']) AND eregi("^[a-z0-9\-]+$", $_POST['ShowOnCategories']) AND $_POST['ShowOnCategories'] == 'yes')
				$this->options['ShowOnCategories'] = true;
			else
			{
				$this->options['ShowOnCategories'] = false;
			}

			if(isset($_POST['ShowOnTags']) AND eregi("^[a-z0-9\-]+$", $_POST['ShowOnTags']) AND $_POST['ShowOnTags'] == 'yes')
				$this->options['ShowOnTags'] = true;
			else
			{
				$this->options['ShowOnTags'] = false;
			}

			if(isset($_POST['ShowOnSearch']) AND eregi("^[a-z0-9\-]+$", $_POST['ShowOnSearch']) AND $_POST['ShowOnSearch'] == 'yes')
				$this->options['ShowOnSearch'] = true;
			else
			{
				$this->options['ShowOnSearch'] = false;
			}
			
			
			switch($_POST['SearchIndex'])
			{
				case "Music":
					$this->options['SearchIndex'] 		= 'Music';
				break;
				
				case "Video":
					$this->options['SearchIndex'] 		= 'Video';
				break;
				
				case "Software":
					$this->options['SearchIndex'] 		= 'Software';
				break;

				case "Books":
				default:
					$this->options['SearchIndex'] 		= 'Books';
				break;
			}


			

			if(isset($_POST['DefaultTags']) AND eregi("^.*$", $_POST['DefaultTags']))
				$this->options['DefaultTags'] = $_POST['DefaultTags'];
			else
			{
				$this->admin_alert("The Default Tags you entered was improperly formatted.");
				$post_errors = true;
			}

			if(isset($_POST['TitleText']) AND eregi("^.*$", $_POST['TitleText']))
				$this->options['TitleText'] = $_POST['TitleText'];
			else
			{
				$this->admin_alert("The Title Text you entered was improperly formatted.");
				$post_errors = true;
			}

			if(isset($_POST['MaxResults']) AND eregi("^[0-9]+$", $_POST['MaxResults']) AND $_POST['MaxResults'] >= 0 AND $_POST['MaxResults'] <= 25)
				$this->options['MaxResults'] = $_POST['MaxResults'];
			else
			{
				$this->admin_alert("The Max Results must only be a number between 0 and 25.");
				$post_errors = true;
			}

			if(isset($_POST['ShowImages']) AND $_POST['ShowImages'] == 'yes')
				$this->options['ShowImages'] = true;
			else
			{
				$this->options['ShowImages'] = false;
			}

			if(isset($_POST['ImageSize']) AND is_numeric($_POST['ImageSize']))
				$this->options['ImageSize'] = $_POST['ImageSize'];
			else
			{
				$this->options['ImageSize'] = '0';
			}

			if(isset($_POST['ShowText']) AND $_POST['ShowText'] == 'yes')
				$this->options['ShowText'] = true;
			else
			{
				$this->options['ShowText'] = false;
			}

			if(isset($_POST['ShowDesc']) AND is_numeric($_POST['ShowDesc']))
				$this->options['ShowDesc'] = $_POST['ShowDesc'];
			else
			{
				$this->options['ShowDesc'] = '0';
			}

			if(isset($_POST['SortBy']) AND $this->validate['SortBy'][$_POST['SortBy']])
				$this->options['SortBy'] = $_POST['SortBy'];
			else
			{
				$this->options['SortBy'] = 'random';
			}

			if(isset($_POST['DisplayPosition']) AND $this->validate['DisplayPosition'][$_POST['DisplayPosition']])
				$this->options['DisplayPosition'] = $_POST['DisplayPosition'];
			else
			{
				$this->options['DisplayPosition'] = '0';
			}

			if(isset($_POST['LinkTarget']) AND $_POST['LinkTarget'] == '_blank')
				$this->options['LinkTarget'] = '_blank';
			else
			{
				$this->options['LinkTarget'] = '';
			}

			if(isset($_POST['StyleSheet']) AND $_POST['StyleSheet'])
			{
				if(!is_file($this->basePath . '/css/' . $_POST['StyleSheet']))
				{
					$post_errors = true;
					$this->admin_alert("The stylesheet you selected doesn't seem to exist.");
				}
				elseif(preg_match('|^[a-z0-9_\.\-]$|i', $_POST['StyleSheet'])) 
				{
					$post_errors = true;
					$this->admin_alert("The stylesheet you selected is invalid.");
				}
				else
					$this->options['StyleSheet'] = $_POST['StyleSheet'];
			}

			if(isset($_POST['CacheExpiry']) AND eregi("^[0-9]+$", $_POST['CacheExpiry']) AND $_POST['CacheExpiry'] >= 1 AND $_POST['CacheExpiry'] <= 43200)
				$this->options['CacheExpiry'] = $_POST['CacheExpiry'];
			else
			{
				$this->admin_alert("The Cache Expiry Minutes must only be a number between 15 and 43200 (30 days).");
				$post_errors = true;
			}
			
			if(isset($_POST['ClearCacheNow']) AND eregi("^[a-z0-9\-]+$", $_POST['ClearCacheNow']) AND $_POST['ClearCacheNow'] == 'yes')
			{
				$sql = "TRUNCATE " . $this->table_cache;
				global $wpdb;
				$wpdb->query($sql);
				$this->admin_alert("The database cache has been cleared of all items.");
			}

			if(isset($_POST['AllowTip']) AND eregi("^[a-z0-9\-]+$", $_POST['AllowTip']) AND $_POST['AllowTip'] == 'yes')
			{
				if($this->options['AllowTip'] == false) $this->admin_alert("Thank you for your support and generosity.");
				$this->options['AllowTip'] = true;
			}
			else
			{
				$this->options['AllowTip'] = false;
			}

			if(!$post_errors)
			{
				update_option('amazonPressOptions', $this->options);
				$this->admin_alert("Options saved!");
			}
		}

		if(!isset($this->options['AWSSecretAccessKeyId'])
			AND time() < 1250294400)
		{
			$this->admin_alert("ALERT: Your AWS Secret Access Key will be required for this plugin to continue working after Aug. 15, 2009 due to recent changes with the Amazon system.");
		}
		
		if(!isset($this->options['ShowDesc'])) $this->options['ShowDesc'] = '0';
		
		if(!isset($this->options['ImageSize']) or !is_numeric($this->options['ImageSize']))  {
			if($this->options['ShowImages'] == true)
				$this->options['ImageSize'] = '1';
			else
				$this->options['ImageSize'] = '0';
		}
		
		if(!isset($this->options['LinkTarget'])) $this->options['LinkTarget'] = '';
		if(!isset($this->options['SortBy'])) $this->options['SortBy'] = '0';
		if(!isset($this->options['DisplayPosition'])) $this->options['DisplayPosition'] = '0';
		
		$homePath = $this->adminurl;
		
		include($this->basePath . "/html/options.php");
	}
	
	function wp_admin_errors()
	{
		global $wpdb;
		$homePath = $this->adminurl;
		
		if(isset($_GET['clear_log']) AND $_GET['clear_log'] == 'true')
		{
			$sql = "TRUNCATE `" . $this->table_log . "`;";
			$wpdb->query($sql);
		}
		
		$sql = "SELECT COUNT(*) FROM " . $this->table_log;
		$count = $wpdb->get_results($sql, ARRAY_A);
		$total = $count[0]['COUNT(*)'];

		$page = 1;
		$start = 0;
		$limit = 20;
		$page_navigation = $this->display_paged_nav($total, false, $limit);
		
		if(isset($_GET['pageNumber']) AND is_numeric($_GET['pageNumber']) AND $_GET['pageNumber'] > 0) $page = $_GET['pageNumber'];
		
		$start = $limit * ($page-1);
		if($start > $total) {
			$start = 0;
			$page = 1;
		}
		
		$sql = "SELECT * FROM " . $this->table_log . " ORDER BY `id` DESC LIMIT $start,$limit";
		$errors = $wpdb->get_results($sql);

		include($this->basePath . "/html/error_log.php");
	}

	function wp_admin_cache()
	{
		global $wpdb;
		$homePath = $this->adminurl;
		$show_default = false;
		
		if(isset($_GET['action']))
			$action = $_GET['action'];
		else
			$action = '';
		
		
		switch($action)
		{
			case 'block':
				if(isset($_GET['asin']) AND isset($_GET['keyword']))
				{
					$this->block($_GET['keyword'], $_GET['asin']);
				}
				
				$this->admin_alert('Disabled product.');
				$show_default = true;
			break;
			
			case 'unblock':
				if(isset($_GET['asin']) AND isset($_GET['keyword']))
				{
					$this->unblock($_GET['keyword'], $_GET['asin']);
				}
				
				$this->admin_alert('Enabled product.');
				$show_default = true;
			break;
			
			case 'clear_cache':
				$sql = "SELECT COUNT(*) FROM " . $this->table_cache;
				$count = $wpdb->get_results($sql, ARRAY_A);
				$total = $count[0]['COUNT(*)'];
				include($this->basePath . "/html/cache_clear.php");
			break;
			
			case 'clear_cache_confirm':
				$sql = "TRUNCATE " . $this->table_cache;
				$wpdb->query($sql);
				$this->admin_alert("The database cache has been cleared of all items.");
			
			default:
				$show_default = true;
			break;
		}
		
		if($show_default == true)
		{
				$sql = "SELECT COUNT(*) FROM " . $this->table_cache;
				$count = $wpdb->get_results($sql, ARRAY_A);
				$total = $count[0]['COUNT(*)'];
		
				$page = 1;
				$start = 0;
				$limit = 5;
				$page_navigation = $this->display_paged_nav($total, false, $limit);
				
				if(isset($_GET['pageNumber']) AND is_numeric($_GET['pageNumber']) AND $_GET['pageNumber'] > 0) $page = $_GET['pageNumber'];
				
				$start = $limit * ($page-1);
				if($start > $total) {
					$start = 0;
					$page = 1;
				}
				
				$sql = "SELECT * FROM " . $this->table_cache . " ORDER BY `keyword` LIMIT $start,$limit";
				$cache = $wpdb->get_results($sql, ARRAY_A);
				
				include($this->basePath . "/html/cache.php");
		}
	}
	
	function display_paged_nav($num_results, $show=true, $num_per_page=10)
	{
		$url = $_SERVER['REQUEST_URI'];
		$output = 'Page: ';
		
		if(preg_match('#^([^\?]+)(.*)$#isu', $url, $regs))
			$url = $regs[1];
		
		$q = $_GET;
		
		if(isset($q['pageNumber'])) $page = $q['pageNumber'];
		else $page = 1;
		$total_pages = ceil($num_results / $num_per_page);
		
		for($i=1; $i<=$total_pages; $i++)
		{
			$q['pageNumber'] = $i;
			$tmp = array();
			foreach($q as $key=>$value)
				$tmp[] = "$key=$value";
			$qvars = implode("&", $tmp);
			$new_url = $url . '?' . $qvars;
			
			if($i != $page)
			{
				if($i == $page-1
					OR $i == $page+1
					OR $i == 1
					OR $i == $total_pages
					OR $i == floor($total_pages/2)
					OR $i == floor($total_pages/2)+1
					)
					{
						$output .= "<a href='$new_url'>$i</a> ";
					}
					else
						$output .= '. ';
			}
			else
				$output .= "<strong>$i</strong> ";
		}
		
		$output = ereg_replace('(\. ){2,}', ' .. ', $output);
		if($show) echo $output;
		return($output);
		
	}
	
	function add_custom_box()
	{
		if( function_exists( 'add_meta_box' )) {

			add_meta_box( 'amazonpress_sectionid', 'Amazon Products Feed',
						array(&$this, 'inner_custom_box'), 'post', 'advanced' );

			add_meta_box( 'amazonpress_sectionid', 'Amazon Products Feed',
						array(&$this, 'inner_custom_box'), 'page', 'advanced' );

		} else {
			add_action('dbx_post_advanced', array(&$this, 'old_custom_box') );
			add_action('dbx_page_advanced', array(&$this, 'old_custom_box') );
		}
	}

	function inner_custom_box() {
		global $post;
		$amazonpress_keywords = get_post_meta($post->ID, '_amazonpress_keywords', true);
		$amazonpress_disabled = (get_post_meta($post->ID, '_amazonpress_disabled', true) == 'true') ?  'checked' : '';
		$amazonpress_sortby = (get_post_meta($post->ID, '_amazonpress_sortby', true)) ? get_post_meta($post->ID, '_amazonpress_sortby', true) : 'default';
		
		include($this->basePath . "/html/postmeta_edit_box.php");
	}

	function old_custom_box() {
		?>
		<div class="dbx-box-wrapper">
			<fieldset id="amazonpress_fieldsetid" class="dbx-box">
				<div class="dbx-handle-wrapper">
					<h3 class="dbx-handle">Amazon Products Feed</h3>
				</div>

				<div class="dbx-c-ontent-wrapper">
					<div class="dbx-content">

					<?php $this->inner_custom_box(); ?>

					</div>
				</div>
			</fieldset>
		</div>
		<?php
	}

	function save_postdata( $post_id ) {

		if(!isset($_POST['post_type']))
			$_POST['post_type'] = false;
		
		
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ))
				return $post_id;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ))
				return $post_id;
		}

		if(isset($_POST['amazonpress_keywords'])) $amazonpress_keywords = $_POST['amazonpress_keywords'];
		else $amazonpress_keywords = '';
		
		if(isset($_POST['amazonpress_disabled'])) $amazonpress_disabled = $_POST['amazonpress_disabled'];
		else $amazonpress_disabled = '';

		if(isset($_POST['amazonpress_sortby'])) $amazonpress_sortby = $_POST['amazonpress_sortby'];
		else $amazonpress_sortby = 'default';

		add_post_meta($post_id, '_amazonpress_keywords', $amazonpress_keywords, true)
			or update_post_meta($post_id, '_amazonpress_keywords', $amazonpress_keywords);

		add_post_meta($post_id, '_amazonpress_disabled', $amazonpress_disabled, true)
			or update_post_meta($post_id, '_amazonpress_disabled', $amazonpress_disabled);

		add_post_meta($post_id, '_amazonpress_sortby', $amazonpress_sortby, true)
			or update_post_meta($post_id, '_amazonpress_sortby', $amazonpress_sortby);

		return $post_id;
	}
	
	function aws_signed_request($region, $params, $public_key, $private_key)
	{
	    /*
	    Copyright (c) 2009 Ulrich Mierendorff
	
	    Permission is hereby granted, free of charge, to any person obtaining a
	    copy of this software and associated documentation files (the "Software"),
	    to deal in the Software without restriction, including without limitation
	    the rights to use, copy, modify, merge, publish, distribute, sublicense,
	    and/or sell copies of the Software, and to permit persons to whom the
	    Software is furnished to do so, subject to the following conditions:
	
	    The above copyright notice and this permission notice shall be included in
	    all copies or substantial portions of the Software.
	
	    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
	    THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
	    DEALINGS IN THE SOFTWARE.
	    */
	    
	    $method = "GET";
	    $host = "webservices.amazon.".$region;
	    $uri = "/onca/xml";
	    
	    $params["Service"] = "AWSECommerceService";
	    $params["AWSAccessKeyId"] = $public_key;
	    $params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
	    $params["Version"] = "2011-08-01";
 	    
	    ksort($params);
	    
	    $canonicalized_query = array();
	    foreach ($params as $param=>$value)
	    {
	        $param = str_replace("%7E", "~", rawurlencode($param));
	        $value = str_replace("%7E", "~", rawurlencode($value));
	        $canonicalized_query[] = $param."=".$value;
	    }
	    $canonicalized_query = implode("&", $canonicalized_query);
	    
	    if($private_key)
	    {
		    $string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;
		    
		    $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $this->decrypt($private_key), True));
		    
		    $signature = str_replace("%7E", "~", rawurlencode($signature));
		    
	    	$request = "http://".$host.$uri."?".$canonicalized_query."&Signature=".$signature;
	    }
	    else
	    	$request = "http://".$host.$uri."?".$canonicalized_query;
	    		    
		if(function_exists('file_get_contents'))
			$response = file_get_contents($request);
		
		if(!$response)
		{
			$response = $this->getpath($request);
		}
		
		$this->done_request = true;

	    if ($response === False)
	    {
	        return False;
	    }
	    else
	    {
	    	return($response);
	    }


	}
	
	function encrypt($ptext)
	{
		$key = $this->encKey;
		$ptext = trim($ptext);
		if($ptext == "") return(base64_encode($ptext));
		
	   	srand((double) microtime() * 1000000); //for sake of MCRYPT_RAND
	   	$key = md5($key); //to improve variance
		$td = mcrypt_module_open('rijndael-128', '','cbc', '');
	  	$key = substr($key, 0, mcrypt_enc_get_key_size($td));
	  	$iv_size = mcrypt_enc_get_iv_size($td);
	  	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	   	if (mcrypt_generic_init($td, $key, $iv) != -1) {
			/* Encrypt data */
	     	$c_t = mcrypt_generic($td, $ptext);
	     	mcrypt_generic_deinit($td);
	     	mcrypt_module_close($td);
	      	$c_t = $iv.$c_t;				// we are including iv (fixed size) in the encrypted message
	       	return base64_encode($c_t);
	   } //end if
	}
	
	function decrypt($etext) {
		$key = $this->encKey;
		$etext =  base64_decode($etext);
		if($etext == "") return($etext);
		
		$key = md5($key); //to improve variance
	  	$td = mcrypt_module_open('rijndael-128', '','cbc', '');
	  	$key = substr($key, 0, mcrypt_enc_get_key_size($td));
	  	$iv_size = mcrypt_enc_get_iv_size($td);
	  	$iv = substr($etext,0,$iv_size);	// extract iv
	  	$etext = substr($etext,$iv_size);
	   	if (mcrypt_generic_init($td, $key, $iv) != -1) {
		     /* Encrypt data */
	    	$c_t = mdecrypt_generic($td, $etext);
	     	mcrypt_generic_deinit($td);
	     	mcrypt_module_close($td);
	       	return trim($c_t);
	   } //end if
	}
	
	function widget_init()
	{
		if ( !function_exists('register_sidebar_widget') )
			return;
		
		register_sidebar_widget('AmazonPress Widget',
			array(&$this, 'widget_display'));
			
		register_widget_control('AmazonPress Widget', array(&$this, 'widget_config'));
	}

	function widget_display($args) 
	{
		extract($args);
		
		$options = $this->options['WidgetOptions'];
		$options['class'] = 'amazonpresswidget';
		
		$title = $options['Title'];
		$content = $this->display($options['DefaultTags'], false, $options);
		
		echo $before_widget, $content, $after_widget;
	}
	
	function widget_config()
	{
		$options = $this->options['WidgetOptions'];
		if(!isset($options['TitleText']) OR !$options['TitleText']) $options['TitleText'] = $this->options['TitleText'];
		if(!isset($options['DefaultTags']) OR !$options['DefaultTags']) $options['DefaultTags'] = $this->options['DefaultTags'];
		if(!isset($options['MaxResults']) OR !$options['MaxResults']) $options['MaxResults'] = $this->options['MaxResults'];
		if(!isset($options['ImageSize'])) $options['ImageSize'] = $this->options['ImageSize'];
		if(!isset($options['ShowText'])) $options['ShowText'] = $this->options['ShowText'];
		if(!isset($options['ShowDesc'])) $options['ShowDesc'] = $this->options['ShowDesc'];
		if(!isset($options['SortBy'])) $options['SortBy'] = $this->options['SortBy'];
		
		if(isset($_POST['amazonpresswidget-submit']) AND $_POST['amazonpresswidget-submit'] == '1')
		{
			if(isset($_POST['amazonpress-title']) AND $_POST['amazonpress-title'] != '') $options['TitleText'] = $_POST['amazonpress-title'];
			if(isset($_POST['amazonpress-tags']) AND $_POST['amazonpress-tags'] != '') $options['DefaultTags'] = $_POST['amazonpress-tags'];
			if(isset($_POST['amazonpress-results']) AND $_POST['amazonpress-results'] != '') $options['MaxResults'] = $_POST['amazonpress-results'];
			
			if(isset($_POST['amazonpress-image']) AND is_numeric($_POST['amazonpress-image'])) $options['ImageSize'] = $_POST['amazonpress-image']; else $options['ImageSize'] = '0';
			if(isset($_POST['amazonpress-text']) AND $_POST['amazonpress-text'] == 'yes') $options['ShowText'] = '1'; else $options['ShowText'] = '0';
			if(isset($_POST['amazonpress-desc']) AND $_POST['amazonpress-desc'] == 'yes') $options['ShowDesc'] = '1'; else $options['ShowDesc'] = '0';
			if(isset($_POST['amazonpress-sortby']) AND $this->validate['SortBy'][$_POST['amazonpress-sortby']]) $options['SortBy'] = $_POST['amazonpress-sortby']; else $options['SortBy'] = $this->options['SortBy'];
			
			$this->options['WidgetOptions'] = $options;
			update_option('amazonPressOptions', $this->options);
		}
		
		include($this->basePath . "/html/widget_edit_box.php");
	}
	
}
}