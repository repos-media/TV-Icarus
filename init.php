<?php  if(!defined('TVic')) exit('Direct file access not allowed.');

/**
 * TV-Icarus Coded by TuxLyn
 * Website: http://GoTux.net/
 * Email: TuxLyn[@]Gmail.com
 **
 * License: docs/license.txt
 */

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	function config($item) {
		require(root.'config.php');
		if (!isset($config[$item]))
			{ return FALSE; }
		return $config[$item];
	} #config

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// URL Functions, that only allows 1-Page and 1-Variable.
	function uri($n=1) {
		$uri = array_filter(explode('/', $_SERVER['REQUEST_URI']));
		return isset($uri[$n]) ? $uri[$n] : $uri;
	} #uri

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// Breadcrumbs
	// add support for 'episode' url, show->name instead of show->id.
	function bc() {
		$path = array_filter(explode('/', $_SERVER['REQUEST_URI']));
		$base = config('base'); $bc = array("<a href=\"$base\">Home</a>");
		$last = array_keys($path); $last = end($last);		
		foreach ($path as $x => $crumb) { $title = ucwords($crumb);
			($x != $last) ? $bc[] = "<a href=\"$base\">$title</a>" : $bc[] = $title;
		} return implode(' &raquo; ', $bc);
	} #bc

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// Breadcrumbs - Last Item (id) for episodes
	function crumb() {
		$bc = explode('&raquo;', bc());
		$bc = strlen($bc[2]-1);
		switch ($bc) {
			case 1: return substr(bc(),0,-1); break;
			case 2: return substr(bc(),0,-2); break;
			case 3: return substr(bc(),0,-3); break;
			case 4: return substr(bc(),0,-4); break;
			case 5: return substr(bc(),0,-5); break;
			default: return bc(); break;
		} #switch
	} #crumb

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	function menu() {
		$pages = array('News' => 'news', 'Guide' => 'guide',
			'Shows' => 'shows', 'Search' => 'search');
		$custom = config('custom');
		$base = config('base');
		echo '<ul>';
		while (list($title, $link) = each($pages)) {
			echo "<li> <a href=\"$base$link\">$title</a> </li>";
		} #while-pages
		if (is_array($custom) && isset($custom)) {
			echo '<br />';
			while (list($title, $link) = each($custom)) {
				echo "<li> <a href=\"$base$link\">$title</a> </li>";
			} #while-custom
		} #is_array-custom
		echo '</ul>';
	} #menu

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	function nav() {
		echo '<ul id="qb">';
		/* foreach (range('0', '9') as $i)
			{ echo '<li><a href="'.config('base').'shows/'.
				strtolower($i).'">'.$i.'</a></li>'; } */
		foreach (range('A', 'Z') as $i)
			{ echo '<li><a href="'.config('base').'shows/'.
				strtolower($i).'">'.$i.'</a></li>'; }
		echo '</ul>';
	} #nav

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	function safe($str) {
		$str = strip_tags($str);
		$str = stripslashes($str);
		$str = htmlspecialchars($str);
		$str = preg_replace('#<!\[CDATA\[.*?\]\]>#s', '', $str);
		return $str;
	} #safe

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	function cache($file, $time, $url) {
		if (file_exists($file) && (time() - 
			$time < filemtime($file))) {
			if (file_exists($file) && 
				filesize($file) == 0) {
				$content = file_get_contents($url);
				$xml = fopen($file, 'w');
				fwrite($xml, $content);
				fclose($xml);
				$xml = simplexml_load_file($file);
			} else {
				//where done, just grab xml from cache =)
				$xml = simplexml_load_file($file);
			} #file
		} else { // grab fresh xml
			$content = file_get_contents($url);
			$xml = fopen($file, 'w');
			fwrite($xml, $content);
			fclose($xml);
			$xml = simplexml_load_file($file);
		} #xml
		return $xml;
	} #cache

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	function pages($pg) {
		$file = root."pages/$pg.php";
		if (file_exists($file)) {
			$content = safe($file);
			include($content);
		} else {
			echo 'Error 404: No such page.';
		} #file_exists
	} #page

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function news() {
	$file = root.'cache/news.xml';
	$xml = cache($file, '720*60', news);

echo <<<HTML
<h3 class="title round3"> News </h3>
<article class="round2 shadow2">
HTML;

foreach ($xml->xpath('//item') as $item) {
	$title = safe($item->title);
	$desc = safe($item->description);
	$link = $item->link;
	$date = date('l, F d, Y', strtotime($item->pubDate));

echo <<<HTML
  <section>
    <small> $date </small>
    <h4> <a href="$link">$title</a> </h4>
    <p> $desc </p>
  </section>
HTML;

} #foreach
echo '<small> News Last Updated on '.
	date('M d, Y', filemtime($file)).' </small>';
} #news

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function guide() {
	$base = config('base');
	$file = root.'cache/countdown.xml';
	$xml = cache($file, '3600*24', guide);
		
		echo "<h3 class=\"title round3\"> Guide </h3>
		  <article class=\"round2 shadow2\"> <table id=\"shows\">";

foreach ($xml->xpath('//country') as $country) {
	$name = $country['name'];

echo <<<TH
  <tr>
    <th class="country show"> $name Shows </th> <th> Info </th>
    <th class="name"> Titles </th> <th> Episodes </th> 
    <th> Airtime </th> <th> Relative </th>
  </tr>
TH;

foreach ($country->show as $show) {				
	$sid = $show->showid;
	$name = $show->showname;
	$link = $show->showlink; //todo

echo <<<SHOW
  <td class="show"> <a href="{$base}episode/$sid">$name</a> </td>
  <td> <a href="$link"><img src="$base/images/tvrage.png" alt="TVRage" /></a> </td>
SHOW;

foreach ($show->upcomingep as $upc) {
	$link = $upc->link;
	$title = $upc->title;
	$epnum = $upc->epnum;
	$date = $upc->airdate;
	$rdate = $upc->relativedate;

echo <<<TD
  <td class="name"> $title </td> <td> $epnum </td>
  <td> $date </td> <td> $rdate </td> </tr>
TD;

} #foreach-upc
} #foreach-show
} #foreach-country
echo '</table> <p class="info"> For more detailed countdown please see: 
	<a href="http://tv-schedule.com/">TV-Schedule.com</a> </p>';
} #guide

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function shows() {

	$base = config('base');
	$args = config('args');
	$bc = bc();
	
echo <<<HTML
<h3 class="title round3"> $bc </h3>
<article class="round2 shadow2">
HTML;
		
	nav(); if (isset($args)) {
		$file = root."cache/lists/$args-showlist.xml";
		$xml = cache($file, '3600*168', shows.$args);
	} else {
		$file = root.'cache/lists/a-showlist.xml';
		$xml = cache($file, '3600*168', shows.'a');
	} #args

echo <<<TH
<table id="shows">
  <th class="show"> Shows </th>
  <th> Country </th>
  <th class="status"> Status </th>
TH;

foreach ($xml->xpath('///show') as $show) {
	$name = $show->name;
	$sid = $show->id;
	$country = $show->country;
	$status = $show->status;

	switch($show->status) {
		case 1: $status = 'Returning Series'; break;
		case 2: $status = 'Canceled/Ended'; break;
		case 3: $status = 'TBD/On The Bubble'; break;
		case 4: $status = 'In Development'; break;
		case 7: $status = 'New Series'; break;
		case 8: $status = 'Never Aired'; break;
		case 9: $status = 'Final Season'; break;
		case 10: $status = 'On Hiatus'; break;
		case 11: $status = 'Pilot Ordered'; break;
		case 12: $status = 'Pilot Rejected'; break;
		default: $status = 'Unknown'; break;
	} #switch

echo <<<TR
  <tr>
    <td class="show"> <a href="{$base}episode/$sid">$name</a> </td>
    <td> $country </td> <td class="status"> $status </td>
  </tr>
TR;

} #foreach-show
echo '</table>';
} #shows

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function episode() {

	$base = config('base');
	$args = config('args');

	$file = root."cache/eps/$args-eplist.xml";
	$xml = cache($file, '3600*24', episode."$args-eplist.xml");

	$title = $xml->xpath('/Show/name');
	$back = strtolower(substr($title[0],0,1));

echo <<<HTML
<h3 class="title round3"> <a href="$base">Home</a> 
&raquo; <a href="{$base}shows/$back">Shows</a>
&raquo; {$title[0]} </h3>
<article class="round2 shadow2">
HTML;

foreach ($xml->xpath('/Show') as $show) {	
	$name = $show->name;
	$tssn = $show->totalseasons;
	$sid = $show->showid;
	$link = $show->showlink;
	$start = $show->started;
	$ended = ($show->ended == 0) ? 'n/a' : $show->ended;
	$image = $show->image;
	$country = $show->origin_country;
	$status = $show->status;
	$class = $show->classification;
	$genre = $show->genres->genre;
	$genre = ($genre !='') ? $genre : 'n/a';
	$runtime = $show->runtime;
	$network = $show->network;
	$airtime = ($show->airtime !='') ? $show->airtime : 'n/a';
	$airday = ($show->airday !='') ? $show->airday : 'n/a';
	$timezone = $show->timezone;

echo <<<SHOW
  <a href="$link"><img src="$image" alt="$name" id="cover" /></a>
  <div id="box">
  	<table id="info">
  	  <tr> <th colspan="4">
  	    <h3> <a href="$link">$name</a> </h3>
  	  </th> </tr>
  	  <tr> <th> Country: </th> <td> $country </td>
  	    <th> Class: </th> <td> $class </td> </tr>
  	  <tr> <th> Network: </th> <td> $network </td>
  	    <th> Genre: </th> <td> $genre </td> </tr>
  	  <tr> <th> Seasons: </th> <td> $tssn </td>
  	    <th> AirTime: </th> <td> $airtime </td> </tr>
  	  <tr> <th> Runtime: </th> <td> $runtime </td>
  	    <th> AirDay: </th> <td> $airday </td> </tr>
  	  <tr> <th> Started: </th> <td> $start </td>
  	    <th> Timezone: </th> <td> $timezone </td> </tr>
  	  <tr> <th> Ended: </th> <td> $ended </td>
  	    <th> Status: </th> <td> $status </td> </tr>
  	</table>
  </div> <!--#box-->

<link rel="stylesheet" href="{$base}scripts/smoothness/jquery-ui.css" />
<script src="{$base}scripts/jquery-min.js"></script>
<script src="{$base}scripts/jquery-ui-min.js"></script>
<script src="{$base}scripts/effects.js"></script>
<script>accordion();</script>

<div id="accordion">
SHOW;

foreach ($xml->xpath('////Season') as $list) {
	
echo <<<TH
<h3> &raquo; Season {$list['no']} </h3>
<div>
<table id="shows">
  <tr>
    <th> # </th>
    <th class="name"> Title </th>
    <th> Episode </th>
    <th> AirDate </th>
  </tr>
TH;
		
foreach ($list as $eps) {
	$epnum = $eps->epnum;
	$snnum = $eps->seasonnum;
	//$prodnum = $eps->prodnum;
	$airdate = $eps->airdate;
	$link = $eps->link;
	$title = $eps->title;
			
echo <<<HTML
  <tr>
  <td> $epnum </td>
  <td class="name"> <a href="$link">$title</td> </td>
  <td> {$list['no']}x$snnum </td>
  <td> $airdate </td>
  </tr>
HTML;

} #foreach-episode

echo '</table> <!--#shows--> </div>';

} #foreach-list

echo '</div> <!--#accordion-->';

} #foreach-show
} #episode

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function search() {
	$base = config('base');

echo <<<HTML
<h3 class="title round3"> Search </h3>
<article class="round2 shadow2">
  <div id="msg">
    <p> Type your favorite show keyword in the search box and click submit. </p>
    <p> Please note that searches done remotely and may take a few seconds. </p>
  </div> <!--#msg-->
  <form action="#" method="post" id="search">
    <p> Search for your favorite show: </p>
    <p> <input type="hidden" name="submitted" />
    <input type="text" name="show" maxlength="50" />
    <input type="submit" value="Search" /> </p>
  </form>
<table id="shows">
HTML;

if ( !empty($_POST['show']) && isset($_POST['show']) 
	&& isset($_POST['submitted']) ) {
	$string = safe($_POST['show']);
	$xml = simplexml_load_file(search.$string);

echo <<<TH
  <tr>
    <th class="show"> Show Name </th> <th> Info </th> <th> Genre </th> 
    <th> Country </th> <th> Seasons </th> <th> Started </th> 
    <th> Ended </th> <th class="status"> Status </th>
  </tr>
TH;

foreach ($xml->show as $show) {
	$sid = $show->showid;
	$name = $show->name;
	$link = $show->link;
	$country = $show->country;
	$started = $show->started;
	$ended = $show->ended;
	$ended = ($ended == 0) ? 'n/a' : $ended;
	$seasons = $show->seasons;
	$status = $show->status;
	$class = $show->classification;
	$genre = $show->genres->genre;
	$genre = ($genre !='') ? $genre : 'n/a';
	
echo <<<TR
  <tr>
    <td class="show"> <a href="{$base}episode/$sid">$name</a> </td>
    <td> <a href="$link"><img src="$base/images/tvrage.png" alt="TVRage" /></a> </td>
    <td> $genre </td> <td> $country </td> <td> $seasons </td> <td> $started </td> 
    <td> $ended </td> <td class="status"> $status </td>
  </tr>

TR;

} #foreach-show
} #isset-show-submit
echo "</table> <!--#shows-->\n";
} #search

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	function base() { echo config('base'); }
	
	function title() {
		$title = config('title');
		echo ($title !='') ? $title : 'TV-Icarus';
	} #title
	
	function tagline() {
		$tagline = config('tagline');
		echo ($tagline !='') ? $tagline : 'Your own tv shows guide website.';
	} #tagline
	
	function pgtitle() {
		$path = array_filter(explode('/', $_SERVER['REQUEST_URI']));
		$pgtitle = isset($path[1]) ? ' | '.ucfirst($path[1]) : '';
		echo $pgtitle;
	} #title

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

## EOF: ./init.php
