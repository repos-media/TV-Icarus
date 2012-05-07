<?php  define('TVic', 1);

/**
 * TV-Icarus Coded by TuxLyn
 * version 0.1 {2012-05-07}
 * Website: http://GoTux.net/
 * Email: TuxLyn[@]Gmail.com
 **
 * License: docs/license.txt
 */

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	error_reporting(E_ALL|E_STRICT);
	ini_set('display_errors', 'On');

	function pre($array) {
		return '<pre>'.print_r($array,1).'</pre>';
	} #pre

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	define('root', dirname(__FILE__).'/');
	define('news', 'http://www.tvrage.com/news/rss.php');
	define('guide', 'http://services.tvrage.com/feeds/countdown.php');
	define('shows', 'http://services.tvrage.com/feeds/show_list_letter.php?letter=');
	define('episode', 'http://services.tvrage.com/feeds/full_show_info.php?sid=');
	define('search', 'http://services.tvrage.com/feeds/search.php?show=');
	
	require(root.'init.php'); // initialize bootstrap

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// OUTPUT: header + page with content + footer
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	require(root.'pages/header.php');

	switch (@config('page')) {
		case 'news': news(); break;
		case 'guide': guide(); break;
		case 'shows': shows(); break;
		case 'episode': episode(); break;
		case 'search': search(); break;
		default: news(); break;
	} #switch

	require(root.'pages/footer.php');

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
## EOF: ./index.php
