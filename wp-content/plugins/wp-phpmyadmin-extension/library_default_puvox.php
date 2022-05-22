<?php
/**
 *		 #################################################################
 *		 ############### Puvox.software [ Coder: T.Todua ] ############### 
 *		 ########## Base Library & Classes for all our plugins. ##########
 *		 #################################################################
 *     Due to large amount of our plugins, in order to achieve the reliable and secure code, we decided to have a central library file for our development, where we put all our every-day functions or some only-for-testing methods, which could be used by our developers on-the-fly.
 *
 *	There are three classes:
 *		1- Library of useful PHP functions
 *		2- Library of useful WP-specific functions
 *		3- Main base for our plugins (necessary initializations,hooks & etc)
 *
 *  P.S: We even advise every plugin developer to have their own re-usable library(class) file. You can even copy this library to build yours. 
 ***************
 *
 * @package   Puvox.software - reusable PHP class
 * @author    T.Todua <support@puvox.software>
 * @license   GPL-3.0+
 * @link      https://puvox.software
 * @copyright Puvox.software
 *
*/

namespace Puvox;
if (! defined('Puvox_namespace')) { define('Puvox_namespace', true);





//==========================================================================================================
//==========================================================================================================
//==================================== 1) Library of typical PHP functions  ================================
//==========================================================================================================
//==========================================================================================================

#region 1

// Only used if there exists any your custom 'initial_class'
if (file_exists($extendfile=__DIR__.'/library_extra_puvox.php')) include_once($extendfile);
if (!class_exists('\Puvox\initial_class'))  { class initial_class{} }


class standard_php_library extends initial_class
{ 
	public function __construct()
	{ 
		$this->init_defaults();
	}
	
	public function property($propertyName) 		{ return property_exists($this, $propertyName) ? $this->{$propertyName} : null; }
	public function definedTRUE($var)				{ return (defined($var) && constant($var)); }	
	public function definedVALUE($var,$value=NULL)	{ return (defined($var) ? constant($var) : (!is_null($value) ? $value : false ) ); }
	public function globalTRUE($var,$value=NULL)	{ return (array_key_exists($var,$GLOBALS) && $GLOBALS[$var]); }
	public function globalVALUE($var,$value=NULL)	{ return (array_key_exists($var,$GLOBALS) ? $GLOBALS[$var] : (!is_null($value) ? $value : false) ); }

	//WP immitations
	public function add_filterX($a=null,$b=null,$c=null,$d=null)	{if(function_exists('add_filter')) 		return add_filter($a,$b,$c,$d);  	}
	public function add_actionX($a=null,$b=null,$c=null,$d=null)	{if(function_exists('add_action')) 		return add_action($a,$b,$c,$d);  	}
	public function add_shortcodeX($a=null,$b=null,$c=null,$d=null)	{if(function_exists('add_shortcode'))	return add_shortcode($a,$b,$c,$d);  }
	public function constantX($var){return $this->definedVALUE($var);}
	public function print_r($obj,$silent=true){ return print_r($obj, $silent); }
	public function v($obj){ echo '<pre>'; var_dump($obj); echo '</pre>'; }
	public function vv($obj){
		$out = ''; 
		$out .= '<pre>'; 
		$content = $this->jsonPretty($obj); //print_r($obj, true);
		$out .= htmlentities( $this->br2nl( $content )) ;  
		try{ 	
			$trace = debug_backtrace();
			if ( isset($trace[1]) )
			{
				$file=isset($trace[1]['file']) ? basename($trace[1]['file']) : '';
				$line=isset($trace[1]['line']) ? basename($trace[1]['line']) : '';
				
				$out .= ($this->isCron() ? " [$file:$line]" : '<span style="font-size:0.6em; margin:1px; padding:1px; background:pink;">'.$file.':'.$line.'</span>'); 
			}
		}
		catch(\Exception $e){} 
		$out .= '</pre>'; 
		return $out;
	} 
	public function var_dump($obj, $echo=true){ 
		if (is_a($obj,'Exception')) $obj = $this->ExceptionMessage($obj);
		$out= $this->vv($obj) ; 
		if ($echo) {echo $out."\n";} else return $out; 
	}
	public function var_dumpx($obj){ $this->var_dump($obj); exit; }
	public function vx($obj){ $this->vv($obj); exit; }
	public function br2nl($string) { return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string); }
	
	public function ExceptionMessage($ex, $extended=true){ 	return "Exception Message: {$ex->getMessage()} \r\n[{$ex->getTraceAsString()}] \r\n";	}  //[{$ex->getFile()}::{$ex->getLine()}]
	// ###################### //
	
	public static function sleep($seconds){ 
		if ( self::swoole_inside_coroutine() ) 
			\Swoole\Coroutine\System::sleep($seconds); 
		else {
			if ( filter_var($seconds, FILTER_VALIDATE_INT) !== false)
				sleep($seconds);
			else
				usleep($seconds*1000000);
		} 
	}
	public function usleep($milliseconds){ if (self::swoole_inside_coroutine()) \Swoole\Coroutine\System::sleep($milliseconds/1000000); else usleep($milliseconds); }

	public function force_https(){
		if(!$this->is_https) {  header("Location: https://" . $this->domainReal . $_SERVER["REQUEST_URI"], true, 301); exit;  }
	}
	public function string_to_truefalse($string) { return ( $string ==='true' ? true : ($string ==='false' ? false : $string)); }
	public function truefalse_to_string($string) { return ( $string === true ? 'true' : ($string ===false ? 'false' : $string)); }
	public function bool_to_sign($string) { return ( $string===true ||  $string==="true" ? 1 : ( $string===false || $string==="false" ? -1 : 0) ); }

	public function jsonPretty($array_or_txt, $reparse=false) {  
		if(is_string($array_or_txt)) { 
			if ( $reparse && $this->is_JSON($array_or_txt)) 
				return json_decode($array_or_txt, JSON_PRETTY_PRINT); 
			return $array_or_txt;
		}
		else 
			return $this->stringify($array_or_txt, true);
	}
	public function stringify($data, $pretty=false) { 
		if( $this->is_simple_type($data) ) 
			return (!is_bool($data) ? $data : ($data? 'true':'false') );
		else{
			if ( is_a($data, 'Exception') || is_a($data, 'Exception') ){  
				return print_r($data,true);  //exception (https://pastebin_com/P73cgSkq) are only handled well by this
			}
			else{
				return ( $pretty ? json_encode($data, JSON_PRETTY_PRINT) : json_encode($data));  
			}
		}
	}
	public function textify_or_not($data) { 
		if( $this->is_simple_type($data) ) 
			return (!is_bool($data) ? $data : ($data? 'true':'false') );
		else{
			return json_encode($data);
		}
	}
	public static function uniqueId($args, $addition=''){ return md5(json_encode($args)."_$addition"); }

	public function stripUnwantedTagsAndAttrs($html_str, $tags=null){
		$xml = new DOMDocument();
		libxml_use_internal_errors(true);
		$allowed_tags = is_null($tags) ? ["html", "body", "b", "br", "em", "hr", "i", "li", "ol", "p", "s", "span", "table", "tr", "td", "u", "ul"] :'';
		$allowed_attrs = ["class", "id", "style"];
		if (!strlen($html_str)){return false;}
		if ($xml->loadHTML($html_str, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)){
			foreach ($xml->getElementsByTagName("*") as $tag){
				if (!in_array($tag->tagName, $allowed_tags)){
					$tag->parentNode->removeChild($tag);
				}else{
					foreach ($tag->attributes as $attr){
						if (!in_array($attr->nodeName, $allowed_attrs)){
							$tag->removeAttribute($attr->nodeName);
						}
					}
				}
			}
		}
		return $xml->saveHTML();
	}


	public function get_visitor_ip() {
		$proxy_headers = array("CLIENT_IP", "FORWARDED", "FORWARDED_FOR", "FORWARDED_FOR_IP", "HTTP_CLIENT_IP", "HTTP_FORWARDED", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED_FOR_IP", "HTTP_PC_REMOTE_ADDR", "HTTP_PROXY_CONNECTION", "HTTP_VIA", "HTTP_X_FORWARDED", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED_FOR_IP", "HTTP_X_IMFORWARDS", "HTTP_XROXY_CONNECTION", "VIA", "X_FORWARDED", "X_FORWARDED_FOR");
		foreach($proxy_headers as $proxy_header) {
			if (isset($_SERVER[$proxy_header])) {
				if(preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $_SERVER[$proxy_header])) {
					return $_SERVER[$proxy_header];
				}
				else if (stristr(",", $_SERVER[$proxy_header]) !== FALSE) {
					$proxy_header_temp = trim(array_shift(explode(",", $_SERVER[$proxy_header])));
					if (($pos_temp = stripos($proxy_header_temp, ":")) !== FALSE) {$proxy_header_temp = substr($proxy_header_temp, 0, $pos_temp); }
					if (preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $proxy_header_temp)) { return $proxy_header_temp; }
				}
			}
		}
		return ( !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '__UNDEFINED_REMOTE_ADDR__');
	}

	public function mail_scrambler($email) {  return str_replace('@', '&#64;', $email);}



	public function expire_headers()
	{
		ini_set('session.cookie_httponly', 1);		
			//always display as new
		header("Cache-Control: no-cache, must-revalidate, max-age=0");
			//expired in past
		header("Expires: ".			date	('D, d M Y H:i:s', time() - 86400 *2) . " GMT");
		header("Vary: Accept-Encoding");
		header("Last-Modified: ".	gmdate	("D, d M Y H:i:s", time() - 86400 *2) . " GMT"); 
	}
	public static function var_dump_umlimited(){
		ini_set("xdebug.var_display_max_children", '-1');
		ini_set("xdebug.var_display_max_data", '10000');
		ini_set("xdebug.var_display_max_depth", '-1');
	}

	public static function currentUrlContains($phrase,$case_sens=true){
		return self::contains($_SERVER['REQUEST_URI'], $phrase, $case_sens);
	}

	public function change_max_upload_post()
	{
		if (property_exists($this,'upload_max_limit')) 
		{
			$this->upload_max_limit = max($this->upload_max_limit, ini_get('post_max_size'));
			ini_set('post_max_size', $this->upload_max_limit.'M'); ini_set('upload_max_filesize', upload_max_limit.'M');   ini_set('upload_max_size', upload_max_limit.'M');
		}		
	}


	#region    timers
	public function t1()	{ $this->timerstart();	}
	public function t2()	{ $this->timermiddle();	}
	public function t3()	{ $this->timerend();	}
	public function timerstart($echo=false)	{ $this->timer_started= microtime(true); if ($echo) echo '<pre>'.self::number_format( $this->timer_started ).'</pre>';	}
	public function timermiddle($echo=false){ $this->timer_middle = microtime(true); if ($echo) echo '<pre>'.self::number_format( $this->timer_middle ).'</pre>';	}
	public function timerend($echo=false)		
	{ 
		$this->timer_ended	=microtime(true);  if ($echo) echo '<pre>'.self::number_format( $this->timer_ended ).'</pre>'; 
		if(!empty($this->timer_middle)) {
			echo '<br/>^Start - Middle:'.self::number_format($first=($this->timer_middle-$this->timer_started) );
			echo '<br/>^Middle - End : '.self::number_format($second=($this->timer_ended-$this->timer_middle) ) ;	 echo ' [<b style="color:red;">' . round( max($first,$second)/min($first,$second), 2)  . '</b>x diff]<br/>';  
		}
		echo "<br/>^Whole cycle: ". self::number_format($this->timer_ended - $this->timer_started) ;
		exit;	
	}
	public function timernow($name){
		echo $name . ": "; 
		$now =  floatval(microtime(true));
		if(empty($this->lastime)) echo "start" ; else  $this->decimal_outputer($now - $this->lastime ) ;
		$this->lastime=  floatval(microtime(true));
		echo "\r\n";
	}	

	public function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	public function microtime()	{ return $this->microtime_float();	}

	//only for explicit call
	public function test_load_times_1(callable $func1, $iterations=1000)
	{
		var_dump($this->microtime(true));
		for ($i=1; $i<$iterations; $i++) { $func1(); }
		var_dump($this->microtime(true));
	}
	#endregion

	//  if ( is_admin() && file_exists($lib_start=__DIR__."/$name") && !defined("_puvox_machine_") ) { rename($lib_start, $lib_final); } require_once($lib_final);

	public function convert_urls_in_text($text) {
		return preg_replace('@([^\"\']https?://([-\w\.]+)+(:\d+)?(/([\w/_\.%-=#][^<]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $text);
	}

	public function randomString($length = 11) {
		return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1, $length);    //random_stringg($length= 15){ return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);}
	}

	public function PlainString(&$text1=false,&$text2=false,&$text3=false,&$text4=false,&$text5=false,&$text6=false,&$text7=false,&$text8=false){
		for($i=1; $i<=8; $i++){    if(${'text'.$i}) {${'text'.$i} = preg_replace('/\W/si','',${'text'.$i});} 	}
		return $text1;
	}

	public function adjustedUrlPrefixes($url){
		if(strpos($url, '://') !== false){
			return preg_replace('/^(http(s|)|):\/\/(www.|)/i', 'https://www.', $url);
		}
		else{
			return 'https://www.'.$url;
		}
	}

	public function remove_www($url) 	{ 
		return str_replace( ['://www.'], '://', $url ); 
	}

	public function remove_https_www($url){
		return str_replace( ['https://www.','http://www.','http://','https://'], '', $url ); 
	}

	public function slashesForward($url, $add_trailing_slash=true){ 
		return $this->remove_double_slashes ( str_replace(['\\','/'], '/', $url) );
	}
	
	public function slashesBackward($url, $add_trailing_slash=true){ 
		return $this->remove_double_slashes ( str_replace(['\\','/'], '\\', $url) );
	}
	
	public function normalize_with_slashes($url, $add_trailing_slash=true){ 
		return rtrim( $this->OneSlash($url), '/')  . ($add_trailing_slash ? '/' : '') ; 
	}

	public function OneSlash($url){
		$prefix='';
		if(substr($url,0,2)=='//'){
			$prefix = '//';
			$url=substr($url,2);
		}
		return $prefix.preg_replace( '/([^:])\/\//',  '$1/', $url);
	}
	
	//function to replace double-slashes with one slashes
	public function remove_double_slashes($input){
		$isSchemed = stripos($input, '://') !==false;
		$input=str_replace('//','/', $input);  $input=str_replace('\\\\','\\', $input);  return ($isSchemed ? str_replace(':/','://', $input) : $input);
	}
	
	public function replace_slashes($path){
		return 	str_replace( ['/','\\',DIRECTORY_SEPARATOR], '/', $path); 
	}
	public function remove_extra_slashes($path){
		return 	str_replace( '//', '/', $path); 
	}
	
	public function urlify($path){
		return str_replace( '\\', "/", $path); 
	}
	public function IsRestirctedDirecotryRequested($url=false, $dieORreturn=true ){ if (!$url) {$url=$_SERVER['REQUEST_URI'];}
		$url =stripslashes($url);
		if (  stristr($url,'\\')  ||   substr($url, 0, 2)=='..' || stristr($url,'../')  ||  stristr($url,'/..')  ||  stristr($url,'?')  ||  stristr($url,'*')  ||  stristr($url,'.php')	){
			if ($dieORreturn) {die("incorrect path requested.. error4292");} 	else{ return true;}
		}
	}

	public function directory_separatored($path){
		return str_replace(array('/','\\'),DIRECTORY_SEPARATOR, $path); 
	}

	// https://www.php.net/manual/en/function.realpath.php
	public static function realpath($path){
        // Cleaning path regarding OS
        $path = mb_ereg_replace('\\\\|/', DIRECTORY_SEPARATOR, $path, 'msr');
        // Check if path start with a separator (UNIX)
        $startWithSeparator = $path[0] === DIRECTORY_SEPARATOR;
        // Check if start with drive letter
        preg_match('/^[a-z]:/', $path, $matches);
        $startWithLetterDir = isset($matches[0]) ? $matches[0] : false;
        // Get and filter empty sub paths
        $subPaths = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'mb_strlen');

        $absolutes = [];
        foreach ($subPaths as $subPath) {
            if ('.' === $subPath) {
                continue;
            }
            // if $startWithSeparator is false
            // and $startWithLetterDir
            // and (absolutes is empty or all previous values are ..)
            // save absolute cause that's a relative and we can't deal with that and just forget that we want go up
            if ('..' === $subPath
                && !$startWithSeparator
                && !$startWithLetterDir
                && empty(array_filter($absolutes, function ($value) { return !('..' === $value); }))
            ) {
                $absolutes[] = $subPath;
                continue;
            }
            if ('..' === $subPath) {
                array_pop($absolutes);
                continue;
            }
            $absolutes[] = $subPath;
        }

        return
            (($startWithSeparator ? DIRECTORY_SEPARATOR : $startWithLetterDir) ?
                $startWithLetterDir.DIRECTORY_SEPARATOR : ''
            ).implode(DIRECTORY_SEPARATOR, $absolutes);
	}






	public function stripUrlPrefixes($url){
		return preg_replace('/http(s|):\/\/(www.|)/i', '',  $url);
	}

	public function getDomain($url){
		return preg_replace('/http(s|):\/\/(www.|)(.*?)(\/.*|$)/i', '$3', $url);
	}

	public function stripDomain($url){
		return str_replace( $this->adjustedUrlPrefixes($this->domainReal), '', $this->adjustedUrlPrefixes($url) );
	}

	// i.e. 5m, 1H, 2H, 1D, 240M, etc...
	public function stockTF_to_seconds($string, $minuteSymbol="m", $monthSymbol="M"){
		$res=$string;
		$arr=['s'=>1, 'S'=>1, $minuteSymbol=>1*60, 'h'=>60*60, 'H'=>60*60, 'd'=>24*60*60, 'D'=>24*60*60, 'w'=>7*24*60*60, 'W'=>7*24*60*60, $monthSymbol=>31*24*60*60, 'y'=>365*24*60*60, 'Y'=>365*24*60*60];
		foreach ($arr as $key=>$val) { if (empty($key)) continue; if (strpos($string, $key)!==false) { $res = str_ireplace($key, '', $string) * $val; break; }  }
		return $res;
	}
	
 

	public function str_replace_last($search, $replace, $subject)
	{
		$pos = strrpos($subject, $search);
		if($pos !== false) $subject = substr_replace($subject, $replace, $pos, strlen($search)); 
		return $subject;
	}
	
	public function str_replace_first($from, $to, $content, $type="plain"){
		if($type=="plain"){
			$pos = strpos($content, $from);
			if ($pos !== false) {
				$content = substr_replace($content, $to, $pos, strlen($from));
			}
			return $content;
		}
		elseif($type=="regex"){
			$from = '/'.preg_quote($from, '/').'/';
			return preg_replace($from, $to, $content, 1);
		}
	}
	
	public function timeMS(){ return round(microtime(true)*1000); }
	
	public function toString($inp){
		return $inp."";
	}
	public function contains_numeric($str){
		$str=$this->toString($str);
		for($i=0; $i<=9; $i++) {
			if (strpos($str, $this->toString($i) )!==false){
				return true;
			}
		}
		return false;
	}
	
	public function dayForTime($time){
		return strtotime(date('Y-m-d', $time));
	}
	public function currentDatetime($time='', $MS=false){
		$format = 'Y-m-d H:i:s'.($MS?'.v':'') ;
		return ( !empty($time) ? gmdate($format, $time) : gmdate($format) );
	}
	public static function timeToDate($time, $ms=000){
		return self::time_to_date($time, $ms=000);
	}
	public static function time_to_date($time, $ms=000){
		return date("Y-m-d H:i:s.$ms", $time);
	}
	public static function timeToDateTZ($time){
		$time_rounded = floor($time);
		// if milliseconds
		if ($time_rounded>1000000000000){
			$ms = $time_rounded % 1000;
			$timeS= floor($time_rounded/1000);
			$dat = date("Y-m-d\TH:i:s.$ms\Z", $timeS);
		}
		else{
			$dat = date("Y-m-d\TH:i:s.000\Z", $time_rounded);
		}
		return $dat;
	}
	
	public function isWeekend($time){
		return date('N',$time) > 5;
	}

	public static function safemode_basedir_set(){
		return ( ini_get('open_basedir') || ini_get('safe_mode') ) ;
	}
	public static function header($type){
		switch ($type){
			case "json" : header('Content-Type: application/json;  charset=utf-8'); break;
			case "text" : header('Content-Type: text/plain;  charset=utf-8'); break;
			case "js"   : header('Content-Type: application/javascript;  charset=utf-8'); break;
		}
	}

	public function file_get_contents($path, $createIfNotExistWithContent=null )
	{
		$path = self::realpath($path);  
		if (!file_exists($path)){
			if (is_null($createIfNotExistWithContent)){
				return "";
			}
			else{
				if (method_exists($this,'file_put_contents')){
					$this->file_put_contents($path, $createIfNotExistWithContent);
				}
				return $createIfNotExistWithContent;
			}
		} 
		else {
			// is_readable( $path ) will not work here, because of LOCK-waiting
			$wait_for_lock_seconds= 0.2;
			if($fp = fopen($path,'r')) {
				$startTime = microtime(true);
				do{
					$canRead = flock($fp, LOCK_SH);
					if(!$canRead) {
						self::sleep( 0.01 );   // Releases CPU for others
					}
					// If the lock is not acquired and the timeout has not expired, continue to acquire the lock
				} while((!$canRead) && ((microtime(true) - $startTime) < $wait_for_lock_seconds ));
				if($canRead) {
					//file_get_contents($path)
					$contents=file_get_contents($path); // fread($fp, filesize($path));
					flock($fp,LOCK_UN);  
				}
				else{
					throw new \Exception("Could not get read-access to file $path");
				}
				fclose($fp);
				return $contents;
			}
			else{
				throw new \Exception("Could not open access to file $path");
			}
		}
	}


	public static function exitPlain($content, $encode=false){
		self::header('text');
		if ($encode) $content = json_encode($content);
		print($content); exit;
	}

	public static function exitJson($content){
		self::header('json');
		if (is_array($content)) $content = json_encode($content);
		exit($content);
	}

	public function try_increase_exec_time($seconds, $memory=null){
		if( ! $this-> safemode_basedir_set() ) {
			if(!is_null($memory)) $this->try_increase_memory($memory);
			return ini_set('max_execution_time', $seconds); //stackoverflow.com/questions/8914257
		}
		return false;
	}

	public static function set_memory_limit($new_limit = 'mbs'){
		if( ! self::safemode_basedir_set() ) {
			$limitBytes = $new_limit * 1048576;
			$currentLimit = trim(ini_get('memory_limit'));
			$lastChar = strtolower($currentLimit[strlen((int) $currentLimit)-1]);
			switch($lastChar) {
				case 'g': $currentLimit *= 1024;
				case 'm': $currentLimit *= 1024;
				case 'k': $currentLimit *= 1024;
			}
			if ($currentLimit < $limitBytes)
				return ini_set('memory_limit', $new_limit . 'M');
		}
		return false;
	}

	// find defined bits/flags/enums
	public static function binding_flags($flag)
	{
		$setBits = array();
		for ($i = 1; $i <= 32; $i++) {
			if ($flag & (1 << $i)) {
				$setBits[] = (1 << $i);
			}
		}
	
		// Sort array to order the bits
		sort($setBits);
	
		return $setBits;
	}
	// find if bit/flag/enum is defined
	public static function flag_exists($existing, $target){
		return in_array($target,self::binding_flags($existing));//$target & (1 << $target);
	}

	public function MessageAgainstMaliciousAttempt(){
		return 'Not allowed. Try again.';//'Well... I know that these words won\'t change you, but I\'ll do it again: Developers try to create a balance & harmony in internet, and some people like you try to steal things from other people. Even if you can it, please don\'t do that.';
	}

	public function link($link, $text){ return $this->href_url($link, $text);  }
	public function href_url($link, $text){ return '<a href="'.$link.'" target="_blank">'.$text.'</a>'; }
	public function href($link, $text){ return $this->href_url($link, $text); }

	public function mkdir($dest, $permissions=0755, $create=true){ return $this->mkdir_recursive($dest, $permissions, $create); }
	public function mkdir_recursive($dest, $permissions=0755, $create=true){
		if(!is_dir($dest)){
			//at first, recursively create parent directory if doesn't exist
			$parent = dirname($dest);
			if( !is_dir($parent ) ){ $this->mkdir_recursive($parent, $permissions, $create); }
			else {
				if ( is_writable( $parent ) ){
					return mkdir($dest, $permissions, $create); 
				}
				else{
					var_dump("This plugin don't have permission to create directory: $parent");
					return false;
				}
				
			}
		}
		else{return true;}
	}

	public function rmdir($dirPath){ return $this->rmdir_recursive($dirPath); }
	public function rmdir_recursive($dirPath){
		if(!empty($dirPath) && is_dir($dirPath) ){
			$dir  = new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS); //upper dirs not included,otherwise DISASTER HAPPENS :)
			$files = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($files as $path) $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname()); //{if (is_file($path)) {unlink($path);} else {$empty_dirs[] = $path;} } if (!empty($empty_dirs)) {foreach ($empty_dirs as $eachDir) {rmdir($eachDir);}} 
			rmdir($dirPath);
			return true;
		}
		return true;
		//include_once(ABSPATH.'/wp-admin/includes/class-wp-filesystem-base.php');
		//\WP_Filesystem_Base::rmdir($fullPath, true);
	}
	public function emptyDir($dirPath){
		return array_map( 'unlink', array_filter((array) glob("$dirPath/*") ) );
	}
	
	public function copy_recursive($source, $dest, $permissions = 0755){
		if (is_link($source))	{ return symlink(readlink($source), $dest); }
		elseif (is_file($source))	{ 
			if(!file_exists(dirname($dest))){$this->mkdir_recursive(dirname($dest), $permissions, true); }
			if(!copy($source, $dest)) {echo "not copied ($source ---> $dest )";} return true; 
		}
		elseif (is_dir($source))	{ 
			$this->mkdir_recursive($dest, $permissions, true); 
			foreach (glob($source.'/*') as $each){	$basen= basename($each);
				if ($basen != '.' && $basen != '..') { $this->copy_recursive("$each", "$dest/$basen", $permissions);	}
			}
		}
	}

    // ##### CUSTOM OPTIONS #####
	public function optsNameX1(){ return 'all_options_'.$this->module_NAMESPACE; }
	public function get_option($name, $defaultValue=null){ 
		if($this->isWP)
		{
			$opts = get_site_option($this->optsNameX1(),[]);
		}
		else{
			$opts = $this->get_option_json($name,$defaultValue); 
		}
		return is_null($name) ? $opts : (array_key_exists($name, $opts) ? $opts[$name] : '');
	}
	public function update_option($name, $value, $autoload=null){  
		$opts = $this->get_option($this->optsNameX1(),[]);
		if( !is_null($name) ){
			$opts[$name]= $value;
		}
		else{
			$opts = $value;
		}
		return ($this->isWP) ? update_site_option($this->optsNameX1(), $opts, $autoload) :  $this->update_option_json($name,$value,$autoload);  
	}

	public function add_my_site_options($array)
	{ 
		$this->extra_options_enabled=true;
		$this->_my_site_options=$this->get_option(null);
		$final=[];
		foreach($array as $key=>$value){
			$final[$key]=array_key_exists($key, $this->_my_site_options) ? $this->_my_site_options[$key] : $value;
		}
		if($this->_my_site_options!=$final) { $this->update_my_site_options($final); }
	}
	public function get_my_site_option($name=null, $default=null, $force_update=false)
	{
		$this->_my_site_options=$this->get_option($name);
		if ($name!=null)
		{
			if (! array_key_exists($name, $this->_my_site_options) || $force_update){
				$this->_my_site_options[$name]=$default;
				$this->update_my_site_options();
			}
			return $this->_my_site_options[$name];
		}
		return $this->_my_site_options;
	}
	public function update_my_site_options($array=null)
	{
		$this->update_option(null,  ( $array ?: $this->_my_site_options) );
	}



	// ########################### SQLITE ###########################
	//  $db = new \PDO('sqlite:'.$db_path);  $db ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	public function sqlite_db_init($db_path="/example/my.db"){
		try {
			$db = new \SQLite3($db_path);
		} catch(\Exception $ex) { 
			die('Error: '.$ex->getMessage());
		}
		return $db ;
	} 
	public function sqlite_create_table_PDO($db){
		$db->exec( "CREATE TABLE IF NOT EXISTS myValuesTable (
			id INTEGER PRIMARY KEY, 
			title TEXT, 
			text TEXT, 
			status TEXT, 
			time INTEGER)"
		);
	} 
	public function sqlite_insert_PDO($title, $text, $status, $time) {
        $sql = 'INSERT INTO myValuesTable(title,text,status,time) VALUES(:title,:text,:status,:time)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title'	=> $title,
            ':text'		=> $text,
            ':status'	=> $status,
            ':time' 	=> $time,
        ]);
        return $this->pdo->lastInsertId();
    } 
	public function sqlitePDO_update($id, $title, $text, $status, $time) {
        // SQL statement to update status of a task to completed
        $sql = "UPDATE myValuesTable SET "
				. "title  = :title, "
                . "text   = :text, "
                . "status = :status, "
                . "time   = :time "
                . "WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':title'  => $title,
            ':text'   => $text,
            ':status' => $status,
            ':time'   => $time,
            ':id'     => $id,
        ]);
    }

	public function sqlitePDO_fetchAll($db, $tablename) {
		$which = '*'; //'project_id, project_name';//
        $stmt = $db->query('SELECT '.$which .' FROM '.$tablename);
        $res = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }
	
    public function pdoCommand($db_name="db_all.db") { 
        $this->pdo	= $this->sqlite_db_init($db_name);
        $statement->execute(); 
        $res = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
	
	public function sqlite_create_table_TRANSLATIONS($db)
	{
		$sql =
		'CREATE TABLE IF NOT EXISTS translations (	
			ID INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
			program_name		VARCHAR(150),
			string				TEXT 	NOT NULL,
			lang				TEXT 	NOT NULL,
			value				TEXT 	NOT NULL,
			time				INT,
			suggestion			TEXT
		);';
		return $db->query($sql);     //possibles: VARCHAR(50)  ,  PRIMARY KEY (`ID`), UNIQUE KEY `ID` (`ID`) 	) AUTO_INCREMENT=1;'; 
	}	
	
	public function sqlite_insert_TRANSLATION($db, $string, $lang, $value, $time, $program_name, $suggestion) { 
		$statement = $db->prepare('INSERT INTO translations ( string, lang, value, time, program_name, suggestion ) VALUES (:string, :lang, :value, :time, :program_name,  :suggestion );');
		return $statement->execute([':string'=>$string, ':suggestion'=>$suggestion]);
	}
			
	public function sqlite_string_exists($string, $lang=false, $program_name=false) 
	{
		$statement = $this->db->prepare('SELECT * from translations where string= :string'. ( $lang? ' and lang = :lang' : '') .' LIMIT 1' ); // . ( $program_name? ' and program_name = :program_name' : '') 
		$statement->bindValue(':string',	$string);
			if ($lang)			
		$statement->bindValue(':lang',	$lang); 
		$res = $statement->execute();  
		return !empty($res->fetchArray(SQLITE3_ASSOC));
	}
	
	public function sqlite_get()
	{
		$statement = $this->db->prepare('SELECT * from translations where string= :string and lang= :lang ');  //. ( $program_name? ' program_name = :program_name' : '') 
		$statement->bindParam(':string',$string);
		$statement->bindParam(':lang',	$lang);
		$ret = $statement->execute(); 
		$res = $ret->fetchArray(SQLITE3_ASSOC);
		if(!empty($res)){
			$this->found=true;
			$return= $res['value'];
		}
	}
		//$current_day_logname = 'log_'.date('Y-m-d'). ($new_file ? time().$new_file : '') . '.txt'; 
		//$this->helpers->file_put_contents($current_day_logname,$data, FILE_APPEND); 
	// ########################### END SQLITE ###########################
	
	

	// DOM PARSER
	public function new_dom_document($content)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$internalErrors = libxml_use_internal_errors(true);	//disable
		$dom->loadHTML( $content);
		libxml_use_internal_errors($internalErrors);		//restore
		$finder = new \DOMXpath( $dom );
		$nodes= $finder->query( "//*" );
		foreach ($nodes as $node) {
			if ($node->hasAttributes())
			{
				$error = $node->ownerDocument->saveHTML($node); break;
			}
		}
	}
	
	public function domDocument_load($content)
	{
		if (!property_exists($this,'tempDom')) $this->tempDom = new \DOMDocument('1.0', 'UTF-8');
		$internalErrors = libxml_use_internal_errors(true);	//disable
		$this->tempDom->loadHTML( $content);
		libxml_use_internal_errors($internalErrors);		//restore
		return $this->tempDom;
	}
	public function domDocument_remove($el)
	{
		$el->parentNode->removeChild($el);
	}
	public function domDocument_body($dom)
	{ 
		$body = $dom->getElementsByTagName('body');
		if ( $body && 0<$body->length ) {
			$body = $body->item(0);
			return $dom->savehtml($body);
		}
		return "-1";
	}

	
	public function domDocument_getElementById($dom, $idName, $showError=false) {
		try{
			return $dom->getElementById($idName)->nodeValue;
		}
		catch(\Exception $ex){
			return ($showError ? "DomError:".$ex->getMessage() : null);
		}
	}

	public function domDocument_getElementsByClassName($dom, $ClassName, $tagName=null) {
		$Elements = $tagName ? $dom->getElementsByTagName($tagName) : $dom->getElementsByTagName("*");
		$Matched = array();
		for($i=0;$i<$Elements->length;$i++) {
			if($Elements->item($i)->attributes->getNamedItem('class')){
				if($Elements->item($i)->attributes->getNamedItem('class')->nodeValue == $ClassName) {
					$Matched[]=$Elements->item($i);
				}
			}
		}
		return $Matched;
	}
	public function domDocument_getElementsByClass_2(&$parentNode, $tagName, $className) {
		$nodes=array();
		$childNodeList = $parentNode->getElementsByTagName($tagName);
		for ($i = 0; $i < $childNodeList->length; $i++) {
			$temp = $childNodeList->item($i);
			if (stripos($temp->getAttribute('class'), $className) !== false) {
				$nodes[]=$temp;
			}
		}

		return $nodes;
	}


	public function get_dom_element_data($html_data, $tag_type='id', $tag_key='')
	{
        $dom = new \DOMDocument('1.0', 'UTF-8');;  
        $dom->loadHTML($html_data, LIBXML_HTML_NOIMPLIED | LIBXML_DTDVALID | LIBXML_NOENT | LIBXML_NOERROR );   // https://www.php.net/manual/en/libxml.constants.php#constant.libxml-html-noimplied
        $dom->preserveWhiteSpace = false; //disact whitespace
        if( $tag_type=='id'){
			$json_obj = $dom->getElementById($tag_key); 
			$data = $json_obj->nodeValue;
		}
        try{
			return json_decode($data);
		}
		catch(\Exception $ex){
			return $data;
		}
	}
	// ========================

	
	public function include_dir($dir){ 
		foreach(glob($dir ."/*.php$") as $file) include_once($file);
	}


	public function filesContents($files=[], $inModule=true){ 
		$cont = '';
		foreach($files as $file){
			$cont .= $this->file_get_contents( ($inModule? $this->moduleDIR : '').$file);
		}
		return $cont;
	}

	// files collection
	public function globFiles($glob_pattern, $first='', $last='')
	{
		$files = glob($glob_pattern);
		$new_files =$files;
		$first_file='';
		$last_file ='';
		foreach($files as $file)
		{
			if( !empty($first) && strpos($file, $first)!==false ) {
				$first_file = $file;
				$new_files=array_diff( $new_files, [$file] );
			}
			if( !empty($last)  && strpos($file, $last) !==false ) {
				$last_file = $file;
				$new_files=array_diff( $new_files, [$file] );
			}
		}
		if( !empty($first_file) ) array_unshift($new_files, $first_file);
		if( !empty($last_file) ) array_push($new_files, $last_file);
		return $new_files;
	}

	public function fileUrl($file){ 
		return $this->moduleURL."/$file?vers_=".$this->filedate($this->moduleDIR. "/$file");
	}

	public function FullIframeScript(){ ?>
		<script>
		function MakeIframeFullHeight_tt(iframeElement, cycling, overwrite_margin){
			cycling= cycling || false;
			overwrite_margin= overwrite_margin || false;
			iframeElement.style.width	= "100%";
			var ifrD = iframeElement.contentDocument || iframeElement.contentWindow.document;
			var mHeight = parseInt( window.getComputedStyle( ifrD.documentElement).height );  // Math.max( ifrD.body.scrollHeight, .. offsetHeight, ....clientHeight,
			var margins = ifrD.body.style.margin + ifrD.body.style.padding + ifrD.documentElement.style.margin + ifrD.documentElement.style.padding;
			if(margins=="") { margins=0; if(overwrite_margin) {  ifrD.body.style.margin="0px"; } }
			(function(){
				var interval = setInterval(function(){
				if(ifrD.readyState  == 'complete' ){
					setTimeout( function(){
						if(!cycling) { setTimeout( function(){ clearInterval(interval);}, 500); }
						iframeElement.style.height	= (parseInt(window.getComputedStyle( ifrD.documentElement).height) + parseInt(margins)+1) +"px";
					}, 200 );
				}
				},200)
			})();
				//var funcname= arguments.callee.name;
				//window.setTimeout( function(){ console.log(funcname); console.log(cycling); window[funcname](iframeElement, cycling); }, 500 );
		}
		</script>
		<?php
	}




	public function stripslashes_from_strings_only( $value ) {
		return is_string( $value ) ? stripslashes( $value ) : $value;	
	}
	public function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}
	
	public function stripslashes_deep2($value){ 
		return $this->array_map_deep([$this,'stripslashes_from_strings_only'] , $value ); 
	}

	
    public function childrenCheckboxed($array, $keyname, $post_array=null)
    {
		$ref_Array= !is_null($post_array) ? $post_array : $array;
        foreach ($array as $key=>$name)
        {
            $array[$key][$keyname]= isset($ref_Array[$key][$keyname]);
        }
        return $array;
    }
	// ================================================





	//only open and close the same-origin creator of session  (argument can be TRUE/FALSE or STRING too
	public function session_state ($arg) { 
		if($arg===true)	{	if(session_status() == PHP_SESSION_NONE)	{ $GLOBALS['my_session_pp']='sess'.rand(1,99999999); session_start();  return $GLOBALS['my_session_pp']; }   	}     
		else			{	if(session_status() == PHP_SESSION_ACTIVE)	{ if(!$arg || $arg==$GLOBALS['my_session_pp']) session_write_close();  }   	}  
	}
	public function set_session_var ($name,$value) {
		$id= $this->session_state(true);
		$_SESSION[$name] = $value;
		$this->session_state($id);
	}
	
	public function startSessionIfNotStarted(){
		if(session_status() == PHP_SESSION_NONE)  { $this->session_being_opened = true; session_start();  }
	}
	public function endSessionIfWasStarted( $method=2){
		if(session_status() != PHP_SESSION_NONE && property_exists($this,"session_being_opened") )  {
			unset($this->session_being_opened);
			if($method==1) session_destroy();
			elseif($method==2) session_write_close();
			elseif($method==3) session_abort();
			elseif($method==4) session_unset();
		}
	}









	// ########################################################### //
	// ###################### ARRAY OPERATIONS ################### //
	// ########################################################### //
	// NOTE: Generaly, creating new variable has much better memory performance, then "unset"

	// ======== manual  ========
	//convert unsorted array (i.e. [ 'first'=>["a","b","c"], 'second'=>[1,2,3] , ] ) to associative   [ "a"=>1, "b"=2 ]
	public function array_to_associative($array) {
		
	}
	public function string_to_array($string, $divider='|', $allow_empty=false){ $res=( array_map('trim', !empty($string)?explode($divider, $string):[] ) ); return $allow_empty? $res : array_filter($res); }
	public function array_to_string($array)	{ return implode(",",  array_map('trim',array_filter($array)) ); }
	public function arrayPhpToJs($array)	{ return '["'. implode('","', (!empty($array[0]) && is_array($array[0]) ? $this->arrayPhpToJs($array) : $array) ) .'"]'; }

	public function object_to_array($data) //faster than json_encode & decode : https://stackoverflow.com/a/4345578/2377343
	{
		if (is_array($data) || is_object($data))
		{
			$result = [];
			foreach ($data as $key => $value)
			{
				$result[$key] = (is_array($data) || is_object($data)) ? $this->object_to_array($value) : $value;
			}
			return $result;
		}
		return $data;
	}
	public static function array_to_object($data) { 
		if (is_object($data))
		{
			foreach ($data as $key=>$value)
			{
				if(is_array($value) || is_object($value))
					$data->$key = self::array_to_object($value);
			}
			return $data;
		}
		else{
			$result = new \stdClass();
			foreach ($data as $key => $value)
			{
				$result->$key = (is_array($value) || is_object($value)) ? self::array_to_object($value) : $value;
			}
			return $result;
		}
	}

	
	public static function array_add_prefix_to_keys($array, $prefix){
		$new_array =[];
		foreach ($array as $k => $v) {
			$new_array[$prefix.$k] = $v;
		}
		return $new_array;
	}
	public static function add_prefix_to_array_keys($array, $prefix){
		return self::array_add_prefix_to_keys($array, $prefix);
	}
	public static function array_add_prefix_to_values($array, $prefix){
		$new_array =[];
		foreach ($array as $k => $v) {
			$new_array[$k] = $prefix.$v;
		}
		return $new_array;
	}

	public static function array_shuffle($list) {
		if (!is_array($list)) return $list;
		$keys = array_keys($list);
		shuffle($keys);
		$random = [];
		foreach ($keys as $key)
			$random[$key] = $list[$key];
		return $random;
	}
	

	public function array_map_recursive( $func, $value) {  //removed callable $func
		if (is_array($func)){
			$array = $value;
			foreach($func as $each_func){
				$array = $this->array_map_recursive($each_func,$array);
			}
			return $array;
		}
		else{
			return filter_var($value, \FILTER_CALLBACK, ['options' => $func]);
		}
	}
	
	public function array_map_deep( $callback , $value) 
	{
		if ( is_array( $value ) ) {
			foreach ( $value as $index => $item ) {
					$value[ $index ] = $this->array_map_deep($callback,  $item );
			}
		} elseif ( is_object( $value ) ) {
			$object_vars = get_object_vars( $value );
			foreach ( $object_vars as $property_name => $property_value ) {
					$value->$property_name = $this->array_map_deep( $callback, $property_value );
			}
		} else {
			$value = call_user_func( $callback, $value );
		}
		return $value;
	}


	public function array_keys($arrayOrObject){
		if(is_array($arrayOrObject))
			return array_keys($arrayOrObject);
		elseif (is_object($arrayOrObject))
			return array_keys(get_object_vars($arrayOrObject));
		return [];
	}

	public static function count($arr_or_object)
	{
		if(is_object($arr_or_object)) return count(get_object_vars( $arr_or_object ));
		else return count($arr_or_object);
	}

	public function array_value($array, $key, $default=''){
		if (is_object($array)) {
			return (property_exists($array, $key) ? $array->$key : $default);
		}
		else{
			return (array_key_exists($key, $array) ? $array[$key] : $default);
		}
	}

	public function array_value_sub($array, $key1, $key2, $default=''){
		if (is_object($array)) {
			return ( !property_exists($array, $key1) || !property_exists($array->$key1, $key2) ) ? $default : $array->$key1->$key2;
		}
		else{
			return ( !array_key_exists($key1,$array) || !array_key_exists($key2, $array[$key1]) ) ? $default : $array[$key1][$key2];
		}
	}

	public static function in_array($needle, $haystack) {
		return in_array($needle, self::array_values($haystack) );
	}

	public static function in_arrayi($needle, $haystack) {
		return in_array(strtolower($needle), array_map('strtolower', $haystack));
	}

	public static function array_values($array_or_object){
		if (is_object($array_or_object)){
			$arr = [];
			foreach($array_or_object as $key=>$val){
				$arr[] = $val;
			}
			return $arr;
		}
		else{
			return array_values($array_or_object);
		}
	}

	public static function arrayize($val) {
		return self::is_array($val) ? $val : [$val];
	}
	public function valueIs($array, $key, $value){
		return $this->array_value($array, $key)===$value;
	}
	public function valueSetDefault(&$array, $key, $value){
		if ( ! array_key_exists($key, $array) ) $array[$key]==$value;
		return $array;
	}
	public function array_child($array, $child_relation='i.e. response->items'){
		if (!empty($child_relation))
		{
			$childTargetArr= $this->string_to_array($child_relation, '->');
			foreach ( $childTargetArr as $childKey) {
				$array = is_array($array) ? $array[$childKey] : $array->$childKey;
			}
		}
		return $array;
	}


	// ##########################
	public function array_makeKeyedBySubkey($array, $targetKey){  //this is 2x faster than below
		return array_column($array, null, $targetKey);
	}
	// strict defines, whether throw exceptions if targetkey not found
	public function array_makeKeyedBySubkey_manual($array, $targetKey, $strict=true){
		$new_array=[];
		foreach($array as $key=>$subArray)
		{
			if ($strict){
				$name = $this->is_array($subArray) ? $subArray[$targetKey] : $subArray->{$targetKey};
			}
			else{
				if ( is_array($subArray) && array_key_exists($targetKey,$subArray) )
					$name = $subArray[$targetKey];
				else if ( is_object($subArray) && property_exists($subArray, $targetKey) )
					$name = $subArray->{$targetKey};
				else
					$name = $key;
			}
			$new_array[$name] = $subArray;
		}
		return $new_array;
	}
	public function array_makeKeyedByValue($array){
		$new_array=[];
		foreach($array as $value)
		{
			$new_array[$value] = $value;
		}
		return $new_array;
	}

	//insert sub-child item in array-children with the child's keyname
	public function array_insertChildSameKey(&$array, $targetKey){
		$new_array=[];
		foreach($array as $key=>$subArray)
		{
			$subArray[$targetKey] =$key;
			$new_array[$key] = $subArray;
		}
		$array = $new_array;
		return $array;
	}
	public function array_parentize($array, $targetSubKey_1, $targetSubKey_2=''){
		$new_array=[];
		foreach($array as $key=>$subArray)
		{
			$parent1_key_will_be = $this->array_value($subArray,$targetSubKey_1); 
			$parent2_key_will_be = empty($targetSubKey_2) ? $key : $this->array_value($subArray, $targetSubKey_2); 
			$new_array[$parent1_key_will_be][$parent2_key_will_be] = $subArray;
		}
		return $new_array;
	}	


	public function array_sub_with_keyvalue($array, $keyName, $shouldBeEqualTo, $resortKeysFromZero=false, $case_sensitive=true)
	{ 
		$newArray = [];
		foreach($array as $key=>$subArray) {
			if (is_array($subArray))
			{
				$add=false;
				if ( array_key_exists($keyName, $subArray) )
				{
					$v_1 = $subArray[$keyName];
					$v_2 = $shouldBeEqualTo;
					if( is_numeric($v_1) && is_numeric($v_2) )
					{
						$v_1 =self::number_format($v_1);
						$v_2 =self::number_format($v_2);
					}
					if( $v_1 === $v_2){
						$add=true;
					}
				}
				if ($add){
					if ($resortKeysFromZero)
						$newArray[] = $subArray;
					else
						$newArray[$key] = $subArray;
				}
			}
			elseif ( is_object($subArray) ) {
				$add=false;
				if ( property_exists($subArray,$keyName) )
				{
					$v_1 = $subArray->$keyName;
					$v_2 = $shouldBeEqualTo;
					if( is_numeric($v_1) && is_numeric($v_2) )
					{
						$v_1 =self::number_format($v_1);
						$v_2 =self::number_format($v_2);
					}
					if( $v_1 === $v_2){
						$add=true;
					}
				}
				if ($add){
					if ($resortKeysFromZero)
						$newArray[] = $subArray;
					else
						$newArray[$key] = $subArray;
				}
			}
		}
		return $newArray;
	}	
	public function array_sub_without_keyvalue($array, $keyName, $shouldBeEqualTo, $resortKeysFromZero=false, $case_sensitive=true)
	{ 
		$newArray = [];
		foreach($array as $key=>$subArray) {
			if (is_array($subArray))
			{
				$add=true;
				if ( array_key_exists($keyName, $subArray) )
				{
					$v_1 = $subArray[$keyName];
					$v_2 = $shouldBeEqualTo;
					if( is_numeric($v_1) && is_numeric($v_2) )
					{
						$v_1 =(float)($v_1);
						$v_2 =(float)($v_2);
					}
					if( $v_1 === $v_2){
						$add=false;
					}
				}
				if ($add){
					if ($resortKeysFromZero)
						$newArray[] = $subArray;
					else
						$newArray[$key] = $subArray;
				}
			}
			elseif ( is_object($subArray) ) {
				$add=true;
				if ( property_exists($subArray,$keyName) )
				{
					$v_1 = $subArray->$keyName;
					$v_2 = $shouldBeEqualTo;
					if( is_numeric($v_1) && is_numeric($v_2) )
					{
						$v_1 =(float)($v_1);
						$v_2 =(float)($v_2);
					}
					if( $v_1 === $v_2){
						$add=false;
					}
				}
				if ($add){
					if ($resortKeysFromZero)
						$newArray[] = $subArray;
					else
						$newArray[$key] = $subArray;
				}
			}
		}
		return $newArray;
	}
	
	
	// CONTAIN //
	public function array_sub_with_keyvalue_contain($array, $keyName, $shoulContain, $resortKeysFromZero=false, $case_sensitive=true)
	{ 
	   //todo
	}
	//TODO
	public function array_sub_without_keyvalue_contain($array, $keyName, $shouldNotContain, $resortKeysFromZero=false, $case_sensitive=true)
	{ 
		foreach($array as $key=>$subArray) {
			if ( array_key_exists($keyName, $subArray) && $this->contains($subArray[$keyName],$shouldNotContain,$case_sensitive) ){
				unset($array[$key]);
			}
		}
		return $array;
	}
	//TODO
	public function array_sub_sub_without_keyvalue_contain($array, $childKey, $keyName, $shouldNotContain, $resortKeysFromZero=false, $case_sensitive=true)
	{ 
		foreach($array as $key_1=>$subArray_1) {
			foreach($subArray_1[$childKey] as $key_2=>$subArray_2) {
				if ( array_key_exists($keyName, $subArray_2) && $this->contains($subArray_2[$keyName],$shouldNotContain,$case_sensitive) ){
					unset($array[$key_1][$childKey][$key_2]);
				}
			}
		}
		return $array;
	}
	// ### CONTAIN ### //

	//TODO
	public function array_sub_replace_keyname($array, $keyName, $replaceWithKeyname)
	{ 
		//$newArray = [];
		//foreach($array as $key=>$subArray) {
		//	if ( $keyName===$key ){
		//		$newArray[$replaceWithKeyname] = $subArray;
		//	}
		//	else{
		//		$newArray[$key] = $subArray;
		//	}
		//}
		//return $newArray;
	}

	public function array_sub_replace_value($array, $keyName, $replaceWhatValue, $replaceWithValue)
	{ 
		$newArray = [];
		foreach($array as $key=>$subArray) {
			if ( array_key_exists($keyName, $subArray) && $subArray[$keyName] === $replaceWhatValue )
				$subArray[$keyName]=$replaceWithValue;
			$newArray[$key] = $subArray;
		}
		return $newArray;
	}

	public function array_subarray_value_above($array, $keyName, $value, $allow_null=false)
	{ 
		$newArray = [];
		foreach($array as $key=>$subArray) {
			//if array
			if ( is_array($subArray) && array_key_exists($keyName, $subArray) && ($subArray[$keyName] >= $value || ($allow_null && is_null($subArray[$keyName]) ) ) ) 
				$newArray[$key] = $subArray;
			//if object
			if ( is_object($subArray) && property_exists($subArray, $keyName) && ($subArray->$keyName >= $value || ($allow_null && is_null($subArray->$keyName) ) ) ) 
				$newArray[$key] = $subArray;
		}
		return $newArray;
	}



	public function array_only_keys_that_contain($array, $string_or_array, $case_sens=true, $position='any'){
		$new=[];
		if (!empty($string_or_array))
		{
			$contained_strs = $this->arrayize($string_or_array);
			foreach($array as $key=>$block)  {
				if ( $this->contains_AgainstArray($key, $contained_strs, $case_sens,$position) ){
					$new[$key] = $block;
				}
			}
		}
		return $new;
	}
	public function array_only_keys_that_not_contain($array, $string_or_array, $case_sens=true, $position='any'){
		$new=[];
		if (!empty($string_or_array))
		{
			$contained_strs = $this->arrayize($string_or_array);
			foreach($array as $key=>$block)  {
				if ( ! $this->contains_AgainstArray($key, $contained_strs, $case_sens,$position) ){
					$new[$key] = $block;
				}
			}
		}
		return $new;
	}

	public function array_only_keys_that_contain_not_contain($array, $INCLUDE_string_or_array=[], $INCLUDE_case_sens=true, $INCLUDE_position='any', $EXCLUDE_string_or_array=[], $EXCLUDE_case_sens=true, $EXCLUDE_position='any'){
		$INCLUDEDs = is_array($INCLUDE_string_or_array) ? $INCLUDE_string_or_array : [$INCLUDE_string_or_array];
		$EXCLUDEDs = is_array($EXCLUDE_string_or_array) ? $EXCLUDE_string_or_array : [$EXCLUDE_string_or_array];
		$isInlcude= !empty($INCLUDEDs);
		$isExclude= !empty($EXCLUDEDs);

		$isObject = is_object($array);
		$new_array=$isObject ? (object) [] : [];
		foreach( $array as $key=>$block)  {
			$set=true;
			if ( $set && ($isInlcude && ! $this->contains_AgainstArray($key, $INCLUDEDs, $INCLUDE_case_sens, $INCLUDE_position) )){
				$set=false;
			}
			if ( $set && ($isExclude && $this->contains_AgainstArray($key, $EXCLUDEDs, $EXCLUDE_case_sens, $EXCLUDE_position) )){
				$set=false;
			}
			if($set){
				if($isObject) $new_array->$key = $block;
				else          $new_array[$key] = $block;
			}
		} 
		return $array;
	}
	public function array_only_values_that_contain($array, $string_or_array, $case_sens=true, $position='any'){
		$new_array=[];
		if (!empty($string_or_array))
		{
			if( $this->is_array($string_or_array) )
			{
				foreach($array as $key=>$block)  {
					if ( $this->contains_AgainstArray($block, $string_or_array, $case_sens, $position) ){
						$new_array[$key]=$block;
					}
				}
			}
			else{
				foreach($array as $key=>$block)  {
					if ( $this->contains($block, $string_or_array, $case_sens, $position) ){
						$new_array[$key]=$block;
					}
				}
			}
		}
		return $new_array;
	}
	public function array_only_values_that_not_contain($array, $string_or_array, $case_sens=true, $position='any'){
		$new_array=[];
		if (!empty($string_or_array))
		{
			if( $this->is_array($string_or_array) )
			{
				foreach($array as $key=>$block)  {
					if ( !$this->contains_AgainstArray($block, $string_or_array, $case_sens, $position) ){
						$new_array[$key]=$block;
					}
				}
			}
			else{
				foreach($array as $key=>$block)  {
					if ( !$this->contains($block, $string_or_array, $case_sens, $position) ){
						$new_array[$key]=$block;
					}
				}
			}
		}
		return $new_array;
	}


	public static function is_array($data){
		return is_array($data) || is_object($data);
	}

	public function arrayKeyRename($array, $keyToRemove, $keyToAdd){
		if (array_key_exists($keyToRemove,$array)) 
		{
			$array[$keyToAdd]=$array[$keyToRemove];
			unset($array[$keyToRemove]);
		}
		return $array;
	}
	
	public function arrayKeyRenameRecursive($array, $keyToRemove, $keyToAdd){
		$array = $this->arrayKeyRename($array, $keyToRemove, $keyToAdd);
		$new_array =[];
		if (is_array($array))
		{
			foreach($array as $key=>$value)
			{
				$new_array[$key]= !is_array($value) ? $value : $this->arrayKeyRenameRecursive($value, $keyToRemove, $keyToAdd);
			}
		}
		else{
			$new_array = $array;
		}
		return $new_array;
	}
	
	//another : https://stackoverflow.com/a/49993735/2377343
	public function arrayChangeKeyCaseRecursive(&$arr, $case = CASE_LOWER)
	{
		return array_map(function($item) use($case) {
			if(is_array($item))
				$item = $this->arrayChangeKeyCaseRecursive($item, $case);
			return $item;
		}, array_change_key_case($arr, $case));
	}
	public function arrayKeyLowercase(&$arr, $case = CASE_LOWER)
	{
		$arr = array_change_key_case($arr, $case);
		$arr = array_map(function(&$item) use($case) {
			if(is_array($item))
				$this->arrayKeyLowercase($item, $case);
			return $item;
		}, $arr );
	}
	

	public function insertValueAtPosition($arr, $insertedArray, $position) {
		$i = 0;
		$new_array=[];
		foreach ($arr as $key => $value) {
			if ($i == $position) {
				foreach ($insertedArray as $ikey => $ivalue) {
					$new_array[$ikey] = $ivalue;
				}
			}
			$new_array[$key] = $value;
			$i++;
		}
		return $new_array;
	}

	public function array_has_subchild_with_value($array,$subchild_key, $subchild_value, $strict=true){
		$found = false;
		foreach($array as $key=>$child){
			$val =  $this->array_value($child, $subchild_key);
			if     ( $strict && $val===$subchild_value)
				$found=true;
			elseif (!$strict && $val==$subchild_value)
				$found=true;
		}
		return $found;
	} 

	// supports asterisks i.e. *->keyname  ( https://pastebin_com/BcC8ztDp )
	public function array_with_keys($array_or_object, $keyToRemain)
	{
		$asterisk       = '*'; //should always have child ->
		$child_divisor  = '->';
		$keyToRemain_ALL= is_array($keyToRemain)?$keyToRemain:[$keyToRemain];

		if (is_array($array_or_object))
		{
			$new_arr 	  = [];
			//normal
			$keysToRemain_NORMAL = array_filter($this->array_only_values_that_not_contain($keyToRemain_ALL, $child_divisor) );
			$keysToRemain_REGEX  = array_filter($this->array_only_values_that_contain($keyToRemain_ALL, $child_divisor) );
			foreach( $array_or_object as $key_1=>$val_1 )
			{
				if ( in_array($key_1, $keysToRemain_NORMAL) ){ 
					$new_arr[$key_1]=$val_1;
				}
			}
			foreach( $array_or_object as $key_1=>$val_1 )
			{
				if(array_key_exists($key_1, $new_arr) )
					continue;
				foreach( $keysToRemain_REGEX as $eachRegex )
				{
					$parts = explode($child_divisor, $eachRegex, 2 );
					$first = $parts[0];
					$second= $parts[1];
					if( $first===$asterisk || $first===$key_1){
						if(is_array($array_or_object[$key_1])){
							$sVals=  $this->array_with_keys($array_or_object[$key_1],$second);
							if (empty($new_arr[$key_1])) $new_arr[$key_1]=[];
							$new_arr[$key_1]= array_merge_recursive($new_arr[$key_1],$sVals); 
						}
						elseif( $first===$key_1 ){
						//	$new_arr[$key_1]=$array_or_object[$key_1];
						}
					}
				}	
			}
			return $new_arr;
		}
		elseif(is_object($array_or_object ))
		{ 
			foreach($array_or_object as $key=>$value) {
				if( !in_array($key,$keyToRemain_ALL) ){
					unset($array_or_object->$key);
				}
			}
			return $array_or_object;
		}
		else{
			return $array_or_object;
		}
	} //old: https://pastebin_com/Tbm8sEGH 

	
	// supports asterisks i.e. *->keyname  ( https://pastebin_com/BcC8ztDp )
	public function array_unset_keys($array_or_object, $keysToUnset)
	{ 
		$asterisk       = '*'; //should always have child ->
		$child_divisor  = '->';
		$keysToUnset_ALL= is_array($keysToUnset)?$keysToUnset:[$keysToUnset];
		
		$is_arr=is_array($array_or_object);
		$is_obj=is_object($array_or_object);
		if ($is_arr || $is_obj)
		{
			$new_arr      = [];
			$keysToUnset_NORMAL = array_filter($this->array_only_values_that_not_contain($keysToUnset_ALL, $child_divisor) );
			$keysToUnset_REGEX  = array_filter($this->array_only_values_that_contain($keysToUnset_ALL, $child_divisor) );

			foreach( $array_or_object as $key_1=>$val_1 )
			{
				if ( $is_arr && !in_array($key_1, $keysToUnset_NORMAL)){
					$new_arr[$key_1]=$val_1;
				}
				if( $is_obj && in_array($key_1, $keysToUnset_NORMAL)){
					unset($array_or_object->$key_1);
				}
			}

			foreach( ($is_arr ? $new_arr : $array_or_object) as $key_1=>$val_1 )
			{
				foreach( $keysToUnset_REGEX as $eachRegex )
				{
					$parts = explode($child_divisor, $eachRegex, 2 );
					$first = $parts[0];
					$second= $parts[1];
					if( $first===$asterisk || $first===$key_1 ){
						if( 
							($is_arr && !is_object($new_arr[$key_1]) && !is_array($new_arr[$key_1])) 
								|| 
							($is_obj && !is_object($array_or_object->$key_1) &&  !is_array($array_or_object->$key_1)  ) 
						)
						{
							//$new_arr[$key_1]=$array_or_object[$key_1];
						}
						else{
							if ( $is_arr )
								$new_arr[$key_1]=$this->array_unset_keys($new_arr[$key_1],$second);
							if ( $is_obj )
								$array_or_object->$key_1=$this->array_unset_keys($array_or_object->$key_1,$second); 
						}
					}
				}	
			}
			if($is_arr) return $new_arr;
			if($is_obj) return $array_or_object;
		}
	}
	///////////////


	public function non_empty_arrayyyy($x=array()){ if (!is_array($x) || empty($x) || (is_array($x) && count($x)==1 && $x[0]==null)  ){ return array('');} else return $x; }

	public function arrayKeyEquals($array, $key, $value)
	{
		return (is_array($array) && array_key_exists($key, $array) && $array[$key]==$value); //(!empty($array)
	}
	public function arrayKeyValue($array, $key, $value)
	{
		return ( is_array($array) && array_key_exists($key, $array) ? $array[$key] : $value);
	}
	
	public static function array_part($array, $amount, $from="start|end")
	{
		return self::array_trim($array, $amount, $from);
	}
	public static function array_trim($array, $amount, $from="start|end")
	{
		$count = count($array);
		return $count<=$amount ? $array : array_slice($array, $from="start" ? 0 : $count-$amount, $amount);
	}

	//add only in case the array didnt containted it already
	public function Add_in_array_if_not_already_added($my_arrayy,$target_value){
		if (array_search($target_value, $my_arrayy) !== true) {	$my_arrayy[] = $target_value;}			return $my_arrayy;
	}

	//remove item from array by value
	public function unset_by_value(&$my_arrayy, $target_value, $reindex=false){
		$new=[];
		if (!empty($my_arrayy) && is_array($my_arrayy) ) {
			foreach ($my_arrayy as $key => $value){  
				if ($value != $target_value) { 
					if(!$reindex)
						$new[$key] = $my_arrayy[$key]; 
					else
						$new[] = $my_arrayy[$key]; 
				}   
			}
		}
		$my_arrayy = $new;
		return $my_arrayy;
	}

	public function array_is_associative(array $array) {
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}
	
	public function isAssociative(array $arr){
		if (array() === $arr) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	
	public function array_get_by_subkey($array, $subkey, $subvalue){
		foreach ($array as $each){
			//if()
		}
		return [];
	}

	public function nextKeyInArray($target_keyname, $array){
		$keys = array_keys($array);
		$index_of_target_keyname = array_search($target_keyname,  $keys , true);
		return (count($array) > $index_of_target_keyname+1 ) ? $keys[$index_of_target_keyname+1]  :  $keys[0];
	}

	public function nextValueInArray($target_value, $array, $by_key=false){
		$keys = array_keys($array);
		$target_keyname = $by_key ? $target_value : array_search($target_value,  $array, true );
		$index_of_target_keyname = array_search($target_keyname,  $keys, true );
		return (count($array) > $index_of_target_keyname+1 ) ? $array[ $keys[$index_of_target_keyname+1] ]  :  $array[  $keys[0]  ];
	}

	public function array_search($targetValue, $array){
		foreach($array as $key=>$value){
			if ($value===$targetValue)
				return $key;
		}
		return false;
	}

	
	public function stringContainsArrayValues($string, $array, $case_sensitive=false){
		return $this->stringContainsArrayValueAny($string, $array, $case_sensitive);		
	}
	public function stringContainsArrayValueAny($string, $array, $case_sensitive=false){
		$found='';
		$string = $case_sensitive ? $string : strtolower($string);
		$array = $case_sensitive ? $array : array_map('strtolower', $array);
		foreach($array as $each)
		{
			if (stripos($string,$each)!==false){
				$found=$each; break;
			}
		}
		return $found;
	}
	public function stringContainsArrayValueAll($string, $array, $case_sensitive=false){
		$result=true;
		$string = $case_sensitive ? $string : strtolower($string);
		$array  = $case_sensitive ? $array  : array_map('strtolower', $array);
		foreach($array as $each)
		{
			if (stripos($string,$each)===false){
				$result=false; break;
			}
		}
		return $result;
	}
	
	public function arrayValuesContainString($array, $string, $case_sensitive=false){
		$found=false;
		$string = $case_sensitive ? $string : strtolower($string);
		foreach($array as $each)
		{
			$each = $case_sensitive ? $each : strtolower($each);
			if (stripos($each,$string)!==false){
				$found=true; break;
			}
		}
		return $found;
	}

	
	public function array_member($array, $whichNum=0){
		return $array[ array_keys($array)[$whichNum] ];
	}

	public function array_members($array, $from_start=0, $from_end=0){
		$i=0;
		$new_arr=[];
		$count = count($array);
		foreach($array as $key=>$value)
		{
			$i++;
			if ($i<= $from_start )
				$new_arr[$key]=$value;
			if ($i> $count-$from_end )
				$new_arr[$key]=$value;
		}
		return $new_arr;
	}

	public function getIndexOfKey($array, $key){
		return array_search($key, array_keys($array) );
	}
	public function getIndexOfValue($array, $key){
		return array_search($key, $array );
	}

	public function getMemberByIndex($array, $idx){
		$keys= array_keys($array);
		return (!empty($keys) && !empty($array[$keys[$idx]])) ? $array[$keys[$idx]] : null ;
	}

	public function resortArrayByKey($array, $key, $remove_current= false){
		$remaining =  array_splice ($array, $this->getIndexOfKey($array, $key)   );
		if($remove_current){
			$array[$key]= $remaining[$key];
			unset($remaining[$key] );
		}
		return array_merge($remaining, $array);
	}
	
	//in multi dimensional array
	public function findArrayByKeyValue($array, $key, $value){
		foreach($array as $subArray){
			if (array_key_exists($key, $subArray) && $subArray[$key]==$value){
				return $subArray;
			}
		}
		return [];
	}
	public function findObjectByKeyValue($array, $key, $value){
		$item = null;
		foreach($array as $struct) {
			if (property_exists($struct,$key) && $struct->{$key} == $value) {
				return $struct;
			}
		}
		return new \stdClass();
	}

	// array_diff ( array $array , array ...$arrays ): Compares array against one or more other arrays and returns the values in array that are not present in any of the other arrays.
	//response only gives the items, that are in ARRAY_1, and doesn't matter if there are more things in ARRAY_2. See sample: pastebin_com/AUx4238H
	public function array_diff_assoc_recursive( $array_source, $array_toward)
	{
		$difference = [];
		if (empty($array_toward))
			return $array_source;
			
		foreach($array_source as $key => $value_1){
			// if target array does not have this key
			if ( 
				(is_array($array_source) && !array_key_exists($key, $array_toward) )
				||
				(is_object($array_source) && !property_exists($array_toward, $key) )
			)
			{
				$difference[$key] = $value_1;
			}
			else {
				$value_2 = is_array($array_toward) ? $array_toward[$key] :  $array_toward->$key;
				// if member is simple-type
				if( !is_array($value_1) && !is_object($value_1) &&$value_1!=$value_2 ){
					$difference[$key] = $value_1; 
				}
				// if member is array/object
				elseif ( is_array($value_1) || is_object($value_1) ) {
					if ( !is_array($value_2) && !is_object($value_2) ) {
						$difference[$key] = $value_1;
					}
					else{
						$newDiff = $this->array_diff_assoc_recursive($value_1, $value_2 );
						if( !empty($newDiff) )
						{
							$difference[$key] = $newDiff;
						}
					}
				}
			}
		}
		return $difference;
	}
	public function array_diff_recursive( $array_source, $array_compared_to )
	{
		return [];
	}
	public function array_difference( $array_source, $array_compared_to )
	{
		$diff = [
			'first'=> $this->array_diff_assoc_recursive($array_source, $array_compared_to),
			'second'=> $this->array_diff_assoc_recursive($array_compared_to, $array_source),
		];
		
		$diff['added'] = [];
		$diff['changed'] = [];
		$diff['removed'] = [];
		foreach ($diff['first'] as $keyNm => $block) {
			if ( !array_key_exists($keyNm, $diff['second']) ) 
				$diff['added'][$keyNm]=$block; 
			else 
				$diff['changed'][$keyNm]=$block; 
		}
		foreach ($diff['second'] as $keyNm => $block) {
			if ( !array_key_exists($keyNm, $diff['first']) ) 
				$diff['removed'][$keyNm]=$block; 
		}
		return $diff;
	}
	
	public function array_diff_key_full( $array_source, $array_toward )
	{
		$diff = [
			'added'=> array_diff_key($array_source, $array_toward),
			'removed'=> array_diff_key($array_toward, $array_source),
		];
		return $diff;
	}
	
	


	public function array_difference_full( $array_source, $array_toward )
	{
		$difference = [ 'new'=>[], 'old'=>[], 'different'=>[] ];
		if (empty($array_toward)){
			$difference['old'] = $array_source;
		}
		else{
			foreach($array_toward as $key => $value){
				// if target array does not have this key, then tell that this key is new
				if ( !array_key_exists($key, $array_source) )
				{
					$difference['new'][$key] = $value;
				}
			}

			foreach($array_source as $key => $value){
				// if target array does not have this key, then tell that this key is new
				if ( !array_key_exists($key, $array_toward) )
				{
					$difference['old'][$key] = $value;
				}
				else {
					$value_2 = $array_toward[$key];
					// if member is simple-type
					if( !is_array($value) )
					{
						if ( $value_2 != $value ) 
						{
							$difference['different'][$key] = $value;
						}
					}
					// if member is array/object
					else { 
						// if array
						if ( is_array($value) ) 
						{
							if (!is_array($value_2) ) {
								$difference['different'][$key] = $value;
							}
							else{
								$newDiff = $this->array_difference_full($value, $value_2);
								if( !empty($newDiff) )
								{
									$difference['different'][$key] = $newDiff;
								}
							}
						}
					}
				}
			}
		}
		return $difference;
	}

	public static function array_keys_diff($ar1, $ar2){ return self::array_diff_keys($ar1, $ar2); }
	public static function array_diff_keys($ar1, $ar2){
		$k1=array_keys($ar1);  $k2=array_keys($ar2);
		return [ array_diff($k1, $k2), array_diff($k2, $k1) ];
	}

	public function array_intersect_assoc_recursive(&$arr1, &$arr2) {
		if (!is_array($arr1) || !is_array($arr2)) {
	//      return $arr1 == $arr2; // Original line
			return (string) $arr1 == (string) $arr2;
		}
		$commonkeys = array_intersect(array_keys($arr1), array_keys($arr2));
		$ret = array();
		foreach ($commonkeys as $key) {
			$var= $this->array_intersect_assoc_recursive($arr1[$key], $arr2[$key]);
			$ret[$key] = & $var;
		}
		return $ret;
	}
	
	public function array_emptify_keys($array, $insert_key_in_body='')
	{
		$new=[];
        foreach($array as $key1=>$block) {
			$new[]=$block;
		}
		return $new;
	}
	public function array_emptify_keys_sub($array, $insert_key_in_body='')
	{
		$new=[];
        foreach($array as $key1=>$block1) {
			foreach($block1 as $key2=>$block2) {
				if(!empty($insert_key_in_body))
				{
					if(is_object($block2))
						$block2->$insert_key_in_body = $key2;
					else
						$block2[$insert_key_in_body] = $key2;
				}
				$new[$key1][]=$block2;
			}
		}
		return $new;
	}
	 

	public function array_merge($obj_or_array1, $obj_or_array2)
	{
		$is_obj = is_object($obj_or_array1);
		if ($is_obj){
			return (object) array_merge((array) $obj_or_array1, (array) $obj_or_array2);
		}
		else{
			return array_merge($obj_or_array1, $obj_or_array2);
		}
	}
    public function array_merge_sub($array, $unique=false) {
        $final_arr=[];
        foreach($array as $block) $final_arr=array_merge($final_arr, $block);
        return ($unique ? array_unique($final_arr) : $final_arr);
    }
    public function array_merge_sub_sub($array, $which_key, $unique=false) {
        $final_arr=[];
        foreach($array as $block) 
			$final_arr=array_merge($final_arr, $this->array_value($block,$which_key) );
        return ($unique ? array_unique($final_arr) : $final_arr);
    }
	public function array_merge_recursive_distinct ( array $array1, array $array2 )
	{
	  $merged = $array1;
	  foreach ( $array2 as $key => &$value )
	  {
		if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
		{
		  $merged[$key] = $this->array_merge_recursive_distinct ( $merged [$key], $value );
		}
		else
		{
		  $merged[$key] = $value;
		}
	  }
	  return $merged;
	}

	public function recursive_for_array_value($array,$function_name=false){ 
		//on first run, we define the desired function name to be executed on values
		if ($function_name) { $GLOBALS['current_func_name']= $function_name; } else {$function_name=$GLOBALS['current_func_name'];}
		//now, if it's array, then recurse, otherwise execute function
		return is_array($array) ? array_map('recursive_for_array_value', $array) : $function_name($array); 
	}


	public function array_sort_by_key($array, $key, $direction=SORT_ASC){
		$is_obj= is_object($array);
		if ($is_obj)
			$array = (array) $array;
		$columns = array_column($array, $key);
        if ( array_multisort($columns, $direction, $array) )
			return ($is_obj ? (object) $array : $array);
		else
			throw new \Exception("Multisort failed");
	}

	public function keyAtIndex($index, $array){
		$keys = array_keys($array);
		return $keys[$index];
	}

	public function keyAfterKey($keyname, $array, $increment){
		$keys = array_keys($array);
		$current_key_index = array_search($keyname, $keys);
		return $keys[array_search($keyname,$keys)+$increment];
	}

	public function arraySetKeysFromChild($array, $keyName){
		$new=[];
		foreach($array as $key=>$value) { if (isset($value[$keyName])) $new[$value[$keyName]]=$value;   }
		return $new; 
	}
	public function array_column_with_keys($array,$keyName){ 
		$new=[]; foreach($array as $key=>$value) { if (isset($value[$keyName])) $new[$key]=$value[$keyName];   }
		return $new;  
	}

	public function ArrayColumnWithKey2($array,$keyName){
		return array_filter(array_combine(array_keys($array), array_column($array, $keyName)));
	}
	
	public function ArrayOnlyWithKey($array, $keyName, $target_level){
		$new	= []; 
		$old	= $array; 
		$value_0= $array;
		$cur_level = 0;
		if (is_array($value_0) && $target_level>=$cur_level)
		foreach($value_0 as $key_1=>$value_1) { 
			$cur_level = 1;
			if ($cur_level == $target_level && $key_1 == $keyName)
			{
				$new[$key_1] = $value_1;
				unset($old[$key_1]);
			}
			else{
				if (is_array($value_1) && $target_level>=$cur_level)
				foreach($value_1 as $key_2=>$value_2) { 
					$cur_level = 2;
					if ($cur_level == $target_level && $key_2 == $keyName)
					{
						$new[$key_1][$key_2] = $value_2;
						unset($old[$key_1][$key_2]);
					}
					else{
						if (is_array($value_2) && $target_level>=$cur_level)
						foreach($value_2 as $key_3=>$value_3) { 
							$cur_level = 3;
							if ($cur_level == $target_level && $key_3 == $keyName)
							{
								$new[$key_1][$key_2][$key_3] = $value_3;
								unset($old[$key_1][$key_2][$key_3]);
							}
							else{
								if (is_array($value_3) && $target_level>=$cur_level)
								foreach($value_3 as $key_4=>$value_4) { 
									$cur_level = 4;
									if ($cur_level == $target_level && $key_4 == $keyName)
									{
										$new[$key_1][$key_2][$key_3][$key_4] = $value_4;
										unset($old[$key_1][$key_2][$key_3][$key_4]);
									}
									else{
										if (is_array($value_4) && $target_level>=$cur_level)
										foreach($value_4 as $key_5=>$value_5) { 
											$cur_level = 5;
											if ($cur_level == $target_level && $key_5 == $keyName)
											{
												$new[$key_1][$key_2][$key_3][$key_4][$key_5] = $value_5;
												unset($old[$key_1][$key_2][$key_3][$key_4][$key_5]);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return ['with'=>$new, 'without'=>$old];  
	}


	public function array_last( $array )
	{
		if (is_array($array))
		{
			$keys = array_keys($array);
			$key_last = $keys[ count($keys)-1];
			return $array[$key_last];
		}
		elseif (is_object($array))
		{
			$keys = get_object_vars($array);
			$key_last = $keys[count($keys)-1];
			return $array->$key_last;
		}
	}






	// #################### conversions ##########################

	public function php_to_js_array($array){
		return '["'. implode('","', $array ) .'"]';
	}
	public function xmlToArray($content)
	{
		try
		{
			$xml = simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOCDATA);
			return json_decode( json_encode($xml), TRUE);
		} catch (Exception $ex) {
			return ['xmlerror'=>$ex];
		}
	}

	public function xmlToArrayByKey($content, $keyName, $removeIfKeyIsOnlyChild=false)
	{
		try
		{
			$xml = simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOCDATA );
			$array= json_decode( json_encode($xml), TRUE);
			return $this->xmlSetChild($array, $keyName, $removeIfKeyIsOnlyChild);
		} catch (Exception $ex) {
			return ['xmlerror'=>$ex];
		}
	}
	public function xmlSetChild($array, $keyName, $removeIfKeyIsOnlyChild=false)
	{
		$new_array= [];
		foreach ($array as $key_1=>$value_1)
		{
			if (is_array($value_1) && isset($value_1[0]))
			{
				$sub_arr=[];
				foreach ($value_1 as $idx=>$value_2)
				{
					$keyValue = $value_2['@attributes'][$keyName];
					if( $removeIfKeyIsOnlyChild && count($value_2)==1 && count($value_2['@attributes'])==1 ) 
						$new_array[$key_1][$keyValue] = null;
					else
						$new_array[$key_1][$keyValue] = $this->xmlSetChild($value_2, $keyName, $removeIfKeyIsOnlyChild);
				}
			}
			else{
				$new_array[$key_1]=$value_1;
			}
		}
		return $new_array;
	}

	public static function xml_to_array($xml_string, $replace_colons=false, $opts=0 ){
		return json_decode(self::xml_to_json($xml_string, $replace_colons, $opts), true);
	}
	public static function xml_to_json($xml_string, $replace_colons=false, $opts=0 ){
		if ($opts===0) 
			$opts = LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_NOENT ; //https://www.php.net/manual/en/libxml.constants.php : LIBXML_NOCDATA | LIBXML_COMPACT  | LIBXML_DTDLOAD |   
		if ($replace_colons)
			$xml_string = self::xml_tag_replace_colons($xml_string);
		$xml = simplexml_load_string($xml_string, null, $opts);  
		return json_encode($xml);
	}

	public static function xml_tag_replace_colons($xmlData){
		return preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1$2_', $xmlData);
	}
	
	
	public function array_to_xml_output($array) {
		$xml_data = new \SimpleXMLElement('<?xml version="1.0"?><xml_data></xml_data>');
		$this->array_to_xml($array, $xml_data);
		//$result = $xml_data->asXML('/file/path/name.xml');
		return $xml_data->asXML();
	}

	public function array_to_xml( $data, &$xml_data ) {
		foreach( $data as $key => $value ) {
			if( is_numeric($key) ){	$key = 'item'.$key; } //dealing with <0/>..<nuemric/> issues
			if( is_array($value) ) { $subnode = $xml_data->addChild($key);	array_to_xml($value, $subnode);	} 
			else {	$xml_data->addChild("$key",htmlspecialchars("$value"));	}
		}
	}

	//##############  ARRAYs #################

	
	public function unquote($txt){
		return str_replace('"', "", str_replace("'", "", $txt) );
	} 


	public function getExtension($fileUrl)
	{
		$array=explode('.', basename(parse_url($fileUrl)['path']));
		return $array[count($array)-1]; 
	}
	public function filename($fileUrl)
	{
		$array=explode('.', basename(parse_url($fileUrl)['path']));
		$min = min(count($array), 2);
		return basename($array[$min-2]); 
	}

	public function ListAllInDir($path, $only_files = false) {
		$all_list = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
				( $only_files ? \RecursiveIteratorIterator::LEAVES_ONLY : \RecursiveIteratorIterator::SELF_FIRST )
		);
		$files = [];
		foreach ($all_list as $file)
			$files[] = $file->getPathname();

		return $files;
	}


	
	public function setProperty($obj, $property, $value) {
		$reflection = new \ReflectionClass($obj);
		$property = $reflection->getProperty($property);
		$property->setAccessible(true);
		return $property->setValue($obj, $value);
	}


	public function is_localhost($domain=''){
		return in_array( (!empty($domain) ? $domain : $this->domainCurrentWithoutPort), ['localhost','127.0.0.1','::1']); 
	}
	

	public function is_JSON($string){
		return $this->is_JSON_string($string);
	}
	public function maybe_json($string){
		$firstLetter = substr($string, 0, 1); 
		$maybe_json = $firstLetter==='{' || $firstLetter==='[';
		return $maybe_json;
	}
	

	public function JsonData($string){
		if ( !is_string($string) ) return null;
		$x = json_decode($string, true);
		if (!is_array($x)) return null;
		return $x;
	}

	public function is_JSON_string($string){
		return (is_string($string) && is_array(json_decode($string, true)));
	}

	public function arrayed_json($answer){
		$result = [];
		if(!$this->is_JSON_string($answer)){
			$result['error'] = $answer;
		}
		else{
			$result = json_decode($answer, true);
		}
		return $result;
	}

	public function arrayed_answer($answer){
		$result = [];
		if(!$this->is_JSON_string($answer)){
			$result['error'] = $answer;
		}
		else{
			$result = json_decode($answer, true);
		}
		return $result;
	}

	
	


	#region ################  CACHE ###############
	public $CACHE_CHOSEN_PROGRAM= 'redis';
	public function cache_get($key, $default=null){
		return call_user_func([$this, "cache_get_{$this->CACHE_CHOSEN_PROGRAM}"], $key, $default);
	}
	public function cache_set($key, $data, $seconds = 8640000){
		return call_user_func([$this, "cache_set_{$this->CACHE_CHOSEN_PROGRAM}"], $key, $data, $seconds);
	}
	public function cache_append($key, $data, $seconds = 8640000){
		return call_user_func([$this, "cache_append_{$this->CACHE_CHOSEN_PROGRAM}"], $key, $data, $seconds);
	}

	#region ### phpRedis (better than "pRedis" ) [edit: 02mxypZ1 ] ###
	private $redis_instance = null; 
	public $redis_host = ['127.0.0.1', 6379];
	public function cache_get_redis($key, $default=null){
		$this->helper_cache_redis_init($this->redis_host);
		$key = $this->slug . $key;
		$redis = $this->helper_redis_getInstance();
		$val = $redis->get($key);
		$this->helper_redis_IfSwooleCloseNeeded($redis);
		if (!$val)
			return $default;
		return (self::is_serialized($val) ? unserialize($val) : $val);
	}
	public function cache_set_redis($key, $data, $seconds = 8640000){
		$this->helper_cache_redis_init($this->redis_host);
		$key = $this->slug . $key;
		$redis =$this->helper_redis_getInstance();
		$result = $redis->set($key, ($this->is_simple_type($data) ? $data : serialize($data)), $seconds );
		$this->helper_redis_IfSwooleCloseNeeded($redis);
		return $result;
	}
	public function cache_append_redis($key, $data, $seconds = 8640000){
		$this->helper_cache_redis_init($this->redis_host);
		$existing_arr = $this->cache_get_redis($key,[]);
		$new  = $this->is_array($existing_arr) ? $this->array_merge($existing_arr,$data) : $existing_arr. $data;
		$final_value  = ($this->is_simple_type($new) ? $new : serialize($new));
		return $this->cache_set_redis($key, $final_value, $seconds);
	}
	public function cache_clear_redis(){
		$this->helper_cache_redis_init($this->redis_host);
		$this->helper_redis_getInstance()->flushAll();
	}
	// helpers, with added swoole support
	private $redis_pool=null;   private $redis_start_inited=false;
	public function helper_cache_redis_init($server_array){
		try{
				
			if (self::swoole_inside_coroutine()){
				if ( is_null($this->redis_pool) ){
					$this->redis_pool = new \Swoole\Database\RedisPool( (new \Swoole\Database\RedisConfig)->withHost($this->redis_host[0])->withPort($this->redis_host[1])->withAuth('')->withDbIndex(0)->withTimeout(1)
					);
				}
			}
			else {
				if ( is_null($this->redis_instance) ){
					$this->redis_instance= new \Redis(); 
					$this->redis_instance->connect( $server_array[0], $server_array[1]);
					// SERIALIZER_NONE | SERIALIZER_PHP | SERIALIZER_IGBINARY | SERIALIZER_MSGPACK );
					$this->redis_instance->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
					$this->redis_instance->setOption(\Redis::OPT_PREFIX, __NAMESPACE__.':');	// use custom prefix on all keys
				} 
			}
		}
		catch(\Exception $ex){
			if (get_class($ex)==="RedisException"){
				if (!$this->redis_start_inited){
					$this->redis_start_inited=true;
					self::asyncFunctions_helper_exec_command('sudo service redis-server start');
					sleep(2);
					$this->helper_cache_redis_init($server_array);
				}
			}
		}
	} 
	public function helper_redis_getInstance()
	{
		return (self::swoole_inside_coroutine() ? $this->redis_pool->get() : $this->redis_instance);
	}
	public function helper_redis_IfSwooleCloseNeeded($redis_instance)
	{
		if ( self::swoole_inside_coroutine() ){
			$this->redis_pool->put($redis_instance);
		}
	}
	#endregion 

	// memcached: todo 
	#endregion  ################ CACHE ALL ################
	


	//from Wordpress codex
	public static function is_serialized( $data, $strict = true ) { if ( ! is_string( $data ) ) { return false; } $data = trim( $data ); if ( 'N;' === $data ) { return true; } if ( strlen( $data ) < 4 ) { return false; } if ( ':' !== $data[1] ) { return false; } if ( $strict ) { $lastc = substr( $data, -1 ); if ( ';' !== $lastc && '}' !== $lastc ) { return false; } } else { $semicolon = strpos( $data, ';' ); $brace     = strpos( $data, '}' ); if ( false === $semicolon && false === $brace ) { return false; } if ( false !== $semicolon && $semicolon < 3 ) { return false; } if ( false !== $brace && $brace < 4 ) { return false; } } $token = $data[0]; switch ( $token ) { case 's': if ( $strict ) { if ( '"' !== substr( $data, -2, 1 ) ) { return false; } } elseif ( false === strpos( $data, '"' ) ) { return false; } case 'a': case 'O': return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data ); case 'b': case 'i': case 'd': $end = $strict ? '$' : ''; return (bool) preg_match( "/^{$token}:[0-9.E+-]+;$end/", $data ); } return false; }


	//https://www.php.net/manual/en/errorfunc.constants.php
	public function errors_exception(){
		//set_error_handler("warning_handler", E_WARNING);
		set_error_handler(function($errno, $errstr, $errfile, $errline) {
			// error was suppressed with the @-operator
			if (0 === error_reporting()) {
				return false;
			}
			
			throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
		});
	}


	// TODO - handle $_POST
	public function disable_cache($hard=false, $file=false){
		header("Expires: Mon, 4 Jan 1999 12:00:00 GMT");        // Expired already 
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");     
		header("Cache-Control: no-cache, must-revalidate");      // good for HTTP/1.1 
		header("Pragma: no-cache"); 
		if($hard){
			if(!isset($_GET['rand']))
				$this->php_redirect( $this->AddStringToUrl($_SERVER['REQUEST_URI'], 'rand='.rand(1,9999999) )   );
		}
		ini_set("opcache.enable", 0); 
		if($file){
			opcache_invalidate($file);
		}
	}


		
	public function my_mail($a=null,$b=null,$c=null,$d=null,$e=null){ return (!$this->definedTRUE("MAILS_DISABLED") ? mail($a,$b,$c,$d,$e) : "MAILS_NOT_ENABLED__error99234"); }

	public function get_yout_Vid_Aud_array($ID,$TITL)	{return yout_DownUrls($ID, $TITL);}

	public function default_mail_headers($from=false){ return $headers='MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n" . 'From: mesg@' .$_SERVER['HTTP_HOST'] ."\r\n".'Reply-To: mesg@'.$_SERVER['HTTP_HOST'] . "\r\n" . "X-Mailer: PHP/" . phpversion(); }	
		


	//use whenever you want to show something on the first happening
	// first_cookie_message('ini_get_noexits','<script>alert("ini_get doesnt work on server. i will hide forever now");</script>')
	public function first_cookie_message($identifier, $message){
		$cName=filter_var($identifier, FILTER_SANITIZE_STRING);
		if (!isset($_COOKIE[$cName])){
			setcookie($cName,'okk',time()+99999999, $this->definedVALUE('homeFOLD','/'));
			die($message);
		}
	}

	public function CookieSet($name){ if (empty($_COOKIE[$name])) { return false;} else { return true;} }
	public function CookieSetOnceExecution($name){ if (empty($_COOKIE[$name])) { setcookie($name, time(), time()+ 999999,  $this->definedVALUE('homeFOLD','/') ); return true; } return false; }
	public function CookieNotSet($name){ CookieSetOnceExecution($name); }

	public function set_cookie($name, $val, $time_length = 86400, $path=false, $domain=false, $httponly=true){
		$site_urls = parse_url( (function_exists('home_url') ? home_url() : $_SERVER['SERVER_NAME']) );
		$real_domain = $site_urls["host"];
		$path = $path ? $path : ( (!empty($this) && property_exists($this,'homeFOLDER') ) ?  $this->homeFOLDER : '/');
		$domain = $domain ? $domain : ((substr($real_domain, 0, 4) == "www.") ? substr($real_domain, 4) : $real_domain);
		setcookie ( $name , $val , time()+$time_length, $path = $path, $domain = $domain,  $only_on_secure_https = FALSE,  $httponly  );
	}
	public function setcookie_secure($name, $val, $time_length = 86400, $httponly=true, $homeurl=false){
		$real_domain = $homeur ?: $_SERVER['HTTP_HOST'];
		$domain = (substr($real_domain, 0, 4) == "www.") ? substr($real_domain, 4) : $real_domain;
		$path = $path ?: ( (!empty($this) && property_exists('pathAfterDomain', $this) ) ?  $this->pathAfterDomain : '/');
		setcookie ( $name , $val , time()+$time_length, $path, $domain = $domain ,  $only_on_https = FALSE,  $httponly  );
	}

	public function page_load_limited_for_seconds($seconds = 3, $cookiename = 'pageloader_limiter'){
		if (isset($_COOKIE[$cookiename])) {
			
		}
	}
	public function siteSlug() { return str_replace(array('.','/',':'),'_', $this->domain  ); }

	public function site_visitor_default_cookiee() {return 'default_visitr_'.siteSlug(); }

	public function SetCookieForVisitors(){ setcookie(site_visitor_default_cookiee(), time()+1000, time()+1000, $this->definedVALUE('homeFOLD','/'));  }
	//      SetCookieForVisitors();

	public function die_if_not_this_site_youtube(){if (!isset($_COOKIE[site_visitor_default_cookiee()])) {  die('noauth_6453'); } }

	public $share_urls =
	[
		'facebook'	=>'https://www.facebook.com/sharer/sharer.php?u=', 
		'twitter'	=>'https://twitter.com/share?url='
	];

	public function validate_mail( $mail ){  //$_POST['email']
		return !filter_var( $mail, FILTER_VALIDATE_EMAIL );
	}

	// only for explicit temp use
	public function password_site($password, $hint="Type password")
	{
		$rnd_ext = 'pss_'.str_replace('.','_', $this->domain);
		if ( isset($_POST['passwk']) && $password == $_POST['passwk'] ) { setcookie($rnd_ext, $password,  time()+1111111,  $this->homeFOLD); header("location:".$_SERVER['REQUEST_URI']);exit; } 
		elseif (!isset($_COOKIE[$rnd_ext]) || $_COOKIE[$rnd_ext]!=$password ){ echo '<div style="display:flex; justify-content:center;"><form action="" method="post">  <b>'.$hint.'</b>:<input name="passwk" value="">  <input type="submit" value="Enter"></form></dvi>';exit;}
	}	

	public function get_filename_($url){ return basename(parse_url($url)['path']); }

	public function scriptt($name, $with_css=false)	{ 
		return  ( (!empty($GLOBALS['already_loaded_'.$name])) ? '<!-- already outputed "'.$name.'" -->' :  $GLOBALS['already_loaded_'.$name]='<script type="text/javascript" src="'. $GLOBALS['odd']['scripts'][$name]['js'].'"></script>')  
			.  
		( !$with_css ? '' : '<link rel="stylesheet" href="'.  $GLOBALS['odd']['scripts'][$name]['css'].'"> '   );
	}

	public function scriptss(){
		foreach(func_get_args() as $key=>$value){ echo (!is_array($value) ? scriptt($value) : scriptt($value[0], $value[1]) ); }
	}


	public function translate__MONTH($text,$target_lang=''){   global $odd;	//switch ($text) { case 'January':	return TRANSLL('monthh1',$target_lang);
		if( !empty($odd['months_langs'][$target_lang]) && array_key_exists($text, $odd['months_langs'][$target_lang]))	{  
			$text = $odd['months_langs'][$target_lang][$text];
			if (mb_detect_encoding($text) =='UTF-8') {$text= mb_substr ($text,0,3,'utf-8') ; }  
		} 
		else{
			$text = TRANSLL($text,$target_lang);
		}
		return $text;
	}


	public function translate__DAY($text,$target_lang='') {	
		if (in_array($text, array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')) ) {
			return TRANSLL($text,$target_lang); 
		} return $text;
	}

	// language specifics
	public function GEO_to_ENG($input){  return strtr($input, array(
		"�?"=>"a",	"ბ"=>"b",	"გ"=>"g",	"დ"=>"d",	"ე"=>"e",	"ვ"=>"v",	"ზ"=>"z",	"თ"=>"T",	"ი"=>"i",
		"კ"=>"k",	"ლ"=>"l",	"მ"=>"m",	"ნ"=>"n",	"�?"=>"o",	"პ"=>"p",	"ჟ"=>"J",	"რ"=>"r",	"ს"=>"s",
		"ტ"=>"t",	"უ"=>"u",	"ფ"=>"f",	"ქ"=>"q",	"ღ"=>"R",	"ყ"=>"y",	"შ"=>"S",	"ჩ"=>"C",	"ც"=>"c",
		"ძ"=>"Z",	"წ"=>"w",	"ჭ"=>"W",	"ხ"=>"x",	"ჯ"=>"j",	"ჰ"=>"h"    	));
	}
	public function ENG_to_GEO($input) { return strtr($input, array(
		'a'=>'�?',	'b'=>'ბ',	'g'=>'გ',	'd'=>'დ',	'e'=>'ე',	'v'=>'ვ',	'z'=>'ზ',	'T'=>'თ',	'i'=>'ი',
		'k'=>'კ',	'l'=>'ლ',	'm'=>'მ',	'n'=>'ნ',	'o'=>'�?',	'p'=>'პ',	'J'=>'ჟ',	'r'=>'რ',	's'=>'ს',
		't'=>'ტ',	'u'=>'უ',	'f'=>'ფ',	'q'=>'ქ',	'R'=>'ღ',	'y'=>'ყ',	'S'=>'შ',	'C'=>'ჩ',	'c'=>'ც',
		'Z'=>'ძ',	'w'=>'წ',	'W'=>'ჭ',	'x'=>'ხ',	'j'=>'ჯ',	'h'=>'ჰ'		));
	}

	//UPPERCASE CHARS sometimes MESS-UP several FUNCTION's USAGE. So, sometimes we need lowercased words
	public function GEO_to_ENG__LowerCased($m) { return strtolower(strtr($m, array( 
		"�?"=>"a",	"ბ"=>"b",	"გ"=>"g",	"დ"=>"d",	"ე"=>"e",	"ვ"=>"v",	"ზ"=>"z",	"თ"=>"t",	"ი"=>"i",
		"კ"=>"k",	"ლ"=>"l",	"მ"=>"m",	"ნ"=>"n",	"�?"=>"o",	"პ"=>"p",	"ჟ"=>"dj",	"რ"=>"r",	"ს"=>"s",
		"ტ"=>"t",	"უ"=>"u",	"ფ"=>"f",	"ქ"=>"q",	"ღ"=>"gh",	"ყ"=>"y",	"შ"=>"sh",	"ჩ"=>"ch",	"ც"=>"c",
		"ძ"=>"dz",	"წ"=>"w",	"ჭ"=>"tch",	"ხ"=>"x",	"ჯ"=>"j",	"ჰ"=>"h"    	)));
	}

	public function Rus_To_Eng__LowerCased($input){  return strtr($input, array(
		"а"=>"a","�?"=>"a",		"б"=>"b","Б"=>"b",		"в"=>"v","В"=>"v",		"г"=>"g","Г"=>"g",		"д"=>"d","Д"=>"d",
		"е"=>"e","Е"=>"e",		"ё"=>"yo","�?"=>"yo",	"ж"=>"zh","Ж"=>"zh",	"з"=>"z","З"=>"z",		"и"=>"i","И"=>"i",
		"й"=>"j","Й"=>"j",		"к"=>"k","К"=>"k",		"л"=>"l","Л"=>"l",		"м"=>"m","М"=>"m",		"н"=>"n","�?"=>"n",
		"о"=>"o","О"=>"o",		"п"=>"p","П"=>"p",		"р"=>"r","Р"=>"r",		"�?"=>"s","С"=>"s",		"т"=>"t","Т"=>"t",
		"у"=>"u","У"=>"u",		"ф"=>"f","Ф"=>"f",		"х"=>"kh","Х"=>"kh",	"ц"=>"ts","Ц"=>"ts",	"ч"=>"ch","Ч"=>"ch",
		"ш"=>"sh","Ш"=>"sh",	"щ"=>"sch","Щ"=>"sch",	"ъ"=>"","Ъ"=>"",		"ы"=>"y","Ы"=>"y", 		"ь"=>"","Ь"=>"",
		"�?"=>"e","Э"=>"e",		"ю"=>"yu","Ю"=>"yu",	"�?"=>"ya","Я"=>"ya",    ));
	}
	public function ic1251_to_utf8($s){
		$s= str_replace('С?',$a1='fgr43443443',$s);
		$s= str_replace('Р?',$a2='tg5gh45h3hg3',$s);
		$s= str_replace('пїЅпїЅ?',$a3='fgr35gh35hg3gdfw',$s);
		$s= str_replace('СЊС?',$a4='XXX83rhf423888df8d23d1',$s);
		$s= str_replace('бѓ?',$a5='XXX83rhf423888df8d23d2',$s);
		$s= mb_convert_encoding($s, "windows-1251", "utf-8");
		$s= str_replace($a5,'ი',$s);
		$s= str_replace($a3,'ი',$s);
		$s= str_replace($a1,'ш',$s);
		$s= str_replace($a2,'И',$s);
		$s= str_replace($a4,'шь',$s);
		return $s;
	}

	public function INCORRECT_GEO_to_ENG($input){  return strtr($input, array(
		"áƒ�?"=>"a", "áƒ‘"=>"b", "áƒ’"=>"g",  "áƒ“"=>"d",  "áƒ�?"=>"e",  "áƒ•"=>"v",  "áƒ–"=>"z",  "áƒ—"=>"T",  "áƒ˜"=>"i",  "áƒ™"=>"k", "áƒš"=>"l",  "áƒ›"=>"m",  "áƒœ"=>"n",  "áƒ�?"=>"o", "áƒž"=>"p",  "áƒŸ"=>"J",  "áƒ "=>"r",  "áƒ¡"=>"s",    "áƒ¢"=>"t",  "áƒ£"=>"u",  "áƒ¤"=>"f",  "áƒ¥"=>"q",  "áƒ¦"=>"R",  "áƒ§"=>"y",  "áƒ¨"=>"S",  "áƒ©"=>"C",  "áƒª"=>"c",  "áƒ«"=>"Z",  "áƒ¬"=>"w",  "áƒ­"=>"W",  "áƒ®"=>"x",  "áƒ¯"=>"j",  "áƒ°"=>"h"   ));
	}
	
	// # language specifics
	
	
	public function validate_post_id($id)	 { if (!is_numeric($id) || strlen($id)>7) 								{die("incorrrrrect_postid error81"); }}
	public function validate_simple_word_of_s_GET($text){if (preg_match('/[\<\>\'\=\$\"\?\(\{]/si',$text))			{die("incorrrrrect error86");}}
	//
	// Validation
	public function validate_url($url)	{ return filter_var($url, FILTER_VALIDATE_URL) !== false && (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url)); }  
	public function validate_email($email)	{
		if(empty($email))  return false;
		$regex = '/'. ($name='([a-z0-9_.-]+)').  ($at='@').  ($sub_domain='([a-z0-9.-]+){2,255}') . ($period='.').  ($ext='([a-z]+){2,10}'). '/i';
		return empty(preg_replace($regex, '', $email) );
	}
	// ########## Sanitization ##########
	// https://php.net/manual/en/filter.filters.sanitize.php
	
	public static function sanitize($key){ return preg_replace( '/[^a-zA-Z0-9_\-]/', "_", trim($key) ); }  //same as wp
	public static function sanitize_key($key, $use_dash=false ){ return preg_replace( '/[^a-z0-9_\-]/', ($use_dash===true ? "_": (is_string($use_dash) ? $use_dash: "") ), strtolower(trim($key) )); }  //same as wp  , 
	public static function sanitize_key_($key, $use_dash=true ){ return self::str_replace_recursive( "__","_",  self::sanitize($key, $use_dash) ); }
	public static function sanitize_text($str,$use_dash=false) { return preg_replace("/[^a-zA-Z0-9\!\@\#\$\%\^\&\*\(\)\-\_\+\=\,\.\/\?\;\[\]\{\}\|\s]+/", ($use_dash ? "_":""), trim($str)); }	 //  \= \/ 
	//Try this to remove everything except a-z, A-Z and 0-9, -, _, .
	public static function sanitize_nonoword($text)		{ return preg_replace('/\W/si','',$text); }   
	public static function sanitize_alhpabet($key){ return preg_replace( '/[^a-zA-Z]/',"", $key); }

	public static function sanitize_text_entities($str,$use_dash=false){ return self::sanitize_text(htmlentities($str,$use_dash)); }	
	public static function sanitize_text_filter($string) { return filter_var($string,FILTER_SANITIZE_STRING);}
			// other versions
			// [^a-zA-Z0-9\-\_\.]
			// return strtr($input, [ " "=>"-",	"."=>"--",	":"=>"--",	","=>"-",	"/"=>"-",	";"=>"--",	"—"=>"",	"–"=>"-" ]);
			// str_replace(array(' ','-',',','.','/','\\','|','!','@','#','$','%','^','&','*','(',')'),'_',   strip_tags( trim($str) ));
			// preg_replace('/[^\w\d_\-]/', '',  filter_var($input,	FILTER_SANITIZE_STRING)	);
	public static function sanitize_digits($string){ return filter_var($string,FILTER_SANITIZE_NUMBER_INT);}
	public static function sanitize_url($string)  	{ return filter_var($string,FILTER_SANITIZE_SPECIAL_CHARS);}
	public static function SanitizeSymbol($str)	{ return str_replace(array('/','\\','|','!','*'), '_',   strip_tags( strtoupper(trim($str) )) ) ; } 
	public static function sanitize_url_dots($url)	{ return self::remove_double_slashes(str_replace('/..','', str_replace('\\','', $url) ) ); }

	public static function sanitize_unicode($text, $replace_with=''){ 
		$x= preg_replace('/[\x00-\x1F\x7F-\xFF]/', $replace_with, $text); 
		return preg_replace('/['.$replace_with.']+/', $replace_with, $x);
	}

	public static function sanitize_text_field($text)
	{
		if(function_exists('sanitize_text_field'))
			return sanitize_text_field($text);
		else
			return self::sanitize_text($text);
	}
	public static function sanitize_text_field_recursive($data)
	{
		if ( empty($data) ) {
			return $data;
		}
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( is_array( $value ) ) {
					$value = self::sanitize_text_field_recursive($value);
				} else {
				   $value = stripslashes(sanitize_text_field($value));
				}
				$data[$key] = $value;
			}
			return $data;
		}
		return sanitize_text_field($data);
	}

	public static function sanitize_comma_array($string, $type="key")
	{
		$values = explode(',', $this->sanitize_text_field($string));
		$sanitized_values = $values;
		$sanitized_values = array_map('sanitize_key', $sanitized_values);
		$sanitized_values = array_map('trim', $sanitized_values);
		$sanitized_text = implode(',', $sanitized_values);
		return $sanitized_text;
	}
	public static function remove_new_lines($text)
	{
		return str_replace(["\r","\n"],'', $text);
	}
	public static function newline($amount=1)
	{
		return str_repeat("\r\n",$amount);
	}
	//
	public static function removWhitespaces($input, $oneSpace=true){ 
		$what = $oneSpace ? ' ':'';
		$input= str_replace("   ",		$what,$input );
		$input= str_replace("  ",		$what,$input );
		$input= str_replace("\t\t",		$what,$input );
		$input= str_replace("\t",		$what,$input );
		$input= str_replace("\r\n\r\n",	$what,$input );
		$input= str_replace("\r\n ",	$what,$input );
		if (!$oneSpace){
			$input= str_replace(" ",	$what,$input );
		}
		return $input;
	}
	

	public static function stripCOODs($input){ return strip_shortcodes(strip_tags($input, '<h1></h1><br><br/><br /><br/ ><br / >< br>< br/>'));}

	public static function str_replace_recursive($value, $replace, $string) {
		$string = str_replace($value, $replace, $string);
		if (strpos($string, $value)!==false) {
			$string= self::str_replace_recursive($value, $replace, $string);
		}
		return $string;
	}

	// remove dots (.) and "after plus part" (+xxxx) from gmail address
	public function sanitize_gmail($user_mail)
	{
		$sanitized_email = preg_replace_callback( '/(.*)\@/si', 
			function($matches){return str_replace('.','',$matches[0]); },  
			preg_replace( '/\+.*\@/s', '@', $user_mail )
		);
		return $sanitized_email;
	}

	public function sanitize_utf8_filenamee($input){
		$filename_sanitized = $this->GEO_to_ENG__LowerCased($input);
		$filename_sanitized = $this->Rus_To_Eng__LowerCased($filename_sanitized);
		$filename_sanitized = str_replace(' ','-',$filename_sanitized);
		$filename_sanitized = utf8_encode($filename_sanitized);
		return $filename_sanitized;
	}
	public function remove_html_parts($content)
	{
		$content = preg_replace('/(.*)\<body\>/si','',$content);
		$content = preg_replace('/<script(.*?)script\>/si','',$content);
		$content = preg_replace('/<iframe(.*?)iframe\>/si','',$content);
		$content = preg_replace('/\<\/body(.*)/si','',$content);
		return trim($content);
	}
	
	public function ensure($value, $array){
		if (in_array($value, $array))
			return $value;
		else {
			$msg =  "Provided value was not in array. Value: " . $this->var_dump($value) . "\r\n<br/>Array:".$this->var_dump($array) ;
			throw new \Exception( $msg );
		}
	}
	
	public function benchmark_function($callback, $amount=100000, $hint=''){
		$before = microtime(true);
		$val=[];
		for ($i=0 ; $i<$amount; $i++) {
			$val[]=call_user_func($callback);
		}
		$after = microtime(true);
		echo "Benchmark results for $hint: ". self::number_format($after-$before, 5) . " sec\n<br/>";
		return $val;
	}
	
	public function decode_encoded_utf8($string){
		return preg_replace_callback('#\\\\u([0-9a-f]{4})#ism', function($matches) { return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE"); }, $string);
	}

	// directory correction
	public function directory_canonicalize3($address)
	{
		$address = explode('/', $address);
		$keys = array_keys($address, '..');

		foreach($keys AS $keypos => $key)
		{
			array_splice($address, $key - ($keypos * 2 + 1), 2);
		}

		$address = implode('/', $address);
		$address = str_replace('./', '', $address);

		return $address;
	}

	public function directory_canonicalize2($address)
	{
		$address =preg_replace_callback(
			'/(.*?|)\/(.*?)(\/..*?)\b/i',  
			function ($matches){
				if(!empty($matches[3])){
					return ($matches[3]);
				}
				return $matches[0];
			},
			$address
		);
		return $address;
	}

	public function RemoveParameterFromUrl($full_url, $param_name){
		return $final = preg_replace('/(\&|\?)'.$param_name.'(\=(.*?(&|#)|.*)|)/i', (!empty('$4') ? '$4' : ''), $full_url);
	}
	
	public function remove_query_from_url($url, $which_argument=false){ 
		return preg_replace( '/'.  (  $which_argument ? '(\&|)'.$which_argument.'(\=(.*?)((?=&(?!amp\;))|$)|(.*?)\b)' : '(\?.*)').'/i' , '', $url);  
	}
	public function get_query_from_url($url){ 
		$query= $this->array_value(parse_url($url), 'query','');
		parse_str($query, $output);
		return $output;
	}
	
	public function checked_if_value($array, $key){
		return ( $this->array_value($array, $key)  ? ' checked="checked"' : '');
    }
	// ##################################

	
	// Output decimals better, i.e.  $x= 0.000021;  or  $x= 123424235.325434645
	// method 1
	public static function remove_zero_from_end($input){
		return floatval($input);//

		// Method 2 : 
		// while( ($last=substr($input,-1))=="0" || $last=="." )
		// 	$input= substr($input,0,-1);
		// return $input;

		// Method 3 :
		// return strpos($nbr,'.')!==false ? rtrim(rtrim($nbr,'0'),'.') : $nbr;
	}
	public function add_zero_in_front($num, $maxLength){
		$finalNum = $num;
		while( strlen($finalNum)<$maxLength ){
			$finalNum = "0".$finalNum;
		}
		return $finalNum;
	}
	public function trim_zero_dot($input){
		$sanitized=rtrim( $input, "0");
		if(substr($sanitized, -1) =="."){
			$sanitized=substr($sanitized,0, -1);
		}
		return $sanitized;
	}

	public function doubleNormal($input, $round_to=15, $use_sprintf=true){ 
		return (!is_float($input) && !is_numeric($input) ? $input : (float) $this->trim_zero_dot( $use_sprintf ? sprintf("%.{$round_to}f", $input) : self::number_format($input, $round_to) ) );	
	}
	public static function number_format($input, $decimals=15, $method=1){ 
		if ($method===1)
			$ret = number_format($input, $decimals, null, ''); 
		else 
			$ret =sprintf("%.{$decimals}f", $input); 
		return self::remove_zero_from_end($ret);
	}
	

	
	// method 2
	public function decimal_outputer($input, $length=5, $only_dot=false){  
		$timeParts = explode('.', $input);
		if(count($timeParts)<=1) return $input;
		return ($only_dot ? '' : $timeParts[0] . '.') . substr($timeParts[1], 0, $length); //sprintf('%.10F',$input); 
	}
	//
	public function doubleNormalArray($array){
		return $this->array_map_deep([$this,'doubleNormal'], $array);
	}

	
	public function cut__my($text, $chars, $points = "...") {  $text = strip_tags($text);	if( strlen($text) <= $chars) { return $text;} else { return mb_strimwidth($text,0,$chars, $points,'utf-8'); } }
	public function trim_string($text, $chars, $points = "...") {  if( strlen($text) <= $chars) { return $text;} else { return mb_strimwidth($text,0, $chars, $points,'utf-8'); } }

	public function myUTF8truncate($string, $width){
		if (mb_str_word_count($string) > $width) {
			$string= preg_replace('/((\w+\W*|| [\p{L}]+\W*){'.($width-1).'}(\w+))(.*)/', '${1}', $string);
		}
		return $string;
	}
	
	
	public function customm_word_length_sentence($got_content,$words_length,$StripOrNot=true, $preserved=''){
		$got_content = trim($got_content); 			//https://php.net/manual/en/function.trim.php
		//$got_content = strip_shortcodes($got_content); //https://stackoverflow.com/a/20403438/2165415
		$got_content = str_replace(']]>', ']]>', $got_content);
		$got_content= str_replace("\n",' ',$got_content);
		$got_content= str_replace("\r",' ',$got_content);
		$got_content = !$StripOrNot ? $got_content : strip_tags($got_content,$preserved) ;
		$words = explode(' ', $got_content, $words_length + 1);
		if(count($words) > $words_length) :
			array_pop($words);
			array_push($words, '…');
			$got_content = implode(' ', $words);
		endif;
		return $got_content;	
	}

	public function unicode_words_count($string) {	preg_match_all('/[\pL\pN\pPd]+/u', $string, $matches);	return count($matches[0]);}
	public function text_splitt($msg, $word_numbs) {
		$msg = preg_replace('/[\r\n]+/', ' ', $msg);
		$chunks = wordwrap($msg, $word_numbs*20 , '\n', true);
		return explode('\n', $chunks);
	}
	
	public function trim_to_charlength($text, $charlength) {
		$charlength++;

		if ( mb_strlen( $text ) > $charlength ) {
			$subex = mb_substr( $text, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				echo mb_substr( $subex, 0, $excut );
			} else {
				echo $subex;
			}
			echo '...';
		} else {
			echo $text;
		}
	}

	
	// ### substr shorthands:
	public function charsFromStart($word, $amount)
	{
		return substr($word, 0, $amount);
	}
	public function charsFromEnd($word, $amount)
	{
		return substr($word, -$amount);
	}
	public function charsWithoutStartEnd($word, $removeFromStart, $removeFromEnd)
	{
		return substr($word, $removeFromStart, -$removeFromEnd);
	}
	

	// makes a string from an assiciative array
	public function implodeAssoc($glue,$arr) 
	{ 
		$keys=array_keys($arr); 
		$values=array_values($arr);
		return(implode($glue,$keys).$glue.implode($glue,$values)); 
	}

	
	public function url_correction_for_html_output($content){ 
		return preg_replace_callback( 
			'/\<(img|link|iframe|frame|frameset|script|embed|video|audio)([^>]*)/si', 
			function($matches) { return '<'.$matches[1].preg_replace('/=(\"|\')(http(s|):)/si','=$1', $matches[2]);	}, 
			$content
		);
	}
	
	public function array_max_pair($array){
		$peakVal = -999999999999;
		$peakPair = [];
		foreach($array as $key=>$value){
			if ($value>=$peakVal){
				$peakVal=$value;
				$peakPair=[$key,$value];
			}
		}
		return $peakPair;
	}
	public function array_min_pair($array){
		$peakVal = +999999999999;
		$peakPair=[];
		foreach($array as $key=>$value){
			if ($value<=$peakVal){
				$peakVal=$value;
				$peakPair=[$key,$value];
			}
		}
		return $peakPair;
	}
	
	public function equals_string($content, $target){ 
		return strtolower($content)===strtolower($target);
	}

	public static function contains($content, $needle, $case_sens=true, $position='any'){ 
		if ($position==='start'){
			return self::startsWith($content, $needle, $case_sens);
		}
		elseif ($position==='end'){
			return self::endsWith($content, $needle, $case_sens);
		}
		else{
			return ($case_sens ? strpos($content, $needle)!==false : stripos($content, $needle)) !== false;
		}
	}

	public static function contains_AgainstArray($content, $needles_array, $case_sens= true, $position='any'){   
		foreach($needles_array as $needle){
			if (self::contains($content, $needle, $case_sens, $position) ){
				return true;
			}
		}
		return false;
	}

	// https://stackoverflow.com/a/860509/2377343
	public static function startsWith($haystack, $needle, $case_sens=true) {
		return $needle === "" || 
		( 
			( $case_sens && strpos($haystack, $needle, 0) === 0 )
				||
    		( !$case_sens && stripos($haystack, $needle, 0) === 0 )
		)
		; 
	}
	public static function startsWith_AgainstArray($haystack, $needles_array, $case_sens=true) { 
		foreach($needles_array as $needle){
			if (self::startsWith($haystack, $needle, $case_sens))
				return true;
		}
		return false;
	}
	public static function endsWith($haystack, $needle, $case_sens=true) { 
		$expectedPosition = strlen($haystack) - strlen($needle);
		if ($case_sens)
			return strrpos($haystack, $needle, 0) === $expectedPosition;
		return strripos($haystack, $needle, 0) === $expectedPosition;
	}
	public static function endsWith_AgainstArray($haystack, $needles_array, $case_sens=true) { 
		foreach($needles_array as $needle){
			if (self::endsWith($haystack, $needle,$case_sens))
				return true;
		}
		return false;
	}


	public function startsWithRemove($haystack, $needle) { return (!$this->startsWith($haystack, $needle) ? $haystack : substr($haystack, strlen($needle) ) ); }
	public function endsWithRemove($haystack, $needle) { return (!$this->endsWith($haystack, $needle) ? $haystack : substr($haystack, 0, -1 * strlen($needle) ) ); }
	public function startsWithReplace($haystack, $needle, $replace) { return (!$this->startsWith($haystack, $needle) ? $haystack : $replace.substr($haystack, strlen($needle) ) ); }

	public function die_if_not_this_site_visitor(){ //if half day passed
		if (empty($_COOKIE['ytdow___']) || $_COOKIE['ytdow___'] > time()*3 + 43200 ) {die('incorrect_download_<b>123</b>.<script type="text/javascript">top.window.location = "http://'.$_SERVER['HTTP_HOST'].'";</script>');}
	}

	public function js_redirect($url=false, $echo=true){
		$str = '<script>window.location = "'. ( $url ?: $_SERVER['REQUEST_URI'] ) .'"; document.body.style.opacity=0; </script>';
		if($echo) { exit($str); }  else { return $str; }
	}

	public function php_redirect($url=false, $code=302){
		//avoid redirection from customizer: if (!empty($_COOKIE['MLSS_cstRedirect']) || defined('MLSS_cstRedirect')) {return;}
		header("Cache-Control: no-store, no-cache, must-revalidate"); header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");   
		header("location: ". ( $url ?: $_SERVER['REQUEST_URI'] ), true, $code); exit;
	}
	public function redirect($url=false, $code=302){
		return $this->php_redirect($url,$code);
	}
	public function js_redirect_message($message,$url=false){
		echo '<script>alert(\''.$message.'\');</script>';
		$this->js_redirect($url);
	}
				
	public function get_output(callable $funct, $clear=true){  
		ob_start();
		$res = call_user_func($funct);
		if ($clear)
		{
			$cont= ob_get_clean(); 
			ob_flush(); 
		}
		else{
			$cont= ob_get_contents(); 
		}
		// $cont= ob_get_contents();
		//ob_get_clean();
		return $cont; 
	}	
	//output js header 
	public function get_js_header_output(){  

        header("Pragma: public");
        header("Cache-Control: public, maxage=".$expires);
		header("Content-type: application/javascript;  charset=utf-8");
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
	}
	public static function swap_pair($pairname, $divisor='/'){
		$arr= explode($divisor, $pairname);
		return (count($arr)<=0 ? $pairname : $arr[1].$divisor.$arr[0]);
	}
	
	public static function words_before_after_regex($content, $searchPhrase, $wordsAmount)
	{
		preg_match_all('/(?:[^\s]+\s){0,'.$wordsAmount.'}'.$searchPhrase.'(?: [^ ]+){0,'.$wordsAmount.'}/si', $content, $matches);
		return $matches; 	//preg_match_all('/(.*?)'.$query.'(.*)/i', $badxml_data, $matches);
	}
	



	#region ### TELEGRAM FUNCTIONS ###
	// public function telegram($text) { return $helpers->telegram_message( ['chat_id'=>'-1001234567890', 'text'=>$text, 'parse_mode'=>'html', 'disable_web_page_preview'=>true ],   $bot_key ); }          | resp: pastebin_com/u0J1Cph3
	public function telegram_message($array, $botid, $repeated_call=false){
		$array['disable_web_page_preview'] = array_key_exists('disable_web_page_preview', $array) ?  $array['disable_web_page_preview'] : true;
		$array['text'] = $this->br2nl($array['text']);
		$array['text'] = strip_tags($array['text'],'<b><strong><i><em><u><ins><s><strike><del><a><code><pre>'); // allowed: https://core.telegram.org/bots/api#html-style
		$array['text'] = substr($array['text'],0,4093); //max telegram message length 4096
		$res = $this->get_remote_data_array( ['url'=>'https://api.telegram.org/bot'.$botid.'/sendMessage', 'post'=> $array], true );  //'sendMessage?'.http_build_query($array, '');
		if (!$res->error){
			$answer = $res->response;
			$response = json_decode($answer);
			// if it was successfull
			if ($response->ok)
			{
				return $response;
			}
			//i.e. {"ok":false,"error_code":400,"description":"Bad Request: can't parse entities: Unsupported start tag \"br/\" at byte offset 43"}
			else{
				// for some reason, if still unsupported format submitted, resubmit the plain format
				$txt = "Bad Request: can't parse entities";
				if( stripos($response->description, $txt) !==false ){
					$array['text'] = "[SecondSend] \r\n". strip_tags($array['text']) ;
					if ( ! $repeated_call ){
						return $this->telegram_message($array, $botid, true);
					}
				}
				return $response;
			} 
		}	
		else{
			return (object)['ok'=> false, 'description'=> $res ];
		}
	}

	public $telegram_interval_ms = 40; //~30 times per second, so we'd better 40ms
	private $telegram_last_time=0;

	public function telegram_message_cached($array, $botid){
		$curMS  = $this->timeMS();
		$goneMS = $curMS - $this->telegram_last_time;
		if ( $goneMS < $this->telegram_interval_ms ){
			$this->usleep( ($this->telegram_interval_ms-$goneMS) *1000 );
		}
		$this->telegram_last_time = $curMS;

		$key = $this->cache_key_create(array_merge($array, [$botid]));
		if ( ! $this->is_cached_id('function__telegram_message_cached', $key) )
			return $this->telegram_message($array, $botid);
		else 
			return (object)( ["ok"=>true, "success"=>false, 'reason'=>"$key was cached", 'content'=> json_encode($array) ] );
	}

	public function telegram_message_cached_with_channel($array, $botid){
		$answer = $this->telegram_message_cached ( $array, $botid );
		$this->telegram_channel_name_save($answer);
		return $answer;
	}

	public function telegram_channel_name_save($response){
		$existing = $this->telegram_channel_name_get();
        $res = $response; //already decoded
		if ( $this->array_value($res,'ok') )
		{
			// check to ensure (because cached ids dont have result)
			if ( $this->array_value($res,'result') )
			{
				$id    = $res->result->chat->id;
				$title = $res->result->chat->title;
				$type  = $res->result->chat->type;  //group or channel
				$existing[$id] = (object)['title'=>$title, 'type'=>$type];
				update_option('telegram_channel_names_temp', $existing);
			}
		}
	} 
	public function telegram_channel_name_get($id=''){
		$channelsArray = get_option('telegram_channel_names_temp',[]);
		return !empty($id) ? $this->array_value($channelsArray, $id) : $channelsArray;
	}




	// https://github.com/ttodua/useful-php-scripts/blob/master/get-remote-url-content-data.php 
	public static function get_remote_data($url, $post_params=null, $request_options=null, $force_static=false)	
	{
		$func = $post_params ? "wp_remote_post" : "wp_remote_get";
		$is_wp = (function_exists($func));
		$request_options = !empty($request_options)? $request_options : [];

		if (!$is_wp || ($force_static && is_callable('parent::get_remote_data'))  )
		{
			return parent::get_remote_data($url, $post_params, $request_options);
		}
		else
		{ 
			if($func=="wp_remote_get") {
				$out= wp_remote_get($url, $request_options );
			}
			if($func=="wp_remote_post") {
				$post_array = (is_array($post_params)) ? $post_params : (parse_str($post_params , $new) ? $new : $new );		
				$args['body']=$post_array;
				$args= array_merge($args, $request_options);
				$out= wp_remote_post($url, $args );
				//$out= call_user_func($func, $url, $args );
			}
			return wp_remote_retrieve_body($out); //same as $out['body'] 
		}
		return "empty_data. Create your own remote function";
	}

	public function get_remote_data_array($arr, $force_curl=false, $repeated_call=false)
	{
		if (is_array($arr))
		{
			$url 			= $arr['url'];
			$post_params	= $this->array_value($arr,'post',    null);
			$request_options= $this->array_value($arr,'options', []);	if ($request_options==null) $request_options=[];
			$should_be_json = $this->array_value($arr,'json',    false);
			$retry			= $this->array_value($arr,'retry',   true);
		}
		else{
			$url 			= $arr;
			$post_params	= null;
			$request_options=[];
			$should_be_json = true;
			$retry			= true;
		}

        $request_options = array_merge($request_options, ['headers'=>['Cache-Control'=>'no-cache']] );

		$data = $this->get_remote_data($url, $post_params, $request_options, $force_curl) ;
		if (empty($data))
		{
			$res= (object)['error'=>'empty data', 'response'=>''];
		}
		else{
			if ($should_be_json)
			{
				$dataTemp = $this->JsonData($data);
				if (is_null($dataTemp))
					$res= (object)['error'=>'not json', 'response'=>($data) ];
				else
					$res= (object)['error'=>false, 'response'=>json_decode($data) ];
				/*
					try { 
						$res= (object)['error'=>false, 'response'=>json_decode($data) ];
					}
					catch(\Exception $ex){
						$res= (object)['error'=>'not json', 'response'=>($data) ];
					}
				*/
			}
			else{
				$res= (object)['error'=>false, 'response'=>$data ];
			}
		}
		// if still error, and retry allowed
		if ($res->error && $retry && !$repeated_call)
		{
			$this->usleep(100000);
			$res = $this->get_remote_data_array($arr, $force_curl, $repeated_call=true);
		}
		return $res;
	}

	 


    //i.e. set_cookies_from_url("http://example.com/?username=user&auth=key');
    public function set_cookies_from_url($url)
    {
        $d=$this->get_remote_data($url, false, ["curl_opts"=>["CURLOPT_HEADERFUNCTION"=>
            ( function ($ch, $headerLine) {
                if (preg_match('/^Set-Cookie:\s*([^;]*)/mi', $headerLine, $cookieArr) == 1)
                {
                    $cookie = $cookieArr[1];
                    $cookie_vars = explode('=', $cookie, 2);
                    $this->example_cookies[$cookie_vars[0]] = $cookie_vars[1];
                }
                return strlen($headerLine); // Needed by curl
                }
            )
            ]]
        );
        foreach($this->example_cookies as $key=>$name)
        {
            $this->set_cookie($key,$name, 86000, '/target_dir/');
        }
        $this->set_cookie("sample_confirm","1");
    }
   
	// ----
	public function get_client_ip() {
		$proxy_headers = array("CLIENT_IP", "FORWARDED", "FORWARDED_FOR", "FORWARDED_FOR_IP", "HTTP_CLIENT_IP", "HTTP_FORWARDED", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED_FOR_IP", "HTTP_PC_REMOTE_ADDR", "HTTP_PROXY_CONNECTION", "HTTP_VIA", "HTTP_X_FORWARDED", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED_FOR_IP", "HTTP_X_IMFORWARDS", "HTTP_XROXY_CONNECTION", "VIA", "X_FORWARDED", "X_FORWARDED_FOR");
		foreach($proxy_headers as $proxy_header) {
			if (isset($_SERVER[$proxy_header])) {
				if(preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $_SERVER[$proxy_header])) {
					return $_SERVER[$proxy_header];
				}
				else if (stristr(",", $_SERVER[$proxy_header]) !== FALSE) {
					$proxy_header_temp = trim(array_shift(explode(",", $_SERVER[$proxy_header])));
					if (($pos_temp = stripos($proxy_header_temp, ":")) !== FALSE) {$proxy_header_temp = substr($proxy_header_temp, 0, $pos_temp); }
					if (preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $proxy_header_temp)) { return $proxy_header_temp; }
				}
			}
		}
		return $_SERVER["REMOTE_ADDR"];
	}

	
	// $ipinfo = json_decode(getIpInfo($_SERVER['REMOTE_ADDR']), true);
	// if($ipinfo['country_name'] != 'Georgia'){
	public function getIpInfo($ip, $type=1, $api=""){
		$info="";
		if($type==1){
			$info = $this->get_remote_data('https://geoip-db.com/json/'.$ip);	
			//"country_code":"GE", "country_name":"Georgia", "city":"null", "postal":null, "latitude":42, "longitude":43.5, "IPv4":"xxx.xxx.xxx.xxx", "state":"null"
		}
		elseif($type==2){
			// PLEASE DONT USE THIS API
			$info_initial = $this->get_remote_data('https://geoipify.whoisxmlapi.com/api/v1?apiKey='.$api.'&ipAddress='.$ip);	
			// {"ip":"xxx.xxx.xxx.xxx","location":{"country":"AU","region":"Victoria","city":"Research","lat":-37.7,"lng":145.1833,"postalCode":"3095","timezone":"Australia\/Melbourne"}}
			$decoded = json_decode($info_initial, true);
			$loc =$decoded['location'] ;
			unset($decoded['location']) ;
			$ipinfo_new = array_merge( $decoded,$loc );
			return  $ipinfo_new;
		}
		return $info;
	}


	public function CurrentSiteIs($site){ return $site == $_SERVER['HTTP_HOST']; }

	public function output_js_headers()
	{
		session_cache_limiter('none');
		// https://stackoverflow.com/a/1385982/2377343
											$year=60*60*24*365;//year
		//Caching with "CACHE CONTROL"
			header('Cache-control: max-age='.$year .', public');
		//Caching with "EXPIRES"  (no need of EXPIRES when CACHE-CONTROL enabled)
			//header('Expires: '.gmdate(DATE_RFC1123,time()+$year));
		//To get best cacheability, send Last-Modified header and ...
			header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime(__file__)));  //i.e.  1467220550 [it's 30 june,2016]
		//reply using: status 304 (with empty body) if browser sends If-Modified-Since header.... This is cheating a bit (doesn't verify the date), but remove if you dont want to be cached forever:
			// if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {  header('HTTP/1.1 304 Not Modified');   die();	}
		header("Content-type: application/javascript;  charset=utf-8");
	}

		
	public function input_fields_from_array($value, $keyname='', $replace_spaces=false){	//$keyname= (strpos($keyname,'[') === false) ? '['.$keyname.']' : $keyname;
		echo '<div class="array_fields1"><style>.array_fields1 textarea{max-height:200px!important;  border-radius: 5px; width:100%; color:#53ae14; border: 2px solid black; margin:0 0 0 0px; height:50px; }  .def_textareaa{height:70px;} .high_textarea{height:130px;} .new_block{MARGIN:0 0 0 50px; border:2px solid; border-width:0 0 0 2px;} .txtar{padding:0 0 0 25px;}  .new_block .keyname{color:rgb(248, 48, 83);} </style>';
		$this->input_fields_from_array_RECURSIVE($value, $keyname, $replace_spaces);
		echo '</div>';
	}
	public function input_fields_from_array_RECURSIVE($value, $keyname='', $replace_spaces=false){		
		if (!is_array($value)){
			$height=30; $lines=explode("\r\n",$value); 
				foreach($lines as $eachLINE){
					$height= $height+ceil(mb_strlen($eachLINE)/100) * 30; 
				}
				// replace multiple whitespaces with single
				$value =   !$replace_spaces ? $value : preg_replace('!\s+!', ' ', str_replace("\t",' ', $value));
			echo 
			'<div class="each_ln">
				<div class="keyname">'.$keyname.'</div>
				<div class="txtar"><textarea class="" style="height:'. $height.'px;" name="'.$keyname.'">'.$value.'</textarea></div>
			</div>';
		}
		else{
			echo '<div class="new_array_title">'.$keyname.'</div>';
			foreach ($value  as $keyname1=>$value1){
				echo '<div class="new_block">';
				$this->input_fields_from_array_RECURSIVE($value1, $keyname.'['.$keyname1.']',  $replace_spaces);
				echo '</div>';
			}
		}
	}

	public function random_color($alpha='FF') {
		return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT).$alpha;
	}

	public function contentHeight($content, $lineHeight=30){
		$lines=explode("\n", $content); 
		$height = $lineHeight;
		foreach($lines as $eachLINE){
			$height = $height+ceil(mb_strlen($eachLINE)/100) * $lineHeight; 
		} 
		return $height;
	}
	
	
	public function js_autosize_textarea($classname=null)
	{ ?><script>
		function autoSizeTextareas(className)
		{
			let tx = document.querySelectorAll(className);
			for (let i = 0; i < tx.length; i++) {
				tx[i].setAttribute('style', 'height:' + (tx[i].scrollHeight) + 'px;overflow-y:hidden;');
				var oninput = function () {
				  this.style.height = 'auto';
				  this.style.height = (this.scrollHeight) + 'px';
				};
				tx[i].addEventListener("input", oninput, false);
			}
		}
		</script> <?php
		if ($classname) { ?><script>document.addEventListener('readystatechange', event => {
			if (event.target.readyState === "interactive") {  
				autoSizeTextareas('<?php echo $classname;?>');
			}
		});</script><?php }
	}


	public function dropdown_from_array($array, $name, $selected){
		$out =
		'<select name="'.$name.'">';
			foreach($array as $each) $out .= 
		'<option value="'.$each.'"'. ( $each==$selected ?' selected ':'') . ">$each</option>";
		$out .= '</select>';
		return '<div>'.$out.'</div>';
	}

	public function dropdown_for_categories($ul___id_class, $ShowPlusMinusDropdown = false){	
		if (!defined('drp_already_out')) {   define('drp_already_out', true);  ?>
	<style>
	.ChildHidden {} 
	.OPCL_containtr{float:right; display: inline-block; text-align:right; height:30px;width:30px; }
	.drop_CLOSE{background:transparent url("<?php echo $this->baseURL.'library/media/other/sign-minus.png';?>") no-repeat scroll 0% 0%; }
	.drop_OPEN{background:transparent url("<?php echo $this->baseURL.'library/media/other/sign-plus.png';?>") no-repeat scroll 0% 0%;}
	.ChildHidden ul.sub-menu{display:none;}	
	.OpenCloseSp {display: inline-block;height: 30px;width: 30px;}
	zzzz body li.ChildHidden > a {display: inline-block;} 
	</style>
	<script type="text/javascript">
	public function make_element_children_dropdowned(element, ShowPlusMinusSign){
		if (element) {
			element.each(function( index,key ) { 
			  if (key.className.indexOf("menu-item-has-children") >= 0) {
				$( this ).addClass("ChildHidden");
				if (ShowPlusMinusSign) { $(this).children('a').append('<span class="OPCL_containtr"> <span class="OpenCloseSp drop_OPEN"> </span> </span>'); }
				
				$( this ).children('a').click(function() {
					if (ShowPlusMinusSign) { $(this).children('.OPCL_containtr').find('span.OpenCloseSp').toggleClass('drop_OPEN drop_CLOSE'); }
					$(this).siblings('ul.sub-menu').toggle();
					return false;
				});
			  }
			});
		}
	}
	</script>	
	<?php 
		} ?>
		<script type="text/javascript">
		var Containr = $("<?php echo $ul___id_class;?>");
		var ShowPlusMinusSign = false;  <?php if ($ShowPlusMinusDropdown) { ?> ShowPlusMinusSign = true;  <?php } ?>
		make_element_children_dropdowned(Containr,ShowPlusMinusSign);
		</script>
		<?php	
	}

	 
	public function expand_CHILD_menu_by_a_name($ul___a_class, $A_href_NAMEs=array() ){	?>
		<script type="text/javascript">
		var A_names = [<?php foreach ($A_href_NAMEs as $key=>$each) {echo '"'.$each.'"'; if($key != count($A_href_NAMEs)-1) echo ',';  } ?>];
		var Containr2 = $("<?php echo $ul___a_class;?>");
		if (Containr2) { 
			Containr2.each(function( index,key ) { 
			  if (A_names.indexOf(key.innerHTML) > -1) {
				var ff= $(this).siblings('ul.sub-menu').addClass("displayblock");
			  }
			});
		}
		</script>	
		<?php	
	}
	
	
	
	
	
	public function loader($type="")
	{
		$circlecolor="#ffffff"; 
		$head = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgb(241, 242, 243); display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">'; //< ?xml version="1.0" encoding="utf-8"? >
		
		if ($type=='infinity')
			$out = $head.'<circle cx="50" cy="50" fill="none" stroke="#292664" stroke-width="15" r="36" stroke-dasharray="169.64600329384882 58.548667764616276" transform="rotate(338.174 50 50)"> <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.8s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle>';
		elseif ($type=='eclipse')
			$out = $head.'<path d="M10 50A40 40 0 0 0 90 50A40 49.2 0 0 1 10 50" fill="#1d3f72" stroke="none" transform="rotate(235.214 50 54.6)"><animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 54.6;360 50 54.6"></animateTransform></path>';
		elseif ($type=='normal')
			$out = $head.'<path fill="none" stroke="#1d3f72" stroke-width="8" stroke-dasharray="42.76482137044271 42.76482137044271" d="M24.3 30C11.4 30 5 43.3 5 50s6.4 20 19.3 20c19.3 0 32.1-40 51.4-40 C88.6 30 95 43.3 95 50s-6.4 20-19.3 20C56.4 70 43.6 30 24.3 30z" stroke-linecap="round" style="transform:scale(0.8);transform-origin:50px 50px"><animate attributeName="stroke-dashoffset" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0;256.58892822265625"></animate></path>';
		else //dots
			$out = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 130 130" preserveAspectRatio="xMidYMid">            <g><circle cx="16" cy="64" r="16" fill="'.$circlecolor.'" fill-opacity="1"/><circle cx="16" cy="64" r="16" fill="'.$circlecolor.'" fill-opacity="0.67" transform="rotate(45,64,64)"/><circle cx="16" cy="64" r="16" fill="#ffffff" fill-opacity="0.42" transform="rotate(90,64,64)"/><circle cx="16" cy="64" r="16" fill="'.$circlecolor.'" fill-opacity="0.2" transform="rotate(135,64,64)"/><circle cx="16" cy="64" r="16" fill="'.$circlecolor.'" fill-opacity="0.12" transform="rotate(180,64,64)"/><circle cx="16" cy="64" r="16" fill="'.$circlecolor.'" fill-opacity="0.12" transform="rotate(225,64,64)"/><circle cx="16" cy="64" r="16" fill="'.$circlecolor.'" fill-opacity="0.12" transform="rotate(270,64,64)"/><circle cx="16" cy="64" r="16" fill="'.$circlecolor.'" fill-opacity="0.12" transform="rotate(315,64,64)"/><animateTransform attributeName="transform" type="rotate" values="0 64 64;315 64 64;270 64 64;225 64 64;180 64 64;135 64 64;90 64 64;45 64 64" calcMode="discrete" dur="720ms" repeatCount="indefinite"></animateTransform></g></svg>';
		$out = $out.'<!-- generated by https://loading.io/ --></svg>'; 
		return $out;
	}


	public function get_user_browser(){ 
		if (empty($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT']="unknown";
		$b = $_SERVER['HTTP_USER_AGENT']; $final =array();

		//(START FROM MOBILE check!!!!)
		if(
			preg_match('/android.+mobile|Windows Mobile|Nokia|avantgo|Mozilla(.*?)(Android|Mobile|Blackberry|Symbian)|OperaMini|Opera Mini|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|ap|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$b)
			||
			preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($b,0,4))
			)									{	$final['brwsr'] = "Mobilee";	}
		//if typical browsers
		elseif(preg_match('/Firefox/i',$b))		{	$final['brwsr'] = "Firefox";	}
		elseif(preg_match('/Safari/i',$b))		{	$final['brwsr'] = "Safari";	}
		elseif(preg_match('/Chrome/i',$b))		{	$final['brwsr'] = "Chrome";	}
		elseif(preg_match('/Flock/i',$b))		{	$final['brwsr'] = "Flock";		}
		elseif(preg_match('/Opera/i',$b))		{	$final['brwsr'] = "Opera";		}
				elseif(preg_match('/MSIE 6/i',$b))				{$final['brwsr'] = "MSIE 6";	}
				elseif(preg_match('/MSIE 7/i',$b))				{$final['brwsr'] = "MSIE 7";	}
				elseif(preg_match('/MSIE 8/i',$b))				{$final['brwsr'] = "MSIE 8";	}
				elseif(preg_match('/MSIE 9/i',$b))				{$final['brwsr'] = "MSIE 9";	}
				elseif(preg_match('/MSIE 10/i',$b))				{$final['brwsr'] = "MSIE 10";	}
				elseif(preg_match('/Trident\/7.0; rv:11.0/',$b)){$final['brwsr'] = "MSIE 11";	}
				else											{$final['brwsr'] = "UNKNOWNNN";	}
		//===========================================================================================================
		$final['full_brwsr_namee']	 = $b;
		//other parameters
		return $final;
	}


	public function get_user_OperatingSystem() { 
		if (empty($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT']="unknown";
		$user_agent=$_SERVER['HTTP_USER_AGENT']; $final =array(); $final['os_namee']="_Unknown_OS_";  $final['os_typee']="_Unknown_OS_";
		$os_array=array(
			'MOUSED'	=> array(
				'/windows nt 10.0/i'=>'Windows 10', '/windows nt 6.3/i'=>'Windows 8.1', '/windows nt 6.2/i'=>'Windows 8', '/windows nt 6.1/i'=>'Windows 7',	'/windows nt 6.0/i'=>'Windows Vista','/windows nt 5.2/i'=>'Windows Server 2003/XP x64', '/windows nt 5.1/i'=>'Windows XP', '/windows xp/i'=>'Windows XP','/windows nt 5.0/i'=>'Windows 2000','/windows me/i'=>'Windows ME','/win98/i'=>'Windows 98','/win95/i'=>'Windows 95','/win16/i'=>'Windows 3.11',
				'/macintosh|mac os x/i' =>'Mac OS X','/mac_powerpc/i'=>'Mac OS 9', '/linux/i'=>'Linux','/ubuntu/i'=>'Ubuntu',
								),
			'NOMOUSED'	=> array(
				'/iphone/i'=>'iPhone','/ipod/i'=>'iPod','/ipad/i'=>'iPad','/android/i'=>'Android','/blackberry/i'=>'BlackBerry', '/webos/i'=>'Mobile'
								)
		); 
		foreach($os_array as $namee=>$valuee) { foreach ($valuee as $regex => $value1) {	if(preg_match($regex, $user_agent)){$final['os_namee']=$value1;  $final['os_typee'] = $namee;}		} }
		return $final;
	}


	public function OS_platforms()
	{
		if (property_exists($this, 'platforms_cached')) return $this->platforms_cached;
		$this->platforms_cached = array_merge( $this->get_user_browser(), $this->mobile_detect(), $this->get_user_OperatingSystem() );
		return $this->platforms_cached;
	}
	
	// https://stackoverflow.com/a/31476046/2377343 
	public function get_url_parts($url,$part){	 $x='';
		$pURL = parse_url($url);	$pthURL = pathinfo($url);		
		//for example: https://example.com/myfolder/sympony.mp3?aa=1&bb=2?cc=#gggg
		if		($part=='scheme'){ 	$x = !empty($pURL['scheme'])	?	$pURL['scheme']				:'';}	//  http
		elseif	($part=='hostname'){ 	$x = !empty($pURL['host'])		?	$pURL['host']				:'';}   //  example.com
		elseif	($part=='query'){ 		$x = !empty($pURL['query'])		?	$pURL['query']				:'';}   //  aa=1&bb=2?cc=
		elseif	($part=='hash'){ 		$x = !empty($pURL['fragment'])	?	$pURL['fragment']			:'';}   //  gggg
		elseif	($part=='file'){ 		$x = !empty($pURL['path'])		?	$pURL['path']				:'';}   //  /myfolder/sympony.mp3
		elseif	($part=='filename'){ 	$x = !empty($pURL['path'])		?	basename($pURL['path'])		:'';}   //  sympony.mp3
		elseif	($part=='extension'){	$x = !empty($pURL['path'])		?	pathinfo($pURL['path'], PATHINFO_EXTENSION) :'';}   //  mp3
		elseif	($part=='folder'){ 		$x = !empty($pURL['path'])		?	dirname($pURL['path'])		:'';}   //  /myfolder
		elseif	($part=='dirname'){ 	$x = !empty($pthURL['dirname'])	?	$pthURL['dirname']			:'';}   //  https://example.com/myfolder
		elseif	($part=='afterfolder'){	$x = !empty($pthURL['basename'])?	$pthURL['basename']			:'';}   //  sympony.mp3?aa=1&bb=2?cc=#ggg
		
		return $x;
	}

	public function urlencodeall($x) {
		$out = '';
		for ($i = 0; isset($x[$i]); $i++) {
			$c = $x[$i];
			if (!ctype_alnum($c)) $c = '%' . sprintf('%02X', ord($c));
			$out .= $c;
		}
		return $out;
	}

	public function json_encode_unicode($data){ return json_encode($data, JSON_UNESCAPED_UNICODE); }
	
	public function FilterUrlFromLang($url){	return preg_replace('/(\&|\?)lg\=((.*?)&|(.*))/si','',$url); }
	
	public function utf8_declarationn() { return '<meta http-equiv="content-type" content="text/html; charset=UTF-8">'; }
	public function utf8_declarationn_auto() { return '<meta http-equiv="content-type" content="'.get_bloginfo('html_type').'; charset='.get_bloginfo('charset').'">'; }


	public function HTML_DOCTYPE_DECLARATIONsss(){  $lng = (defined('LNG') ? LNG : '') ;
		return 
	'<!DOCTYPE html>
	<html id="pagehtml" class="LN_'.$lng.'" xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="https://opengraphprotocol.org/schema/" xmlns="https://www.w3.org/1999/xhtml" lang="'.$lng.'" xml:lang="'.$lng.'" >';
	}


	public function default_rss_head_tags(){ 
	?> 	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
		<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" /> <?php	
	}

	//add_actionX('wp_head','check_if_js_cookies_enabled');
	public function check_if_JS_enabled(){	$out = 
		'<noscript>
			<div style="text-align:center; position:absolute;background-color:red;">Enable Javascript in your Browser to avoid BROWSER problems!</div>
		</noscript>';
		return	$out;
	}				
	public function check_if_COOKIES_enabled(){ $out1 = 
			'<script>
			function check_if_cookies_are_enabled(){ 
				var temp_cooK_name="__verify=1"; var dattee = new Date();dattee.setTime(dattee.getTime()+(30*1000));
				document.cookie = temp_cooK_name + ";expires=" + dattee.toUTCString();
				var supportsCOOCKIES = document.cookie.length >= 1 && document.cookie.indexOf(temp_cooK_name) > -1;
				if (supportsCOOCKIES) {document.write(\'<div style="text-align:center; position:absolute;background-color:red;">Enable cookies in your<br/> browser to avoid <br/>browser problems!</div>\');}
			}
			check_if_cookies_are_enabled();
			</script>';
		return $out1;
	}			
		
	
	public function old_browser_message($first=null, $incompatible_browsers=array('MSIE') ){
		global $odd;
		if (in_array($this->platforms()['brwsr'], $incompatible_browsers) ) { echo '<div style="padding:20px;text-align:center;position:fixed; top:0px;left:0px; z-idnex:99; background:red;color:black; ">Your have an INCOMPATIBLE BROWSER! Please, use any modern browser (<b><a href="https://www.firefox.com">Firefox</a>, <a href="https://www.opera.com">Opera</a>, <a href="https://www.apple.com/safari/‎">Safari</a> , <a href="https://www.chrome.com">Chrome</a></b>..) to view site normally. </div>'; }
	}	


	public function facebook_rescarpe_url($url){  $x= $this->get_remote_data('https://graph.facebook.com/','id='.urlencode($url).'&scrape=true'); }




	// ==================== text to image==============
	// # Usage #
	//TextToImage_my( 
	//	$text='Helloooo World!' , 
	//	$separate_line_after_chars=40,   $font='./Arial%20Unicode.ttf',    $size=24,   $rotate=0,   $padding=0,   $transparent=true,  $color=['r'=>0,'g'=>0,'b'=>0],   $bg_color=['r'=>255,'h'=>255,'b'=>255] 
	//);
	
	public function TextToImage($text, $separate_line_after_chars=40,  $font='./Arial%20Unicode.ttf', 
		$size=24,$rotate=0,$padding=2,$transparent=true, $color=array('r'=>0,'g'=>0,'b'=>0), $bg_color=array('r'=>255,'g'=>255,'b'=>255) ){
		$amount_of_lines= ceil(strlen($text)/$separate_line_after_chars)+substr_count($text, '\n')+1;
		$all_lines=explode("\n", $text);  $amount_of_lines = count($all_lines);    $text_final='';
		foreach($all_lines as $key=>$value){ 
			while( mb_strlen($value,'utf-8')>$separate_line_after_chars){	
				$text_final .= mb_substr($value, 0, $separate_line_after_chars, 'utf-8')."\n";
				$value = mb_substr($value, $separate_line_after_chars, null, 'utf-8');
			}  
			$text_final .= mb_substr($value, 0, $separate_line_after_chars, 'utf-8') . ( $amount_of_lines-1 == $key ? "" : "\n");
		}

		Header("Content-type: image/png");
		$width=$height=$offset_x=$offset_y = 0;
		// you can use: if (!file_exists($font))  file_put_contents('https://github.com/edx/edx-certificates/raw/master/template_data/fonts/Arial%20Unicode.ttf', $font);
														// get the font height.
														$bounds = ImageTTFBBox($size, $rotate, $font, "W");
														if ($rotate < 0)		{$font_height = abs($bounds[7]-$bounds[1]);	} 
														elseif ($rotate > 0)	{$font_height = abs($bounds[1]-$bounds[7]);	} 
														else { $font_height = abs($bounds[7]-$bounds[1]);}
				
		// determine bounding box.
		$bounds = ImageTTFBBox($size, $rotate, $font, $text_final);
		if ($rotate < 0){		$width = abs($bounds[4]-$bounds[0]);					$height = abs($bounds[3]-$bounds[7]);
								$offset_y = $font_height;								$offset_x = 0;
		} 
		elseif ($rotate > 0) {	$width = abs($bounds[2]-$bounds[6]);					$height = abs($bounds[1]-$bounds[5]);
								$offset_y = abs($bounds[7]-$bounds[5])+$font_height;	$offset_x = abs($bounds[0]-$bounds[6]);
		} 
		else{					$width = abs($bounds[4]-$bounds[6]);					$height = abs($bounds[7]-$bounds[1]);
								$offset_y = $font_height;								$offset_x = 0;
		}
		$height = $height +  $font_height*($amount_of_lines+1);
		$image = imagecreate($width+($padding*2)+1,$height+($padding*2)+1);
		
		$background = ImageColorAllocate($image, $bg_color['r'], $bg_color['g'], $bg_color['b']);
		$foreground = ImageColorAllocate($image, $color['r'], $color['g'], $color['b']);

		if ($transparent) ImageColorTransparent($image, $background);
		ImageInterlace($image, true);
	  // render the image
		ImageTTFText($image, $size, $rotate, $offset_x+$padding, $offset_y+$padding, $foreground, $font, $text_final);
		imagealphablending($image, true);
		imagesavealpha($image, true);
	  // output PNG object.
		imagePNG($image);
	}
	
	public function textToImage2($your_text="heloooo", $width=250, $height=80)
	{
		$IMG = imagecreate( $width, $height );
		$background = imagecolorallocate($IMG, 0,0,255);
		$text_color = imagecolorallocate($IMG, 255,255,0); 
		$line_color = imagecolorallocate($IMG, 128,255,0);
		imagestring( $IMG, 10, 1, 25, $your_text,  $text_color );
		imagesetthickness ( $IMG, 5 );
		//imageline( $IMG, 30, 45, 165, 45, $line_color );
		header( "Content-type: image/png" );
		imagepng($IMG);
		imagecolordeallocate($IMG, $line_color );
		imagecolordeallocate($IMG, $text_color );
		imagecolordeallocate($IMG, $background );
		imagedestroy($IMG); 
		exit;   
	}
	// https://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/
	public function hex2rgba($color, $opacity = false) {
		$default = 'rgb(0,0,0)'; if(empty($color))  return $default;  if ($color[0] == '#' ) {	$color = substr( $color, 1 );	} 		if (strlen($color) == 6) {$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );	} elseif ( strlen( $color ) == 3 ) {$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );} else {return $default;} 		$rgb =  array_map('hexdec', $hex); 	if($opacity){if(abs($opacity) > 1)throw new \Exception("Opacity cant be more than 1");$opacity = self::number_format((float)$opacity, 2); $output = 'rgba('.implode(",",$rgb).','.$opacity.')';		} else {$output = 'rgb('.implode(",",$rgb).')';	} 	return $output;
	}

	// https://stackoverflow.com/a/5925612/2377343  |  https://stackoverflow.com/questions/15852122/
	public function hex_color($name){
		$arr=['aliceblue'=>'F0F8FF', 'antiquewhite'=>'FAEBD7', 'aqua'=>'00FFFF', 'aquamarine'=>'7FFFD4', 'azure'=>'F0FFFF', 'beige'=>'F5F5DC', 'bisque'=>'FFE4C4', 'black'=>'000000', 'blanchedalmond '=>'FFEBCD', 'blue'=>'0000FF', 'blueviolet'=>'8A2BE2', 'brown'=>'A52A2A', 'burlywood'=>'DEB887', 'cadetblue'=>'5F9EA0', 'chartreuse'=>'7FFF00', 'chocolate'=>'D2691E', 'coral'=>'FF7F50', 'cornflowerblue'=>'6495ED', 'cornsilk'=>'FFF8DC', 'crimson'=>'DC143C', 'cyan'=>'00FFFF', 'darkblue'=>'00008B', 'darkcyan'=>'008B8B', 'darkgoldenrod'=>'B8860B', 'darkgray'=>'A9A9A9', 'darkgreen'=>'006400', 'darkgrey'=>'A9A9A9', 'darkkhaki'=>'BDB76B', 'darkmagenta'=>'8B008B', 'darkolivegreen'=>'556B2F', 'darkorange'=>'FF8C00', 'darkorchid'=>'9932CC', 'darkred'=>'8B0000', 'darksalmon'=>'E9967A', 'darkseagreen'=>'8FBC8F', 'darkslateblue'=>'483D8B', 'darkslategray'=>'2F4F4F', 'darkslategrey'=>'2F4F4F', 'darkturquoise'=>'00CED1', 'darkviolet'=>'9400D3', 'deeppink'=>'FF1493', 'deepskyblue'=>'00BFFF', 'dimgray'=>'696969', 'dimgrey'=>'696969', 'dodgerblue'=>'1E90FF', 'firebrick'=>'B22222', 'floralwhite'=>'FFFAF0', 'forestgreen'=>'228B22', 'fuchsia'=>'FF00FF', 'gainsboro'=>'DCDCDC', 'ghostwhite'=>'F8F8FF', 'gold'=>'FFD700', 'goldenrod'=>'DAA520', 'gray'=>'808080', 'green'=>'008000', 'greenyellow'=>'ADFF2F', 'grey'=>'808080', 'honeydew'=>'F0FFF0', 'hotpink'=>'FF69B4', 'indianred'=>'CD5C5C', 'indigo'=>'4B0082', 'ivory'=>'FFFFF0', 'khaki'=>'F0E68C', 'lavender'=>'E6E6FA', 'lavenderblush'=>'FFF0F5', 'lawngreen'=>'7CFC00', 'lemonchiffon'=>'FFFACD', 'lightblue'=>'ADD8E6', 'lightcoral'=>'F08080', 'lightcyan'=>'E0FFFF', 'lightgoldenrodyellow'=>'FAFAD2', 'lightgray'=>'D3D3D3', 'lightgreen'=>'90EE90', 'lightgrey'=>'D3D3D3', 'lightpink'=>'FFB6C1', 'lightsalmon'=>'FFA07A', 'lightseagreen'=>'20B2AA', 'lightskyblue'=>'87CEFA', 'lightslategray'=>'778899', 'lightslategrey'=>'778899', 'lightsteelblue'=>'B0C4DE', 'lightyellow'=>'FFFFE0', 'lime'=>'00FF00', 'limegreen'=>'32CD32', 'linen'=>'FAF0E6', 'magenta'=>'FF00FF', 'maroon'=>'800000', 'mediumaquamarine'=>'66CDAA', 'mediumblue'=>'0000CD', 'mediumorchid'=>'BA55D3', 'mediumpurple'=>'9370D0', 'mediumseagreen'=>'3CB371', 'mediumslateblue'=>'7B68EE', 'mediumspringgreen'=>'00FA9A', 'mediumturquoise'=>'48D1CC', 'mediumvioletred'=>'C71585', 'midnightblue'=>'191970', 'mintcream'=>'F5FFFA', 'mistyrose'=>'FFE4E1', 'moccasin'=>'FFE4B5', 'navajowhite'=>'FFDEAD', 'navy'=>'000080', 'oldlace'=>'FDF5E6', 'olive'=>'808000', 'olivedrab'=>'6B8E23', 'orange'=>'FFA500', 'orangered'=>'FF4500', 'orchid'=>'DA70D6', 'palegoldenrod'=>'EEE8AA', 'palegreen'=>'98FB98', 'paleturquoise'=>'AFEEEE', 'palevioletred'=>'DB7093', 'papayawhip'=>'FFEFD5', 'peachpuff'=>'FFDAB9', 'peru'=>'CD853F', 'pink'=>'FFC0CB', 'plum'=>'DDA0DD', 'powderblue'=>'B0E0E6', 'purple'=>'800080', 'red'=>'FF0000', 'rosybrown'=>'BC8F8F', 'royalblue'=>'4169E1', 'saddlebrown'=>'8B4513', 'salmon'=>'FA8072', 'sandybrown'=>'F4A460', 'seagreen'=>'2E8B57', 'seashell'=>'FFF5EE', 'sienna'=>'A0522D', 'silver'=>'C0C0C0', 'skyblue'=>'87CEEB', 'slateblue'=>'6A5ACD', 'slategray'=>'708090', 'slategrey'=>'708090', 'snow'=>'FFFAFA', 'springgreen'=>'00FF7F', 'steelblue'=>'4682B4', 'tan'=>'D2B48C', 'teal'=>'008080', 'thistle'=>'D8BFD8', 'tomato'=>'FF6347', 'turquoise'=>'40E0D0', 'violet'=>'EE82EE', 'wheat'=>'F5DEB3', 'white'=>'FFFFFF', 'whitesmoke'=>'F5F5F5', 'yellow'=>'FFFF00', 'yellowgreen'=>'9ACD32'];
		return '#'.trim($this->array_value($arr, $name, 'FFFFFFFF'));
	}
	
	//======helper function==========
	//if(!function_exists('mb_substr_replace')){
	  function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = "UTF-8") {
		if (extension_loaded('mbstring') === true){
			$string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);
			if ($start < 0) { $start = max(0, $string_length + $start); }
			else if ($start > $string_length) {$start = $string_length; }
			if ($length < 0){ $length = max(0, $string_length - $start + $length);  }
			else if ((is_null($length) === true) || ($length > $string_length)) { $length = $string_length; }
			if (($start + $length) > $string_length){$length = $string_length - $start;} 
			if (is_null($encoding) === true) {  return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length); }
			return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
		}
		return (is_null($length) === true) ? substr_replace($string, $replacement, $start) : substr_replace($string, $replacement, $start, $length);
	  }
	//}
	//if(!function_exists('mb_str_word_count')){
		function mb_str_word_count($string, $format = 0, $charlist = '[]') {
			$string=trim($string);
			if(empty($string)){$words = array();}    else {$words = preg_split('~[^\p{L}\p{N}\']+~u',$string);}
			switch ($format) {   case 0: return count($words); break;       case 1:      case 2: return $words; break;          default: return $words; break;    }
		}
	//}

	
	public function  header_mail($from=false, $host= false){ 
		$from = $from ? $from : "contact"; 
		$host = $host ? $host : $_SERVER['HTTP_HOST'];//$_SERVER['SERVER_ADDR']; 
		return array('From: '.$from.'@'.$host . "\r\n" .  'Reply-To: '.$from.'@'.$host . "\r\n" .  'X-Mailer: PHP/' . phpversion());
	}


	public function value_or_input_field($namee){
		if (!empty($GLOBALS['editing_inputs'])){
			
		}
		else{
			
		}
	}
	
	public function ksort_recursive(&$array) {
	   foreach ($array as &$value) {
		  if (is_array($value)) $this->ksort_recursive($value);
	   }
	   return ksort($array);
	}













	

	public function die_if_array_key($array, $key){ if (array_key_exists($key, $array)) exit($array[$key]); }

	public function chars_array_($alhpanumeric=true){  return  ( $alhpanumeric ?
			array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z')
			:
			array('!','$','+','<','[',']','%',',','.','=','&','-','<','>','|', '"', '\'', '\\', '~','(','/',')','!',' ',"\r","\n", '*', '{','}','?','`','@',':',';','^')
		);
	}


	public function preg_quote_fast($text){
		$specs =array('/', '.','\\','+','*','?','[','^',']','$','(',')','{','}','=','!','<','>','|',':','-');
		$new_array_for_strtr = array();
		foreach($specs as $each){
			$new_array_for_strtr[$each] = '\\'.$each;
		}
		$text = strtr( $text, $new_array_for_strtr);
		return $text;
	}

	public function Convert_Empty_to_Zero ($var){ if (empty($var)) return 0; else return $var; }


	public function checkboxes($checkbox_name,$current_value, $unchecked_value,$checked_value){
		$out = '<input type="hidden" name="'.$checkbox_name.'" value="'.$unchecked_value.'" /><input class="chbkx" type="checkbox"  name="'.$checkbox_name.'" value="'.$checked_value.'" '. ($current_value==$checked_value ? 'checked="checked"': '') .' />'; return $out;
	}

	public function js_library($url_or_Tag=true, $defaultPath=""){
		if( empty($defaultPath) && function_exists('home_url') )
			$defaultPath = plugin_dir_url($this->moduleFILE);
		$url = $defaultPath . '/libray_default_puvox.js';
		return $url_or_Tag ? $url : '<script src="'.$url.'"></script>';
	}

	public function OutputIfNotPC($var){ if($GLOBALS['odd']['is_portable_platform']){echo $var;} }

	// common funcs
			

	public function my_translate_month_inside($string = '27/January/2015'){
		foreach($GLOBALS['odd']['months_arr'] as $each){
			if(strpos($string,$each)!==false) { 
				$string = str_replace($each,translate__MONTH($each), $string);
			}
		}
		return $string;
	}

	public function get_First_words($sentence , $desired_words_amount=5){
		$all_words = explode(' ', $sentence);  $words_amount = count($all_words);  $words_index_amount=$words_amount-1;
		$out = '';
		if ($words_amount > $desired_words_amount) {
			for($i = 0; $i< $desired_words_amount; $i++) {
				if(array_key_exists( $i,$all_words)){
					$out = $out.' '.$all_words[$i];
				}
			}
		}
		else {$out = $sentence;  }
		return strip_tags($out);
	}

	public function get_Last_words($sentence , $desired_words_amount=5){
		$all_words = explode(' ', $sentence);  $words_amount = count($all_words);  $words_index_amount=$words_amount-1;
		$out = '';
		if ($words_amount > $desired_words_amount) {
			for($i = 0; $i< $desired_words_amount; $i++) {
				if(array_key_exists( ($words_index_amount-$i),$all_words)){
					$out = $all_words[($words_index_amount-$i)].' '.$out;
				}
			}
		}
		else {$out = $sentence;  }
		return strip_tags($out);
	}

	public function my_utf8_decode($textt){
		$var = $textt;	$var = iconv("UTF-8","ISO-8859-1//IGNORE",$var);	$var = iconv("ISO-8859-1","UTF-8",$var); $var = str_replace(' ','',$var);
		return $var;
	}



	// ============================================= YOUTUBE DOWNLOAD FUNCTIONS ====================================================
	// https://pastebin_com/bFePMkfy
	
	//	https://img.youtube.com/vi/XXXXXXXXX/0.jpg (a bit larger)   // 1,2,3
	//  https://img.youtube.com/vi/xxxxxxxxx/mqdefault.jpg
	//  https://img.youtube.com/vi/xxxxxxxxx/hqdefault.jpg
	//  https://img.youtube.com/vi/xxxxxxxxx/maxresdefault.jpg
	public function get_youtube_thumbnail($id,$quality='maxres'){return 'https://i.ytimg.com/vi/'.$id.'/'.$quality.'.jpg';}  
		

	//to check if variable are normal
	public function get_youtube_id_from_url($url) {
		preg_match('/(http(s|):|)\/\/(www\.|)youtu(be\.com|\.be)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $results); 
		return (isset($results[6]) ? $results[6] : false);
	}
	public function get_youtube_id_from_contents($url){ 
		if (stripos($url,'youtu.be/')!==false)			{preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/si', $url, $final_ID); $x= !empty($final_ID[4]) ? $final_ID[4] : '';}
		elseif  (stripos($url,'youtube.com/')!==false)	{preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/si', $url, $IDD);$x= !empty($IDD[5]) ? $IDD[5] : ''; }
		return (!empty($x) ? $x : '');
	}

	public function get_youtube_id_from_contents_JAVASCRIPT(){ return '<script type="text/javascript">'. 'function getYtIdFromURL(URLL){var r=URLL.match(/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/); return r[1];} '. '</script>';
	} 
	public function validate_youtube_id($id){ if (strlen($id)!=11 || preg_match('/[\<\>\'\=\$\"\?\(\{]/si',$text)) {die("incorrrrrect_ID_ error79");	 }}
	//#################################
		
	// force ssl	
	public function redirect_to_https(){
		if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")
		{
			$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $redirect);
			exit();
		}
	}

	public function redirect_to_nonwww($https=true){
		if( stripos($_SERVER['HTTP_HOST'],'www.') !== false ) {
			$redirect =  ($https ? 'https' : 'http') . '://' . str_replace('www.','', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI'];
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $redirect);
			exit();
		}
	}
	
	public function Serialized_Fixer($serialized_string){
		// securities
		if (empty($serialized_string)) 						return '';
		if ( !preg_match('/^[aOs]:/', $serialized_string) )	return $serialized_string;
		if ( @unserialize($serialized_string) !== false ) 	return $serialized_string;
		
		return
		preg_replace_callback(
			'/s\:(\d+)\:\"(.*?)\";/s', 
			function ($matches){	return 's:'.strlen($matches[2]).':"'.$matches[2].'";';	},
			$serialized_string )
		;
	}

	public function myIMGurlencode2($imgUrl){
		preg_match('/(.*)\/(.*)/si',$imgUrl, $n);	$x = (!empty($n[1]) && !empty($n[2])) ? $n[1].'/'.str_replace('+','%20',urlencode($n[2])) : "error_29858";  return $x;
	}

	public function myIMGurlencode($imgUrl){
		return str_replace('/'.basename($imgUrl) ,  '/'.str_replace('+','%20',basename($imgUrl)),       $imgUrl);
	}


	public function AddStringToUrl($url, $string){
		return $url .( stripos($url,'?')===false ?  '?'.$string :  '&'.$string);
	}

	//check, if AJAX has requested error send
	public function check_error_AJAX_request(){	if (isset($_REQUEST['ErrorAjax'])){  	
		$this->error_notify_admin__MYDDD(  rawurldecode($_REQUEST['ErrorAjax']) ,  urlencode($_REQUEST['p'])  );  exit("sent");
	}}

	public function error_notify_admin($error_msg=false,$postidd=false){ return error_notify_admin__MYDDD($error_msg,$postidd); }
	public function error_notify_admin__MYDDD($error_msg=false,$postidd=false){ 	if (is_localhost) return;
		// usage https://github.com/ttodua/useful-javascript/blob/master/AJAX-examples
		//'<script type="text/javascript">myyAjaxRequest('error_ajaxx=' + encodeURIcomponent(document.URL) + '&p= [[[[$GLOBALS['post']->ID]]]] &bla=blabla');</script>';
			$message	="\r\n\r\n\r\n\r\n\r\n\r\n===============================================================".date("Y-m-d H:i:s")."\r\n" . $error_msg. ' ||| URL:'.  ($postidd ?  get_permalink($postidd) : "") . " | " . $_SERVER['REQUEST_URI']. ' | REFERER:'. $_SERVER['HTTP_REFERER']."\r\n\r\nbacktrace:\r\n".print_r(debug_backtrace(), true); 
		
		//write into file
			//$file=$this->baseDIR.'/zzz___ajax_error_notifications_'.$this->my_site_variables__secret('rand_name', RandomString(11)).'.txt';   OR    $this->file_put_contents($file,$message, FILE_APPEND);
		// send to mail
			$subjectt	='error_'.$_SERVER['HTTP_HOST'];
			$message=str_replace(array("\r\n","\n"),"<br/>",$message);  $message=str_replace(array("\s"," ","\t"),"&nbsp;",$message);
			return $this->my_mail($this->error_to_mailaddress, $subjectt, $message, $this->default_mail_headers() );
			return "mail was not sent... check functionality";
	}

	// i.e. get_remote_data(' tinyurl.com/api-create.php?url='.$url); 
	public function get_short_link($url) { return $url; }

	public function allowed_extensions_of_url( $url ) {
		$ext = array( 'jpeg', 'jpg', 'gif', 'png' );
		$info = (array) pathinfo( parse_url( $url, PHP_URL_PATH ) );
		return isset( $info['extension'] ) && in_array( strtolower( $info['extension'] ), $ext, TRUE );
	}

	public function m1($tag=""){ $this->var_dump("\r\n<br/>* [MemoryUsage]A$tag :". $this->memory_usage()); }
	public function memory_usage(){ return memory_get_usage()/pow(1024,2); }
	public function gc_enable(){ return gc_enable(); }
	public function gc_clean() { return gc_collect_cycles(); }

	
	// create: https://vectr.com/new      https://vectorpaint.yaks.co.nz/     
	// convert : https://hnet.com/png-to-svg/  ( https://image.online-convert.com/convert-to-svg | https://convertio.co/ )
	// view: https://www.rapidtables.com/web/tools/svg-viewer-editor.html
	public function images($which, $type="png", $url_or_tag=true)
	{
		$url=[];
		switch ($which)
		{
			 //see visually: https://i.imgur.com/MNxlU7s.png
			case "overlay-pro"		: $url['svg'] = '<svg height="15pt" preserveAspectRatio="xMidYMid meet" viewBox="0 0 14 15" width="14pt" xmlns="http://www.w3.org/2000/svg"><g transform="matrix(.1 0 0 -.1 0 15)"><path d="m20 125c-13-14-21-27-18-30 2-3 17 9 33 25 16 17 24 30 19 30-6 0-21-11-34-25z"/><path d="m53 91c-73-80-67-94 7-17 33 35 60 66 60 69 0 16-18 2-67-52z"/><path d="m85 50c-27-28-45-50-39-50 13 0 99 88 93 95-3 2-27-18-54-45z"/><path d="m125 10c-3-5-1-10 4-10 6 0 11 5 11 10 0 6-2 10-4 10-3 0-8-4-11-10z"/></g></svg>';  break; 
			 //see visually: https://i.imgur.com/6oHljXM.png
			case "questionMark-1"	: $url['svg'] = '<svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="64px" height="64px" viewBox="0 0 640 640" preserveAspectRatio="xMidYMid meet"> <g id="layer101" fill="#2a589e" stroke="none"> <path d="M234 616 c-124 -40 -224 -175 -224 -301 0 -106 83 -231 185 -279 76 -37 184 -37 260 0 110 52 191 182 183 294 -8 111 -83 222 -182 270 -59 28 -163 36 -222 16z"/> </g> <g id="layer102" fill="#6a88b6" stroke="none"> <path d="M270 470 l0 -40 45 0 45 0 0 40 0 40 -45 0 -45 0 0 -40z"/> <path d="M36 389 c-20 -103 3 -203 65 -273 119 -135 329 -135 449 1 22 25 40 50 40 55 0 6 -20 -12 -45 -40 -43 -48 -115 -93 -146 -91 -8 0 -2 5 14 11 31 11 34 20 12 38 -8 7 -12 16 -10 21 3 4 -10 2 -29 -6 -20 -8 -52 -14 -73 -14 l-38 2 45 6 c84 13 130 54 130 114 0 12 -23 51 -50 86 -28 35 -50 72 -50 82 0 15 -7 19 -35 19 -31 0 -35 -3 -35 -26 0 -31 14 -60 47 -95 54 -59 17 -125 -58 -102 l-34 10 47 -1 c55 -1 67 7 49 36 -7 11 -8 17 -2 13 6 -3 11 -2 11 2 0 5 -23 16 -50 26 -70 25 -158 79 -199 121 -19 20 -38 36 -42 36 -3 0 -9 -14 -13 -31z"/> </g> <g id="layer103" fill="#e5ebf3" stroke="none"> <path d="M280 470 c0 -39 1 -40 35 -40 34 0 35 1 35 40 0 39 -1 40 -35 40 -34 0 -35 -1 -35 -40z"/> <path d="M280 372 c0 -19 51 -105 69 -116 15 -9 14 -59 -1 -74 -14 -14 -77 -16 -111 -3 -23 9 -25 7 -29 -20 -3 -16 0 -33 5 -36 5 -3 40 -8 77 -11 148 -10 196 76 105 188 -25 30 -45 63 -45 72 0 14 -8 18 -35 18 -27 0 -35 -4 -35 -18z"/> </g> </svg>'; break;
			 //see visually: https://i.imgur.com/73R7eLv.png
			case "questionMark-2"	: $url['svg'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:se="http://svg-edit.googlecode.com" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" width="220.36365738125096" height="219.96970986050064" style=""><rect id="backgroundrect" width="100%" height="100%" x="0" y="0" fill="#FFFFFF" stroke="none" class="" style=""/>  <g class="currentLayer" style=""><title>Layer 1</title><path fill="#4a90d6" fill-opacity="1" stroke="#ebeb1a" stroke-opacity="1" stroke-width="0" stroke-dasharray="none" stroke-linejoin="round" stroke-linecap="butt" stroke-dashoffset="" fill-rule="nonzero" opacity="1" marker-start="" marker-mid="" marker-end="" d="M0,110 C0,49.22651933701658 49.226519337016555,0 110,0 C170.77348066298345,0 220,49.22651933701658 220,110 C220,170.77348066298345 170.77348066298345,220 110,220 C49.226519337016555,220 0,170.77348066298345 0,110 z" id="svg_1" class="" filter=""/><foreignObject fill="#4a90d6" stroke="#222222" stroke-width="2" stroke-linejoin="round" stroke-dashoffset="" fill-rule="nonzero" font-size="156" font-family="Arial, Helvetica, sans-serif" letter-spacing="0" word-spacing="0" marker-start="" marker-mid="" marker-end="" id="svg_9" x="23.115700873212546" y="29.279336103256995" width="224.30167929102208" height="168.0040174784078" style="color: rgb(36, 36, 36);" class="" transform="rotate(0.08801647275686264 832.5809326171628,364.08416748049103) "><p style="border: none;outline: none;font-size: inherit;line-height: 1em;padding:0;margin:0;" xmlns="http://www.w3.org/1999/xhtml"><p xmlns="http://www.w3.org/1999/xhtml" style="border: none;outline: none;font-size: inherit;line-height: 1em;padding:0;margin:0;"></p><p xmlns="http://www.w3.org/1999/xhtml" style="border: none;outline: none;font-size: inherit;line-height: 1em;padding:0;margin:0;"></p><p xmlns="http://www.w3.org/1999/xhtml" style="border: none;outline: none;font-size: inherit;line-height: 1em;padding:0;margin:0;"> ?</p><p style="border: none;outline: none;font-size: inherit;line-height: 1em;padding:0;margin:0;"></p><p style="border: none;outline: none;font-size: inherit;line-height: 1em;padding:0;margin:0;"></p></p></foreignObject></g><defs><marker id="DotS" refX="0" refY="0" orient="auto" inkscape:stockid="DotS" overflow="visible"> <path transform="scale(.2) translate(7.4 1)" d="M-2.5-1c0 2.76-2.24 5-5 5s-5-2.24-5-5 2.24-5 5-5 5 2.24 5 5z" fill-rule="evenodd" stroke="#000" stroke-width="1pt" style="fill: rgb(235, 235, 26); stroke: rgb(235, 235, 26); stroke-dasharray: none;"/></marker></defs></svg>'; break;
			 //see visually: https://i.imgur.com/mx70WNM.png
			case "rating-transparent" : $url['svg'] = '<svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="544px" height="128px" viewBox="0 0 5440 1280" preserveAspectRatio="xMidYMid meet"><g id="layer101" fill="#ffb900" stroke="none"><path d="M335 1134 c-51 -14 -56 -69 -25 -256 l21 -119 -91 -100 c-72 -79 -90 -106 -91 -132 -3 -60 3 -102 16 -115 7 -8 69 -21 149 -31 74 -10 139 -21 143 -25 5 -4 40 -69 78 -144 l70 -137 75 0 75 0 70 137 c39 75 73 140 78 144 4 4 68 15 142 25 127 16 143 16 270 0 74 -10 138 -21 142 -25 5 -4 40 -69 78 -144 l70 -137 75 0 75 0 70 137 c39 75 73 140 78 144 4 4 68 15 142 25 127 16 143 16 270 0 74 -10 138 -21 142 -25 5 -4 40 -69 78 -144 l70 -137 75 0 75 0 70 137 c39 75 73 140 78 144 4 4 68 15 142 25 127 16 143 16 270 0 74 -10 138 -21 142 -25 5 -4 40 -69 78 -144 l70 -137 75 0 75 0 70 137 c39 75 73 140 78 144 4 4 68 15 142 25 127 16 143 16 270 0 74 -10 138 -21 142 -25 5 -4 40 -69 78 -144 l70 -137 75 0 75 0 70 137 c39 75 73 140 78 144 4 4 69 15 143 25 80 10 142 23 149 31 7 7 14 29 16 48 5 89 3 95 -91 199 -49 56 -90 102 -90 103 0 2 9 55 20 118 23 135 25 209 6 235 -11 15 -27 19 -74 20 -54 0 -73 -6 -181 -60 l-121 -59 -121 59 c-108 54 -127 60 -181 60 -47 -1 -63 -5 -74 -20 -19 -26 -17 -97 6 -237 l21 -119 -72 -80 c-40 -43 -75 -79 -79 -79 -7 0 -130 134 -143 156 -3 6 2 62 13 125 23 135 25 208 6 234 -11 15 -27 19 -74 20 -54 0 -73 -6 -181 -60 l-121 -59 -121 59 c-108 54 -127 60 -181 60 -47 -1 -63 -5 -74 -20 -19 -26 -17 -97 6 -237 l21 -119 -72 -80 c-40 -43 -75 -79 -79 -79 -7 0 -130 134 -143 156 -3 6 2 62 13 125 23 135 25 208 6 234 -11 15 -27 19 -74 20 -54 0 -73 -6 -181 -60 l-121 -59 -121 59 c-108 54 -127 60 -181 60 -47 -1 -63 -5 -74 -20 -19 -26 -17 -97 6 -237 l21 -119 -72 -80 c-40 -43 -75 -79 -79 -79 -7 0 -130 134 -143 156 -3 6 2 62 13 125 23 135 25 208 6 234 -11 15 -27 19 -74 20 -54 0 -73 -6 -181 -60 l-121 -59 -121 59 c-108 54 -127 60 -181 60 -47 -1 -63 -5 -74 -20 -19 -26 -17 -97 6 -237 l21 -119 -72 -80 c-40 -43 -75 -79 -79 -79 -7 0 -130 134 -143 156 -3 6 2 62 13 125 23 135 25 208 6 234 -11 15 -27 19 -74 20 -54 0 -73 -6 -181 -60 l-121 -59 -117 57 c-113 57 -177 74 -228 61z"/></g></svg>'; break;
			//see visually: https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg
			case "paypal" : $url['svg'] = '<svg xmlns="http://www.w3.org/2000/svg" width="248" height="66" viewBox="0 0 124 33"><path fill="#253B80" d="M46.211 6.749h-6.839a.95.95 0 0 0-.939.802l-2.766 17.537a.57.57 0 0 0 .564.658h3.265a.95.95 0 0 0 .939-.803l.746-4.73a.95.95 0 0 1 .938-.803h2.165c4.505 0 7.105-2.18 7.784-6.5.306-1.89.013-3.375-.872-4.415-.972-1.142-2.696-1.746-4.985-1.746zM47 13.154c-.374 2.454-2.249 2.454-4.062 2.454h-1.032l.724-4.583a.57.57 0 0 1 .563-.481h.473c1.235 0 2.4 0 3.002.704.359.42.469 1.044.332 1.906zM66.654 13.075h-3.275a.57.57 0 0 0-.563.481l-.145.916-.229-.332c-.709-1.029-2.29-1.373-3.868-1.373-3.619 0-6.71 2.741-7.312 6.586-.313 1.918.132 3.752 1.22 5.031.998 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .562.66h2.95a.95.95 0 0 0 .939-.803l1.77-11.209a.568.568 0 0 0-.561-.658zm-4.565 6.374c-.316 1.871-1.801 3.127-3.695 3.127-.951 0-1.711-.305-2.199-.883-.484-.574-.668-1.391-.514-2.301.295-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.499.589.697 1.411.554 2.317zM84.096 13.075h-3.291a.954.954 0 0 0-.787.417l-4.539 6.686-1.924-6.425a.953.953 0 0 0-.912-.678h-3.234a.57.57 0 0 0-.541.754l3.625 10.638-3.408 4.811a.57.57 0 0 0 .465.9h3.287a.949.949 0 0 0 .781-.408l10.946-15.8a.57.57 0 0 0-.468-.895z"/><path fill="#179BD7" d="M94.992 6.749h-6.84a.95.95 0 0 0-.938.802l-2.766 17.537a.569.569 0 0 0 .562.658h3.51a.665.665 0 0 0 .656-.562l.785-4.971a.95.95 0 0 1 .938-.803h2.164c4.506 0 7.105-2.18 7.785-6.5.307-1.89.012-3.375-.873-4.415-.971-1.142-2.694-1.746-4.983-1.746zm.789 6.405c-.373 2.454-2.248 2.454-4.062 2.454h-1.031l.725-4.583a.568.568 0 0 1 .562-.481h.473c1.234 0 2.4 0 3.002.704.359.42.468 1.044.331 1.906zM115.434 13.075h-3.273a.567.567 0 0 0-.562.481l-.145.916-.23-.332c-.709-1.029-2.289-1.373-3.867-1.373-3.619 0-6.709 2.741-7.311 6.586-.312 1.918.131 3.752 1.219 5.031 1 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .564.66h2.949a.95.95 0 0 0 .938-.803l1.771-11.209a.571.571 0 0 0-.565-.658zm-4.565 6.374c-.314 1.871-1.801 3.127-3.695 3.127-.949 0-1.711-.305-2.199-.883-.484-.574-.666-1.391-.514-2.301.297-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.501.589.699 1.411.554 2.317zM119.295 7.23l-2.807 17.858a.569.569 0 0 0 .562.658h2.822c.469 0 .867-.34.939-.803l2.768-17.536a.57.57 0 0 0-.562-.659h-3.16a.571.571 0 0 0-.562.482z"/><path fill="#253B80" d="M7.266 29.154l.523-3.322-1.165-.027H1.061L4.927 1.292a.316.316 0 0 1 .314-.268h9.38c3.114 0 5.263.648 6.385 1.927.526.6.861 1.227 1.023 1.917.17.724.173 1.589.007 2.644l-.012.077v.676l.526.298a3.69 3.69 0 0 1 1.065.812c.45.513.741 1.165.864 1.938.127.795.085 1.741-.123 2.812-.24 1.232-.628 2.305-1.152 3.183a6.547 6.547 0 0 1-1.825 2c-.696.494-1.523.869-2.458 1.109-.906.236-1.939.355-3.072.355h-.73c-.522 0-1.029.188-1.427.525a2.21 2.21 0 0 0-.744 1.328l-.055.299-.924 5.855-.042.215c-.011.068-.03.102-.058.125a.155.155 0 0 1-.096.035H7.266z"/><path fill="#179BD7" d="M23.048 7.667c-.028.179-.06.362-.096.55-1.237 6.351-5.469 8.545-10.874 8.545H9.326c-.661 0-1.218.48-1.321 1.132L6.596 26.83l-.399 2.533a.704.704 0 0 0 .695.814h4.881c.578 0 1.069-.42 1.16-.99l.048-.248.919-5.832.059-.32c.09-.572.582-.992 1.16-.992h.73c4.729 0 8.431-1.92 9.513-7.476.452-2.321.218-4.259-.978-5.622a4.667 4.667 0 0 0-1.336-1.03z"/><path fill="#222D65" d="M21.754 7.151a9.757 9.757 0 0 0-1.203-.267 15.284 15.284 0 0 0-2.426-.177h-7.352a1.172 1.172 0 0 0-1.159.992L8.05 17.605l-.045.289a1.336 1.336 0 0 1 1.321-1.132h2.752c5.405 0 9.637-2.195 10.874-8.545.037-.188.068-.371.096-.55a6.594 6.594 0 0 0-1.017-.429 9.045 9.045 0 0 0-.277-.087z"/><path fill="#253B80" d="M9.614 7.699a1.169 1.169 0 0 1 1.159-.991h7.352c.871 0 1.684.057 2.426.177a9.757 9.757 0 0 1 1.481.353c.365.121.704.264 1.017.429.368-2.347-.003-3.945-1.272-5.392C20.378.682 17.853 0 14.622 0h-9.38c-.66 0-1.223.48-1.325 1.133L.01 25.898a.806.806 0 0 0 .795.932h5.791l1.454-9.225 1.564-9.906z"/></svg>'; break;
		}
		return $url[$type];
	}
	public function encodeSvg($content){ return str_replace(['<','>', '#', '"'], ['%3C','%3E', '%23', '\''], $content); }
	public function imageSvg($which){ return 'data:image/svg+xml;charset=UTF-8,'. $this->encodeSvg( $this->images($which, 'svg') ); }
	
	public function addQueryArg($url, $key, $value){
		$pair = urlencode($key).'='.urlencode($value);
		return ($this->contains($url,'?') ? $url."&$pair" :  $url."?$pair");
	}

	public function question_mark($text, $dialog=0, $question_mark="") { 
		$mouseover='';
		$content = '';
		if($dialog==0){
			$content = $text;
		}
		else if($dialog==1){
			$content = '';
			$mouseover = ' onmouseover="jQuery(\'#\'+this.parentNode.id).tooltip({ items:this,   content:\''.$text.'\', show: { effect: \'blind\', duration: 800 } 	}).tooltip(\'open\');"'; 	
		}
		else if($dialog==2){
			$content = '';
			$mouseover = ' onmouseover="jQuery(\'<div>'.$text.'</div>\').dialog({   modal:true,   width:600 });"';
		}
		if (empty($question_mark)) $question_mark=$this->imageSvg('questionMark-1');
		return '<span id="xx"><img src="'. $question_mark .'" class="question_mark" style="cursor:crosshair; width:20px;" alt="'.$content.'" title="'.$content.'" '.$mouseover.' /></span>';
	}


	
	public function between($a,$b,$c){
		return ($a<$b && $b<$c);
	}
	public function inside($a,$b,$c){
		return ($this->between($a,$b,$c) || $this->between($c,$b,$a) );
	}
	
	//addTextOnImage( ['text'=>'hello',  'input'=>'img.png', 'echo'=>false, 'method'=>'gd|imagick', 'fontsize'=>9, 'angle'=>-15, 'x'=>11, 'y'=>14, 'color'=>'#e7e7e7', 'opacity'=>0.5, 'stroke'=>['#e7e7e7',$width=4,$alpha=0.5], 'spaces'=>3]); //also, font
	public function addTextOnImage($opts=[])
	{
		//v_dump(glob("C:\Windows\Fonts\*"));
		//v_dump($Imagick->queryFonts("*"));
		$text 		= $opts['text'];
		$imagePath	= $opts['input'];
		$fontsize	= $opts['fontsize'];
		list($width, $height, $type, $attr) =getimagesize($imagePath); 
		$x_position = $this->arrayKeyValue($opts,'x',0);
		$y_position = $this->arrayKeyValue($opts,'y',0);
		if (strpos($x_position,'%')!==false) $x_position = $width * str_replace('%','',$x_position)/100;
		if (strpos($y_position,'%')!==false) $y_position = $height * str_replace('%','',$y_position)/100;
		
		if( $this->arrayKeyEquals($opts, 'text_repeat', true) )
		{
			$final_text="";
			$multiplier=4; //lets say 3 for assurance
			$spaces_between = $this->arrayKeyValue($opts, 'spaces',5);
			$repeated_per_width = ($width / (strlen($text) * $fontsize)) * $multiplier;
			$repeated_per_height= ($height / ($fontsize)) * $multiplier;
			
			for ($i=0; $i<$repeated_per_height; $i++)
			{
				$t= "";
				for ($j=0; $j<$repeated_per_width; $j++)
				{
					$t .= $text . str_repeat(" ", $spaces_between );
				}
				$final_text .=$t. "\r\n";
			}
			$text = $final_text;
		}

		if ( $this->arrayKeyEquals($opts, 'method', 'gd') ) 
		{
			// FETCH IMAGE & WRITE TEXT
			$im = imagecreatefrompng($imagePath); 
			//imagecolorclosest  imagecolorallocate
			$red = imagecolorclosest($im, 0xFF, 0x00, 0x00);		
			$black = imagecolorclosest($im, 0x00, 0x00, 0x00);		
			$white = imagecolorclosest($im, 255, 255, 255);
			// imagecolorallocate(imagecreatetruecolor(111, 111), 2, 2, 2)
			//$color = $red;//$red;

			imagefttext($im, $fontsize=$opts['fontsize'], $angle=$opts['angle'], $x_pos=$x_position, $y_pos=$y_position, $color=$opts['color'], $font=$opts['font'], $text);
			imagealphablending($im, false);
			imagesavealpha($im, true);
			if ($resize=false)
			{
				$percent=0.5;
				$new_width = $width * $percent;
				$new_height = $height * $percent;
				$image_p = imagecreatetruecolor($new_width, $new_height);
				imagecopyresampled($im, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			}

			// Output and free memory
			header('Content-type: image/png');
			imagepng($im);
			imagedestroy($im);
		}
		else
		{
			// https://mlocati.github.io/articles/php-windows-imagick.html
			// https://www.php.net/manual/en/book.imagick.php
			$Imagick = new \Imagick();
			$Imagick->readImage($imagePath);
			$Imagick->setImageFormat( $format = $this->arrayKeyValue($opts,'format','png') );
			//$Imagick->setCompressionQuality ( 0 );
			
			$ImagickDraw = new \ImagickDraw();
			$ImagickDraw->setFontSize( $fontsize );
			$ImagickDraw->setTextAntialias ( true );
			if (array_key_exists('font',$opts) )
				$ImagickDraw->setFont( $font=$opts['font'] );
			
			if ( array_key_exists('stroke', $opts))
			{
				$ImagickDraw->setStrokeColor($opts['stroke'][0]);
				$ImagickDraw->setStrokeWidth($opts['stroke'][1]);
				$ImagickDraw->setStrokeOpacity($opts['stroke'][2]);
			}
			$ImagickDraw->setFillColor($color=$opts['color']);
			$ImagickDraw->setFillOpacity($opacity=$opts['opacity']);
			
			//$ImagickDraw->setGravity( Imagick::GRAVITY_CENTER );
			$Imagick->annotateImage( $ImagickDraw, $x_pos=$x_position, $y_pos=$y_position, $angle=$opts['angle'], $text);

			if ($opts['echo'])
			{
				header( "Content-Type: image/{$Imagick->getImageFormat()}" );
				echo $Imagick->getImageBlob();
			}
			else{
				$Imagick->writeImage($imagePath);
			}
		}
	}
	

	

	public static $nodepath='';
	public static function node_exec($filepath){
		//process.stdout.write("hii");
		return empty(self::$nodepath) ? 'Node path not set' : self::cmd(self::$nodepath .' '.$filepath);
	}


	

	#region     ############### ASYNC FUNCTIONS ############### //
	// https://www.reddit.com/r/PHP/comments/no7abs/

	public $async_methods_available = ['reactphp', 'amphp', 'amphp_parallel', 'spatie','parallel','fibers', 'swoole', 'exec', 'proc-open', 'popen', 'pcntl_fork', '_no_async_' ];
	// to execute non-blocking async callback-function |  args:   [$func,$args]  where $args is array 
	public function asyncFunction($callback, $which_method='spatie'){
		$method = strtolower($which_method);
		if ( !in_array( $method, $this->async_methods_available) ) 
			throw new \Exception( "$method not supported, use from: ". implode(' | ',$this->async_methods_available) );
		
		$func_name = 'asyncFunctions_helper_'.$method;
		call_user_func([$this,$func_name],  [ [$callback, $arg1=[]] ] );
	}

	// to execute blocking/non-blocking call of multiple gourp for async callback-functions
	// #######    EXAMPLE   #######
	//
	//   $func = function($param1=null, $param2=null, ... ){
	//	    echo "\nParams are : ". print_r(func_get_args(),true);
	//   };
	//
    //   $arr = [ [$func], [$func, "hi"], [$func, "good", "bye"] ] ;
	//   $helpers->asyncFunctions($arr,'parallel');

	public function asyncFunctions($callbacksArray, $which_method='spatie', $blocking=true, $exception_handler=null){
		$method = strtolower($which_method);
		if ( !in_array( $method, $this->async_methods_available) ) 
			throw new \Exception( "$method not supported, use from: ". implode(' | ',$this->async_methods_available) );

		$example = ': CALLBACK & ARGUMENT pair, like [$callback,$arg1,...] or [$callback], where $args behaves like as in call_user_func_array';
		if ( !is_array($callbacksArray) )
			throw new \Exception("asyncFunctions was passed incorrect argument. Should be array of $example"); 
		
		$callbacksArray_NEW =[]; // just add arguments
	
		foreach($callbacksArray as $eachPair){
			if (is_callable($eachPair))
			{
				$callbacksArray_NEW[] = [$eachPair, []];
			}
			elseif (is_array($eachPair))
			{
				$callback = $eachPair[0];
				array_shift($eachPair); //unset first one, as all other ones are arguments passed to call_user_func
				$args     = empty($eachPair)? [] : $eachPair;
				$callbacksArray_NEW[] = [$callback, $args];
			}
			else{
				throw new \Exception("asyncFunctions was passed incorrect array of callback. Each array child should be $example");
			}
		}
		
		if ($blocking)
		{
			$func_name = 'asyncFunctions_helper_'.$method;
			call_user_func([$this,$func_name], $callbacksArray_NEW, $exception_handler);
		}
		else{
			foreach($callbacksArray_NEW as $callbackAndArgPair){
				$this->asyncFunction($callbackAndArgPair, $which_method);
			}
		}
	}


	// ##### PARALLEL (requires extension) #####
    public static function asyncFunctions_helper_parallel($callbacksArray, $exception_handler=null)
    {
		foreach( $callbacksArray as $callback_arg_pair){
			//\parallel\run($callback = $callback_arg_pair[0], $args = $callback_arg_pair[1] ); 
			//$r1 = new \parallel\Runtime();$r1->run
			self::asyncFunction_parallel($callback = $callback_arg_pair[0], $args = $callback_arg_pair[1]);
		}
    } 
    public static function asyncFunction_parallel($callback, $args=[])
    {
		\parallel\run($callback, $args); 
    } 


	// ##### SPATIE #####  [ https://github.com/spatie/async/issues/120 ]
    public static function asyncFunctions_helper_spatie($callbacksArray, $exception_handler=null)
    {
		if ( ! $this->spatieSupported()  ){
			$msg = "Spaties needed extensions are not enabled: pcntl & posix";
			//if ( !$this->is_localhost()) throw new \Exception($msg); else   
			$this->var_dump($msg . " ; However, continuing execution in synchronous mode");
		}
		$pool = \Spatie\Async\Pool::create();
		foreach( $callbacksArray as $callback_arg_pair){
			$pool->add( function() use($callback_arg_pair){  
				call_user_func_array($callback = $callback_arg_pair[0], $args = $callback_arg_pair[1]);
			} )->then(function ($output) {
				// Handle success
			})->catch(function (\Spatie\Async\Pool\Throwable $exception) {
				var_dump($exception);
			});
		}
		$pool->wait(); 
    }  public static function spatieSupported(){ return \Spatie\Async\Pool::isSupported(); } 


	
	// ##### SWOOLE (requires extension) #####
	// use advanced usage: https://www.swoole.co.uk/docs/modules/swoole-coroutine-run
    public static function asyncFunctions_helper_swoole($callbacksArray, $exception_handler=null)
    {
		// needs to be checked ( as: https://www.swoole.co.uk/docs/modules/swoole-coroutine-run )
		if (self::swoole_installed() && function_exists('\\Swoole\\Coroutine\\run') )
		{
			if ( !self::swoole_inside_coroutine() ) 
			{
				\Swoole\Coroutine\run(function() use ($callbacksArray,$exception_handler)  {
					self::asyncFunctions_helper_swooleGoTrigger($callbacksArray,$exception_handler);
				});
			}
			else{ 
				self::asyncFunctions_helper_swooleGoTrigger($callbacksArray,$exception_handler);
			} 
		}
		else{
			var_dump("Swoole not installed, running plain function");
			self::asyncFunctions_helper__no_async_($callbacksArray);
		}
    }  
    private static function asyncFunctions_helper_swooleGoTrigger($callbacksArray, $exception_handler=null)
	{
		//$wg = new \Swoole\Coroutine\WaitGroup();
		foreach( $callbacksArray as $callback_arg_pair){
			\go(function() use ($callback_arg_pair, $exception_handler) {
				try{
					//$wg->add(1); 
					call_user_func_array($callback = $callback_arg_pair[0], $args = $callback_arg_pair[1]);
					//$wg->done();
				}
				catch(\Exception $ex){
					if (is_callable($exception_handler))
						call_user_func($exception_handler, new \Exception($ex->getMessage()) );
					else 
						trigger_error($ex->getMessage());
				}
			});
		}
		//$wg->wait();
	}  
	public static function swoole_inside_coroutine(){ return (self::swoole_installed() && \Swoole\Coroutine::getCid()!=-1 && \Swoole\Coroutine::getPcid()!==false ); } // https://cloud.tencent.com/developer/article/1771756
	public static function swoole_inside_coroutine_run(){ return (self::swoole_installed() && \Swoole\Coroutine::getPcid()==-1); } 
	public static function swoole_inside_coroutine_go(){ return (self::swoole_installed() && is_numeric(\Swoole\Coroutine::getPcid()) && \Swoole\Coroutine::getPcid()>0); } 

	public static function swoole_installed(){ return class_exists('\\Swoole\\Coroutine'); }
	public static function helper_SwooleToggle($enable_or_disable ){
		// SWOOLE_HOOK_ALL;  // https://www.geeksforgeeks.org/php-bitwise-operators/ ::: swoole_hook_all is: 2147479551  | SWOOLE_HOOK_CURL : 2048  | SWOOLE_HOOK_NATIVE_CURL : 4096    \Swoole\Runtime::getHookFlags(); // $currentHooks ^ SWOOLE_HOOK_CURL  ^ SWOOLE_HOOK_NATIVE_CURL; <-- cannot enable both  |  \Swoole\Runtime::setHookFlags($final); // https://github.com/swoole/swoole-src/issues/4280
		if (!self::swoole_inside_coroutine())
			return;
		if($enable_or_disable)
			\Swoole\Runtime::setHookFlags(SWOOLE_HOOK_ALL);
		else
			\Swoole\Runtime::setHookFlags(0);
	}

	// ##### AMPHP #####
	public static function asyncFunctions_helper_reactphp($callbacksArray, $exception_handler=null){
		$loop = \React\EventLoop\Factory::create();
		$promise = new \React\Promise\Promise(function($resolve){
		});
		foreach($callbacksArray as $callback_arg_pair) { 
			$promise->then(function($v) use ($loop, $callback_arg_pair, $exception_handler) {
				$loop->run( function() use($callback_arg_pair, $exception_handler){
					try{
						call_user_func_array($callback = $callback_arg_pair[0], $args = $callback_arg_pair[1]);
					}
					catch(\Exception $ex){
						if (is_callable($exception_handler))
							call_user_func($exception_handler,$ex);
						else 
							trigger_error($ex->getMessage());
					}
				}  );
			});
		}
		$loop->run();		
	}


	// ##### AMPHP #####
	public static function asyncFunctions_helper_amphp($callbacksArray, $exception_handler=null){

		$promises = [];
		\Amp\Loop::run(function () use($callbacksArray) { 
			foreach($callbacksArray as $eachCallbackAndArg) { 
				\Amp\Loop::defer($eachCallbackAndArg[0]); 
			}
		});

		return;
		//...
			$promises[] = \Amp\call( function() use ($eachCallbackAndArg) {
				return call_user_func($eachCallbackAndArg[0],$eachCallbackAndArg[1]);
			} );  
		//\Amp\Loop::run();  \Amp\asyncCall
		\Amp\Promise\wait(\Amp\Promise\all($promises)); 

		return;
		// ..
			\Amp\asyncCall( function() use ($eachCallbackAndArg) {
				return call_user_func($eachCallbackAndArg[0],$eachCallbackAndArg[1]);
			} ); 
		\Amp\Loop::run();
	}

	// https://github.com/amphp/parallel-functions  && https://github.com/amphp/parallel-functions/issues/28
	public static function asyncFunctions_helper_amphp_parallel($callbacksArray, $exception_handler=null){
		foreach($callbacksArray as $callback_arg_pair) {
			//$pool = new \Amp\Parallel\Worker\DefaultPool();
			$callbacks[] = call_user_func_array(\Amp\ParallelFunctions\parallel($callback_arg_pair[0]), $callback_arg_pair[1]); //, $pool
		}
		$result = \Amp\Promise\wait(\Amp\Promise\all($callbacks));
	}



	// ##### EXEC #####
	// https://stackoverflow.com/questions/49592786/asynchronous-call-to-shell-exec-php
	// https://stackoverflow.com/questions/222414/asynchronous-shell-exec-in-php
	// https://stackoverflow.com/questions/45953/php-execute-a-background-process#45966
	// https://stackoverflow.com/questions/2212635/best-way-to-manage-long-running-php-script !!
	public static function asyncFunctions_helper_exec($command = null, $with_php=false){
		// moved to separate file, due to WP restrictions
		return self::asyncFunctions_helper_exec2($command, $with_php);
	}

	// no async, just default fallback
    public static function asyncFunctions_helper__no_async_($callbacksArray, $exception_handler=null)
    {		
		self::call_user_funcs($callbacksArray);
    } 
    public static function call_user_funcs($callbacksArray)
    {
		foreach($callbacksArray as $callbackArray){
			$callback = $callbackArray[0];
			unset($callbackArray[0]); $args = $callbackArray; //all other items are arguments passed to call_user_func
			call_user_func_array($callbackArray[0], $args);
		}
    }
	#endregion


	


	public function resizeImage($imagePath, $width, $height=0, $auto_proportion=true, $filter=false, $blur=1)
	{
		$Imagick = new \Imagick();
		$Imagick->readImage($imagePath);
		//$Imagick->setImageFormat( $format = $this->arrayKeyValue($opts,'format','png') );
		$filter= !$filter ? \Imagick::FILTER_LANCZOS : $filter;  //FILTER_LANCZOS
		$Imagick->resizeImage($width, $height, $filter, $blur, $auto_proportion );
		$Imagick->writeImage($imagePath);
	}
	
	// get-timezones : pastebin_com/4tXjgY7B

	public function add_prefix_to_object_keys($object, $prefix){
		$new_object = new stdClass();
		foreach ($object as $k => $v) { 
			$new_object->{$prefix . $k} = $v;
		}
		return $new_object;
	}

	public function convertClockToSeconds($input="4h", $minute_symbol="m", $month_symbol="M")
	{
		$array=['s'=>1,'S'=>1,     'm'=>60,    'h'=>3600,'H'=>3600,     'd'=>86400,'D'=>86400,     'w'=>604800,'W'=>604800,     'M'=>2678400];//31days
		foreach($array as $key=>$value)
		{
			if ( strpos($input,$key)!==false ) { $input=str_replace($key,'', $input); $input=$input*$value; }
		}
		return $input;
	}
	
	public function dieMessage($txt){
		echo 
		'<div style="padding: 50px; margin:100px auto; width:50%; text-align:center; line-height: 1.4; display:flex; justify-content:center; flex-direction:column; font-family: cursive; font-size: 1.7em; box-shadow:0px 0px 10px gray; border-radius: 10px;">'.
			'<div><h3>'.$txt.'</h3></div>'.
		'</div>';
		exit;
	}


	public function Return_If_Isset($var){ if (isset($var)) { return $var; }    else { return false; }  }
	public function Return_If_Not_Empty($var){ if (!empty($var)) { return $var; }    else { return false; }  }
	public function Return_If_Array_Key($array, $keyname){ if (array_key_exists($keyname, $array)) { return $array[$keyname]; }    else { return false; }  }

	// custom always-loaded scripts 
	// my_script_url("css|js",  "public|admin")
	public function my_script_url($type="js|css", $kind="public|admin", $with_tag=false) 
	{
		if ($type=='js'){
			return ($with_tag? '<script type="text/javascript" src="':'') . $this->js_library() .'&vers='.$this->changeable_JS_CSS_version . ($with_tag? '"></script>':'');
		}
		elseif ($type=='css'){
			return ($with_tag? '<link rel="stylesheet" href="':'') . $this->baseScriptsUrl.'style-'.$kind.'.css?vers='.$this->changeable_JS_CSS_version. ($with_tag? '"	type="text/css" media="all" />':'');
		}
	}
	public function my_loader_css_js($css=true, $js=true)
	{  	
		$admin = function_exists('is_admin') ? is_admin() : false;
		if ($css)	echo $this->my_script_url('css', ( $admin ? 'admin':'public'), true);
		if ($js) 	echo $this->my_script_url('js',  '', true);
	}
	
	public function my_loader_css_js_trigger()
	{  	
		$screen= is_admin()? 'admin' : 'public';
		$this->my_loader_css_js($css=$this->load_styles['css'][$screen], $js=$css=$this->load_styles['js'][$screen]);
	}

	// ================================== STYLES ================================== //
	
	private $all_enqueue_scripts=[];
	public function init_loadscripts($override_array)
	{
	  $initial_scripts=
	  [
		//
		'my_javascript'	=> ['screen'=>['admin'=>0, 'public'=>0], 'urls'=>[
			'js' => $this->my_script_url('js','')
		]],
		'my_style_public'=> ['screen'=>['admin'=>0, 'public'=>0], 'urls'=>[
			'css' => $this->my_script_url('css','public')
		]],
		'my_style_admin'=> ['screen'=>['admin'=>0, 'public'=>0], 'urls'=>[
			'css' => $this->my_script_url('css','admin')
		]]
	  ];
	  $overrides =  method_exists($this, 'define_load_links') ?  $this->define_load_links() : [];
	  
	  $this->all_enqueue_scripts = array_replace_recursive( array_merge($initial_scripts,$overrides),  $override_array );
	}
	
	
	public function my_styles_hook($pure_php=false) {
		$front_or_back = function_exists('is_admin') && is_admin() ? 'admin' : 'public';
		$current_screen = $front_or_back=='public' ? 'wp' : 'admin';  // gets: admin or public
		foreach ($this->all_enqueue_scripts as $name=>$block)
		{
			if($block['screen'][$front_or_back])
			{
				if (!empty($block['urls']))
				foreach ($block['urls'] as $JS_or_CSS=>$url)
				{
					$type_ = ($JS_or_CSS=="js") ? 'script' : ($JS_or_CSS=="css" ? 'style' : $JS_or_CSS);
					if ($pure_php===true) 
					{
						if ($type_=='style')
							echo '<link rel="stylesheet" href="'.$url.'" type="text/css" media="all" />';
						else {
							echo '<script src="'.$url.'"></script>';
						}
					}
					else
					{
						$this->register_stylescript($current_screen, $type_, $name, $url);
					}
				}
			}
		}
	}

	//example testmode : pastebin_com/bUncPcFD
	
	public function filedate($file){
		return date("Y-M-D--H-i-s", filemtime($file) ); 
	}

	public function TRANSLL($phraze,$LNG=false, $desired=array())	{ return apply_filters('MLSS', $phraze, ($LNG ? $LNG: (defined('LNG') ? LNG : '' )  ),  $desired    );   }

	public function MY_LANGSS(){
		if (!function_exists('LANGS__MLSS')){
			if(!empty($GLOBALS['my_custom_langs'])) return $GLOBALS['my_custom_langs'];
			if(defined('ERROR_SHOWN__MLSS') || DISABLE_MLSS_ERROR ) {return array();}	

			$xx344=debug_backtrace();
				echo '<script>alert(\'plugin "Multi-Language Site (basis)" seems not installed. please install it.\r\n\r\n\ File:'. $xx344[0]['file'] .' \r\n\ line:'.$xx344[0]['line'].'\');</script>';  
				if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {die('error_45y4e5ge4g'); }	 define('ERROR_SHOWN__MLSS',1);
		}
		else{  return LANGS__MLSS();  }
	}
	
	 
	//if ( !$this->above_version('5.4') ) { echo("php_version is ". PHP_VERSION ." (quite old). HIGHLY recomended to update to higher version, or this program might not funciton normally ". __FILE__ ); }
	public function above_version($version= "5.4"){
		return version_compare(phpversion(), $version, '>=');
	}

	public function noindex_meta_tag() { return '<meta name="robots" content="noindex, nofollow">'; }

	public function valueToString( $value ){
		return is_bool($value) ? ($value ? 'true' : 'false' ) : strip_tags(  $value ) ;
	}
	public function stringToValue( $value ){
		return is_bool($value) ? $value : ( !is_string($value) ?  $value : ( $value =='true' ? true : (  $value =='false' ? false : $value) ) );
	}

    public function randomId($divider="_"){ return "K".time() . $divider . rand(1,9).rand(0,9999999999999); }


	
	
	// #####################################
	//check either GET or argv (note, in argv, the parameters should be quoted, like: 
	//       * * * * *  /usr/bin/php -q /var/www/example/wp-cron.php "cron_cycle=3&param=1"	
	public static function Argv($key='', $default='')
	{
		global $argv;
		$array = [];
		if (!empty($argv[1])) {
			parse_str($argv[1], $array);
		}
		return empty($key) ? $array : ( array_key_exists($key, $array) ? $array[$key] : $default);
	}
	public function Get($key='', $default='')
	{
		return empty($key) ? $_GET : ( array_key_exists($key, $_GET) ? $_GET[$key] : $default);
	}
	// THIS IS ALWAYS SANITIZED IN IMPLEMENTATIONS
	public function Post($key=''){
		if (!empty($_POST)) {
			$array = $_POST;
		}
		else
		{
			$fc = file_get_contents('php://input');
			if (!empty($fc) )
			{
				$array= json_decode($fc, true);
				if (is_null($array))
				{
					$if_parse_str = false; //enable if wanted
					if ($if_parse_str && strpos($fc,'=')!==false)
					{
						parse_str($fc, $array);
					}
					else{
						return $fc;
					}
				}
			}
			else{
				$array = [] ;
			}
		}
		

		return !empty($key) ? $this->array_value($array, $key, null) : $array;
	}
	public function inputData(){
		return $this->POST();
	}
	
	public function ArgvGet($key='', $default=null)
	{
		global $argv;
		$array = [];
		if (!empty($argv[1])) {
			parse_str($argv[1], $array);
		}
		else {
			$array=$_GET;
		}
		return !empty($key) ? $this->array_value($array,$key,$default) : $array;
	}
	public function ArgvIsSet($key)
	{
		global $argv;
		$array = [];
		if (!empty($argv[1])) {
			parse_str($argv[1], $array);
		}
		return array_key_exists($key,$array);
	}
	public function ArgvGetIsSet($key)
	{
		global $argv;
		$array = [];
		if (!empty($argv[1])) {
			parse_str($argv[1], $array);
		}
		else {
			$array=$_GET;
		}
		return array_key_exists($key,$array);
	}

	public static function ArgvFile()
	{
		global $argv;
		return !empty($argv[0]) ? $argv[0] : '';
	}

	public static function IsCron(){
		try{
			$sapi_type = php_sapi_name();
			return (substr($sapi_type, 0, 3) == 'cli' || empty($_SERVER['REMOTE_ADDR']));
		}
		catch(\Exception $ex){
			return false;
		}
	}
	
	public static function array_to_argv($array=null)
	{
		$str = http_build_query($array);
		return $str;
	}

	//convert command line to $_GET
	public function argv_to_array($argv_=null,$index=1)
	{
		$array=[];
		if (!empty($argv_[$index])) parse_str($argv_[$index], $array);	
		return $array;
	}


	public static function php_path_current(){ return PHP_BINARY; }
	
	public static function command_current($addToParams=''){
		return self::php_path_current().' "'.self::ArgvFile().'" "'.self::array_to_argv(self::Argv()) .$addToParams.'"';
	}

	public static function is_simple_type($var){
		return ( is_string($var) || is_numeric($var) || is_bool($var) );
	}


	public function argvs_get_post($argv_,$index=1)
	{
		//$array=[];
		//if (!empty($argv_[$index])) parse_str($argv_[$index], $array);	
		return $array;
	}
	
	public function serialize_argv($argvs)
	{
		if(empty($argvs) || !is_array($argvs)) return $argvs;
	
		$new_ar=[];
		foreach($argvs as $key=>$value)
		{
			if(stripos($value,'=')===false)
			{
				$new_ar[$key] = $value;
			}
			else{
				parse_str($argvs[$key], $params);
				$key1=array_keys($params)[0];
				if(!empty($argvs) && is_array($params))
					$new_ar[$key1] =  $params[$key1];
			}
		}
		return $new_ar;
	}
	
	// #####################################





	public function array_fields($array, $parent="plugin_slug[sample][sub]", $pairs=false)
	{ 
		echo '<div class="inpHolder">';

		echo '<div class="inputsBlock">';
		if (is_array($array) && !empty($array)) 
		{
			foreach ($array as $optName=>$value)
			{
				echo $this->field_out_helper1($parent, $optName, $value, $pairs) ;
			}
		}

		$sample_field = $this->field_out_helper1($parent, "", "", $pairs);
		//echo $sample_field;
		echo '</div>';
		?>
				<?php $unique = $this->sanitize_nonoword($parent); ?>
		<a class="button" href="#" onclick="return <?php echo $unique;?>_addNewArrayField_k(this);" class="addNewArrayInput"><?php _e('Add New');?></a>
		<script>
		function <?php echo $unique;?>_addNewArrayField_k(el)
		{ 
			var targetEl = el.parentNode.parentNode.parentNode.getElementsByClassName("eachInputBlock")[0];
			var rand=  Math.random().toString(36).substring(2); 
			var newElString = targetEl.outerHTML.replace( /(inputKey_[\w]*)/g, "inputKey_"+rand).replace(/value="(.*?)"/g, 'value=""');
			targetEl.parentNode.insertAdjacentHTML("beforeend", newElString);
			return false;
		}
		</script>
		<?php
		echo '</div>';
	}

	public function field_out_helper1($parent, $optName, $value, $pairs)
	{
		$output='<div class="eachInputBlock">';
		$rand= "inputKey_".rand(1,999999)."_".rand(1,999999)."_".rand(1,999999);  
		if (!$pairs) { 
			$key = (!empty($optName) ? $optName : $rand);
			$output .= '<input name="'.$parent.'['.$key.']"  class="eachInput each_'.$key.' regular-text" type="text" value="'.$value.'"  placeholder="" />';
		} else {
			$output .= '<input name="'.$parent.'['.$rand.'][name]"  class="eachInput each_'.$rand.' medium-text _key" type="text" value="'. (!empty($optName) ? $optName : "").'"  placeholder="name" />';
			$output .= '<input name="'.$parent.'['.$rand.'][value]"  class="eachInput each_'.$rand.' medium-text _value" type="text" value="'.$value.'"  placeholder="value" />';
		}
		$output .='</div>';
		return $output;
	}



	// 	<!-- GEORGIAN automatic keyboard while typing in SEARCH --> <script type="text/javascript" src="'. $this->baseURL .'/library/js/geokbd.js"></script>

	public function arrayFieldsResort($ar)
	{
		$new=[];
		foreach($ar as $key=>$val)
		{
			$new[ $this->sanitize_text_field($val["name"]) ] = $this->sanitize_text_field($val["value"]);
		}
		return $new;
	}


	public function get_fb_name_regex($fb_url){
		preg_match('/'.preg_quote('^(?:https?://)?(?:www.|m.|touch.)?(?:facebook.com|fb(?:.me|.com))/(?!$)(?:(?:\w)#!/)?(?:pages/)?(?:[\w-]/)?(?:/)?(?:profile.php?id=)?([^/?\s])(?:/|&|?)?.*$/'), $fb_url, $n);
		return $n[1];
	}
	
	
	public function shapeSpace_allowed_html() {

		$allowed_tags = [
			'a' => [
				'class'=>[], 'href'=>[], 'rel'=>[], 'title'=>[],
			],
			'abbr' => [
				'title' => [],
			],
			'b' => [],
			'blockquote' => [
				'cite'  => [],
			],
			'cite' => [
				'title' => [],
			],
			'code' => [],
			'del' => [
				'datetime'=>[], 'title'=>[],
			],
			'dd' => [],
			'div' => [
				'class'=>[], 'title'=>[], 'style'=>[],
			],
			'dl' => [],
			'dt' => [],
			'em' => [],
			'h1' => [],
			'h2' => [],
			'h3' => [],
			'h4' => [],
			'h5' => [],
			'h6' => [],
			'i' => [],
			'img' => [
				'alt'=>[], 'class'=>[], 'height'=>[], 'src'=>[], 'width'=>[],
			],
			'li' => [
				'class'=>[],
			],
			'ol' => [
				'class'=>[],
			],
			'p' => [
				'class'=>[],
			],
			'q' => [
				'cite'=>[],'title'=>[],
			],
			'span' => [
				'class'=>[], 'title'=>[], 'style'=>[],
			],
			'strike' => [],
			'strong' => [],
			'ul' => [
				'class' => [],
			]
		];
		
		return $allowed_tags;
	}	
	
	public function display_errors()
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting( E_ALL );
	}
	
	public function log_errors($path=false, $callback=null)
	{
		//htaccess:
		//	php_flag log_errors on
		//	php_value error_log /home/FTP_username/public_html/error_log.txt
		ini_set("log_errors", 1);
		ini_set("error_log", $path ? $path : $_SERVER['DOCUMENT_ROOT']."/zzz___php-my-errors_".$this->my_site_variables__secret('rand_name', RandomString(11)).".log");
		//error_log( "Hello, errors!" );	
		if (!is_null($callback)) set_error_handler($callback);
	}
	 
	public function javascript_headers()
	{
		session_cache_limiter('none');
		// http://stackoverflow.com/a/1385982/2377343
		//Caching with "CACHE CONTROL"
		header('Cache-control: max-age='.($year=60*60*24*365) .', public');
		//Caching with "EXPIRES"  (no need of EXPIRES when CACHE-CONTROL enabled)
		//header('Expires: '.gmdate(DATE_RFC1123,time()+$year));
		//To get best cacheability, send Last-Modified header and ...
		header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime(__file__)));  //i.e.  1467220550 [it's 30 june,2016]
		//reply using: status 304 (with empty body) if browser sends If-Modified-Since header.... This is cheating a bit (doesn't verify the date), but remove if you dont want to be cached forever:
		// if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {  header('HTTP/1.1 304 Not Modified');   die();	}
		header("Content-type: application/javascript;  charset=utf-8");
	}
	
	public function is_cli()
	{
		if( defined('STDIN') ) return true;  
		if( empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0)  return true; 
		return false;
	}
 
	public function init_defaults()
	{
		try{
			//some of this can be overwriten by init_module
			$this->ip				= $this->get_visitor_ip();
			$this->isMobile			= false;
			$this->isWP				= defined("ABSPATH");
			$this->isCLI			= $this->is_cli();
			$this->is_development 	= defined("_puvox_machine_") ;			// set only in devmachine (in "my_superglobals.php" and in "EnvVariables")
			//only web parts
			$this->is_https			= $this->isCLI ? false 		: ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']==443) );
			$this->https			= $this->isCLI ? 'https://' : ( $this->is_https ? 'https://' : 'http://');
			$this->domainCurrent	= $this->isCLI ? '' 		: (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
			$this->domain			= $this->isCLI ? '' 		: $this->https . $this->domainCurrent;
			$this->requestURL		= $this->isCLI ? '' 		: $this->array_value($_SERVER,'REQUEST_URI'); $this->requestURI=$this->requestURL;
			$this->currentURL		= $this->isCLI ? '' 		: $this->domain.$this->requestURL; 
			$this->domainCurrentWithoutPort=$this->isCLI ? ''   : $this->array_value( parse_url($this->currentURL),'host');
			$this->is_localhost     = $this->isCLI ? false 		: $this->is_localhost();

			// others 
			$this->empty_image		= "data:image/svg+xml;utf8,&lt;svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'&gt;&lt;/svg&gt;";
			$this->extra_options_enabled=false;
		}
		catch(\Exception $ex){
			$this->var_dump($ex);
		}
	}

	// only when called explicitly, i.e. from plugin or module
	public function init_module($args=[])
	{
		// Because this is a class, we don't use "__FILE__" & "__DIR__" here, but "Reflection" to refer to caller file ####
		$isClass=(array_key_exists('class',$args) && !empty($args['class']));
		$reflection				= $isClass ? (new \ReflectionClass( $args['class'] )) : null; 
		$this->module_NAMESPACE	= $isClass ? $reflection->getNamespaceName() : (array_key_exists('NAMESPACE',$args) ? $args['NAMESPACE'] : "EXAMPLE"); 	// get parent's namespace name
		$this->moduleFILE		= $isClass ? $reflection->getFileName() : (array_key_exists('FILE',$args) ? $args['FILE'] : __FILE__); 		// set plugin's main file path
		$this->moduleDIR		= (array_key_exists('DIR',$args) ? $args['DIR'] : dirname($this->moduleFILE) ).DIRECTORY_SEPARATOR ;  	// set plugin's dir	path
		$this->prefix			= strtolower( preg_replace('![^A-Z]+!', '', $this->module_NAMESPACE) );// get prefix from current namespace initials of UpperCase characters (i.e. MyPluginNamespace-->MPN)
		$this->prefix_			= $this->prefix .'_';
		
		//$backtrace = debug_backtrace(); 	$this->_index_file_		= $backtrace[0]['file'];		$this->_index_dir_		= dirname($this->_index_file_);   
		// if this class is used just as a helper php library
		if (!$this->isWP || array_key_exists('homeFOLDER', $args))
		{
			$this->homeFOLDER 		= $args['homeFOLDER'];
			$this->homeURL 			= $this->domain.$this->homeFOLDER;
			$this->doc_root_real 	= $this->slashesForward(str_replace( $this->homeFOLDER,'',  $this->slashesForward($this->moduleDIR) )); // even for symlinked;
			$this->moduleURL		= str_replace($this->doc_root_real,'',  $this->slashesForward($this->moduleDIR)) ;
		}
		// else, if this class is used as plugin class (used mostly by Puvox.Software)
		else
		{
			$this->wpURL 			= network_home_url('/');						// WP installation home 
			$this->wpFOLDER 		= network_home_url('/', 'relative');			// WP folder 
			$this->homeURL			= home_url('/');								// current sub/site home url
			$this->homeFOLDER		= home_url('/', 'relative');					// current sub/site home folder
			$this->moduleURL		= plugin_dir_url($this->moduleFILE);			//
		} 
		$this->httpsReal		= preg_replace('/(http(s|):\/\/)(.*)/i', '$1', $this->homeURL);
		$this->domainReal		= $this->getDomain($this->homeURL);  $this->domainNaked=$this->domainReal;
		$this->domain			= $this->httpsReal.$this->domainReal;
		$this->domain_schemeless= '//'.$this->domainReal;
		$this->siteslug			= str_ireplace('.','_',   $this->domainReal);
		$this->urlAfterHome		= substr($this->requestURL, strlen($this->homeFOLDER) );
		$this->pathAfterHome	= parse_url($this->urlAfterHome, PHP_URL_PATH);
		$this->homeUrlStripped	= $this->stripUrlPrefixes($this->homeURL); 

		$this->baseFILE			= $this->moduleFILE;								//
		$this->baseDIR			= $this->moduleDIR.'/';								//
		$this->baseURL			= property_exists($this, 'baseURL') ? $this->baseURL : $this->moduleURL; //( stripos(__FILE__, 'wp-content'.DIRECTORY_SEPARATOR.'themes') !== false ? themeURL ... 
		$this->baseScriptsFolder= property_exists($this, 'baseScriptsFolder') ? $this->baseScriptsFolder : '';
		$this->baseScriptsDir	= $this->baseDIR . $this->baseScriptsFolder; 
		$this->baseScriptsUrl	= $this->baseURL . $this->baseScriptsFolder; 
		$this->changeable_JS_CSS_version = ( file_exists($file = $this->baseScriptsDir.'/style-public.css') ? 'date_'.filemtime($file) : $this->sanitize_key($this->domainReal).date('m') );
		
		// others
		$this->is_development 	= defined("_puvox_machine_") ;			// set only in devmachine (in "my_superglobals.php" and in "EnvVariables")
		if ($this->is_development)
		{
			$this->display_errors();
			if (!property_exists($this,'triggered_dev_shutdown_hook'))
			{
				$this->triggered_dev_shutdown_hook=true;
				register_shutdown_function( function(){ if (substr(ob_get_contents(), -7)=='</html>') echo('<div data-debug-memory-limit="'. ini_get('memory_limit').'" data-debug-WP_MEMORY_LIMIT="'. (defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : '').'"></div>');} ); 
				$this->START_TIME1 = microtime(true);
				register_shutdown_function( function(){ if (substr(ob_get_contents(), -7)=='</html>') echo('<div data-debug-time-load="'. (microtime(true)-$this->START_TIME1).'"></div>');} );
			}
		} 
	} 
	
} // class

 


#endregion
//==========================================================================================================
//==========================================     ### PHP codes     =========================================
//==========================================================================================================
  























































 
//==========================================================================================================
//==========================================================================================================
//======================================== 2) Library of WP functions  =====================================
//==========================================================================================================
//==========================================================================================================

#region 2
 
class standard_wp_library extends standard_php_library  
{
	public function __construct()
	{
		parent::__construct();
	}

	public $logs_table_maxnum=100;
	public $logs_table_name  ='_default_table_ERRORS_LOGS';
	
	public function init_module($args=[])
	{
		parent::init_module($args);
		
		//get blog-slug
		if(is_multisite()){
			global $blog_id;  if(empty($blog_id)) $blog_id = get_current_blog_id();  
			$current_blog_details = function_exists('get_sites') ? get_site($blog_id) : get_blog_details( array( 'blog_id' => $blog_id ) );
			$b_slug = basename($current_blog_details->path);
		} 
		$this->BLOGSLUG = (!empty($b_slug)? $b_slug : basename($this->homeFOLDER) );

		// others
		$this->this_file_link= '';//$this->baseURL . $this->urlify( explode( basename($this->baseURL), __FILE__  )[1] );
		$this->PHP_customCALL= '';//$this->this_file_link .'?custom_php_load=scripts_load&actionn=';
		
		if ($this->is_development)
		{
			$this->js_debugmode("debugmode");
		} 
	}
	
	public function CurrentHomeIs($path){ return trailingslashit($path)==trailingslashit(str_replace( trailingslashit(network_site_url()), '', trailingslashit(home_url())) ); } //return in_array(home_url(), ["http://$site","https://$site"]) || home_url('', 'relative')==$site; } 

	public function home_url(){ return trailingslashit(home_url());} 


	public function loadScripsStyles()
	{
		add_action( 'admin_head', function (){ if ($this->property('admin_styles')) echo $this->admin_styles; } );

		//load desired scripts 
		add_action( 'wp_enqueue_scripts', 		[$this, 'my_styles_hook'], 9); 
		add_action( 'admin_enqueue_scripts',	[$this, 'my_styles_hook'], 9);  
	}
	
	public function init_properties()
	{
		if ($this->property('auth_expiration_hours'))	 	$this->init__cookieexpiration();
		if ($this->property('navmenu_search_items'))		$this->init__quicksearch();
		if ($this->property('extend_shortcodes')) 			$this->extendShortcodes();
		if ($this->property('posts_per_page')) 				$this->init__postsperpage($this->posts_per_page); 
		// This is not enabled, unless user explicitly enables it during tests!!! IT IS NOWHERE ENABLED, UNLESS YOU INSERT IN CODE YOURSELF. so, don't fear.
		if ($this->property("enable_write_logs"))			$this->SaveLogs( $this->baseDIR  .'/___logs_' ); 
		if ($this->property('disable_update'))				$this->init__disableupdate();
	}
	
	//when is_admin or when page is unknown (for example, custom page or "wp-login.php" or etc... )
	public function Is_Backend(){
		$includes=get_included_files();
		$path	= str_replace( ['\\','/'], DIRECTORY_SEPARATOR, ABSPATH);
		return (is_admin() || in_array($path.'wp-login.php', $includes) || in_array($path.'wp-register.php', $includes) );
		//return (!!array_intersect([$ABSPATH_MY.'wp-login.php',$ABSPATH_MY.'wp-register.php'] , get_included_files())) ;
	}
		
	public function is_gutenberg($active=true){
		return ( function_exists( 'is_gutenberg_page' ) && (!$active || $this->is_gutenberg_page() ) );
	}
		
	public function is_gutenberg_page($active=true){
		if (is_admin()) {
			global $current_screen;
			if (!isset($current_screen)) {$current_screen = get_current_screen();}
			if ( method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor() ||  $this->is_gutenberg(true) ) {
				return true;
			}
		}
		return false;
	}

	public function init__disableupdate() 
	{
		add_filter('site_transient_update_plugins', function ($value) { if (isset($value)) { if ( isset($value->response[$name=plugin_basename($this->baseFILE)]) ) { unset($value->response[$name]); } } return $value; });
	}


	//Get Blog slug, i.e. "subdir"  from "http://example.com/subdir/"
	public function get_blog_name(){
		if(is_multisite())
		{
			global $blog_id;
			$current_blog_details = !function_exists('get_blog_details') ? get_site($blog_id) : get_blog_details( ['blog_id' => $blog_id] );
			$b_slug = basename($current_blog_details->path);
			return $b_slug;
		}
		return false;
	}

	public function sqlResultsToArray($tableName, $first_key, $second_key=false, $data_key=false)
	{ 
		$array=$this->object_to_array( $this->get_table_my($tableName) );

		$new_array=[];
		foreach($array as $id=>$block)
		{
			if(array_key_exists($first_key, $block))
			{
				if ($second_key)
				{
					if(array_key_exists($second_key, $block))
						$new_array[$block[$first_key]][$block[$second_key]] = $data_key ? json_decode($block[$data_key]) : $block;
				}
				else
					$new_array[$block[$first_key]] = $data_key ? json_decode($block[$data_key]) : $block;
			}
		}
		return $new_array;
	}


	public function get_locale__SANITIZED(){
		return ( get_locale() ? "en" : preg_replace('/_(.*)/','',get_locale()) ); //i.e. 'en'
		//$x=$GLOBALS['wpdb']->get_var("SELECT lng FROM ".$this->options." WHERE `lang` = '".$lang."'"); return !empty($x);
	}

	
	public function blog_prefix()
	{
		$blog_prefix = '';
		if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( get_option( 'permalink_structure' ), '/blog/' ) ) {
			$blog_prefix = '/blog';
		}
		$this->blog_prefix = $blog_prefix;
		return $blog_prefix;
	}

	public function path_after_blog()
	{
		$prf = $this->blog_prefix();
		$path = $this->pathAfterHome; 
		return ( ($prf=="/blog") ? str_replace('/blog/', '', '/'.$path) : $path );
	}

	public function disable_metabox_folding()
	{
		add_action('admin_footer', function ()
		{ ?><script>
			jQuery(window).load(function() {
			   jQuery('.postbox .hndle').css('pointer-events', 'none');
			   jQuery('.postbox .hndle input, .postbox .hndle select').css('pointer-events', 'all');
			   { jQuery('.postbox .hndle').unbind('click.postboxes'); jQuery('.postbox .handlediv').remove(); jQuery('.postbox').removeClass('closed'); }
			});
			</script><?php
		} ); 
	}

	
	
	public function checkMyselfAgainstModification()
	{
		//if ($this->is_development) return;
		$name = '_puvox_default_lib_last_revision';
		$opt= $this->get_option_CHOSEN($name, 0 );
		$days=7;
		if( time() - $opt > $days* 86400 )
		{
			//https://plugins.trac.wordpress.org/browser/'.$this->slug.'/trunk/default_library_puvox.php
			update_option_CHOSEN($name, time() );
		}
		if(time() - $opt < 0 ){
			update_option_CHOSEN($name, 0 );
		}
	}

	
	public function enable_admin_debug(){
		if (WP_DEBUG)
			add_filter('wp_php_error_message', function($message, $error){ return $message. $this->var_dump($error['message']); } , 10,2);
		//add_filter( foreach(['wp_die_ajax_handler',  'wp_die_json_handler',  'wp_die_handler'] as $each){ 
	}
	


	// add personal notes page:
	public function mynots222() {
		add_action('admin_menu', function() { 
			add_menu_page('myNOTES', 'myNOTES', 'read','mynotes-urllll', [$this, 'ntsFNC222'] ); } );
	}
	public function ntsFNC222() {
		if (!$this->NonceCheck($_POST['nonce'], 'myact')) return;
		
		if (isset($_POST['nmtIDv'])) { update_option('myfuture_notes_contentt', sanitize_text_field($_POST['nmtIDv'])); }
		$contn = get_option('myfuture_notes_contentt');
					echo 
		'<form style="margin:50px 0 0 0;" action="" method="POST">On this page you can save custom notes';
			if (current_user_can('create_users')) { echo '<div style="color:red;font-style:italic;"></div>';}
					echo
			'<div class="mpmybook_textareaDIV"> 
				<style>	#nmtIDv_div{width:100% !important; height:1000px !important;}</style>';
				wp_editor($contn, 'nmtIDv', $settings = array(
				'editor_class'=>'notesmyyCLASS',    'textarea_name'=>'mynots123', 'editor_height'=>'1000px', 'textarea_rows'=>'1000',
				'tinymce'=>true ,'wpautop'=>false,	'media_buttons'=>true,	'teeny'=>false,	'quicktags'=>false,		'drag_drop_upload'=>true )); echo
			'</div>
			<br/><input style="position:fixed;left:45%;bottom:10px;background-color:#1FC81F;" type="submit" value="SAVE" />
		</form>';
	}

	
	public function my_site_variables__secret($var_name=false, $value=false){
		$final= $this->SITE_VARIABLES = get_site_option('site_variables_my_secret',[]);
		if ($var_name) {
			if(array_key_exists($var_name, $this->SITE_VARIABLES)){
				$final = $this->SITE_VARIABLES[$var_name];
			}
			elseif($value) {
				$final = $this->SITE_VARIABLES[$var_name]=$value;
				update_site_option('site_variables_my_secret', $this->SITE_VARIABLES);
			}
			else{
				$final = '';
			}
			return $final;
		}
		else{ return $this->SITE_VARIABLES; }
	}



	public function dashicon_styled($name, $style){
		return '//" class="dashicons-before '.$name.'" style="opacity:0.91; '.$style;
	}
	


	// ====================== tinymce buttons ==================== //
	
	// $this->my_default_buttons= array('superscript', 'subscript') + array( "|", "youtube_video","audioo", "add_spacee_button", "removeline_button", "abzac_button","videomovie", "lists", "script");
 
	public function tinymce_funcs()
	{
		// Add button in TinyMCE 
		add_action( 'admin_init', 			function(){
			if ( get_user_option('rich_editing') == 'true') {
				add_filter( 'mce_external_plugins',	function ( $plugin_array ) { return array_merge($plugin_array, ["button_handle_" . $this->slug=> $this->homeURL  . '?tinymce_buttons_'.$this->slug] );  } );
				add_filter( 'mce_buttons_2',	function ( $button_names ) { return array_merge( $button_names, array_map(  function($ar){ return $ar['button_name']; }, $this->tinymce_buttons )); } );
				//this is must for REFRESHING!
				add_filter( 'tiny_mce_version',  function ( $ver ) {  $ver += 3;  return $ver;}  );
			}
		} );
		//tinymce buttons if needed
		$this->tinymce_buttons_body();
		foreach($this->tinymce_buttons as $each_button){
			if( !empty($each_button["shortcode"]) ){
				add_shortcode($each_button["shortcode"], [$this, $each_button["shortcode"]] );
			}
		} 
	}

	public function tinymce_buttons_body( )
	{
		if( ! isset($_GET['tinymce_buttons_'. $this->slug] ) ) return;
		header("Content-type: application/javascript;  charset=utf-8");
		?>
		// ************ these useful scripts got from: https://github.com/ttodua/useful-javascript/   **********
		// "<script>"  dont remove this line,,, because, now JAVSCRIPT highlighting started in text-editor
		<?php $random_name = "button_".rand(1,999999999).rand(1,999999999); ?>
		"use strict";

		(function ()
		{
			// Name the plugin anything we want
			tinymce.create( 'tinymce.plugins.<?php echo $random_name;?>',
			{
				init: function (ed, url)
				{

				<?php foreach ($this->tinymce_buttons as $each_button ) { ?>
					// The button name should be the same as used in PHP function of WP
					ed.addButton( '<?php echo $each_button["button_name"];?>',
					{
						// Title of button
						title: '<?php echo $each_button["shortcode"];?>',
						// icon url of button
						image: '<?php echo $each_button["icon"];?>', //url +
						// Onclick action onto button
						onclick: function ()
						{
							// Create shortcode string, with default values
							var val = '<?php echo $this->shortcode_example($each_button["shortcode"], $each_button["default_atts"]);?>';
							// Insert shortcode in text-editor
							ed.execCommand( 'mceInsertContent', false, val );

							//var newtex= prompt("Audio link", ""); if (null == newtex) {return;}
							//var gotted= tinyMCE.activeEditor.selection.getContent({format : 'raw'})
								//var newtex= gotted.replace(/<br \/>/g,'');
								//	newtex= newtex.replace(/\r\n/g,'');
								//	newtex= newtex.replace(/\n/g,'');
								//ed.execCommand('mceInsertContent', false, newtex);
						}
					});
				<?php } ?>


				},
				createControl: function (n, cm) {
					return null;
				}

			});

			// first parameter	- the same name as defined in PHP function of WP
			// second parameter	- the module name (as defined a bit above)
			tinymce.PluginManager.add( '<?php echo "button_handle_" . $this->slug;?>', tinymce.plugins.<?php echo $random_name;?> );

		})();
		//</script>
		<?php
		exit;
	}
	
	// show toggled (2-lined) tinymce editor 
	public function tinymceToggled(){
		add_filter( 'tiny_mce_before_init', function($array){ $array[ 'wordpress_adv_hidden' ]=false; return $array; } );
		add_filter( 'mce_buttons',	function ( $button_names ) { return array_diff( $button_names, ['wp_adv'] ); } ); 
	}
	public function tinymceAddMediaButton(){
		add_filter( 'mce_buttons',	function ( $button_names ) { $button_names[]='wp_add_media';  return $button_names; } );
	}
				
	public function tinymceRemoveButtons($buttons){
		//add_filter( 'mce_buttons',	function ( $button_names ) {  return array_diff( $button_names, $buttons ); } ); 
		//'strikethrough','hr','forecolor','pastetext','removeformat','charmap','outdent','indent','undo','redo','wp_help'
		//add_filter( 'mce_buttons_2',	function ( $button_names ) {  return array_diff( $button_names, $buttons ); } ); 
	}
			//if ($GLOBALS['current_screen']->post_type==$post_type) { 
    public function remove_media_button()
    {
		add_action('admin_head', function() { remove_action('media_buttons', 'media_buttons'); });
	}
	//other mods: https://developer.wordpress.org/reference/functions/wp_tinymce_inline_scripts/
	// =========================== TinyMce ================================ //
 


	public function compress_php_header($isWP=false)
	{
		ob_start('ob_gzhandler');	//similar as: ini_set('zlib.output_compression', '1');
		if ($isWP){
			add_action('wp', (function (){ if (!is_admin()) ob_start('ob_gzhandler'); } ) ,1);
			remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
		}
	}

	
	public function remove_admin_bar($from_backend_too=true)
	{
		add_action('init', function() use ($from_backend_too) {
			if ($this->is_administrator()) return;
			//remove admin bar from FRONTEND 
			add_filter('show_admin_bar', '__return_false');	//show_admin_bar(false);
			//remove admin bar from BACKEND
			if ($from_backend_too)
			{
				add_filter( 'admin_title', function(){ $GLOBALS['wp_query']->is_embed=true;  add_action('admin_xml_ns', function(){ $GLOBALS['wp_query']->is_embed=false; } ); } );
				remove_action( 'in_admin_header', 'wp_admin_bar_render', 0 );
			}
		});
	}
	

	//add_action( 'pre_get_posts', 'querymodify_2322',77); 
	public function querymodify_2322($query) { $q=$query;
		if( $q->is_main_query() && !is_admin() ) {
			if($q->is_home){
				$q->init();			
				$q->set('post_type',LNG);
				$q->set('category__not_in', 64);
				$q->set_query_vars('category__not_in',array(64)  );
			}
		}
		return $q;
	}

	// Register Custom Post 	
	public function register_post_type($name, $title='', $thumb='') {
		// https://codex.wordpress.org/Function_Reference/register_post_type 
		add_action('init', function() use ($name, $title)  {
			$title = !empty($title) ? $title : strtoupper($name);
			register_post_type( $name, [
				'label'	=> __( $title ),	'description' => __( $name.'s'),
				'labels'=> ['name' => $name, 'singular_name' => $name.' '.'page'],
				'supports'	=> ['title','editor', 'thumbnail', 'excerpt', 'page-attributes', 'post_tag', 'revisions','comments','post-formats'],
				'taxonomies'=> ['category', 'post_tag'],  
				'public'=> true,	'query_var'=> true,				'publicly_queryable'=>true,	'show_ui'=> true,	'show_in_menu'	=> true,
				'show_in_nav_menus'	=> true,	'show_in_admin_bar'	=> true,	'menu_position'	=> 18,
				'can_export' => true, 'hierarchical' => true, 'has_archive'=> true, 'menu_icon' => 'dashicons-editor-spellcheck', // https://developer.wordpress.org/resource/dashicons/#editor-spellcheck
				'exclude_from_search' => false,	'capability_type'=> 'page',
				'rewrite' => array('with_front'=>true,   ), 
			] );
		}); 
	}
	//add_action('init', 'my_custom_init');  function my_custom_init() { add_post_type_support( 'page', 'excerpt' ); }


	// increase filtering quick-menu-search results (this seems better than other a bit harder methods, like: https://goo.gl/BWMmDp )
	public function init__quicksearch($amount=30) { $this->navmenu_search_items=$amount; add_action( 'pre_get_posts', [$this, 'myFilter_quicksearch'], 10, 2 );  }
	public function myFilter_quicksearch( $q ) {
		// example of $q properties: https://goo.gl/SNeDwX
		if(isset($_POST['action']) && $_POST['action']=="menu-quick-search" && isset($_POST['menu-settings-column-nonce'])){	
			// other parameters for more refinement: https://goo.gl/m2NFCr
			if( is_a($q->query_vars['walker'], 'Walker_Nav_Menu_Checklist') ){
				$q->query_vars['posts_per_page'] = $this->navmenu_search_items;
			}
		}
		return $q;
	}


	public function init__postsperpage($amount=30) {  $this->posts_per_page=$amount;  add_action( 'pre_get_posts', [$this,'wpsites_query'], 15);  }
	public function wpsites_query( $query ) {
		if ( !is_admin() && $query->is_archive && $query->is_main_query() ) {
			$query->set( 'posts_per_page', $this->posts_per_page );
		}
	} 

	//  add_action( 'admin_init', 'allow_editor_increased_access');
	// https://codex.wordpress.org/Roles_and_Capabilities#edit_theme_options
	public function allow_editor_increased_access(){
		$role_object = get_role( 'editor' );
		if(empty($role_object )) return;
		$role_object->add_cap( 'edit_theme_options' );
		$role_object->add_cap( 'update_core' );
		$role_object->add_cap( 'update_themes' );
		$role_object->add_cap( 'switch_themes' );
		$role_object->add_cap( 'delete_themes' );
		$role_object->add_cap( 'delete_plugins' );
		$role_object->add_cap( 'update_plugins' );
		//$role_object->add_cap( 'create_users' );  // will access ADMIN!
		//$role_object->add_cap( 'edit_users' );  // will access ADMIN!
		//$role_object->add_cap( 'delete_users' );
		$role_object->add_cap( 'remove_users' );
		$role_object->add_cap( 'list_users' );
		$role_object->add_cap( 'edit_files' );
		$role_object->add_cap( 'edit_dashboard' );
		// CAREFULL !
		//$role_object->add_cap( 'manage_options' );
	}

 
	//add_filter('excerpt_more', 'excerpt_more_func');   public function excerpt_more_func($more) {	return ' <a class="read-more" href="'. get_permalink(get_the_ID()) . '"> (Continue Reading)</a>';}
	//add_filter('excerpt_length', 'excerpt_length_func');   public function excerpt_length_func() {    return 25;} 

	public function noindex_pagesss() {
		if ( !is_404() && !is_page() && !is_single() && !is_search() && !is_archive() && !is_admin() && !is_attachment() && !is_author() && !is_category() && !is_front_page() && !is_home() && !is_preview() && !is_tag())  { echo '<meta name="robots" content="noindex, nofollow"><!-- by MLSS -->'; }
	}

	// remove category base: pastebin_com/raw/YpV0wp27
	
	public function referrerIsSameDomain()
	{
		return $this->startsWith(wp_get_referer(), home_url());
	}
	
	//add_action( 'after_setup_theme', 'theme_supportss' );  
	public function add_theme_supports(){
		// https://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
		//remove_theme_support( 'custom-header' ); 
		add_theme_support('menus');			// Add support for:		menus
		add_theme_support('title-tag');   	// Add support for:		titles
		add_theme_support('editor-style');	// Editor Styles
		add_editor_style();
		add_theme_support( 'post-thumbnails');	// Enable Thumbnails for Feature Images 
		set_post_thumbnail_size( 200, 150 );
		add_image_size('my-small-thumbnail', 150, 150, true);
		add_image_size('my-medium-thumbnail', 650, 150, true);
		// Translation Ready
				//load_theme_textdomain( 'my', get_template_directory() . '/languages' );
		//add_theme_support('automatic-feed-links');	// Add default posts and comments RRS feeds links to the head.
		add_theme_support( 'html5', ['search-form'] );//Suppot HTML5 Search Form
		//load_theme_textdomain( 'my', get_template_directory() . '/languages' );
	}

	// add_filter('upload_mimes', 'custom_upload_mimes');
	public function custom_upload_mimes ( $existing_mimes=array() ) {
		// add your extension to the mimes array as below
		$existing_mimes['zip']	= 'application/zip';
		$existing_mimes['gz']	= 'application/x-gzip';
		$existing_mimes['txt']	= 'text/plain'; 
		
		if (!array_key_exists('zip', $existing_mimes)) $existing_mimes['zip'] = 'application/zip';  
		if (!array_key_exists('gz|gzip|zip', $existing_mimes)) $existing_mimes['gz|gzip|zip'] = 'application/x-zip'; 
		//	['gz|gzip'] => application/x-gzip
		//	[rar] => application/rar
		//	[7z] => application/x-7z-compressed
		return $existing_mimes;
	}

	
	public function inprogress_flag($flagname, $max_seconds=60){
		$flagname_final = "inprogress_$flagname";
		$start_time = $this->get_transient($flagname_final);
		if($start_time) {
			if (time()<$start_time+$max_seconds)
				return true;
			else{
				$this->inprogress_flag_reset($flagname);
				return false;
			}
		}
		else{
			$this->set_transient($flagname_final, time(), $max_seconds);
			register_shutdown_function(function() use($flagname){ $this->inprogress_flag_reset($flagname); });
			return false;
		}
	}

	public function inprogress_flag_reset($flagname){
		$flagname_final = "inprogress_$flagname";
		if( !empty($this->get_transient($flagname_final) ) ) {
			if( ! $this->set_transient($flagname_final,0,0) ) {
				if( ! $this->set_transient($flagname_final,0,0) ) {
					throw new \Exception("Cant update status: $flagname_final | ". $GLOBALS['wpdb']->last_error);
				}
			}
		}
	}


	/*
 
		//		wp_register_script( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '1.11.0' );
		//		wp_register_script( 'jquery-core', '/wp-includes/js/jquery/jquery.js', false, '1.11.0' ); 
				//force to load my JQUERY
				if(!$this->definedTRUE('DISABLE_MY_'.$each_UPPERCASE.'_FORCE')){ wp_deregister_script($name);	}
				$registered	= wp_script_is( $name, 'registered' );
				$enqueued	= wp_script_is( $name, 'enqueued' );
				if (!$registered)	{ 
					if(!empty($GLOBALS['odd']['scripts'][$name]['js'])) {
						wp_register_script($name, $GLOBALS['odd']['scripts'][$name]['js'], 	array(), $this->changeable_JS_CSS_version, false );	
					}
					if(!empty($GLOBALS['odd']['scripts'][$name]['css'])) {
						wp_register_style( $name, $GLOBALS['odd']['scripts'][$name]['css'],	array(), $this->changeable_JS_CSS_version, false );	
					}
				}
				if (!$enqueued)		{
					if(!empty($GLOBALS['odd']['scripts'][$name]['js'])) {
						wp_enqueue_script( $name );
					}
					if(!empty($GLOBALS['odd']['scripts'][$name]['css'])) {
						wp_enqueue_style	( $name );
					}
				} 
	*/


	public function register_stylescript($admin_or_wp, $type, $handle=false, $url=false, $dependant=null, $version=false, $target=false)
	{
		add_action( $admin_or_wp.'_enqueue_scripts',	function() use($type, $handle, $url, $dependant, $version, $target) {
			$this->enqueue($type, $handle, $url, $dependant, $version, $target);
		}, 100); 
	}

	public function enqueue($type, $handle=false, $url=false, $dependant=null, $version=false, $target=false)
	{
		//lets allow shorthanded start
		$localstart = 'assets';
		if( substr($url,0, strlen($localstart) ) == $localstart ) {
			$url = $this->moduleURL. $url;
		}
		if ( ! call_user_func("wp_".$type."_is",	$handle, "registered" ) ){
			call_user_func("wp_register_".$type,	$handle, $url,  $dependant,  $version, $target );   //,'jquery-migrate'
		}
		if ( ! call_user_func("wp_".$type."_is",	$handle, "enqueued" ) ){
			call_user_func("wp_enqueue_".$type,	$handle);
		}
	}

	public function deregister_jquery_scripts($admin_or_public=true, $jquery_too=false)
	{
		add_action( ($admin_or_public ? 'admin':'wp').'_enqueue_scripts', function () use($jquery_too) {
			foreach([
				"jquery-ui-widget", "jquery-ui-mouse", "jquery-ui-accordion", "jquery-ui-autocomplete", "jquery-ui-slider", "jquery-ui-tabs",  "jquery-ui-draggable", "jquery-ui-droppable", "jquery-ui-selectable", "jquery-ui-position", "jquery-ui-datepicker", "jquery-ui-resizable", "jquery-ui-dialog", "jquery-ui-button", ($jquery_too ?'jquery':'') 
			] as $script) wp_deregister_script($script); 
		});
	}



	//if used earlier than INIT 
	public function get_permalink_ADOPTED($post=false){
		global $wp_rewrite;	
		if (empty($wp_rewrite)){
			if(is_object($post))	 {$link=$post->guid; }
			elseif(is_numeric($post)){ $post_obj=get_post($post, OBJECT); $link=get_permalink($post_obj->ID); }
		}
		else{
			if(is_object($post))	{ $link=get_permalink($post->ID);}
			else					{ $link=get_permalink($post); }
		}
		return  $link;
	}

	//add_filter('the_content', 'empty_div_add');
	public function empty_div_add($content) {
		if (defined('IS_SINGLEE') && IS_SINGLEE){ $content = '<div class="bef_cont_div"></div><div class="cont_div">'.$content.'</div><div class="aft_cont_div"></div>'; } 
		return $content;
	}


	public function redirect_to_homefolder($siteSlug= "geo")
	{
		// redirect to /GEO
		$redirect_lang = 1;
		if ($redirect_lang)
			if (!is_admin())
				if( stripos($this->currentURL,"/$siteSlug/")===false && stripos($this->currentURL,'/wp-login')===false  && stripos($this->currentURL,'/wp-admin')===false   )
					$this->php_redirect( str_replace($this->domain, $this->domain."/$siteSlug/",  $this->currentURL));
	}

	//not_founded_images_redirections (when on FTP, the file is not found, then automatically, the site is loaded.. so, in this case, use our function.
	public function not_found_images_redirect() {
		if (in_array( $this->get_url_parts($this->currentURL,'extension'), ['png','jpg','jpeg','gif','bmp','svg']))		{  
			echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?><svg viewBox="0 85 80 120" xmlns="http://www.w3.org/2000/svg"><style></style><text x="0" y="100" class="small">Image error</text></svg>'; exit;
		}
	}

	public function OutputImageFile($file=''){ 
		if ( !in_array( $this->getExtension($file), ['jpg','jpeg','png','bmp','gif']) ) exit ('');
		header("Content-type: image/png");  die( $this->file_get_contents($file)  ); 
	}

	public function get_parent_slugs_path($post){
		$final_SLUGG = '';
		if (!empty($post->post_parent)){
			$parent_post= get_post($post->post_parent);
			while(!empty($parent_post)){
				$final_SLUGG =  $parent_post->post_name .'/'.$final_SLUGG; 
				if (!empty($parent_post->post_parent) ) { $parent_post = get_post( $parent_post->post_parent); } else{ break ;} 
			}
		}
		return $final_SLUGG;
	}
	
	// https://stackoverflow.com/questions/18401236/custom-category-tree-in-wordpress
	//foreach (get_terms($allTermSlugs, array('hide_empty'=>0, 'orderby'=>'id', 'parent'=>0)  ) as $category)  echo my_Categ_tree($category->taxonomy,$category->term_id);
	public function my_Categ_tree($TermName='', $termID=null, $separator='', $parent_shown=true ){
		$args = 'hierarchical=1&taxonomy='.$TermName.'&hide_empty=0&orderby=id&parent=';
				if ($parent_shown) {$term=get_term($termID , $TermName); $output=$separator.$term->name.'('.$term->term_id.')<br/>'; $parent_shown=false;}
		$separator .= '-';	
		$terms = get_terms($TermName, $args . $termID);
		if(count($terms)>0){
			foreach ($terms as $term) {
				//$selected = ($cat->term_id=="22") ? " selected": "";
				//$output .=  '<option value="'.$category->term_id.'" '.$selected .'>'.$separator.$category->cat_name.'</option>';
				$output .=  $separator.$term->name.'('.$term->term_id.')<br/>';
				$output .=  my_Categ_tree($TermName, $term->term_id, $separator, $parent_shown);
			}
		}
		return $output;
	}


	public function wp_mails_init()
	{
		add_filter( 'wp_mail_from',			function( $email ) { return 'contact@'.$_SERVER['HTTP_HOST']; } );
		add_filter( 'wp_mail_from_name',	function( $name ) { return 'WordPress Email System'; } );
		add_filter( 'wp_mail_content_type', function($cotnent_type=false){ return "text/html"; } ) ;
		// $headers = array('Content-Type: text/html; charset=UTF-8')
	}

	public function recountCategories($tax_name='category')
	{
		$terms_ids = get_terms( ['taxonomy' => $tax_name, 'fields' => 'ids','hide_empty' => false]);
		wp_update_term_count_now( $terms_ids, $tax_name);
	}

	public function random_val_for_site($name){
		$randoms = get_site_option('randoms_for_main_site', array());
		if(empty($randoms) || empty($randoms[$name])){
			$randoms[$name]= random_stringg(16);
			update_site_option('randoms_for_main_site', $randoms);
		}
		return $randoms[$name];
	}


 
	// https://pastebin_com/sPb1qvJ0
	public function delete_transients_by_prefix($myPrefix, $table_name, $column_name, $prefix=false){
		global $wpdb;
		$myPrefix 		= sanitize_key($myPrefix);
		$sql = $wpdb->prepare("delete from %s where %s like '%s' or %s like '%s'", $table_name, $column_name, '%_transient_$myPrefix%', $column_name, "%_transient_timeout_$myPrefix%" );
		return $wpdb->query($sql);
	}

	// add tinymce styles
	public function add_tinymce_styles($styles){ add_filter( 'tiny_mce_before_init', function($settings) use($styles) { return $this->wptrac_36636_editor_inline_style22($settings, $styles); } );  }
	public function wptrac_36636_editor_inline_style22( $settings, $styles) {$settings['content_style'] = (!empty($settings['content_style']) ? $settings['content_style'] : '') . (!empty($styles) ? addslashes($styles) : ''); return $settings;}
	//add_action( 'after_setup_theme', 'my_theme_add_editor_styles' );
	public function my_theme_add_editor_styles() {
		add_editor_style( PHP_customCALL_1.'tinymce_styles&ver='.$this->changeable_JS_CSS_version );
	}
	//if ( $act=='tinymce_styles'){ header('Content-Type: text/css');  echo '	.anons_of{background: gray;} html .mceContentBody { max-width:100%;}'; exit; }







	// NONCES
	public function defNonceAction($actionName=null){ return ($actionName!=null? $actionName :  "_nonceAction"."_".$this->slug); }
	public function defNonceSlug($slugName=null){ return ($slugName!=null? $slugName :  "_wpnonce"."_".$this->slug); }

	public function checkSubmission($action=null, $slug=null)
	{  
		$slug   = $this->defNonceSlug($slug);
		$action = $this->defNonceAction($action);
		$result = isset($_POST[$slug]) && check_admin_referer($action, $slug);
		return $result;
	}
	public function nonce($action=null, $slug=null, $echo=true)
	{
		$out = wp_nonce_field( $this->defNonceAction($action), $this->defNonceSlug($slug), true, false );
		if ($echo) echo $out; else return $out;
	}
	public function nonceSubmit($text=null, $action=null, $slug=null, $centeredFloat=false, $echo=true)
	{
		$x = $this->submit_button($text, $action, $slug, $centeredFloat);
		if ($echo) echo $x; else return $x;
	}
	public function submit_button($text=null, $action=null, $slug=null, $centeredFloat=false, $echo=false)
	{
		$out = '';
		$out .= $this->nonce( $this->defNonceAction($action), $this->defNonceSlug($slug), false );
		if ($centeredFloat) $out .= '<div class="centered-float submitbutton">';
		$out .= get_submit_button( $text, $type='button-primary', $name='', $wrap=true, $other_attributes= ['id'=>'mainsubmit-button'] );
		if ($centeredFloat) $out .=  '</div>';
		if ($echo) echo $out; else return $out;
	}
	// old
	public function NonceCheck($value, $action_name){ 
		if ( !isset($value) || !wp_verify_nonce($value, $action_name) ) { die("not allowed. error_5151, Refresh the page");}
	}	
	public function NonceCheckk($name='nonce_input_name', $action_name='blabla')  {
		return ( wp_verify_nonce($_POST[$name], $action_name)  ?  true : die("not allowed, refresh page!") );
	}
	public function NonceFieldd($name='nonce_input_name', $action_name='blabla')  { return '<input type="hidden" name="'.$name.'" value="'.wp_create_nonce($action_name).'" />';}





	#region ######### OPTIONS TABLES #########
	/* **** USAGE from plugin *****
		$optsArray = [
			'send_email_to_me'     => ['value'=>true, 'description'=> 'allow sending mail to me'],
			'interval_seconds'     => ['value'=>30],
			'title_of_address'     => ['value'=>'wzt street #2', 'type'=>'textarea'],
		];
		$this->helpers->options_table_full( ['class'=>$this, 'prefix'=>'my_diary', 'options'=>$optsArray, 'in_form'=>true] );
	*/

	public function options_table_full( $args ){
		try{
			$holder_class       = $args['class'];
			$prefix				= $args['prefix']; 
			$res = $this->options_default_parse( ['options'=>$args['options'], 'prefix'=>$prefix, 'nonce'=>$args['nonce'] ] );
			if (!empty($res)){
				$holder_class->opts[ $prefix ] = $res;
				$holder_class->update_opts();
			}
			echo $this->options_default_table( ['options'=>$args['options'], 'values'=>$holder_class->opts[$prefix], 'prefix'=>$prefix, 'nonce'=>$args['nonce'], 'echo'=>$this->array_value($args,'echo',  false), 'echo'=>$this->array_value($args,'in_form', true) ]  );
		}
		catch(\Exception $ex){
			$this->var_dump($ex);
		}
	}

	public function options_default_table($args){
		$options_array      = $args['options'];
		$currentvalues_array= $args['values'];
		$prefix				= $args['prefix'];
		$nonceKey			= $args['nonce'];
		$echo				= $this->array_value($args,'echo',  false);
		$form				= $this->array_value($args,'in_form',  true);

		$out ='';
		$out .= $form ? '<form class="mainForm" method="post" action="">' : '';
 
		$out .= '<style> .form-table .sub_group_array{margin:20px; display:flex; flex-wrap:wrap; } .form-table .arraydiv{display: flex; flex-direction: column; background: #e7e7e7d7; margin: 2px; padding: 4px;}  </style>';
		$out .= '<table class="form-table"><thead></thead><tbody>';
		foreach($options_array as $key=>$block){
			// if block is also array of groups
			if(!array_key_exists('value',$block)){

			}
			else{
				$line = $this->helper_optionsblock_TD($key,$block,$prefix,$currentvalues_array, $desc);
				// add line
				$out .= '<tr class="trline '.$key.'"><td>'. $desc .'</td><td>'.$line.'</td> </tr>';
			}
		}
		$out .= '</tbody></table>';

		$out .= $form ? $this->submit_button('SAVE SETTINGS', $nonceKey, 'nonceactxwe3').'</form>' : '';
		return $out;
	  }
	  
	  public function helper_optionsblock_TD($key, $block, $prefix,$currentvalues_array, &$desc){
		$desc = !empty( $this->array_value($block,'description') ) ? $this->array_value($block,'description') : $key;
		$type = !empty( $this->array_value($block,'type') )        ? $this->array_value($block,'type')        : $key;
		$line = '';
		$placeholder = $this->array_value($block,'placeholder', $this->array_value($block,'value','')); 
		if    ( is_bool($block['value']) )
			$line .= '<input name="'.$prefix.'['.$key.']" value="1" type="checkbox" '. $this->checked_if_value($currentvalues_array,$key).' />';
		elseif( is_double($block['value']) )
			$line .= '<input name="'.$prefix.'['.$key.']" value="'. $this->array_value($currentvalues_array,$key) .'" placeholder="'.$placeholder.'" style="width:70px;" />';
		elseif( is_integer($block['value']) )
			$line .= '<input name="'.$prefix.'['.$key.']" value="'. $this->array_value($currentvalues_array,$key) .'" placeholder="'.$placeholder.'" style="width:70px;" />';
		elseif( is_string($block['value']) ){
			if($type=='textarea') 
				$line .= '<textarea name="'.$prefix.'['.$key.']" placeholder="'.$placeholder.'" >'. $this->array_value($currentvalues_array,$key) .'</textarea>'; 
			else                  
				$line .= '<input name="'.$prefix.'['.$key.']" value="'. $this->array_value($currentvalues_array,$key) .'" placeholder="'.$placeholder.'" />'; 
		}
		elseif( is_array($block['value']) ) {
			$arr = $this->array_value($currentvalues_array,$key);
			$rand_id=$this->randomId('v');
			if(!array_key_exists('disable_autoadd',$block)) $arr[$rand_id] = '';
			$line .= '<div class="sub_group_array">';
			foreach($arr as $bKey=>$bValue ){ 
				$is_random = $bKey==$rand_id;
				if( isset($bValue) || $is_random ) {	
					$line .= '<div class="arraydiv">';
					$line .= "<span>{$bKey}</span>";
					if($type=='textarea') 
						$line .= '<textarea name="'.$prefix.'['.$key.']['.$bKey.']" placeholder="'. (!is_array($placeholder)? $placeholder : '').'">'. $bValue .'</textarea>'; 
					else                  
						$line .= '<input name="'.$prefix.'['.$key.']['.$bKey.']" value="'. $bValue .'" placeholder="'. (!is_array($placeholder)? $placeholder : '').'" />'; 	
					$line .= '</div>';
				}
			}
			$line .= '</div>';
		}
		else{  //default to textbox
			$line .= '<input name="'.$prefix.'['.$key.']" value="'. $this->array_value($currentvalues_array,$key) .'" placeholder="'.$block['value'].'" />'; 
		}
		return $line;
	  }

	  public function options_default_parse( $args ){
		$res = [];
		$options_array      = $args['options'];
		$prefix				= $args['prefix'];
		$nonceKey			= $args['nonce'];

		if( $this->checkSubmission( $nonceKey, 'nonceactxwe3' ) ) {  
			foreach($options_array as $key=>$block){
				if    ( is_bool   ($block['value']) )   $res[$key] = isset($_POST[$prefix][$key]); 
				elseif( is_double ($block['value']) )   $res[$key] = (double)($_POST[$prefix][$key]); 
				elseif( is_integer($block['value']) )   $res[$key] = (int)($_POST[$prefix][$key]); 
				elseif( is_string ($block['value']) )   $res[$key] = stripslashes(sanitize_textarea_field($_POST[$prefix][$key])); 
				elseif( is_array  ($block['value']) )	$res[$key] = $this->array_map_recursive( 'stripslashes', $this->array_map_recursive( 'sanitize_textarea_field', $_POST[$prefix][$key] ) );
			}
		}
		return $res;
	  }
	 
	  #endregion



		// backend
		private function backendActKey($actName){ return 'puvox_backend_call_'.$this->slug. "_".sanitize_key($actName); }
		public function add_action_backend_call($actName, $callback){
			add_action( $this->backendActKey($actName), $callback );
		}

		public function register_backend_call_actions(){
			add_action( 'wp_ajax_'.$this->plugin_slug_u.'_all',	[$this, 'ajax_backend_call'] );
			add_action( 'wp_ajax_nopriv_'.$this->plugin_slug_u.'_all',	[$this, 'ajax_backend_call_nopriv'] );
		}
		public function ajax_backend_call()
		{
			if(isset($_POST['action']) && $_POST['action']==$this->plugin_slug_u .'_all')
			{
				if( empty( $_POST["_wpnonce"] ) || !wp_verify_nonce( $_POST["_wpnonce"], "Puvox_BackendCallJS") ) 
				{
					exit( __('Incorrect nonce. Refresh page and try again.') );
				}

				if(isset($_POST['PRO_check_key'])){
					echo $this->license_status( sanitize_text_field($_POST['PRO_check_key']), "activate");
				}

				elseif(isset($_POST['PRO_save_results'])){

				}
				else{
					do_action($this->backendActKey($_POST['act']));
				}
				wp_die();
			}
			exit( __('Unknown-action') );
		}
		public function ajax_backend_call_nopriv(){
			
		}
		






	//if(!getBlogName()) { header("Location: http://www.mydomain.com/", true, 301); exit; }
	public function getBlogName(){
		global $current_blog; 
		$blog_path = explode('/',$current_blog->path); 
		if(isset($blog_path[2])) {
			return $blog_path[2];
		}
	}

	
	
	public function unzip_url($url, $where)
	{
		$zipLoc = $where.'/temp_'.rand(1,999999). "_". (basename($url)).'.zip';
		wp_remote_get
		(
			$url,
			[
				'timeout'  => 300,
				'stream'   => true,
				'filename' => $zipLoc
			]
		);
		$this->unzip($zipLoc, $where);
		@unlink($zipLoc);
	}

	public function unzip($path, $where)
	{ 
		$this->mkdir_recursive($where);
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		\WP_Filesystem();
		\unzip_file($path, $where);
		$this->usleep(300000);
	}
	
	public function unzip_in_dir($dir, $rewrite=true)
	{
		$this->temp_unziped_folders = [];
		foreach( array_filter(glob($dir.'/*.zip'), 'is_file')  as $each_zip)
		{
			$uniqueTag	= md5($each_zip);
			$each_dir	= substr($each_zip, 0, -4); //trim .zip
			if (empty($each_dir)) return; // ! must have, to avoid empty directory threat

			// remove if previous unpack was partial.
			if( is_dir($each_dir) && $rewrite )
			{
				if( !array_key_exists($uniqueTag, $this->temp_unziped_folders) || $this->temp_unziped_folders[$uniqueTag]==false )
				{
					$this->rmdir_recursive($each_dir);
					$this->usleep(500000);
					//$this->mkdir_recursive($pathh);
				}
			}
			elseif( !is_dir($each_dir) )
			{
				$this->temp_unziped_folders[$uniqueTag] = false;
				$this->unzip($each_zip, dirname($each_zip));
				$this->temp_unziped_folders[$uniqueTag] = true;
			}
		}
	}


	public function is_plugin_active($name)  //i.e. woocommerce/woocommerce.php
	{
		return in_array($name, get_option('active_plugins') );
	}
	public function woocommerce_products_to_array($products)
	{
		$new = [];
		foreach($products as $p) $new[] =$p->get_data(); 
		return $new;
	}
	
	
	//disable emojis
	public function disable_emojicons()
	{
		add_action( 'init', function () {
		  // all actions related to emojis
		  remove_action( 'admin_print_styles', 'print_emoji_styles' );
		  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		  remove_action( 'wp_print_styles', 'print_emoji_styles' );
		  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		} );

		//to remove emojis from TinyMCE
		add_filter( 'tiny_mce_plugins', function ( $plugins ) {
		  if ( is_array( $plugins ) ) {  return array_diff( $plugins, array( 'wpemoji' ) );} 
		  else { return array(); }
		} );
	}

	//add_action( 'admin_init', 'theme_options_init' );
	public function theme_options_init(){	 
		// https://codex.wordpress.org/Function_Reference/add_settings_field#Examples
		add_settings_field( 'myprefix_setting-id', 'This is the setting title', 'myprefix_setting_callback_function', 'general', 'myprefix_settings-section-name', array( 'label_for' => 'myprefix_setting-id' ) );
		
		// https://codex.wordpress.org/Function_Reference/add_settings_section#Notes
		add_settings_section('eg_setting_section',	'Example settings in reading',		'funcXXXX',		'reading');
		function funcXXXX( $arg ) {
			// echo section intro text here
			echo '<p>id: ' . $arg['id'] . '</p>';             // id: eg_setting_section
			echo '<p>title: ' . $arg['title'] . '</p>';       // title: Example settings section in reading
			echo '<p>callback: ' . $arg['callback'] . '</p>'; // callback: eg_setting_section_callback_function
		}
		//register_setting( 'bbbbbla', 'sample_theme_options');
	}
	

	public function shortcode_handler_OLD($atts, $content=false){
		$d=debug_backtrace()[0];
		if(!empty($d['args']))
		{
			if(!empty($d['args'][2]))
			{
				$name = $d['args'][2];
				$args = $this->shortcode_atts($name, $atts);
				return call_user_func( [$this, $name], $args, $content);
			}
		}
	}

	//Advanced custom fields alternative 
	public function acf_getfield_detect(){
		add_action('plugins_loaded', function(){
			if (!function_exists('get_field')){
				function get_field(){
					return 'Advanced Custom Fields plugin is not installed';
				}
			}
		}, 1);
	}
	
	
    public function jqueryRestore() { 
		add_action('admin_head',function(){ echo '<script>$=jQuery;</script>'; });
		add_action('wp_head',	function(){ echo '<script>$=jQuery;</script>'; });
	}

	public function disable_update_email_notifications(){
		add_filter( 'auto_core_update_send_email',   function ( $send, $type, $core_update, $result ) { return ( ! empty( $type ) && $type == 'success'  ? false : true); }, 10, 4 );
		add_filter( 'auto_plugin_update_send_email', '__return_false' );
		add_filter( 'auto_theme_update_send_email',  '__return_false' );
	}

	// ================ flash rules ================= // 
	public function flush_rules_double(){ add_action('wp', [$this, 'MyFlush__rewrite'] ); }
	public function MyFlush__rewrite($RedirectFlushToo=false){	
		$GLOBALS['wp_rewrite']->flush_rules(); 
		flush_rewrite_rules();
		//DUE TO WORDPRESS BUG ( https://core.trac.wordpress.org/ticket/32023 ) , i use this: (//USE ECHO ONLY! because code maybe executed before other PHP functions.. so, we shouldnt stop&redirect, but  we should redirect from already executed PHP output )
		if($RedirectFlushToo) {echo '<form name="mlss_frForm" method="POST" action="" style="display:none;"> <input type="text" name="mlss_FRRULES_AGAIN" value="ok" /> <input type="submit"> </form> <script type="text/javascript"> document.forms["mlss_frForm"].submit(); </script>';}
	}
	public function flush_rules($redirect=false){
		flush_rewrite_rules();
		if($redirect) {
			if ($redirect=="js"){ $this->js_redirect(); }   else { $this->php_redirect(); }
		}
	}
	

	
	// ==================== shortcodes =======================
	public function shortcode_atts($shortcode, $predefined_atts, $passed_atts){
		$new_arr=[]; 
		foreach($predefined_atts as $x){
			$new_arr[ $x[0] ] =  $this->stringToValue($x[1]) ;
		}
		if (!empty($passed_atts)) {
			$filtered_atts=[];
			foreach($passed_atts as $key=>$value){
				$filtered_atts[$key] =  $this->stringToValue($value) ;
			}
			$new_arr = array_merge($new_arr, $filtered_atts);
		}
		$new_arr = $this->sanitize_shortcode_empty_defaults_pre($new_arr);
		$new_atts = shortcode_atts($new_arr, [] );
		return $new_atts;
	}
	
	public function sanitize_shortcode_empty_defaults_pre($atts){
		$ar= ["...","___", 0];
		foreach($ar as $e) { if (array_key_exists($e, $atts)) unset($atts[$e]); }
		return $atts;
	}
	
	public function sanitize_shortcode_empty_defaults($attsArray){
		$new_arr = [];
		foreach($attsArray as $eachAttArr)
		{ 
			if ( empty($eachAttArr[0]) || in_array($eachAttArr[0], ["...","___"] )  ) continue;
			$new_arr[] = $eachAttArr;
		}
		return $new_arr;
	}
	public function shortcode_alternative_message($name, $params_name=false)
	{
		?>
		<div class="alertnative_to_shortcodes">
			<h2><?php _e('(Alternatives to shortcode)'); ?></h2>
			<?php _e('Note, you can always use programatical approach using:'); ?> 
			<br/> <code>&lt;?php echo do_shortcode('[.....]'); ?&gt;</code>
			<br/> or 
			<br/> <code>&lt;?php if (function_exists('<?php echo $name;?>'))		{ echo <?php echo $name;?>(["arg1"=>"value1", ...]); } ?&gt;</code>
		</div>
		<?php
	}
	
	public function shortcode_example_string($array, $strip_tags=false, $htmlentities=false, $ended=false){
		
		$out = '<code>';
		$out .= '['. $array['name'].'<span class="shortcode_atts">';  $atts = $this->sanitize_shortcode_empty_defaults($array['atts']); foreach( $atts  as $key=>$value){ $out .= " ".$value[0].'="'. htmlentities($this->truefalse_to_string($value[1])).'"';} $out .='</span>]'; 
		$out = ( $strip_tags	? strip_tags($out) : $out);
		$out = ( $htmlentities	? htmlentities($out) : $out);		
		if( $ended ) 
			$out .= "...[/".$array['name']."]";
		$out .= '</code>';
		return $out;
	}

	public function shortcode_example($shortcode, $array, $ended=false){
		$out="[$shortcode ";   foreach($array as $key=>$value){   $out .= $key.'="'.$this->valueToString($value) .'" ';  }   $out = trim($out). "]";
		if( $ended ) 
			$out .= "...[/$shortcode]";
		return $out;
	}

	public function shortcodes_table($name, $array)
	{ 
		/*======= example ========
		
		$this->shortcodes_table( "breadcrumbs", [
			[ 'id', 				'',			__('Post ID (you can ignore that parameter if you want to get for current post)', 'breadcrumbs-shortcode') ],
			[ 'delimiter',			'hello', 	__('Your desired delimiter', 'breadcrumbs-shortcode') ],
		] );
		*/
	?>
	<div class="shortcodes_block">
		<h3><?php echo $array['description'];?></h3>
		<table class="form-table shortcodes">
		<tr>
			<td><?php _e('Example:');?></td>
			<td>
				<?php echo $this->shortcode_example_string($array, false,false, array_key_exists('ended', $array) );?>
			</td>
		</tr>
		<tr>
			<td><?php _e('Parameters:');?></td>
			<td>
				<table>
				<tr class="shortcode_tr_descr">
					<td><?php _e('name');?></td><td><?php _e('default value');?></td><td><?php _e('description');?></td>
				<tr>
				<?php 
				foreach($array['atts'] as $key=>$value)
				{ ?>
				<tr>
					<td><code><?php echo htmlentities($value[0]);?></code></td><td><code><?php echo htmlentities($this->truefalse_to_string($value[1]));?></code></td><td><?php echo $value[2];?></td>
				</tr>
				<?php 
				}
				?>
				</table>
			</td>
		</tr>
		</table>
	</div>
		<?php
	}
	
	public function extendShortcodes(){
		add_shortcode ('date_day', function(){ return date_i18n ('l'); } );
		add_shortcode ('date_month', function(){ return date_i18n ('F'); } );
		add_shortcode ('date_year', function(){ return date_i18n ('Y'); } );
		add_shortcode ('date_ymd', function(){ return date_i18n ('y-m-d'); } );

		add_shortcode('image', function ($atts){ 	$GLOBALS['CategImgggg'] = $atts['url'];
			return '<div class="ImgShortcodeHolder"><img src="'.$atts['url'].'" alt="'.(!empty($atts['title']) ? $atts['title'] : basename($atts['url']) ).'" class="ShortcImageee" /></div>';
		}); 
		add_shortcode('link', function ($atts){
			return '<a href="'.basename($atts['url']).'" class="ShortcLinkk" target="_blank" />'.(!empty($atts['title']) ? $atts['title'] : basename($atts['url']) ).'</a>';
		} ); 

		add_shortcode('iframe', function ($atts){ 
			return '<div class="IframeHolder ifr_'.(!empty($atts['class']) ? $atts['class'] : 'defclass' ).'"><iframe src="'.$atts['url'].'"></iframe></div>';
		} ); 
		add_shortcode('@', function ($atts){ 
			return '&#64;';
		} );
		
		add_shortcode('script', function ($atts, $content=false){
			$cont= urldecode(  $content ? $content : $atts['content'] ); 
			return '<span class="cont_script '.(strpos($cont,'<iframe ')!== false  ?  'contains_frame':'') .'">'.$cont.'</span>';
		} ); 

		add_shortcode('list_subpages', function ($atts){ $out = ''; 
			if (IS_SINGULARR){
				$id= $GLOBALS['post']->ID;
				$args = array(
					'authors'=>'',  'child_of'=>$id,   'date_format'=>get_option('date_format'), 'depth'=> 0, 'echo'=>0,'exclude'=>'','include'=>'',
					'link_after'=>'',   'link_before'=>'',  'post_type'=>'page',  'post_status'=>'publish',  'show_date'=>'',  
					'sort_column'=> 'post_date', //'menu_order, post_title',
					'sort_order'=> '',  'title_li'=> __(''),   //'walker'       => new Walker_Page
				);
				$out = wp_list_pages( $args );
			}
			return '<div class="my_subpagelistt">'.$out.'</div>';
		} ); 


		add_shortcode( 'Youtube', function ($atts){
			$idd = get_youtube_id_from_contents($atts['url']);
			return '<div style="clear:both;"></div><div class="ytframe_parent"><iframe class="ytb_framee"  src="https://www.youtube.com/embed/'.$idd.'?rel=0" frameborder="0" allowfullscreen></iframe><div style="clear:both;"></div></div>';
		} );

		add_shortcode('video', function ($atts){ 
			$url	= $atts['url'];
			$player = !empty($atts['player']) ? $atts['player'] : 1;
			
			if ($player==1) { $out = 
				'<style type="text/css">body .video-js .vjs-tech {position:relative;} body #my-video{width: 80%; margin: 0 0 0 10%;} body .video-js .vjs-big-play-button{left: 45%; top:45%;}</style> 
				<video id="my-video" class="video-js" controls preload="auto" width="640" height="264"
				poster="" data-setup="{}">
				<source src="'.$url.'" type="video/mp4">
				<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
				</video>';
			}
			elseif ($player==2) { $out = '<video width="'. (!empty($atts['width']) ? $atts['width'] : 480) . '" height="'. (!empty($atts['width']) ? $atts['width'] : 320) . '" controls="controls" preload="auto" poster="#"> <source src="'.$url.'" type="video/mp4" /> </video>';}
			else {$out='';}
			
			return '<div class="VidShortcodeHolder">'.$out.'</div>';
		} ); 
		
		
		// i.e. [list type="categories"	id="32" depth=0 exclude="4,28"] 
		// i.e. [list type="pages"		id="32" depth=0 exclude="4,28"]    (or id="this")
		// i.e. [list type="menu" id="32"]
		add_shortcode( 'list',  function ($atts){
			$TYPEE	= !empty($atts['type'])	? $atts['type']	: '';  if(empty($TYPEE)) { return 'error2229.  please, set "type" parameter' ;  }
			$args	= $atts;

			if ( 'pages' == $TYPEE){
				// https://codex.wordpress.org/Function_Reference/wp_list_pages   //authors,child_of,date_format,depth,echo,exclude,include,link_after,link_before,post_type,post_status,show_date,sort_column,sort_order,title_li,
						if (empty($args['sort_column'])){$args['sort_column']= 'post_date';}
						//when ESSENTIAL parameters are not set
						if (empty($args['child_of']))	{ return 'error494__set child_of parameter for listed ' .$TYPEE;}
							elseif ($args['child_of']=='this') { $args['child_of']= $GLOBALS['post']->ID;}
						if (empty($args['depth']) )		{ $args['depth']= 1;}
						if (empty($args['echo']) )		{ $args['echo']	= 0;}
						if (empty($args['title_li']) )	{ $args['title_li']	= "";}
						if (empty($args['post_type']))	{$args['post_type']=$GLOBALS['post']->post_type;} 
				$X= wp_list_pages($args);
			}
			
			elseif ( 'categories' == $TYPEE){
				// https://codex.wordpress.org/Function_Reference/wp_list_categories //show_option_all,orderby,order,style,show_count,hide_empty,use_desc_for_title,child_of,feed,feed_type,feed_image,exclude,exclude_tree,include,hierarchical,show_option_none,number,echo,current_category,pad_counts,taxonomy
				
						//when ESSENTIAL parameters are not set
						if (empty($args['child_of']))		{ return 'error494__set child_of parameter for listed ' .$TYPEE;}
						if (empty($args['depth']) )			{ $args['depth']= 0;}
						if (empty($args['echo']) )			{ $args['echo']	= 0;}
						if (empty($args['hide_empty']) )	{ $args['hide_empty']= 0;}
						if (empty($args['title_li']) )		{ $args['title_li']	= "";}
						
					//this doesnt work when used inside LOOP
						//$X =  get_categories('echo=1&child_of=30') );
				$X = wp_list_categories($args);
			}
			elseif ( 'posts' == $TYPEE){
				// https://codex.wordpress.org/Function_Reference/wp_list_categories //show_option_all,orderby,order,style,show_count,hide_empty,use_desc_for_title,child_of,feed,feed_type,feed_image,exclude,exclude_tree,include,hierarchical,show_option_none,number,echo,current_category,pad_counts,taxonomy
				
						//when ESSENTIAL parameters are not set
						if (empty($args['child_of']))		{ return 'error494__set child_of parameter for listed ' .$TYPEE;}
						if (empty($args['depth']) )			{ $args['depth']= 0;}
						if (empty($args['echo']) )			{ $args['echo']	= 0;}
						if (empty($args['posts_per_page']) ){ $args['posts_per_page']	= -1;}
						if (empty($args['hide_empty']) )	{ $args['hide_empty']	= 0;}
						if (empty($args['post_type']) )		{ $args['post_type']	= get_post_types();}
						if (empty($args['category']) )		{ $args['category']	= $args['child_of'];}
						
				$out = '';
				$array =  get_posts($args); 
				foreach ($array as $key=> $value) {
					$out .= '<li class="manual_posts"><a href="'.get_permalink($value->ID).'">'.$value->post_title.'</a></li>';
				}

					//this doesnt work when used inside LOOP
						//$X =  get_categories('echo=1&child_of=30') );
						//$X = wp_list_categories($args);
				$X =$out;
			}
			elseif ( 'menu' == $TYPEE){
				// https://codex.wordpress.org/Function_Reference/wp_nav_menu  //theme_location,menu,container,container_class,container_id,menu_class,menu_id,echo,fallback_cb,before,before,after,link_before,link_after,items_wrap,depth,
					
						//when ESSENTIAL parameters are not set
						if (empty($args['menu']))	{ return 'error494__set "menu" parameter for listed ' .$TYPEE;}
				$X= wp_nav_menu($args);
					//https://codex.wordpress.org/Function_Reference/wp_nav_menu
					//https://codex.wordpress.org/Function_Reference/wp_get_nav_menu_items
					$sample_arr= array(
						'theme_location'  => '',
						'menu'            => '_main_menu',
						'container'       => 'div',			'container_class' => 'sideMyBox',			'container_id'    => 'my_SideTreeee',
						'menu_class'      => 'menu',		'menu_id'         => '',
						'echo'            => 0,				'fallback_cb'     => 'wp_page_menu',
							'before'          => '',		'after'           => '',
							'link_before'     => '',		'link_after'      => '',
							'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'depth'           => 0,
						'walker'          => ''
					);
			}
			return '<div class="listed_shortcode listed_'.$TYPEE.'"><ul>'.$X.'</ul></div>';
		} );

	} 
	
	// ==================== END | Shortcodes =======================
	
	
	
	
	public function init__cookieexpiration(){   add_filter('auth_cookie_expiration', [$this,'my_auth_cookie_expiration'], 99, 3);   }
	public function my_auth_cookie_expiration($seconds, $user_id, $remember){
		$expiration = $remember ? $this->auth_expiration_hours*60*60 : 2*24*60*60;

		// https://en.wikipedia.org/wiki/Year_2038_problem
		if ( PHP_INT_MAX - time() < $expiration ) {
			//Fix to a little bit earlier!
			$expiration =  PHP_INT_MAX - time() - 5;
		}
		return $expiration;
	}

	//breadcrumbs: pastebin_com/CzNyaEKE
	public function addTitleFieldToCat(){
		add_action ( 'edit_category_form_fields', function(){
			$cat_title = get_term_meta( (int) $_POST['tag_ID'], '_pagetitle', true);
			?> 
			<tr class="form-field">
				<th scope="row" valign="top"><label for="cat_page_title"><?php _e('Category Page Title'); ?></label></th>
				<td>
				<input type="text" name="cat_title" id="cat_title" value="<?php echo $cat_title ?>"><br />
					<span class="description"><?php _e('Title for the Category '); ?></span>
				</td>
			</tr>
			<?php
		});
		add_action ( 'edited_category', function() {
			if ( isset( $_POST['cat_title'] ) ) {
				update_term_meta( (int) $_POST['tag_ID'], '_pagetitle', sanitize_text_field($_POST['cat_title']) );
			}
		} );
	}


	public function get_template_filename($post_id=false){
		if(is_page()){
			$name=	get_post_meta( $post_id ?: $GLOBALS['post']->ID, '_wp_page_template', true);   // page-templates/my_homepage_1.php
			return basename($name);
		}
		return false;
	}

	private function wp_htaccess_plugin_flag($start_or_end){ $nm=$start_or_end? "START":"END"; return ($start_or_end? self::newline(2) :'')."##### Plugin $nm ::: {$this->slug} #####".($start_or_end? '':self::newline(2));}
	// i.e.   
	public function add_into_htaccess($code)
	{
		if( is_admin() )
		{
			$htaccess_path = ABSPATH . '/.htaccess';
			
			if(file_exists($htaccess_path))
			{
				$divisor="\r\n";
				$curr_content= $this->file_get_contents($htaccess_path);
				$start_part = $this->wp_htaccess_plugin_flag(true);
				$end_part   = $this->wp_htaccess_plugin_flag(false);
				if( stripos($curr_content, $start_part) === false)	//if it doesnt contain
				{
					$final_content=$curr_content.$divisor.$start_part.$divisor.$code.$divisor.$end_part;
				}
				else{
					// as preg_quote doesnt work, lets make a temp workaround
					$rand_START = "START_".rand(33333,888888);
					$rand_END   = "END_".rand(33333,888888);
					$curr_content= str_replace($start_part,$rand_START, $curr_content);
					$curr_content= str_replace($end_part,  $rand_END,   $curr_content);
					$final_content=preg_replace( "/$rand_START(.*?)$rand_END/si",  $start_part.$divisor.$code.$divisor.$end_part, $curr_content);
				}
				file_put_contents( $htaccess_path, $final_content );
			} 
		}
	}

	// disable georgian russian slugs: https://pastebin_com/UmvhEmuz
 
	//execute explicitly when testing
	//add_action('save_post',  function () { var_dump($_POST); exit; }, 99, 11);  

	public function debug_actions()   { add_action( 'wp_footer', function (){ var_dump( $GLOBALS['wp_filter']); } );   }
	public function calledScript()	  { return $_SERVER["SCRIPT_FILENAME"];}
	public function is_subscriber()	  { return $this->is_helper_('read'); }
	public function is_contributor()  { return $this->is_helper_('edit_posts'); }
	public function is_author()		  { return $this->is_helper_('upload_files'); }
	public function is_editor()		  { return $this->is_helper_('edit_others_posts'); }
	public function is_administrator(){ return $this->is_helper_('install_plugins'); }
	private function is_helper_($what){ return (function_exists('current_user_can') || require_once(ABSPATH.'wp-includes/pluggable.php')) && current_user_can($what); }

	public function user_id(){return (function_exists('current_user_can') || require_once(ABSPATH.'wp-includes/pluggable.php')) ? get_current_user_id() : -1; }
	public function initIfEditor($func, $initPriority=1){
		add_action('init', function() use ($func) { 
			if ($this->is_editor()) 
				call_user_func($func);
		}, $initPriority);
	}

	public function get_user_role( $user_id = 0 ) {
		$user = ( $user_id ) ? get_userdata( $user_id ) : wp_get_current_user();
		return current( $user->roles );
	}


	// ##############
	public function get_transient($transientName, $default=''){
		if( ($value = get_transient($transientName))===false ) { 
			$value = $default;
		}
		return $value;
	}
	public function set_transient($transientName, $value, $seconds=8640000){
		return set_transient($transientName, $value, $seconds);
	}
	public function append_transient_array($transientName, $key=null, $valueToAddInArray=null, $seconds=8640000, $autoreset_if_array_above=999){
		$current_arr= $this->get_transient($transientName, []);
		if( count($current_arr) > $autoreset_if_array_above ) 
		$current_arr = $this->array_part( $current_arr, $autoreset_if_array_above, 'end');
		if ($key) $current_arr[$key] = $valueToAddInArray;
		else $current_arr[] = $valueToAddInArray;
		return $this->set_transient($transientName,$current_arr, $seconds);
	}

	public function timer_remains( $uniqueActionId, $cache_seconds=9999999999, $reset=false ){
		return $this->check_timer_remains($uniqueActionId, $cache_seconds, $reset );
	}
	public function check_timer_remains($uniqueActionId, $cache_seconds=9999999999, $reset=false ){
		$remains = 0;
		if ( $cache_seconds>0 )
		{
			$transientName = $this->slug ."_px_timer_flag_".$uniqueActionId;
			$last_time = $this->get_transient($transientName,0);
			if( $last_time > 0  ) { 
				$remains = ($last_time + $cache_seconds) - time();
				if ($reset)
					$this->set_transient($transientName, time(), $cache_seconds);
			}
			else{
				$this->set_transient($transientName, time(), $cache_seconds);
			}
		}
		return $remains;
	}

	public function check_timer_remains_function($uniqueActionId, $callback, $cache_seconds=9999999999){
		$remains = 0;
		if ( $cache_seconds>0 )
		{
			$transientName = $this->slug ."_px_timer_flag_".$uniqueActionId;
			$last_time = $this->get_transient($transientName,0);
			if( $last_time > 0  ) { 
				$remains = ($last_time + $cache_seconds) - time();
			}
			else{
				call_user_func($callback);
				$this->set_transient($transientName, time(), $cache_seconds);
			}
		}
		return $remains;
	}


	//delete all transients:
	public function delete_all_transients(){
		global $wpdb; 
		$wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE ('_transient_%');");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE ('_site_transient_%');");
	}
	
	public function option_exists($name, $site_wide=false){
		global $wpdb; 
		$sql = $wpdb->prepare("SELECT * FROM %s WHERE option_name ='%s' LIMIT 1", ($site_wide ? $wpdb->base_prefix : $wpdb->prefix).'options', $name);
		return $wpdb->query($sql);
	}
	
	public function transient_exists($name, $site_wide=false){
		$your_transient = 'your_transient';
		$data_timeout = get_option('_transient_timeout_' . $your_transient);
		if ($data_timeout < time())
			echo 'it is expired.';
		else
			echo 'there is a transient';
	}
	


	// ################################

	public function post_is_in_descendant_category($cat, $postttId) {
		$descendants = array_merge( array($cat,''),     get_term_children((int) $cat, 'category')   ); 
		return (in_category($descendants, $postttId))  ? true : false;
	}

	//if post is ansector of category  //is_category(4)		
	public function post_or_cat_is_in_ansector($upper_category_id){
		$truee_fals =false;	global $post;
		//for categories
		if (is_archive())	{ $cur_cat_id = get_query_var('cat');
			if ($cur_cat_id == $upper_category_id || cat_is_ancestor_of($upper_category_id, $cur_cat_id)) {return true;} 
		}
		else	{
			if (in_category( $upper_category_id, $post->ID )) { return true;} 
		}
		//$curr_post = get_post($post->ID); $truee_fals=cat_is_ancestor_of($upper_category_id, $curr_post->$post_category) ? true : $truee_fals;
		
	}


	public function output_js_categories_ids()
	{
		if( ! ($out = get_transient('termids_for_js'))) {
			$terms= get_terms();
			foreach($terms as $term){
				$cats[$term->term_id] = urldecode($term->slug);
			}
			$out = json_encode($cats, JSON_UNESCAPED_UNICODE);
			set_transient('termids_for_js', $out , 60*60);
		}
		echo "<script>cat_term_ids = $out;</script>";	
	}			
		
			
	public function get_metas_by_metakv($key, $value=null, $what=false) {
		global $wpdb;
		$results =  $wpdb->get_results( 
			$wpdb->prepare( "SELECT %s FROM ".$wpdb->postmeta." WHERE meta_key=%s %s", ($what ?: "*"),  $key,  ($value ? " AND meta_value=$value" : "") ) 
		);

		if (!empty($results)) {
			if ($what){
				$array= array();
				foreach($results as $index => $result) {  $array[$index] = $result->{$what};  }
				return $array;	
			}	
			return $results;
		}
		return false;
	}
		
 
	
	// metaboxes, meta-box, media-uploaders:  https://pastebin_com/ePszrRWb
	
	public function error_mail($subject, $text){
		return wp_mail(get_option('admin_email'),  $subject,  $text );
	}

	public function myframe_center($content){
		if(is_singular() && stripos($content,'<iframe') !==false){
			$content= preg_replace('/\<iframe (.*?)\>/si','<div class="frame_parentt" style="text-align:center;">$0</div>', $content);
		}
		return $content;
	}

	public function change_output()
	{
		add_action('wp_loaded', function() { ob_start( function ($buffer) {
			// modify buffer here, and then return the updated code
			$buffer = str_replace('MERCEDES','FERRARIIII',$buffer);
			return $buffer;
		}); } ); 
		add_action('shutdown',  function() { ob_end_flush(); } );     
	}



    
	// name, columns (included), excluded
	public function output_table_from_db_table($opts)
	{
		$opts['excluded'] = $this->array_value($opts,'excluded',[]);
		$opts['reverse']  = $this->array_value($opts,'reverse',true);
		$opts['datetime_key']  = $this->array_value($opts,'datetime_key', 'time');


		$results = $this->db_get_results( $opts['name'], '*');
		if ($opts['reverse'])
			$results = array_reverse($results); //reverse chronologically
		$out = '';
		$out .= '
		<style>
		.typicalTable {border: 1px solid black;}
		.typicalTable td {border: 1px solid black;}
		</style>
		
		';

		$out .= 
		'<table class="typicalTable"><thead>';
			if (!empty($results))
			{
				$headrows = array_keys((array)$results[0]);
				$out .= '<tr>';
				foreach($headrows as $key){
					if (!in_array($key, $opts['excluded'])) 
						$out .= "<th>$key</th>";
				}
				$out .= '</tr>';
			}
			$out .= 
		'</thead><tbody>';

			foreach($results as $eachBlock)
			{
				$out .= '<tr>';
				foreach($eachBlock as $key=>$value){
					if (!in_array($key, $opts['excluded'])) {
						if ($key===$opts['datetime_key']){
							$value = date('Y-m-d H:i:s', $value);
						}
						$out .= "<td>$value</td>";
					}
				}
				$out .= '</tr>';
			}

			$out .= 
		'</tbody></table>';
		return $out;
	}











	#region     DATABASE FUNCTIONS
	
	// $this->create_table_my( 'myTable1', [ '`gmdate` datetime', '`function_args` longtext NOT NULL' ] );
	public function create_table_my($table_name, $array, $auto_increment_ID=true){
		global $wpdb;
		$sql  ="CREATE TABLE IF NOT EXISTS `%s` ( ";
		//	`meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		//	................................. NULL DEFAULT '0',
		//	`meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		//  varchar(255) | longtext | 
		//	PRIMARY KEY (`meta_id`),
		//	KEY `post_id` (`post_id`),
		//	KEY `meta_key` (`meta_key`(191)) 
		//)
		if ($auto_increment_ID===true) 
			$sql .="`ID` bigint NOT NULL AUTO_INCREMENT,";
		foreach( $array as $key=>$val){
			$sql .= $val . ',' ;//' NOT NULL,';
		}
		if ($auto_increment_ID===true)
			$sql .= "PRIMARY KEY (`ID`), UNIQUE KEY `ID` (`ID`) ";
		elseif (is_string($auto_increment_ID))
			$sql .= "PRIMARY KEY (`$auto_increment_ID`), UNIQUE KEY `$auto_increment_ID` (`$auto_increment_ID`) ";
		else
			$sql = $this->charsWithoutStartEnd($sql,0,1);
		$sql .=") ";
		if ($auto_increment_ID===true)
			$sql .=" AUTO_INCREMENT=1 ";
		$sql .=" %s;";
			// text text NOT NULL, name tinytext NOT NULL, time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,  id mediumint(9) NOT NULL AUTO_INCREMENT,
		$charset = $wpdb->get_charset_collate();
		//$charset = 'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci';
		$sql = $wpdb->prepare( $sql, $table_name, $charset );
		$sql = $this->unquote($sql);
		$x= $wpdb->query($sql );
		//require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		//dbDelta( $sql );
		return $x;
	}


	public function update_or_insert($tablename, $NewArray, $WhereArray=[]){   	
		global $wpdb; 
		// if string (i.e. key), then grab it from $NewArray[key]
		if (is_string($WhereArray)) $WhereArray=[$WhereArray=>$NewArray[$WhereArray]];
		elseif (!$this->array_is_associative($WhereArray) ) { foreach($WhereArray as $key) $WhereArray[$key]=$NewArray[$key]; } 
 
		//check if already exist
		if(!empty($WhereArray)){
			$result_Update = $wpdb->update($tablename, $NewArray, $WhereArray );
			if ($result_Update) {
				return true;
			}
			// ### now check, if it exists, and it was not updated, then return false ### 
			$whereStr=''; $i=1; foreach ($WhereArray as $key=>$value){ $whereStr .= $wpdb->prepare( sanitize_key($key) . " = '%s'", $value); if ($i != count($WhereArray)) { $whereStr .=' AND '; $i++;}  }
			$sql = ("SELECT * FROM `".sanitize_key($tablename)."` WHERE $whereStr");
			$CheckIfExists = $wpdb->get_results($sql);
			if (!empty($CheckIfExists)){
				return $result_Update;
			}
		}
		if ( $wpdb->insert($tablename, 	array_merge($NewArray, $WhereArray)	) ) return true;
		return false;
	}

	public function wp_bulk_update($table, $rows, $delete_array) {
		global $wpdb;
		$whereStr=''; $i=1; foreach ($delete_array as $key=>$value){ $whereStr .= $wpdb->prepare( sanitize_key($key) . " = '%s'", $value); if ($i != count($delete_array)) { $whereStr .=' AND '; $i++;}  }
		$wpdb->query("DELETE FROM ". $table. " WHERE $whereStr"); 
		return $this->wp_bulk_insert($table, $rows);
	}

	public function wp_bulk_insert($table, $rows) {
        global $wpdb;
		$columns = array_keys($rows[0]); // Extract column list from first row of data!
		asort($columns);
		$columnList = '`' . implode('`, `', $columns) . '`';
		$sql = "INSERT INTO `$table` ($columnList) VALUES\n";
		$placeholders = [];
		$data = []; 
 
		// Build placeholders for each row, and add values to data array
		foreach ($rows as $row) {
			ksort($row); //because columns were sorted by "array_keys"
			$rowPlaceholders =[];
			foreach ($row as $key => $value) { 
				$value_corrected = is_array($value) ? json_encode($value) : $value;
				$data[] = $value_corrected;
				if (is_numeric($value_corrected)) {		// differentiate values and set placeholders
					$rowPlaceholders[] = is_float($value_corrected) ? '%f' : '%d';
				} else {
					$rowPlaceholders[] = '%s';
				}
			}
			$placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
		}
		$sql .= implode(",\n", $placeholders);	 			// Stitching all rows together
		//$q = rtrim( $q, ',' ) . ';';
		return $wpdb->query($wpdb->prepare($sql, $data));	// Run the query.  Returning number of affected rows for this chunk
    }
 

	public function add_column_my($table_name, $column_name, $column_type="mediumtext", $afterColumn=false){
		global $wpdb;
		$all_columns = $wpdb->get_col( "DESC " . $table_name , 0 );
		$result= 'already exists';
		if (!in_array($column_name, $all_columns )){  
			$sql = "ALTER TABLE `".sanitize_key($table_name)."` ADD `".sanitize_key($column_name)."` ".sanitize_key($column_type)." NOT NULL". ($afterColumn? " AFTER ".sanitize_key($afterColumn) : "");
			//$sql = $wpdb->prepare ($sql, ''); throws error, we dont need here sanitization at all
			$result= $wpdb->query( $sql);  // CHARACTER SET utf8 
		}
		return $result;
	}
	public function db_get_results($table_name='tablename', $which_columns='*', $where_key='', $where_value=''){
		global $wpdb;
		if (empty($where_key) )
			$sql = "SELECT ". (empty($which_columns) ? '*':esc_sql($which_columns))." FROM ".sanitize_key($table_name);
		else
			$sql = $wpdb->prepare( "SELECT ". (empty($which_columns) ? '*':esc_sql($which_columns))." FROM ".sanitize_key($table_name)." WHERE `".sanitize_key($where_key)."` = '%s'", $where_value);
		return $wpdb->get_results( $sql );
	}
			public function db_get_results_sorted($table_name='tablename', $which_columns='*', $where_key='', $where_value='', $set_array_key=''){
				$res = $this->db_get_results($table_name, $which_columns, $where_key, $where_value);
				return ( empty($set_array_key) ? $res : $this->array_makeKeyedBySubkey($res, $set_array_key) );
			}
			
	public function db_get_row($table_name='tablename', $where_key='', $where_value='', $type='object'){
		//$res = $this->db_get_vars($table_name, $what, $key, $value);
		//return (empty($res))
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM `". sanitize_key($table_name). "`". (empty($where_key) ? '' : " WHERE `".sanitize_key($where_key)."` = '%s'") , $where_value);
		$res = $wpdb->get_row( $sql );
		return ($type==='object' ? $res : $this->object_to_array($res));
	} 
	public function db_get_var($table_name='tablename', $where_key='', $where_value='',  $what='', $type='object'){ 
		$res = $this->db_get_row($table_name, $where_key, $where_value, $type);
		return ( !empty($res) && property_exists($res, $what) ? $res->$what : '' );
	} 
	public function db_get_table($table_name='tablename', $line = '`ID` = 42'){
		global $wpdb;  
		$table_name= sanitize_key($table_name);
		$sql =  "SELECT * FROM `$table_name`";
		$res = $wpdb->get_results( $sql);
		return $res;
	}
	public function get_table_my($table_name='tablename', $line = '`ID` = 42'){
		return $this->db_get_table($table_name, $line );
	}
	public function db_table_columns($table_name='tablename' ){
		global $wpdb;  
		$existing_columns = $wpdb->get_col("DESC ". $this->sanitize_key($table_name), 0);
		return $existing_columns;
	}

	public function checkErrorAddColumn($tablename, $added_column_type="mediumtext")
	{
		global $wpdb;
		//"Unknown column 'c_contract' in 'field list'";
		$err =$wpdb->last_error;
		if (!empty($err))
		{
			preg_match('/Unknown column \'(.*?)\' in/', $err, $n);
			if ( !empty($n[1]) )
			{
				$column_name = $this->sanitize_key($n[1]);
				$res = $this->add_column_my($tablename, $column_name, $added_column_type);
				if($res)
					$wpdb->query($wpdb->last_query) ;
				else
					$this->log($wpdb->last_error);
			}
		}
	}
 
    public function db_alldata_tablename()  { 
		return $GLOBALS['wpdb']->prefix. $this->slug.'_datacache';
	}
	public function db_create_datacache_table()
	{    
		$this->create_table_my( $this->db_alldata_tablename(), [ 
			'`uniq_id`      TINYTEXT NOT NULL',
			'`option_name`  TEXT NOT NULL',
			'`text`         LONGTEXT',
			'`time`         bigint'
		] );
	}
	public function cache_db_set($key, $data, $time=false)
	{    
		$time = $time ?: time();
		$res =  $this->update_or_insert( $this->db_alldata_tablename(), 
			[ 'option_name'=>$key, 'text'=>$this->textify_or_not($data), 'time'=>$time],  
			'option_name');
		return $res;
	}
	public function cache_db_get($key, $default=[], $expire_seconds=-1, $decode='array')
	{    
		$expire_seconds = $expire_seconds===-1 ? 999999999 : $expire_seconds;

		$res_initial = $this->db_get_var( $this->db_alldata_tablename(), 'option_name', $key, 'text' );
		if (empty($res_initial)){
			$res=$default;
		}
		else{
			if ( $decode && $this->maybe_json($res_initial) ){
				$res=json_decode($res_initial, ($decode==='array'));
				if (is_null($res)){
					$res = $res_initial;
				}
			}
			else{
				$res=$res_initial;
			}
		}
		return $res;
	}
	public function cache_db_append($key, $data, $time=false)
	{    
		$existing=$this->cache_db_get($key);
		$time = $time ?: time();
		if ( is_array($existing) && is_array($data) ){
			$finalData = array_merge_recursive($existing,$data);
			$finalData = $this->stringify($finalData);
		}
		else 
			$finalData = $this->stringify($existing) . $this->stringify($data);


		$res =  $this->update_or_insert( $this->db_alldata_tablename(), 
			[ 'option_name'=>$key, 'text'=>$finalData, 'time'=>$time],  
			'option_name');
		return $res;
	}
	
	public function cache_db_append_array($key, $data, $time=false)
	{    
		$existing  = $this->cache_db_get($key,[]);
		$newData   = is_array($data) ? $data : [$data];
		$finalData = array_merge_recursive($existing,$newData);
		$res = $this->update_or_insert( $this->db_alldata_tablename(), 
			[ 'option_name'=>$key, 'text'=>json_encode($finalData), 'time'=>$time ?: time()],  
			'option_name');
		return $res;
	}


	// types
	private $db_types_arr=[];
	public function db_type($which_db='', $which_table=''){
		global $wpdb;
		$key = 'key_'. md5($which_db.'_'.$which_table);
		if( ! array_key_exists($key, $this->db_types_arr) ) 
			$this->db_types_arr[$key]= $wpdb->get_var("SELECT `ENGINE` FROM `information_schema`.`TABLES`  WHERE `TABLE_SCHEMA`='".DB_NAME."' AND `TABLE_NAME`='".$wpdb->prefix."users'" );
		return $this->db_types_arr[$key];
	}
	public function db_type_is_myisam(){ return $this->db_type()=="MyISAM"; }

	// https://stackoverflow.com/a/32913817/2377343 [ SET auto_commit = 1 ] 
	public function db_disable_autocommit(){
		global $wpdb;
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
		if ( $this->db_type()=='MyISAM'){ 
			return $wpdb->query('SET AUTOCOMMIT = 0');
		}
		else{ //if($this->db_type()=='InnoDB'){
			return $wpdb->query('START TRANSACTION;');
		}
	}
	public function db_enable_autocommit($commit=true){
		global $wpdb;
		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );
		if ( $this->db_type()=='MyISAM'){ 
			return $wpdb->query('SET AUTOCOMMIT = 1;');
		}
		else{ //if($this->db_type()=='InnoDB'){
			return $wpdb->query('COMMIT;');
		}
	}
	public function db_commit_query($callback){
		$this->db_disable_autocommit();
		call_user_func($callback);
		$this->db_enable_autocommit();
	}












	// ============ ERRORS LOGS ============ //
	// if used in WP (check to create)
	public function create_log_table()
	{
		global $wpdb;
		$res = $wpdb->query("Show tables like '{$this->logs_table_name}'" );
		if (!$res){
			$res = $GLOBALS['wpdb']->query("CREATE TABLE IF NOT EXISTS `{$this->logs_table_name}` (
					`id` int(50) NOT NULL AUTO_INCREMENT,
					`gmdate` datetime, 
					`function_name` longtext NOT NULL,
					`function_args` longtext NOT NULL,
					`message` longtext NOT NULL, 
					PRIMARY KEY (`id`),
					UNIQUE KEY `id` (`id`)
				)  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci AUTO_INCREMENT=1" 
				//	)  " . $wpdb->get_charset_collate()   || DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci AUTO_INCREMENT=1
			);
		}
		else {
			$res = $wpdb->query("SHOW COLUMNS FROM `{$this->logs_table_name}` LIKE 'function_name'");
			if (!$res){
				$this->add_column_my($this->logs_table_name, 'function_name', 'longtext', 'gmdate'); 
			}
		} 
	} 
	
	//i.e. $this->log("couldnt get results", '<code>'.print_r($response, true).'</code>' );
	public function log( $message ="", $exception="", $trim_chars=1000)
	{	
		global $wpdb; 
		try{
			$this->trim_errorslog();
			$trace=debug_backtrace(); array_shift($trace); $last_func = $trace[0];//$trace=array_splice($trace, 0, 6); //get only first 6 functions 
			$chain=""; foreach($trace as $e) {$chain='['.basename($this->array_value($e,'file') ).'::'.$e['function']."] ---> ".$chain;} 
	
			// --- trim-down the object ---
			$args_str=''; foreach($last_func['args'] as $i=>$e) { 
				$final_obj = '';
				if (is_array($e)){
					$innerArray =[];
					foreach($e as $key=>$value){
						$innerArray[$key] = is_object($value) ? get_class($value) : $value;
					}
					$final_obj = $innerArray;
				}
				elseif(is_object($e)){
					$final_obj = get_class($e);
				}
				else{
					$final_obj = $e;
				}
				$args_str .= "\r\n[$i] --->; ". $this->charsFromStart( print_r($final_obj,true), $trim_chars) ;
			} 
			// -----------------------------

			$trimmed_msg = empty($message)   ? '' : '[Message]: '.$this->charsFromStart( print_r($message, true), $trim_chars );
			$trimmed_exc = empty($exception) ? '' : "\r\n".'[Exception]: '.$this->charsFromStart( print_r($exception, true), $trim_chars );

			$res = $wpdb->insert( $this->logs_table_name, $arr=[ 
				'gmdate'       => gmdate("Y-m-d H:i:s.fff"), 
				'function_name'=> $chain, 
				'function_args'=> $args_str, 
				'message'      => $trimmed_msg. $trimmed_exc] 
			);
			return $res;
		}
		catch(\Excetpion $ex){
			return $ex->getMessage();
		}
	}

	public function clear_errorslog(){ return $GLOBALS['wpdb']->query("TRUNCATE TABLE ".$this->logs_table_name ); } 
	public function get_errorslog()	 { return $GLOBALS['wpdb']->get_results("SELECT * from ".$this->logs_table_name."");	}

	// Removes  oldest rows if rows count exceeds the limit
	public function trim_errorslog()
	{ 
		$rows_amount = $GLOBALS['wpdb']->query("SELECT COUNT(*) FROM ".$this->logs_table_name ." GROUP BY `id`");
		if( $rows_amount > $this->logs_table_maxnum )	
		{
			$amount_to_delete=$rows_amount - $this->logs_table_maxnum; 
			return $GLOBALS['wpdb']->query("DELETE FROM ". $this->logs_table_name. " WHERE 1=1 ORDER BY id LIMIT " . $amount_to_delete ); 
		}
		return null;
	}  
	#endregion
	

	public function show_post_categories($vars=array('POST_ID'=>false, 'excluded_categories'=>array(-1) ) )  {   $x=''; 
		if (!$vars['POST_ID']) { $vars['POST_ID']= $GLOBALS['post']->ID; }
		$post_categories = wp_get_post_categories( $vars['POST_ID'] ); 
		$cats=array();
		foreach($post_categories as $c){   $cats[] = get_category( $c );   }
		foreach($cats as $c){ if (!in_array($c->term_id,   ($vars['excluded_categories'] ?: array())      ) ) {$x .= '<a href="'.get_term_link( $c->term_id, 'category' ).'" target="_blank">'.$c->name.'</a>, '; }  }  return $x;
	}
	
	//change slug,if already exists slug for any other posts/or/pages
	//add_action('save_post', 'efrg324f3f32f4',3);	
	public function efrg324f3f32f4($post) 	{
		if (isset($_POST['post_name'])) { 
			global $wpdb;
			$slug = sanitize_text_field($_POST['post_name']); 
			
			$Post_id_1		= $wpdb->get_var($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '%s' AND ( post_type = 'page' OR post_type = 'post') ", $slug) );
			$post_counts_1	= $wpdb->get_var($wpdb->prepare("SELECT count(post_name) FROM ".$wpdb->posts ." WHERE post_name like '%s'", $slug) );
			
			if (!empty($Post_Object_1) || $post_counts_1 < 1) {
				$_POST['post_name'] = $slug. '-'.rand(11,9999999);
			}
		} 
	}	

	//lets load after init of LANGUAGE phrazes
	//if (isset($_GET['contactMAILpage']))  mailform_page(); 
	public function mailform_page($title=false){
		echo '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8"><title>'.(isset($title) ? $title : 'Mail Sending'). '('.$_SERVER['http_host'].')'.'</title></head><body>';      
		echo $this->contacttt_form() ;
		echo '</body></html>';
		exit;
	}
	public function contact_form_init(){
		add_shortcode('MyCONTACT_FORM', 'contacttt_form' );
	}
	public function contacttt_form($extra_block=''){  $rand= rand(1,111); $multiplier=date('j');  
		$extra_bl =(!is_array($extra_block) ? $extra_block : '');
		$x = utf8_declarationn() . '
		<div id="contac_DIV">'
			.'<style type="text/css"> #submitContatForm{cursor:pointer;} .cf_formm{ display:block;	margin:0 auto;}       .cf_formm table{width:100%; max-width:400px; margin:0 auto;} .brdr{ border-radius:5px; border:1px solid; padding:3px; margin:5px; background-color:#E6E6E6; } .cfinputt{display:block; width:92%; height:30px; min-width:140px; }      .cftxtarea{display:block; width:96%; height:200px;} .submt{ cursor:pointer; } td{vertical-align: middle;}         td.leftflot{float:left; padding:0 0 0 10px;}     span.antWORD{font-weight:bold;} </style>'
			.'<form class="cf_formm" action="" method="POST" id="contactFormID" target="_blank"> 	<input type="hidden" name="contactIsSubmited" value="y" />'
			.'<table><tbody>'
			.$extra_bl
			.'<tr><td>Your Name</td><td><input class="cfinputt brdr" name="namee" value="" placeholder="" type="text" /></td></tr>'
			.'<tr><td>Your Mail</td><td><input class="cfinputt brdr" name="emailii" value="" placeholder="" type="text" /></td></tr>'
			.'<tr><td colspan="2"><textarea class="cftxtarea brdr" name="teext"/></textarea></td></tr>'
			.'<tr><td><span class="antWORD">'. $rand . "*". $multiplier.'=<input type="hidden" name="initiall" value="'. $rand .'" /></span></td><td class="leftflot"><input class="cfinputt brdr" type="text" value="" name="antiSpamm"  /></td></tr>'
			.'<tr><td><input class="cfinputt brdr submt" type="submit" value="SEND" id="submitContatForm"  /></td><td>&nbsp;</td></tr>'
			.'</tbody></table>'
			.'</form>'
		.'</div>';
		return $x;
	} 
	public function mailsent_page($to,$subject,$message,$from, $title=false){
		echo '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8"><title>'.(isset($title) ? $title : 'Mail Sending'). '('.$_SERVER['http_host'].')'.'</title></head><body>';      
		echo send_maill($to,$subject,$message,$from);
		echo '</body></html>';
		exit;
	}
	//if(!$this->definedTRUE('avoid_mailcheck')) { add_actionX('init','check_mailsentt'); }
	public function check_mailsentt(){
		if (!empty($_POST['contactIsSubmited'])){
			header('Content-Type: text/html; charset=utf-8');  
			global $odd,$lang;
			$messiji	= isset($_POST['teext'])	? sanitize_text_field($_POST['teext'])	: '';
			$maill		= isset($_POST['emailii'])	? sanitize_text_field($_POST['emailii'])	: ''; 
				$from_mail =(!stristr($maill,'@yahoo.com')) ?		$maill : 'X'.rand(1,1111111).rand(1,1111111).'@no-reply.com';
			$nameei		= isset($_POST['namee'])	? sanitize_text_field($_POST['namee'])	: '';
			
			$admin_mail	= get_option('admin_email');
			$subjectt	= $_SERVER['HTTP_HOST'] . '-dan gamogzavnilia shetyobineba';
			$full_messag="FROM: $nameei ($maill) \r\n\r\n Message:\r\n" . $messiji;
			
			
			//if (($_POST['antisp_conf']=='yess') && (!empty($_POST['namee']) && !empty($_POST['emailii']) && !empty($_POST['teext'])))
			if(empty($_POST['namee']) || empty($_POST['emailii']) || empty($_POST['teext']))					{die(!defined('LNG') ? "please, fill form" : $lang['fill_form_'.LNG] ); }	
			elseif ( ! ( in_array($_POST['antiSpamm']/$_POST['initiall'],  array(date('j'),date('j')-1))) ) 	{die("Error Captcha");	}
			else {  exit(send_maill($admin_mail,$subjectt,$full_messag, $from_mail )); }
		}		
	}
 
	public function check_mailsent2(){
		if(isset($_GET['sendmessage'])){
			$to			= get_option('admin_mail');
			$subject	= !empty($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '' ;
			$from		= validate_mail($_POST['email']) ? sanitize_text_field($_POST['email']) : die("incorrect_mail");
			$name		= isset($_POST['name']) ? sanitize_text_field($_POST['name']) : die("incorrect NAME");
			$messg		= isset($_POST['content']) ? sanitize_text_field($_POST['name']) : die("incorrect NAME");
			$phone		= !empty($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '' ;
			$message	= "Name: ".$name."\r\nE-mail: ".$from."\r\nPhone: ".$phone."\r\nMessage:\r\n\r\n".$messg;
			if($_POST['captxt'] != $_POST['Captcha']){ die("incorrect captcha"); }
			// mailsent_page($to,$subject,$message, $from);
			send_maill($to,$subject,$message,$from);
		}
	}
 
	public function send_maill($to, $subject, $message, $from=false, $reply_to=false){
		$result = my_mail($to ,$subject, $message ,  default_mail_headers($from));

		if ($result) { $success_messg='<span class="seent" style="color:green; display:block; font-size:25px;">SENT!</span>';}
		else		 { $success_messg='<span class="cant_send" style="color:red;">ERRORR..</span>';	}
		return $success_messg;
	}
 
	
	// inject loader into wp_config : https://pastebin_com/zUTBWvpP

	// https://wordpress.stackexchange.com/questions/16382/showing-errors-with-wpdb-update
	public function show_wp_error(){
		global $wpdb; 
		$wpdb->show_errors = TRUE;
		$wpdb->suppress_errors = FALSE;

		$wpdb->show_errors(); $wpdb->print_error();  
		if ($wpdb->last_error) {
		  die('error=' . var_dump($wpdb->last_query) . ',' . var_dump($wpdb->error));
		}
	}


	public function test_favicons(){
		$image = is_admin() ? 'M8,3A2,2,0,1,1,6,1,2,2,0,0,1,8,3' : 'M8,3A2,2,0,1,1,4,3';
		$color = $this->is_localhost() ?'ff0000' : '00ff00';
		$tmp   = '<link rel="icon" rel2="tempFav" type="image/png"  href="'. (!empty($url) ? $url : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='5 1 2 4'%3E%3Cpath d='$image' fill='%23$color'/%3E%3C/svg%3E").'" />';
		add_action('admin_head', function() use($tmp) { echo $tmp; } );
		add_action('wp_head',    function() use($tmp) { echo $tmp; } );
	}

	// add widgets: https://pastebin_com/VzDQgJrF

	// Default classes 
	public function add_content_classes()
	{
		add_action('the_excerpt',		[$this, 'defaultContainers_excerpt']);  
		add_action('the_excerpt_rss',	[$this, 'defaultContainers_excerpt']); //<-deprecated or not?  
		add_action('the_excerpt_feed',	[$this, 'defaultContainers_excerpt']);

		add_action('the_content',		[$this, 'defaultContainers_content'] );
		add_action('the_content_rss',	[$this, 'defaultContainers_content']); //<-deprecated
		add_action('the_content_feed',	[$this, 'defaultContainers_content']);
	}
	public function defaultContainers_content($cont){ return !isset($GLOBALS['post']) ? $cont : '<div class="default-content-clss cnt_' . $GLOBALS['post']->ID .' type_'.$GLOBALS['post']->post_type.' ">'.$cont.'</div>';  }
	public function defaultContainers_excerpt($cont){ return !isset($GLOBALS['post']) ? $cont : '<div class="default-content-clss excp_'. $GLOBALS['post']->ID .' type_'.$GLOBALS['post']->post_type.' ">'.$cont.'</div>';  }

	//CSS CLASSES for BODY
	public function add_my_body_classes() { add_filter( 'body_class', 		[$this, 'add_my_body_classes_HELPER'] ); add_filter( 'admin_body_class', [$this, 'add_my_body_classes_HELPER'] ); }
	public function add_my_body_classes_HELPER( $classes )
	{
		$this->add_body_class_($classes, " ". $this->domainReal);
		$this->add_body_class_($classes, " ". (is_admin() ? "backend":"frontend") );
		//$this->add_body_class_($classes, $GLOBALS['odd']['is_pc_platform'] ? "pcOS" : "mobileOS");
		
		//add role
		$roles = ( array ) wp_get_current_user()->roles;
		$chosen = " ".'role-'.(isset($roles[0]) ? $roles[0] : 'guest');
		$this->add_body_class_($classes, $chosen);
		//
		return $classes;
	}
	public function add_body_class_(&$classes, $value){ if (is_array($classes)) $classes[] = $value;  else $classes .= $value; return $classes;  }
	 
	
	//allsite_options
	public function get_option_my($keyNAME, $re_call = false, $defaultvalue=false){
		if (!isset($this->my_custom_optioned_array) || !array_key_exists($keyNAME, $this->my_custom_optioned_array) || $re_call) {
			$x = get_option('my_optioned_arrayyy',array());
			if (!array_key_exists($keyNAME,$x))  { $x[$keyNAME]=false;}
			if ($x[$keyNAME]==false || !empty($defaultvalue) ) { $x[$keyNAME]=$defaultvalue;   update_option('my_optioned_arrayyy',$x); }
			$this->my_custom_optioned_array = $x;
		}
		return $this->my_custom_optioned_array[$keyNAME];
	}

	public function update_option_my($keyNAME, $value){
		$x= get_optionX($keyNAME, true);
		$x[$keyNAME] = $value;
		update_option('my_optioned_arrayyy',$x);
	}



	public function is_login_page(){ return did_action('login_init'); }
	public function is_posteditor_page(){ global $pagenow;
		if 	( is_admin()
			&& 
			(
				(in_array( $pagenow, array('post.php'))  && 'edit' ==$_GET['action'])	//if Edit page 
				|| (in_array( $pagenow, array('post-new.php'))) 						//if NEW page
			)
		){	return true;	}
		else{ return false;}
		
	} 

	// video audio :og: https://pastebin_com/azNfyg02

} // class
#endregion
//======================================================================= //
//=========================      ### WP codes     ======================= // 
//======================================================================= //








































//======================================================================= //
//======================== 3) Main base for WP plugins  ================= //
//======================================================================= //
//example construct :  https://pastebin_com/rr5bkPcs


#region  Pro-Version functions ( extends above default plugin class)
if (!trait_exists('\Puvox\pro_plugin')){
trait pro_plugin
{
	// ========= my functions for PRO plugins ========== //
	public function addon_path()
	{
		return WP_PLUGIN_DIR .'/_addons/'.$this->slug .'-addon/addon.php';
	}
	
	public function addon_exists()
	{
		return (file_exists($this->addon_path()));
	}

	public function load_pro()
	{
		if ($this->is_pro)
		{
			if ($this->is_pro_legal)
			{
				$puvox_last_class = $this;
				if($this->addon_exists())
					include_once($this->addon_path());
			}
		}
	}


	/*
	// Let's do it!
	if (is_multisite()) {
		single_uninstall();

		// delete data foreach blog
		$blogs_list = $GLOBALS['wpdb']->get_results("SELECT blog_id FROM {$GLOBALS['wpdb']->blogs}", ARRAY_A);
		if (!empty($blogs_list)) {
			foreach ($blogs_list as $blog) {
				switch_to_blog($blog['blog_id']);
				single_uninstall();
				restore_current_blog();
			}
		}
	} else {
		single_uninstall();
	}
	*/
	
	
	public function check_if_pro_plugin()
	{
		$this->is_pro		= null;
		$this->is_pro_legal	= null;
		if( $this->static_settings['has_pro_version'] ){
			//$this->has_pro_version = true;  // it is price of plugin
			$ar= $this->get_license();
			$this->is_pro		= $ar['status'];
			$this->is_pro_legal	= $ar['legal'];
		}
		if(is_admin())
		{
			if ($this->is_pro)
			{
				if (!$this->is_pro_legal)
				{
					add_action('network_admin_notices', [$this, 'admin_error_notice_pro'] ); 
					add_action('admin_notices', [$this, 'admin_error_notice_pro'] ); 
				}
				else{
					$this->pro_check_once_in_a_while();
				}
			}
		}
		$this->addons_dir = WP_PLUGIN_DIR.'/_addons'; //wp_plugins_dir();
	} 

	public function license_keyname(){
		return $this->plugin_slug_u ."_l_key";
	}

	public function get_license($key=false){
		$def_array = [
			'status' => false,
			'legal' => false,
			'key' => '',
			'last_error'=>''
		];
		$license_arr = get_site_option($this->license_keyname(), $def_array );
		return ($key ? $license_arr[$key] : $license_arr);
	}

	public function update_license($val, $val1=false){
		if(is_array($val)){
			$array = $val;
		}
		else{
			$array= $this->get_license();
			$array[$val]=$val1;
		}
		update_site_option( $this->license_keyname(), $array );
	}



	public function license_answer($key, $type="check/or/activate")
	{
		$this->info_arr	= ['key' => $key] + ['siteurl'=>home_url(), 'plugin_slug'=>$this->slug ] + $this->pluginvars() + $this->opts;
		$answer =
			wp_remote_retrieve_body(
				wp_remote_post($this->static_settings['purchase_check'].$type,
					[
						'method' => 'POST',
						'timeout' => 25,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => [],
						'body' => $this->info_arr,
						'cookies' => []
					]
				)
			);
		return $answer;
	}

	public function license_status($license, $type="check/or/activate")
	{
		$key = sanitize_text_field($license);
		$answer = $this->license_answer($key, $type);

		if(!$this->helpers->is_JSON_string($answer)){
			$result = [];
			$result['error'] = $answer;
		}
		else{
			$result = json_decode($answer, true);
		}
		//
		if(isset($result['valid'])){
			if($result['valid']){
				$ar['status']= true;
				$ar['legal']= true;
				$ar['key']= $key;
				$ar['last_error']= '';
				$this->update_license($ar);
			}
			else { 
				// 
				$this->update_license( 'legal', false );
				$this->update_license( 'last_error', json_encode($result['response']) );
				$result['error'] = json_encode($result['response']);
			} 
		}
		else{
			$result['error'] = $answer;
			$this->helpers->log('Error while calling to vendor', $result['error']);
		}
		return json_encode($result);
	}

	public function pro_check_once_in_a_while( $time_length = 864000 )
	{
		$name= '`_last_license_check';
		$value= $this->get_transient_CHOSEN($name);
		if( !$value || time() - $value > $time_length )
		{
			$lic = $this->get_license();
			$res= $this->license_status($lic['key'], 'activate');
			$this->update_transient_CHOSEN($name, time() );
		}
	}

	public function unregistered_pro() { return $this->static_settings['has_pro_version'] && !$this->is_pro_legal; }

	public function admin_error_notice_pro(){ ?>
		<div class="notice notice-error is-dismissible">
			<p><?php  _e( sprintf('Notice: License for plugin <code><b>%s</b></code> is invalidated, so it\'s <b style="color:red;">PRO</b> functionality has been disabled.', $this->static_settings['Name']) );?> <a href="<?php echo $this->plugin_page_url;?>" target="_blank"><?php _e("Re-validate the key");?></a></p> 
		</div>
		<?php
	}

	public function pro_field($echo=true){
		if($this->unregistered_pro()){
			$res= 'data-pro-overlay="pro_overlay"';
			if($echo) echo $res;
			else return $res;
			//echo '<span class="pro_overlay overlay_lines"></span> ';
		}
	}

	public function purchase_pro_block(){
		if ( !$this->static_settings['has_pro_version'])  return;
		if ( $this->is_pro_legal  ) return;

		?>
		<div class="pro_block">
			<style>
			.get_pro_version { line-height: 1.2; z-index: 123; background: #ff1818;  text-align: center; border-radius: 100% 100% 0 0; display: inline-block;  position: fixed; bottom: 0px; right: 0; left: 0; padding: 10px 10px; max-width: 750px; margin: 0 auto; text-shadow: 0px 0px 6px white;  box-shadow: 0px 0px 52px black; }
			.get_pro_version .centered_div > span  { font-size: 1.5em; }
			.get_pro_version .centered_div .or_enter_key_phrase{ font-style: italic; font-size:1em; }
			.get_pro_version .centered_div > span  a { font-size: 1em; color: #7dff83;}
			.init_hidden{ display:none; }
			z#check_results{ display:inline; flex-direction:row; font-style:italic; }
			#check_results .correct{  background: #a8fba8;  }
			#check_results .incorrect{  background: pink;  }
			#check_results span{  padding:3px 5px;  }
			.myplugin .dialog_enter_key{ display:none; }
			.dialog_enter_key_content {  display: flex; flex-direction: column; align-items: center;  }
			.dialog_enter_key_content > *{  margin: 10px ;  }
			.myplugin .illegal_missing {font-size:12px; word-wrap:pre-wrap; }

			[data-pro-overlay=pro_overlay]{  pointer-events: none;  cursor: default;  position:relative;  min-height: 2em;  padding:5px; }
			[data-pro-overlay=pro_overlay]::before{   content:""; width: 100%; height: 100%; position: absolute; background: black; opacity: 0.3; z-index: 1;  top: 0;   left: 0;
				background: url("<?php echo $this->helpers->imageSvg('overlay-pro');?>");
			}
			[data-pro-overlay=pro_overlay]::after{ 
				white-space: pre; content: "<?php $str=__('Only available in FULL VERSION');  echo str_repeat($str.'\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a', 4).$str;?>"; 
				text-shadow: 0px 0px 5px black; padding: 5px;  opacity:1;  text-align: center;  animation-name: blinking;  zzanimation-name: moving;  animation-duration: 6s;  animation-iteration-count: infinite;  overflow:hidden; display: flex; justify-content: center; align-items: center; position: absolute; top: 0; left: 0; bottom: 0; right: 0; z-index: 3; overflow: hidden; font-size: 2em; color: red;
			}
			@keyframes blinking {
				0% {opacity: 0;}
				50% {opacity: 1;}
				100% {opacity: 0;}
			}
			@keyframes moving {
				0% {left: 30%;}
				40% {left: 100%;}
				100% {left: 0%;}
			}
			</style>
			
			<div class="dialog_enter_key">
				<div class="dialog_enter_key_content" title="Enter the purchased license key">
					<input id="key_this" class="regular-text" type="text" value="<?php echo $this->get_license('key');?>"  />
					<button id="check_key" ><?php _e( 'Check key' );?></button>
					<span id="check_results">
						<span class="correct init_hidden"><?php _e( 'correct' );?></span>
						<span class="incorrect init_hidden"><?php _e( 'incorrect' );?></span>
					</span>
				</div>
			</div>

			<div class="get_pro_version">
				<div class="centered_div">
					<?php 
					$need_to_enter_key = false;

					if ( $this->is_pro )
					{ 
						if ( !$this->addon_exists() )
						{ ?>
							<span class="addon_missing">
							( <?php _e('Seems you have bought a PRO version, but the addon is not installed.');?> )
							</span>	
							<?php
						}
						elseif( !$this->is_pro_legal)
						{
							$need_to_enter_key=true;
							?>
							<span class="illegal_missing">
							( <?php _e('Seems you don\'t have a legal key');?>.  <span class="last_err_msg" style="white-space: pre-wrap;">( <?php _e( sprintf('Last error message: <code>%s</code> ',  $this->get_license('last_error') ) ); ?> )</span>  )
							</span>	
							<?php
						}
					}  
					else
					{
						if (!$this->addon_exists()) {  ?>
							<span class="purchase_phrase">
								<a id="purchase_key" href="<?php echo esc_url($this->static_settings['purchase_url']);?>" target="_blank"><?php _e('GET FULL VERSION');?></a> <span class="price_amnt"><?php _e('only');?> <?php echo $this->static_settings['has_pro_version'];?>$</span>
							</span>
						<?php 
						}
						$need_to_enter_key=true;
					} 
					?>
					<?php
					
					if ($need_to_enter_key)
					{
					?>
					<span class="or_enter_key_phrase">
					( <?php _e('After purchase');?> <a id="enter_key"  href=""><?php _e('Enter License Key');?></a> )
					</span>	
					<?php
					}

					?>
				</div>
			</div>
		</div>
		<?php
		$this->plugin_scripts();
	}
	
	public function plugin_scripts(){
		?>
		<script>
		function main_tt()
		{ 
			var this_action_name = '<?php echo $this->plugin_slug_u;?>';

			(function ( $ ) {
				$(function () {
					//$("#purchase").on("click", function(e){ this_name_tt.open_license_dialog(); } );
					$("#enter_key").on("click", function(e){ return this_name_tt.enter_key_popup(); } );
					$("#check_key").off().on("click", function(e){ return this_name_tt.check_key(); } );
				});
			})( jQuery );

			// Create our namespace
			this_name_tt = {

				/*
				*	Method to check (using AJAX, which calls WP back-end) if inputed username is available
				*/
				enter_key_popup: function(e) {

					// Show jQuery dialog
					jQuery('.dialog_enter_key_content').dialog({
						modal: true,
						width: 500,
						close: function (event, ui) {
							//jQuery(this).remove();	// Remove it completely on close
						}
					});
					return false;
				},

				IsJsonString: function(str) {
					try {
						JSON.parse(str);
					} catch (e) {
						return false;
					}
					return true;
				},

				check_key : function(e) {

					var this1 = this;

					var inp_value = jQuery("#key_this").val();

					if (inp_value == ""){  return;  }

					PuvoxLibrary.backend_call(
						{
							'PRO_check_key': inp_value
						},

						// Function when request complete
						function (answer) {

							if(typeof window.ttdebug != "undefined"){  console.log(answer);  }

							if(this1.IsJsonString(answer)){
								var new_res=  JSON.parse(answer);
								if(new_res.hasOwnProperty('valid')){
									if(new_res.valid){
										this1.show_green();
									}
									else{
										var reponse1 = JSON.parse(new_res.response);
										this1.show_red(reponse1.message);
									}
								}
								else {
									this1.show_red(new_res);
								}
							}
							else{
								this1.show_red(answer);
							}
						}
					);
				},

				show_green : function(){
					jQuery("#check_results .correct").show();
					jQuery("#check_results .incorrect").hide();
					alert("<?php _e("Thanks! License is activated for this domain."); echo '\n\n\n\n'; _e("NOTE: Sharing or unauthorized use of the license will be ended with the suspension of the license code.") ;?>");
					PuvoxLibrary.reload_this_page();
					//this.save_results();
				},

				show_red : function(e){
					jQuery("#check_results .correct").hide();
					jQuery("#check_results .incorrect").show();
					jQuery("#check_results .incorrect").html(e);

					// Show jQuery dialog
					if (false)
					jQuery('<div>' + message + '</div>').dialog({ modal: true, width: 500,
						close: function (event, ui) {
							jQuery(this).remove();	// Remove it completely on close
						}
					});
				}

			};
		}
		main_tt();
		</script>

		<?php
	}
}  } // class




if (!trait_exists(__NAMESPACE__.'\\pro_plugin')) { trait pro_plugin{} }
class default_plugin 
{
	use pro_plugin;

	public $helpers;
	public function __construct($arg1=[])
	{ 
		$this->helpers = new standard_wp_library();
		//$this->h = $this->helpers;
		if (method_exists($this, 'after_construct')) $this->after_construct(); //for rebasing the plugin url
		$this->helpers->init_module(['class'=>get_called_class()] + $arg1);
		$this->plugin_inits();
	}

    public function __call($method, $arguments)
    {
        try {
            return call_user_func_array([$this->helpers, $method], $arguments);
        } catch (Exception $e) {
            throw $e;
        }
    }

	public function plugin_inits()
	{			
		$this->wpdb 	= $GLOBALS['wpdb']; 
		$this->helpers->loadScripsStyles();

		if (!$this->helpers->above_version("5.4")){
			register_activation_hook( $this->helpers->moduleFILE,	function(){ exit( __("Sorry, your PHP version ". phpversion() ." is very old. We suggest changing your hosting's PHP version to latest available v7 version.") ); }	);
			return;
		}
		// initial variables
		$this->my_plugin_vars();
		$this->network_managed_is_selected	= is_multisite() && $this->IsNetworkManaged();
		$this->opts				= $this->refresh_options();							// Setup final variables
		$this->refresh_options_TimeGone();
		$this->helpers->logs_table_name	= $this->get_prefix_CHOSEN() . $this->plugin_slug_u.'__errors_log';	// error logs table name
		$this->helpers->logs_table_maxnum= 50;	// maximum rows in errors logs table
		$this->helpers->create_log_table();

		$this->notes_enabled	= false;
		$this->check_if_pro_plugin();
		$this->__construct_my();		// All other custom construction hooks 

		$this->network_wide_active          = is_plugin_active_for_network($this->static_settings['plugin_basename']) ;  //during network-activation,this is yet false so this will not be usable for activate-redirection link

		// single-site: if main-button: admin.php | else: options-general.php
		// multi-site:  if main-button: admin.php (multi & sub) | else: if sub-site: options-general.php else: settings.php
		$this->settingsPHP_page_dynamic = $this->static_settings['menu_button_level']=='mainmenu' ? 'admin.php' : ( !is_multisite() ? 'options-general.php' :  (!is_network_admin() ? 'options-general.php' : 'settings.php' ) );  // || !$this->network_managed_is_selected <--- no need, let users allow to have menu page in other panels too, there we give notice to manage from appropriate place 

		//$this->plugin_page_url = ( !is_multisite() || !$this->ManagerPageAllow_network() || !$this->network_managed_is_selected ) ? admin_url() : network_admin_url();
		$this->plugin_page_url = ( !is_multisite() || !$this->ManagerPageAllow_network() || !$this->network_managed_is_selected ) ? admin_url() : network_admin_url();

		$this->plugin_page_url .= ( !empty($this->static_settings['custom_opts_page']) ?  $this->static_settings['custom_opts_page'] : $this->settingsPHP_page_dynamic.'?page='.$this->slug); 

		$this->plugin_files		= array_merge( (property_exists($this, 'plugin_files') ? $this->plugin_files : [] ),   ['index.php'] );
		$this->translation_phrases= $this->get_phrases();
		$this->is_in_customizer	= (stripos($this->helpers->currentURL, admin_url('customize.php')) !== false);
		$this->myplugin_class	= 'myplugin puvox_plugin postbox version_'. (!$this->static_settings['has_pro_version']  ? "free" : ($this->is_pro_legal ? "pro" : "not_pro") );
		$this->addon_namepart	= 'puvox.software';
		
		$this->define_option_tabs();

		//activation & deactivation (empty hooks by default. all important things migrated into `refresh_options`)
		register_activation_hook( $this->helpers->moduleFILE,	[$this, 'activate']		);
		register_deactivation_hook( $this->helpers->moduleFILE, [$this, 'deactivate']	);

		//translation hook
		add_action('init', [$this, 'load_textdomain'] );

		//==== my other default hooks ===//
		$this->plugin__setupLinksAndMenus();

		//shortcodes
		$this->shortcodes_initialize();

		// if buttons needed
		//if( property_exists($this, 'tinymce_buttons') ) $this->tinymce_funcs();

		// for backend ajax
		$this->helpers->register_backend_call_actions();

		add_action( 'admin_head', [$this,'admin_head_func']);
		add_action( 'current_screen', function(){ $this->admin_scripts(null); } );

		//add uninstaller file
		if(is_admin() && method_exists($this->helpers,'add_default_uninstall')) $this->helpers->add_default_uninstall();	//add_action( 'shutdown', [$this, 'my_shutdown_for_versioning']);

		add_action('wp',		[$this, 'flush_checkpoint'], 999);

		// functions for PRO-ADDON upload
		// add_filter( 'pre_move_uploaded_file', function( $null, $file, $new_file, $type ){ return $path; }, 10, 4);
		$this->pro_file_part = 'puvox-software';
		if($this->static_settings['has_pro_version']) 	{
			add_filter( 'upload_mimes', [$this->helper,'upload_mimes_filter'], 1); 
			add_filter( 'wp_handle_upload', [$this->helper,'wp_handle_upload_filter'], 10, 2);
		}
		 
		$this->init_properties();
	}
	
		
	//add my default values
	public function my_plugin_vars($step=0)
	{
		include_once(ABSPATH . "wp-admin/includes/plugin.php");
		$plugin_vars = $this->pluginvars();
		$this->slug			= sanitize_key($plugin_vars['TextDomain']);	//same as foldername
		$this->plugin_slug	= $this->slug;								//same as foldername
		$this->plugin_slug_u= str_replace('-','_', $this->slug);
		// set in helpers too:
		$this->helpers->slug	     = $this->slug;
		$this->helpers->plugin_slug  = $this->plugin_slug;
		$this->helpers->plugin_slug_u= $this->plugin_slug_u;

		$temp1=[
			// overrided
            'has_pro_version'		=>0, 
            'show_opts'				=>false, 				//true/false/"submodule"(for not showing as menu-button)
			'menu_button_level'		=>'submenu',			// mainmenu or submenu
			'custom_opts_page'		=>'',
            'show_rating_message'	=>false,
            'show_donation_message'	=>true,
            'show_donation_popup'	=>false,
            'display_tabs'			=>[],
            'required_role'			=>'install_plugins', 
            'default_managed'		=>'network',			//network, subsite, both
			'menu_icon' 			=> ''
		];
		
		$AuthorDomain = !property_exists($this, 'PuvoxDomain') ? 'https://puvox.software/' : 'https://127.0.0.1/wp/puvox.software/';
		$temp2	= $plugin_vars   +  
		[
			'plugin_basename'	=> plugin_basename($this->helpers->moduleFILE),
			'menu_text'			=> array(
				'donate'				=>__('Donate'),
				'settings'				=>__('Settings'),
				'open_settings'			=>__('You can access settings from dashboard of:'),
				'activated_only_from'	=>__('Plugin activable only from'),
				'deactivated_only_from'	=>__('Plugin deactivable only from'),
			),
			'lang'				=> $this->helpers->get_locale__SANITIZED(),
			'wp_rate_url'		=> 'https://wordpress.org/support/plugin/'.$this->slug.'/reviews/#new-post',
			'donate_url'		=> 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=contact@puvox.software&tax=0&currency=USD&item_name=For%20Programming%20Services', // business: http://paypal.me/Puvox   ||  personal : http://paypal.me/ttodua || https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=contact@puvox.software&tax=0&currency=USD&item_name=For%20Programming%20Services  || https://stackoverflow.com/a/43083891/2377343
			'donate_default'	=> 4,
			'mail_errors'		=> 'wp_plugin_errors@puvox.software',
			'licenser_domain'	=> $AuthorDomain,
			'musthave_plugins'	=> $AuthorDomain.'blog/must-have-wordpress-plugins/',
			'purchase_url'		=> $AuthorDomain.'?purchase_wp_plugin='.$this->slug,
			'purchase_check'	=> $AuthorDomain.'?purchase_wp_act=',
			'firebase_url'		=> 'https://linksaf.page.link/?link=',
		];
		$temp2	= $temp2   + [
			'wp_tt_freelancers'	=> 'https://goo.gl/wZKANN',
			'wp_fl_freelancers'	=> 'https://goo.gl/JSVy37',
			'wp_pph_freelancers'=> 'https://goo.gl/vhrqiM',
			'wp_elementor_link' =>  $temp2['firebase_url'].urlencode('https://elementor.com/pricing/?ref=16338&campaign='.$this->slug),
		];
		//enrich from main class (overrides some settings. "is_admin" removed because it fails on front-end /wp-json/wp/v2/posts/8?_locale=user )
		if(method_exists($this,'declare_settings') ) 
		{
			$this->declare_settings(); 	
			$this->initial_static_options = $this->initial_static_options + $temp1;
		}
		else{
			$this->initial_user_options=[]; 
			$this->initial_static_options = $temp1;
		}
		$this->static_settings	= array_merge($this->initial_static_options, $temp2 );
	}

	// remove library file on uninstall
	public function add_default_uninstall(){
		if( is_admin() && !$this->is_development)
		{
			$wp_uninstall_file = $this->baseDIR.'/uninstall.php';
			if( !file_exists($wp_uninstall_file) )
			{
				$content='<'.'?php
				// If uninstall not called from WordPress, then exit
				if ( ! defined( "WP_UNINSTALL_PLUGIN" ) ) exit; 
				';
				try{
					@$this->file_put_contents($wp_uninstall_file, $content);
				}
				catch(\Exception $ex){

				}
			}
		}
	}


	public function plugin__setupLinksAndMenus()
	{
		// If plugin has options, show button (in admin menu sidebar)
		if($this->static_settings['show_opts']===true)  //only this, because sometimes if we want to disable menu-button, then we set to "submodule" instead of true
		{
			if (is_multisite()){
				if ( $this->network_wide_active )
					add_action('network_admin_menu', [$this, 'plugin__registerHandle'] );
				if ( !$this->network_managed_is_selected ){
					add_action('admin_menu',  [$this, 'plugin__registerHandle'] );
				}
			}
			else {
				add_action('admin_menu',  [$this, 'plugin__registerHandle'] );
			}		
			//redirect to settings page after activation (if not bulk activation)
			add_action('activated_plugin', function($plugin) { if ($this->is_not_bulk_activation($plugin))  { exit( wp_redirect($this->plugin_page_url.'&isactivation') ); } } );
		}


		// show author & donate urls (unless hidden)
		if ( !array_key_exists('hide_plugin_links', $this->static_settings))
		{
			// add Settings & Donate buttons in plugins list
			add_filter( (is_network_admin() ? 'network_admin_' : ''). 'plugin_action_links_'. $this->static_settings['plugin_basename'],  function($links){
				if(!$this->static_settings['has_pro_version'])	{ $links[] = '<a href="'.$this->static_settings['donate_url'].'">'.$this->static_settings['menu_text']['donate'].'</a>'; }
				if($this->static_settings['show_opts']){ $links[] = '<a href="'.$this->plugin_page_url.'">'.$this->static_settings['menu_text']['settings'].'</a>';  }
				//if(is_network_admin() && $this->initial_static_options['allowed_on'] =='subsite'){ unset($links['activate']); $links[] = '<b style="color:red;">'.$this->static_settings['menu_text']['deactivated_only_from'].' SUB-SITES</b>';  }
				return $links;
			});
		}
	}
		//helper for above func
		public function plugin__registerHandle()
		{
			$menu_button_name = (array_key_exists('menu_button_name', $this->static_settings) ? $this->static_settings['menu_button_name'] : $this->opts['name'] );
			
			if ( $this->helpers->valueIs($this->static_settings, 'menu_button_level', 'mainmenu' )  )  // icons: https://goo.gl/WXAYCi 
				add_menu_page($menu_button_name, $menu_button_name, $this->static_settings['required_role'] , $this->slug, [$this, 'opts_page_output_parent'], $this->static_settings['menu_icon'] );
			else 
				add_submenu_page($this->settingsPHP_page_dynamic, $menu_button_name, $menu_button_name, $this->static_settings['required_role'] , $this->slug,  [$this, 'opts_page_output_parent'] );

			// if target is custom link (not options page)
			if(array_key_exists('menu_button_link', $this->opts)){
				add_action( 'admin_footer', function (){ ?> <script type="text/javascript"> jQuery('a.toplevel_page_<?php echo $this->slug;?>').attr('href','<?php echo $this->opts['menu_button_link'];?>').attr('target','_blank'); </script> <?php }
				);
			}
		}
	

	// ================  dont use activation/deactivation hooks =====================//
	// happens before REAL activation (activated_plugin happens after individual DB activation) 
	public function activate($network_wide)
	{
		// Differentiation only applies when/if MultiSite enabled. Otherwise, always master site
		if ( is_multisite() )
		{ 
			if(
				( !$this->ManagerPageAllow_network() && ( $this->is_network_admin_referrer()  || $network_wide) )
					||
				( !$this->ManagerPageAllow_singlesite() && (!$this->is_network_admin_referrer() || !$network_wide ) )
			)
			{
				$text= '<h2><code>'.$this->opts['name'].'</code>: '. $this->static_settings['menu_text']['activated_only_from']. ' <b style="color:red;">'.($this->ManagerPageValue()).'</b></h2>';
				//$text .=  '<script>alert("'.strip_tags($text).'");</script>';
				//header_remove("Location"); header_remove("X-Redirect-By"); 
				die($text);
			}
		}
		//$this->plugin_updated_hook();
		if ( method_exists($this, 'activation_funcs') ) { $this->activation_funcs($network_wide); } 
	}
	// commented part:  pastebin_com/KNM3iMEs

	public function deactivate($network_wide){
		if(method_exists($this, 'deactivation_funcs') ) {   $this->deactivation_funcs($network_wide);  }
	}

	//load translation
	public function load_textdomain(){
		load_plugin_textdomain( $this->slug, false, basename($this->helpers->baseDIR). '/languages/' );
	}

	public function is_not_bulk_activation($plugin)
	{
		return ( $plugin == $this->static_settings['plugin_basename'] && !((new \WP_Plugins_List_Table())->current_action()=='activate-selected'));
	}
	
	// for some reasons, native "is_network_admin()" doesn't work during ACTIVATION hook, and we need to manually use this
	public function is_network_admin_referrer()
	{
		return (array_key_exists("HTTP_REFERER", $_SERVER) && stripos($_SERVER["HTTP_REFERER"],'/wp-admin/network/') !==false);
	}

	public function pluginvars(){
		// https://goo.gl/Z3z8FW : Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title, AuthorName
		return get_plugin_data( $this->helpers->moduleFILE, $markup = true, $translate = false);    //dont $translate, otherwise you will get error of: https://core.trac.wordpress.org/ticket/43869
	}

	//get latest options (in case there were updated,refresh them)
	public function refresh_options(){
		$this->opts	= $this->get_option_CHOSEN($this->slug, []);
		if(!is_array($this->opts)) $this->opts = $this->initial_user_options;
		foreach($this->initial_user_options as $name=>$value){ if (!array_key_exists($name, $this->opts)) { $this->opts[$name]=$value;  $should_update=true; }  }
		$this->opts = array_merge($this->opts, $this->initial_static_options);
		$this->opts['name']		=$this->static_settings['Name'];
		$this->opts['title']	=$this->static_settings['Title'];
		$this->opts['version']	=$this->static_settings['Version'];
		if(isset($should_update)) {	$this->update_opts(); }
		return $this->opts;
	}

	public function refresh_options_callback($func){
		$this->refresh_options();  call_user_func($func); $this->update_opts();
	}

	public function refresh_options_TimeGone(){
		//if never updated
		if(empty($this->opts['first_install_date'])) {
			$should_update=true;	$this->opts['first_install_date'] = time();
		}
		if(empty($this->opts['last_update_time'])) {
			$should_update=true;	$this->opts['last_update_time'] = time();
		}
		if(empty($this->opts['last_updates'])) {
			$should_update=true;	$this->opts['last_updates'] = [];   		
		}
		//if plugin updated through hook or manually... to avoid complete break..
		if( empty($this->opts['last_version']) || $this->opts['last_version'] != $this->opts['version'] ){
			$should_update=true; $this->opts['last_version'] = $this->opts['version']; $reload_needed=true;
		}
		if(isset($should_update)) {	$this->update_opts(); }
		if(isset($reload_needed)) { $this->plugin_updated_hook(true); }
	}


	
	//once_in_a_while time() timegone transient
	public function last_checkpoint($var_name, $seconds_to_check=86400){
		$opt= "last_checkpoints_rand_24df3023yfdh3qfhs";
		$this->$opt= !empty($this->$opt) ? $this->$opt : get_option($opt, []);
		if(empty($this->$opt) || empty($this->$opt[$var_name]) || !is_numeric($this->$opt[$var_name]) || $this->$opt[$var_name]< time()-$seconds_to_check ){
			$this->$opt[$var_name]	= time();
			update_option($opt, $this->$opt);
			return true;
		}
		return false;
	}


	public function reset_plugin_to_defaults()
	{
		$this->update_opts([]) ;
		$this->update_phrases([]) ;
		if(method_exists($this, 'plugin_reset_callback'))   $this->plugin_reset_callback();
	}

	//update library file on activation/update
	public function plugin_updated_hook($redirect=false)
	{
		return;	
	}
	
	public function get_prefix_CHOSEN(){
		return ($this->network_managed_is_selected ? $GLOBALS['wpdb']->base_prefix : $GLOBALS['wpdb']->prefix);
	}

	// quick method to update this plugin's opts
	public function optName($optname, $prefix=false){
		if( substr($optname,  0, 1) == '`'  ) {  $prefix=true;  $optname= substr($optname,1); }
		return ( !$prefix || stripos($optname, $this->slug) !== false )  ? $optname :  $this->slug . '_' . $optname;
	}

	public $options_save_privilege ='manage_options';
	public function update_opts($opts=null){
		if (!$opts) $opts = $this->opts; 
		return $this->update_option_CHOSEN($this->slug, $opts );
	}
	public function update_opts_permission($opts=null){
		return ( !did_action('init') || !current_user_can($this->options_save_privilege) ? false : $this->update_opts() );
	}

	public function get_option_CHOSEN($optname, $default=false        				, $prefix=false){
		return call_user_func("get_".		( $this->network_managed_is_selected ? "site_" : "" ). "option",  $this->optName($optname, $prefix), $default );
	}
	public function update_option_CHOSEN($optname, $optvalue, $autoload=null		, $prefix=false){
		return call_user_func("update_".	( $this->network_managed_is_selected ? "site_" : "" ). "option",  $this->optName($optname, $prefix), $optvalue, $autoload );
	}
	public function delete_option_CHOSEN($optname									, $prefix=false){
		return call_user_func("delete_".	( $this->network_managed_is_selected ? "site_" : "" ). "option",  $this->optName($optname, $prefix) );
	}

	public function get_transient_CHOSEN($optname, $default=false        			, $prefix=false){
		return call_user_func("get_".		( $this->network_managed_is_selected ? "site_" : "" ). "transient",  $this->optName($optname, $prefix), $default );
	} 
	public function set_transient_CHOSEN($optname, $value, $seconds					, $prefix=false){
		return call_user_func("set_".		( $this->network_managed_is_selected ? "site_" : "" ). "transient",  $this->optName($optname, $prefix), $value, $seconds );
	} 
	public function update_transient_CHOSEN($optname, $optvalue, $expiry=null		, $prefix=false){
		return call_user_func("set_".		( $this->network_managed_is_selected ? "site_" : "" ). "transient",  $this->optName($optname, $prefix), $optvalue, $expiry );
	}
	public function delete_transient_CHOSEN($optname								, $prefix=false){
		return call_user_func("delete_".	( $this->network_managed_is_selected ? "site_" : "" ). "transient",  $this->optName($optname, $prefix) );
	}
	public function delete_transients_by_prefix_CHOSEN($myPrefix					, $prefix=false){
		global $wpdb;
		$table_name		= $this->network_managed_is_selected ? $wpdb->base_prefix.'sitemeta' : $wpdb->prefix .'options' ;
		$column_name	= $this->network_managed_is_selected ? 'meta_key' : 'option_name';
		return $this->helpers->delete_transients_by_prefix($myPrefix, $table_name, $column_name);
	}
	public function get_transient_ONCE_DELETE($optname, $default=false ){
		if( ($x = get_transient($optname))!==false ) { delete_transient($optname); return $x;  }
		return false;
	}
	
	//
	public function postOptionIsset($name){
		return isset($_POST[$this->slug][$name]);
	}
	public function postOptionValue($name){
		return $_POST[$this->slug][$name];	//Note, the response of this method is always "sanitized & filtered" in any implemented methods
	}
	public function postOptionText($name){
		return $this->sanitize_text_field($this->postOptionValue($name));
	}
	public function postOptionNumber($name){
		return (int) $this->postOptionValue($name);
	}
	public function postOptionKey($name){
		return sanitize_key($this->postOptionValue($name));
	}
	//
	
	
	
	public function ManagerPageAllow_network() { return ( (is_multisite() && in_array($this->static_settings['default_managed'], ['network','both']) ) || ( !is_multisite() ) ) ; }
	public function ManagerPageAllow_singlesite() {( (is_multisite() && in_array($this->static_settings['default_managed'], ['subsite','both']) ) || ( !is_multisite() ) ) ; }
	
	public function IsNetworkManaged(){
		return get_site_option( $this->slug . '_network_managed', true );
	}

	public function updateNetworkedState($value){
		$key = $this->slug . '_network_managed';
		if ( ! $this->option_exists( $key, true) ){
			add_site_option( $key, true );
		}
		$res = update_site_option( $key, $value );
		return $res;
	}
	
	public function phrase($key, $is_variable=false) {
		if($is_variable){
			if (!isset($this->translation_phrases[$key])){
				$this->translation_phrases[sanitize_title($key)] = sanitize_title($key);
				$this->update_phrases();
			}
		}
		return ( isset($this->translation_phrases[$key]) ? $this->translation_phrases[$key] : $key ); 
	}
	

	public function is_this_settings_page(){
	  return 
	  (
		is_admin() && 
		( 
			( stripos(get_current_screen()->base, $this->slug) !== false)  &&  (isset($_GET['page']) && $_GET['page']==$this->slug ) 
				||
			( stripos($this->helpers->currentURL, basename($this->plugin_page_url)) !==false  )	//for submodules or custom cases
		)
	  );
	}
		


	public function paypalDonationButton(){ return '<a class="button" style="display:inline-block; line-height:1em; min-height:25px; color:#179bd7; " href="javascript:tt_donate_trigger(event);" onclick="tt_donate_trigger(event);"/> <img style="height:20px; vertical-align:middle;" src="'.  $this->helpers->imageSvg("paypal") .'" /> '. __("donation") .'</a>'; }
	
	public function donations_trigger_popup()
	{
		// ############ donations #############
		if ( $this->static_settings['show_donation_popup'] && ! $this->helpers->valueIs($this->opts, 'donate_popup_a2', '1d') )
		{
			//show only after save/redirection
			if (  $this->opts['first_install_date'] != $this->opts['last_update_time'] )
			{
				$this->opts['donate_popup_a2']="1d";
				$this->update_opts();
				$text = sprintf(__('Dear users, our plugin (<code>%s</code>) is free. However, every plugin needs noticeable amount of work by developer. If you found this plugin useful, your minimal %s will support developer to maintain this plugin and keep it functional. <br/>Thank you.'), $this->static_settings['Name'], $this->paypalDonationButton() );
				?>
				<div id="paypal_donation_popup_2"><input type="hidden" autofocus/><?php echo $text;?></div>
				<script>
				jQuery(function(){  
					window.setTimeout(function(){ 
					jQuery('#paypal_donation_popup_2').dialog({ title:"<?php echo $this->static_settings['Name'];?>",  modal:true,   width:600 });
					
					jQuery("#puvox_donate_button").click(function(e) {
						e.preventDefault();
						jQuery('div.ui-dialog-content').dialog('close');
						jQuery([document.documentElement, document.body]).animate({
							scrollTop: jQuery(".puvox_plugin .in_additional").offset().top
						}, 1000);
					});
					}, 1000 );
				});
				</script>
				<?php
			}
		}
	}


	public function wp_kses($text, $tagsArr=[]){
		return wp_kses($text,$tagsArr);
	}
	public function notes_field(){
		if ($this->notes_enabled && current_user_can("manage_options") )
		{
			$optname=  'my_custom_note_'.$this->plugin_slug;
			//if form updated
			if( $this->checkSubmission('_wp_note_nonce2', 'note_nonce2_' ) )
			{
				update_option($optname, $this->wp_kses($_POST['mynote']) );
			}
			?>
			<div id="notesfield">
				<form action="" method="POST" style="background:pink; padding:5px 10px;">
					<h2>Your notes</h2>
					<textarea style="width:100%; height:200px;" name="mynote"><?php echo get_option($optname,'');?></textarea>
					<?php  $this->nonceSubmit('save', '_wp_note_nonce2', 'note_nonce2_') ; ?>
				</form>
			</div>
			<?php
		}
	}
	
	

	public function is_activation(){
		return (isset($_GET['isactivation']));
	}

	public function reload_without_query($params=array(), $js_redir=true){
		$url = remove_query_arg( array_merge($params, ['isactivation'] ) );
		if ($js_redir=="js"){ $this->js_redirect($url); }
		else { $this->php_redirect($url); }
	}

	public function if_activation_reload_with_message($message){
		if($this->is_activation()){
			echo '<script>alert(\''.$message.'\');</script>';
			$this->reload_without_query();
		}
	}


    public function OptionsForKeys_helper($keyName, $include_type=''){
        $array = [];
        foreach($this->initial_user_options as $key=>$val)
        {
			if (!is_array($val))
			{
				if ( empty($keyName) ){
					$array[$key]=$val;
				} 
				else if ( 
					   ($include_type==""      && $this->helpers->contains($key, $keyName) )
					|| ($include_type=="start" && $this->helpers->startsWith($key,$keyName))
					|| ($include_type=="end"   && $this->helpers->endsWith($key,$keyName))
				)
				{
					$array[$key]=$val;
				}
			}
        }
        return $array;
    }
    public function OptionsForKeys_table($keyName='', $include_type=''){
		?><table class="form-table"><td colspan="100%"><h3>Options</h3></td><?php $this->OptionsForKeys_Output();?></table><?php 
	}
    public function OptionsForKeys_Output($keyName='', $include_type=''){
		foreach ( $this->OptionsForKeys_helper($keyName,$include_type) as $key=>$val ) { 
			$value   = $this->opt($key);
			$is_bool = is_bool($value);  
			?>
			<tr class="def">
				<td scope="row">
					<code><?php echo $key;?></code>
				</td>
				<td scope="row">
					<?php echo '<input name="'.$this->plugin_slug.'['.$key.']" '. ($is_bool ? 'type="checkbox" value="1" '. checked($value, true, false) : 'type="text" value="'. $value .'" />');?>
				</td>
			</tr>
		<?php }
	}
    public function OptionsForKeys_update($keyName='', $include_type=''){
		foreach ( $this->OptionsForKeys_helper($keyName,$include_type) as $key=>$val ) { 
			$value   = $this->opt($key);
			$is_bool = is_bool($value);
			$this->opts[$key] = $is_bool ? isset($_POST[ $this->plugin_slug ][$key]) : sanitize_text_field( stripslashes($_POST[ $this->plugin_slug ][$key]) );   
		}
	}
    public function opt($key) {  return isset($this->opts[$key]) ? $this->opts[$key] : ''; }


	// navigation menu nav menu hooks: pastebin_com/BcGsVpe9

	// if post_exists query: https://goo.gl/aHZzv9

	public function send_error_mail($error){
		return wp_mail($this->static_settings['mail_errors'], 'wp plugin error at '. home_url(),  (is_array($error) ? print_r($error, true) : $error)  );
	}
	
		// unique func to flush rewrite rules when needed. if not hooked into wp_footer, hangs plugin options resaving 
	public function flush_rules_if_needed($temp_key="sample" ){
		// lets check if refresh needed
		$key="b".get_current_blog_id()."_". md5( ( is_file($temp_key) ? md5(filemtime($temp_key)) : $temp_key )    );
		if( !array_key_exists($key, $this->opts['last_updates']) || $this->opts['last_updates'][$key] < $this->opts['last_update_time']){
			$this->opts['last_updates'][$key] = $this->opts['last_update_time'];
			$this->update_opts();
			add_action('wp_footer', function(){ $this->helpers->flush_rules("js"); } );
		}
	}
	
	public function flush_rules_checkmark($redirect=false){
		flush_rewrite_rules();
		$this->opts['needs_flushing'] = true; $this->update_opts();
		if($redirect) {
			if ($redirect=="js"){ $this->helpers->js_redirect(); }   else { $this->helpers->php_redirect(); }
		}
	}
	public function flush_checkpoint(){
		if(isset($this->opts['needs_flushing']))
		{
			unset($this->opts['needs_flushing']);
			$this->update_opts();
			$this->helpers->flush_rules(true);
		}
	}
	
	public function shortcodes_initialize(){
		if(property_exists($this,'shortcodes'))
		{
			//enable shortcodes (if it's disabled)
			add_filter( 'widget_text', 'do_shortcode' );

			foreach($this->shortcodes as $name=>$val)
			{
				//add "name" manually as name
				$this->shortcodes[$name]['name']=$name; 
				$funcName = isset($this->shortcodes[$name]['name']['callback'])? $this->shortcodes[$name]['name']['callback'] : $name;
				add_shortcode($name, [$this, $funcName] );
			}
		}
	}

	
	/*  loader_wp_config.php
	// check if this file is included IN WP-CONFIG
			if (defined("include_my_custom_wpConfig")) {
				if (!defined("my_wp_cofnig_called")) { define("my_wp_cofnig_called", 1);
					define("WP_DEBUG", 	0); 
					define("WP_DEBUG_DISPLAY", 0);
					return;
				}
			} 
	*/


	public function get_phrases()
	{
		return $this->get_option_CHOSEN('`translated_phrases', []);
	}

	public function update_phrases($array=null)
	{
		if(!isset($array)) $array=$this->translation_phrases;
		return $this->update_option_CHOSEN('`translated_phrases', $array);
	}

	public function phrases_array()
	{ 
		$cont='';
		foreach( $this->plugin_files as $each)
		{
			$cont .= file_get_contents( $this->helpers->baseDIR.'/'. basename($each) );
		}
		preg_match_all( '/\$this\-\>phrase\((.*?)\)/si', $cont, $matches );
		$phrases_array = $this->get_phrases();
		foreach($matches[1] as $value) {
			$value=trim($value);
			//if not variable
			if(substr($value, 0, 1) != '$')
			{
				$sanitized_value = preg_replace("/[\"\']/", "", $value);
				$phrases_array[$sanitized_value] = $sanitized_value;
			}
		}
		return $phrases_array;
	}


	public function define_translations_exist(){
		//check if translations exist
		$last_vers  = get_site_option($this->slug . '_transl_lastvers');
		if( ! $last_vers || $last_vers != $this->static_settings["Version"] ){
			update_site_option($this->slug . '_transl_lastvers', $this->static_settings["Version"]);
			$res = !empty($this->phrases_array());
			update_site_option($this->slug . '_transl_exists', $res);
			return $res;
		}
		return get_site_option($this->slug . '_transl_exists');
	}	


	// settings page
	public function define_option_tabs(){
		if(!is_admin()) return;
			
		$this->options_tabs	= array_merge( 
			['Options'],
			( !empty($this->static_settings['display_tabs']) ? $this->static_settings['display_tabs'] : 
				array_merge(
					property_exists($this,'shortcodes') ? ['Shortcodes'] : [],
					$this->define_translations_exist() ? ["Translations & Phrases"] : [] 
				)
			),
			["Errors-Log & Reset"]  //( ! property_exists($this, 'errors_tab') || $this->errors_tab ? ['Errors log'] :  []  )
		);
	}

	public function options_tab($tabs_array =false){
		if(!$tabs_array)  $tabs_array = $this->options_tabs;
		$this->active_tab = $tabs_array[0];
		foreach($tabs_array as $each_tab){
			if (isset($_GET['tab']) && sanitize_key($each_tab)==$_GET['tab'])  
				$this->active_tab=$each_tab;
		}
		echo '<div class="nav-tab-wrapper customNav '. (false && empty($this->static_settings['display_tabs']) ? "displaynone" : "") .'">';
		foreach($tabs_array as $each_tab){
			$tab_TITLE = $each_tab=="Shortcodes" ? "Shortcodes & Api" : $each_tab;
			echo '<a  href="'.add_query_arg('tab', sanitize_key($each_tab) ).'" class="nav-tab '. sanitize_key($each_tab).' '. ($this->active_tab == $each_tab ? 'nav-tab-active  whiteback' : ''). '">'. __( $tab_TITLE).'</a>';
		}
		echo '</div>';
	}
	
	public function checkNonce($str1="mng_nonce", $str2="nonce_mng_" ){
		return !empty( $_POST[$str1] ) && check_admin_referer( $str2 . $this->slug, $str1);
	}
	
	public function opts_page_output_parent($args=false)
	{
		if(is_network_admin())
		{
			if( $this->checkNonce( "mng_nonce_ADM", "nonce_mng_ADM_" ) ) 
			{
				if( isset( $_POST[$this->slug]['managed_from_changer'] ) ){
					$val = $_POST[$this->slug]['managed_from_site']=='network' ;
					$this->network_managed_is_selected = $val;
					$this->updateNetworkedState($val);
					$this->helpers->js_redirect();
				}
			}
			?>
			<style>
			.networked_switcher_itsf_parent {position:relative;}
			.networked_switcher_itsf { box-shadow: 0px 0px 15px #329c1e; z-index: 3; min-width:210px; background:#ffa637; border-radius: 0 10px 0 190px;  border: 2px solid #80808094; padding:2px 10px 4px 30px; position: fixed; top: 32px; right: 0px;  line-height:1.2em; text-align: center; z-index: 77; }
			.networked_switcher_itsf .modeChooser1 { display: flex; flex-direction: row; justify-content: center; align-items: center; }
			@media all and (max-width: 850px) { .networked_switcher_itsf .modeChooser1 { flex-wrap:wrap;}  }
			.networked_switcher_itsf label { padding-right: 20px!important;  border-radius: 0px!important; margin: 2px !important;}
			.networked_switcher_itsf label.networkwide { border-radius: 100px 0px 0px 100px!important;}
			.networked_switcher_itsf label.subsite { border-radius: 0px 100px 100px 0!important;}
			.networked_switcher_itsf input[type="radio"] { margin:2px 0 0 -20px ;}
			</style>
			<div class="networked_switcher_itsf_parent">
				<div class="networked_switcher_itsf">
					<form method="POST" action="" name="networked_switcher_form">
					<?php _e("You can change from where you'd like to manage this plugin's settings page"); ?></b>
					<div class="modeChooser1">
				
						<label for="networkwide" class="networkwide button<?php echo $this->network_managed_is_selected ? "-primary":"";?> "><?php _e("Network dashboard (controls all subsites)");?></label>
						<input id="networkwide" onchange="managed_from_onchanger(this)" type="radio" name="<?php echo $this->slug;?>[managed_from_site]" value="network" <?php checked($this->network_managed_is_selected);?> />
						<label for="subsite" class="subsite button<?php echo !$this->network_managed_is_selected ? "-primary":"";?>"><?php _e("Each Sub-Site with own settings page");?></label>
						<input id="subsite" onchange="managed_from_onchanger(this)"  type="radio" name="<?php echo $this->slug;?>[managed_from_site]" value="subsite" <?php checked(!$this->network_managed_is_selected);?>  />
						<input type="hidden" name="<?php echo $this->slug;?>[managed_from_changer]" value="ok" />  
						<?php wp_nonce_field( "nonce_mng_ADM_".$this->slug, "mng_nonce_ADM" ); ?>
					</div>
					</form>
					<script>
					//jQuery(".modeChooser1").controlgroup();
					function managed_from_onchanger(e)
					{
						document.forms["networked_switcher_form"].submit();
						document.getElementById("wpbody").style.opacity = 0.1;
					}
					</script>
				</div>
			</div>
		<?php 
		}
		if ( (is_network_admin() && $this->network_managed_is_selected) || (!is_network_admin() && !$this->network_managed_is_selected) ){
			if(method_exists($this, 'opts_page_output')) 
				$this->opts_page_output();
		}
		else{
			echo '<div style="display: flex; background: white; flex-direction: column; max-width: 600px; margin: 100px auto; border-radius: 10px; padding: 30px;"><h1>'.__('Plugin is set to be managed per: <span class="perChosen">'. ($this->network_managed_is_selected ? "Network": "Sub-sites") ).'</span></h1></div>';
		}
	}
	

	public function settings_page_check_save(){
		if( $this->checkSubmission( 'nonce3'. $this->slug, 'nonceact3'. $this->slug ) )
		{
			if(!empty($_POST[$this->slug]) ) {
				$this->opts['last_update_time'] = time();
				$this->update_opts();
			}

			if(isset( $_POST[$this->slug]['clear_error_logs'] ) ){
				$this->helpers->clear_errorslog();
			}

			if(isset( $_POST[$this->slug]['reset_plugin_defaults'] ) ){
				$this->reset_plugin_to_defaults(); $this->helpers->js_redirect();
			}
			
			if(isset( $_POST[$this->slug]['import_settings_json'] ) ){
				$array = json_decode( stripslashes($_POST[$this->slug]['import_settings_json']), true);
				$this->update_opts($array);
				$this->helpers->js_redirect();
			}

			if(isset( $_POST[$this->slug]['update_transl_phrases'] ) ){
				$this->translation_phrases =  array_map('sanitize_text_field', $_POST[$this->slug]['translation_phrases']);
				$this->update_phrases( $this->translation_phrases ) ;
			}

			if(isset($_GET[$this->slug.'-remove-pro']) ) {
				delete_site_option($this->license_keyname());
				$this->helpers->js_redirect(remove_query_arg($this->slug.'-remove-pro'));
			}
		}
	}
	 
	public function settings_page_part($type)
	{
		if($type=="start")
		{
			$this->settings_page_check_save();
			?>
			<style>.myplugin .separate_block{ box-shadow: 0px 0px 20px black; margin:20px; padding:20px; } </style>
			<div class="clear"></div>
			<div class="<?php echo $this->myplugin_class;?>">

				<h1 class="plugin-title"><?php echo (array_key_exists('plugin_page_title', $this->opts) ? $this->opts['plugin_page_title'] : $this->opts['name']);?></h1> 
				<?php $this->options_tab();  ?>
				<!-- <h2 class="settingsTitle"><?php _e('Plugin Settings Page!');?></h2> --> 
					<div class="optwindow">
			<?php


			if ($this->active_tab == 'Options')
			{ 
				$this->customOptsOutput();
			} 

			// #########################################################
			if ($this->active_tab == "Shortcodes")
			{ 
				echo '<h1 class="shortcodes_maintitle">'. __('Shortcodes Usage').'</h1>';
				
				foreach($this->shortcodes as $key=>$value)
				{
					$this->helpers->shortcodes_table($key, $value);
					$this->helpers->shortcode_alternative_message($key);
				}


				echo '<div class="hooks_examples">';
				echo '<h1 class="shortcodes_maintitle">'. __('Available hooks (to modify from external functions)') .'</h1>';
				if ( property_exists($this, "hooks_examples") ) {
					foreach ($this->hooks_examples as $key=>$block){
						echo '<div class="hook_example_block '.$key.'">';
						if ($block['type']=='filter'){
							echo '<div class="description">'. __($block['description']) .':</div>';
							echo '<code>add_filter("'.$key.'", "yourFunc", 10, '. count($block['parameters'] ) .' );'."\r\n".'function yourFunc($'. implode(', $', $block['parameters'] ).') { ... return $'.$block['parameters'][0].';} </code>'; 
						}
						echo '</div>';
					}
				}
				echo '</div>';
			}
			// #########################################################


			
			// #########################################################
			if ($this->active_tab == "Translations & Phrases")
			{ ?>
				<div class="translations_page">
					<form method="post" action="">
						<?php _e("Here will show up all phrases that are outputed on your site fron-end by this plugin, so you can translate/customize them."); ?>
						<table class="translations_table">
							<tbody>
								<?php 
								$phrases_arr = $this->phrases_array();
								$phrases = $this->translation_phrases;
								if(is_array($phrases_arr)){
									foreach ($phrases_arr as $key=>$value){
										$value = array_key_exists($key, $phrases) ? $phrases[$key] : $key;
										echo '<tr>';
										echo '<td>'. $key.'</td><td><input type="text" name="'.$this->slug.'[translation_phrases]['.$key.']" value="'. $value .'" /></td>';
										echo '</tr>';
									}
								}
								?>
							</tbody>
						</table>
						<input type="hidden" name="<?php echo $this->slug;?>[update_transl_phrases]" value="ok" />
						<?php
						wp_nonce_field( "nonce_".$this->slug);
						submit_button(  __( 'Save' ), 'button-secondary', '', true  );
						?>
					</form>
				</div>
			<?php
			}
			// #########################################################

			if ( ! ($last_options_json = $this->get_transient_chosen('`_plugin_last_options_json') ) )
			{
				$last_options_json =  $this->opts;
				$this->update_transient_chosen('`_plugin_last_options_json', $last_options_json, DAY_IN_SECONDS * 2);
			}
			
			
			

			// #########################################################
			if ($this->active_tab == "Errors-Log & Reset")
			{ ?>
				<div class="errors_page">
					<div class="errors_table_container">
						<table class="errors_log">
							<style>
							.myplugin .errors_page .errors_table_container { max-height: 400px;  overflow-y: scroll;  border: 1px solid #b5b5b5;}
							.myplugin .errors_page table {border-collapse: collapse;}
							.myplugin .errors_page table tr > * { border: 1px solid #c7c7c7; padding: 3px 5px; }
							.myplugin .errors_page .errors_log tr{transition:0.2s all;}
							.myplugin .errors_page .errors_log tr.headerRow{color:orange;}
							.myplugin .errors_page .errors_log tr:hover{background:#fdf7f7;}
							.myplugin .errors_page .errors_log td{min-width: 10px;} 
							.myplugin .errors_page .errors_log td pre { white-space: pre-wrap; word-wrap: break-word; }
							.myplugin .errors_page table .row_id { min-width:40px; width: 50px; max-width:50px; }
							.myplugin .errors_page table .row_gmdate { min-width:40px; width:80px; max-width:80px; }
							.myplugin .errors_page table .row_function_name { min-width:120px; width: 150px; max-width:150px; }
							.myplugin .import-json-settings{ width:100%; height:100px; }
							</style>
							<tbody>
								<?php
								//$this->log("asdddd", "");  
								$errors = $this->helpers->get_errorslog();
								if(!empty($errors))
								{
									rsort($errors);  //reverse order, last added to top
									$column_count =  count( $keys = array_keys( ((array)$errors[0]) )); 
									echo '<tr class="headerRow">'; for($i=0; $i<$column_count; $i++) echo "<th class='row_{$keys[$i]}'>$keys[$i]</th>";echo '</tr>'; 

									$j=0;
									foreach ($errors as $each_err) {
										//$j++; if ($j>$this->logs_table_maxnum) break;
										$each_err= (array) $each_err;
										echo '<tr>';
										for($i=0; $i<$column_count; $i++){
											$out='';
											$current = $each_err[ array_keys($each_err)[$i]];
											if (!empty($current) )
											{
												$out = $current;
											}
											echo '<td class="row_'.$keys[$i].'"><pre>'. htmlentities($out).'</pre></td>';
										}
										echo '</tr>';
									}

								}
								?>
							</tbody>
						</table>
					</div>
					

					<div class="clear-errors-log centered separate_block">
						<form method="post" action="">
							<input type="hidden" name="<?php echo $this->slug;?>[clear_error_logs]" value="ok" />
							<?php  $this->nonceSubmit( __( 'Clear Errors Log'),  "nonce3".$this->slug, 'nonceact3'.$this->slug) ; ?>
						</form>
					</div>

					
					<div class="plugin-export-import centered separate_block">
						<h2><?php echo __( 'Export settings' );?></h2>
						<textarea class="export_settings"><?php echo htmlentities(json_encode($this->opts));?></textarea>
						<!-- <div style="display:none;">(<?php _e("Backuped settings of 48 hours ago:");?><textarea><?php echo htmlentities(json_encode($last_options_json));?></textearea>) </div> -->
					</div>
					

					<div class="plugin-export-import centered separate_block">
						<h2><?php echo __( 'Import settings' );?></h2>
						<form method="post" action="">
							<textarea class="import-json-settings" name="<?php echo $this->slug;?>[import_settings_json]"></textarea>
							<?php  $this->nonceSubmit( __( 'Import (overwrite) settings !'),  "nonce3".$this->slug, 'nonceact3'.$this->slug) ; ?>
						</form>
					</div>


					<div class="plugin-reset-defaults centered separate_block">
						<form method="post" action="">
							<input type="hidden" name="<?php echo $this->slug;?>[reset_plugin_defaults]" value="ok" />
							<?php  $this->nonceSubmit( __( 'Reset plugin options to defaults' ),  "nonce3".$this->slug, 'nonceact3'.$this->slug) ; ?>
						</form>
					</div>


				</div>
			<?php
			}
			// #########################################################
			
		}

		
		elseif ($type=="end")
		{ ?>
				</div><!-- optwindow -->
				<?php $this->endStyles();?>
			</div><!-- myplugin -->
		<?php
		}
	}



	public function customOptsOutput() {
		if (!$this->helpers->extra_options_enabled) return;
		$all_opts=$this->get_my_site_option();
		//if updated
		if (isset($_POST['securit_noncee223'])){    
			$this->NonceCheckk('securit_noncee223','myopts_exs2');

			$all_opts= $this->sanitize_text_field_recursive( $_POST['my'] ); 
			$this->update_my_site_options($all_opts);
		}

		?>
		<form action="" method="POST" class="custom_opts">
			<?php 	//wp_editor( htmlspecialchars_decode(get_option('nwsMTNG_notes_'.$laang)), 'mtng_notes_styl_ID'. $laang, $settings = array('textarea_name'=>'nwsMTNG_notes_'. $laang,  'editor_class' => "editoor_nws_note")); ?>
			<h2>Extra options   (0= OFF,  1= ON)</h2>
			<?php
			$this->input_fields_from_array($all_opts,'my');
			?>
			<div class="my_save_divv" style="text-align:center; padding:10px;  z-index:999; "><input type="submit" class="my_SUBMITT" value="SAVE" /></div> <?php echo $this->NonceFieldd('securit_noncee223','myopts_exs2'); ?> 
		</form>
		<?php 
	}


	
	public function endStyles($external=false)
	{ ?>
		<?php 
		if ($external===false) {
			//
		}
		elseif ($external===true) {
			echo '<div class="'.$this->myplugin_class.'">'; 
		}
		?>

	<style>
		.myplugin { margin: 20px 20px 0 0; line-height:1.2;  max-width:100%; display:flex; flex-wrap:rap; justify-content:center; flex-direction:column; padding: 20px; border-radius: 100px; }
		.myplugin * { position:relative;}
		.myplugin code {font-weight:bold; padding: 3px 5px;  display: inline-block;}
		.myplugin >h2 {text-align:center;}
		.myplugin h1,
		.myplugin h2,
		.myplugin h3 {text-align:center; margin: 0.5em 1em 1em;}
		.myplugin table tr { border-bottom: 1px solid #cacaca; }
		.myplugin table td {min-width:50px;}
		.myplugin .form-table  { border: 1px solid #cacaca; padding:2px;  }
		.myplugin .form-table td { padding: 15px 5px;  }
		.myplugin .form-table th { padding: 20px 10px 20px 10px; } 
		.myplugin p.submit {text-align: center;}
		.myplugin .optwindow { border: 1px solid #b5b5b56e;  padding: 10px; border-width: 0px 1px 1px 1px; border-radius: 0px 0px 30px 30px; }
		zz.myplugin input[type="text"]{width:100%;}
		.myplugin .additionals{ display:flex;  font-family: initial; font-size: 1.5em;   text-align:center; margin: 25px 5px 5px; padding: 5px; background: #efeab7;  padding: 5px 0 0 20px;  border-radius: 0% 20px 140px 90%; }
		z.myplugin .additionals:before { content: ""; position: absolute; top: 5%; left: 5%; height: 90%; width: 90%; background: #a222ff61; border-radius: 60% 60% 770% 110px;opacity: 0.6; transform: rotatez(-2deg); }
		.myplugin .additionals:after { content: ""; position: absolute; top: 5%; left: 5%; width: 90%; background: #6bd5ff45; border-radius: 10% 40% 20% 110px; opacity: 0.6; transform: rotatez(3deg); z-index: 0; height: 100px; }
		.myplugin .additionals a{font-weight:bold;font-size:1.1em; color:blue;}
		.myplugin .in_additional { z-index:3; width: 700px; background: #ffffff00; box-shadow: 0px 0px 20px #7d7474; border-radius: 30px; padding: 11px; margin: 0 auto; margin: 20px auto; }
		z.myplugin .additionals li { list-style-type: circle; list-style-type: circle; float: left; margin: 5px 0 5px 40px;}
		.myplugin .whiteback { background:white; border-bottom:1px solid white; }
		.myplugin.version_pro .donations_block, .myplugin.version_not_pro .donations_block { display:none; }
		.myplugin .donation_li a{  color: #d47b09; }
		.myplugin .customNav {margin: 0 0 0 0;}
		.myplugin .customNav .errors-logreset{ color: #903e4c; font-size: 0.7em; margin: 0.9em 0 0 0; font-style: italic; opacity:  0.6;  float:right;}
		.myplugin .customNav .nav-tab{ border-radius: 60% 30% 5% 0px; }
		.myplugin .customNav .nav-tab-active{ color: #43ceb5; pointer-events: none; }
		.myplugin .freelancers {font-style: italic; font-family: arial; font-size: 0.9em; margin: 15px; padding: 10px; border-radius: 5px; opacity: 0.7; }
		.myplugin .freelancers a{}
		.myplugin .button { top: -4px; }
		.myplugin .red-button { background: #ec5d5d;   zbackground:  #ffdfdf;}
		.myplugin .pink-button { background: pink; }
		.myplugin .green-button { background: #44d090; }
		.myplugin .float-left { float:left; }
		.myplugin .float-right { float:right; }
		.myplugin .displaynone { display:none; }
		.myplugin .clearboth { clear:both;  height: 20px;  }
		.myplugin .noinput { border: none!important; box-shadow:none!important; width:auto!important; display:inline-block!important; font-weight:bold; }
		.myplugin .translations_table { margin: 20px 0 0 30px; border-collapse: collapse;}
		xxx.ui-widget-overlay { background: #000000; opacity: 0.8; filter: Alpha(Opacity=80); }
		xxx.ui-dialog {z-index: 9222!important; }
		.myplugin .alertnative_to_shortcodes {margin:50px 10px; box-shadow: 0px 0px 20px grey; padding: 40px; }

		.myplugin .ui-sortable-handle.ui-sortable-helper{ cursor: move; }
		.myplugin textarea.fullwidth1{ display:block; width:100%; height:100px; }
		.myplugin textarea.fullwidth2{ display:block; width:100%; height:200px; }
		.myplugin textarea.fullwidth3{ display:block; width:100%; height:300px; }
		.myplugin textarea.fullwidth4{ display:block; width:100%; height:400px; }
		.myplugin textarea.halfwidth1{ display:block; width:50%; height:100px; margin: 0 auto;}
		.myplugin textarea.halfwidth2{ display:block; width:50%; height:200px; margin: 0 auto;}
		.myplugin textarea.halfwidth3{ display:block; width:50%; height:300px; margin: 0 auto;}
		.myplugin textarea.halfwidth4{ display:block; width:50%; height:400px; margin: 0 auto;}

		.myplugin .hook_example_block { margin: 10px 0; line-height: 1.4em; }
		.myplugin a,.myplugin a.button { display: inline; }
		.myplugin .disabled { pointer-events: none; }
		.myplugin .nonclickable { pointer-events: none; }
		.myplugin .hiddentransparent {position: relative; width: 1px; height: 1px; color: transparent; display: inline; overflow: hidden; }

		.myplugin .review_block{ float:right; }
		.myplugin .review_block a{ float:right; font-size:20px; font-weight:bold; }
		.myplugin .review_block .stars{ height:30px; }
		.myplugin .review_block span.leaverating {position:absolute; z-index:4; margin:0 auto; text-align:center; width:auto; white-space:nowrap; top:15px; color:#000000de; font-size:0.8em; left:20px; text-shadow:0px 0px 25px black;}
		.myplugin .review_block img.stars{ height:30px; vertical-align:middle; }

		.myplugin .shortcode_atts{ color:#b900f3; }
		.myplugin .shortcodes_maintitle { font-style:italic; }
		.myplugin table .shortcode_tr_descr { font-weight:bold; color:black; }
		.myplugin .site_author_block{ text-align:center; font-size:0.8em; }
		.myplugin .site_author_block a{ text-decoration:none; color:black;}
		.myplugin .shortcodes_block { box-shadow: 0px 0px 15px #00000066; padding: 10px 0 0 0; margin: 20px 0;}
		.myplugin .shortcodes_block z.h3{ color:#f34500; text-align:center; }

		.myplugin .datachange-save-button{ display:none; }
		.myplugin ._save_button{ display:none; }
		.myplugin .numeric_input{ width:50px; font-weight:bold;}
		.myplugin .form-table td { vertical-align: top; }
		
		.myplugin .centered {display: flex; justify-content: center; align-items: center; flex-direction: column; }
		.myplugin .centered.horizontal { flex-direction: row; }
		.myplugin .centered.vertical { flex-direction: column; }

		.myplugin .centered{ text-align:center; }
		.myplugin .flexrow   { display:flex; flex-direction:row; }
		.myplugin .flexcolumn{ display:flex; flex-direction:column; }
		.myplugin .separate_block{ margin:22px; padding:5px; box-shadow:0px 0px 3px black; }
		.myplugin .centered-float { position: fixed; bottom: 0; margin: 0 auto; left: 0; right: 0; z-index: 333;}
		
		ZZZ_example_jquery_u_ {url: (https://github.com/jquery/jquery-ui/tree/master/themes/base); }
		.ui-widget.ui-widget-content { border: 1px solid #c5c5c5; }
		.ui-corner-all { border-radius: 3px; }
		.ui-widget-header { border: 1px solid #dddddd; background: #e9e9e9; color: #333333; font-weight: bold; }
		
		.ui-dialog { padding: .2em; }
		
		.ui-tooltip {	padding: 8px;	position: absolute;	z-index: 9999;	max-width: 300px;}
		body .ui-tooltip {	border-width: 2px; border:1px solid #e7e7e7; box-shadow:0px 0px 3px gray; }
	</style>

	<div class="clear"></div>
	<div class="newBlock additionals">
	
		<?php if ( $this->static_settings['show_donation_message'] ) { ?>
		<div class="in_additional">
			<h4></h4>
			<h3><?php _e('More Actions');?></h3>
			<ul class="donations_block">
				<li class="donation_li">
					<!-- <?php _e('If you found this plugin useful, any donation is welcomed');?> :  $<input id="donate_pt" type="number" class="numeric_input" value="4" /> <button onclick="tt_donate_trigger(event);"/><?php _e('Donate');?></button> -->
					<?php _e('If you found this plugin useful, any amount of');?> <?php echo $this->paypalDonationButton();?> <?php _e(' is welcomed');?> 
					<script>
					function tt_donate_trigger(e)
					{
						e.preventDefault();
						var url= '<?php echo $this->static_settings['donate_url'];?>'; //+ '/'+ document.getElementById('donate_pt').value
						window.open(url,'_blank');
					}
					</script>
					<!-- <a href="%s" class="button" target="_blank">donation</a> -->
				</li>
			</ul>
			<ul>
				<li>
					<?php if (false) printf(__('You can check other useful plugins at: <a href="%s">Must have free plugins for everyone</a>'),  $this->static_settings['musthave_plugins'] ).'.';	?>
				</li>
			</ul>
		</div>
		<?php } ?>


		<?php if($this->static_settings['show_rating_message']) { ?>
		<div class="review_block">
			<a class="review_leave" href="<?php echo $this->static_settings['wp_rate_url'];?>" target="_blank">
				<span class="leaverating"><?php _e('Rate plugin');?></span>
				<img class="stars" src="<?php echo $this->helpers->imageSvg("rating-transparent");?>" />
			</a>
		</div>
		<?php } ?>

	</div>

	<div class="clear"></div>
	<script> tt_ajax_action = '<?php echo $this->plugin_slug_u;?>_all';</script>
	
	<script>
	function pro_field(targetEl){
		var is_pro = <?php echo $this->unregistered_pro() ? "true" : "false";?>; 
		if(is_pro) {
			targetEl.attr("data-pro-overlay","pro_overlay");
		}
	}
	</script> 

	<?php 
	$this->purchase_pro_block();  
	?>

	
	<!-- Show "SAVE" button after input change,  type="text" id="manual_pma_login_url" data-onchange-save="true"  data-onchange-hide=".type_manual" name=" -->
	<div class="datachange-save-button">
		<?php submit_button( false, 'button-primary _save_button', '', true,  $attrib=  ['id' => '_save_button'] ); ?> 

		<script>
		(function($){ 

			// save button show/hide
			var save_button=$('.myplugin #_save_button');
			$('.myplugin [data-onchange-save]').on("change, input", function(e){
				save_button.insertAfter( $(this) );
				save_button.show();
				save_button.css( { 'margin-left': "-"+(save_button.css("width")), 'position':'relative', 'top':'0px', 'left':save_button.css("width")  });
				var target=$(this).attr("data-onchange-hide"); if(target && target.length) {   $( target ).css("visibility","hidden");   }
			});

			//noinput types
			if($(".noinput").length) $(".noinput").attr('size', $(".noinput").val().length);
		})(jQuery); 
		</script> 
	</div>
	
	<?php $this->donations_trigger_popup(); ?>
	<?php $this->notes_field(); ?>

	<?php if ($external===true) echo '</div>'; ?>
	<?php
	}
	 

	public function admin_scripts($hook)  //i.e. edit.php
	{
		if($this->is_this_settings_page()){
			$this->admin_scripts_out($hook);
		}
	}

	// https://github.com/WordPress/WordPress/blob/master/wp-includes/script-loader.php
	public function admin_scripts_out($hook)  //i.e. edit.php
	{
		$where='admin'; //admin|wp
		if (property_exists($this,'disable_admin_scripts_load')) return;
		
		$this->helpers->register_stylescript($where, 'script', 'jquery');

		//jquery ui core
		//$this->helpers->register_stylescript($where, 'script',	'jquery-ui-core');
		
		//jquery ui EFFECTS
		$this->helpers->register_stylescript($where, 'script',	'jquery-effects-core');

		//jquery ui DIALOG
		$this->helpers->register_stylescript($where, 'script',	'jquery-ui-dialog');
		$this->helpers->register_stylescript($where, 'style',	'wp-jquery-ui-dialog');	
		// download and include locally:	'ui-css', 'https://code (dot) jquery (dot) com/ui/1.12.1/themes/base/jquery-ui.css',  false,  '1.1');

		$this->helpers->register_stylescript($where, 'script',	'jquery-ui-tooltip');
 
		//add_action('admin_footer', function() { <script></script> } );
	}

	
	public function admin_head_func()
	{ 
		if( defined("PuvoxLibrary_scripts_loaded") ) return;  define("PuvoxLibrary_scripts_loaded", true); 
		?>
		<script>
		//window.onload REPLACEMENT
		// window.addEventListener ? window.addEventListener("load",yourFunction,false) : window.attachEvent && window.attachEvent("onload",yourFunction);
		PuvoxLibrary =
		{
			// check for Ajax calls from front-end
			backend_call(actName, data, callback, displayLoader)
			{
				var self=this;

				data["action"]    = tt_ajax_action;
				data["_wpnonce"]  = '<?php echo wp_create_nonce( "Puvox_BackendCallJS");?>';
				data["act"]       = actName;
				var displayLoader = displayLoader || false;
				if (displayLoader)
					PuvoxLibrary.spinner(true);
				jQuery.post
				(
					ajaxurl,
					data,
					function(response){   
					}
				)
				.done(function(res) {
					if (displayLoader)
						PuvoxLibrary.spinner(false);
					callback(self.responseStringify(res), true, res);
				})
				.fail(function(res) {
					if (displayLoader)
						PuvoxLibrary.spinner(false);
					callback(self.responseStringify(res), false, res);
				})
				//.always(function(res) { })
			},

			reload_this_page(){
				window.location = window.location.href;
			},

			is_object(variable){
				return typeof variable === 'object' && variable !== null;
			},
			responseStringify(obj_or_text){
				return ! this.is_object(obj_or_text) ? obj_or_text : ( 'responseText' in obj_or_text ? obj_or_text.responseText : JSON.stringify(obj_or_text) );
			},

			//show Spinner (loader-waiter)
			spinner(action)
			{
				var spinner_id = "tt_spinner";
				if(action)
				{
					var div=
					'<div id="'+spinner_id+'" style="background:black; position:fixed; height:100%; width:100%; opacity:0.9; z-index:9990;   display: flex; justify-content: center; align-items: center;">'+
						'<div style="">Please Wait...</div>'+
					'</div>';
					document.body.insertAdjacentHTML("afterbegin", div);
				}
				else
				{
					var el = document.getElementById(spinner_id);
					if (el) {
						el.parentNode.removeChild(el);
					}
				}
			},

			//shorthand for jQuery dialog
			dialog: function(message)
			{
				jQuery('<div class="ttDialog">'+message+'</div>').dialog({
					modal: true,
					width: 500,
					close: function (event, ui) {
						jQuery(this).remove();	// Remove it completely on close
					}
				});
			},
			//shortand for the same, to remember easily
			message: function(message)
			{
				return PuvoxLibrary.dialog(message);
			},



			// make an element field to blink/animate
			blink_field : function (fieldObj)
			{
				fieldObj.animate({backgroundColor: '#00bb00'}, 'slow').animate({backgroundColor: '#FFFFFF'}, 'slow');
			},


			// hide content if chosen radio box not chosen  
			radiobox_onchange_hider : function (selector,desiredvalue, target_hidding_selector, SHOW_or_hide)
			{
				var SHOW_or_hide = SHOW_or_hide || false;
				if( typeof roh_dropdown_objs == 'undefined') { roh_dropdown_objs = {}; } 
				if( typeof roh_dropdown_objs[selector] == 'undefined' ){
					roh_dropdown_objs[selector] = true ; var funcname= arguments.callee.name;
					//jQuery(selector).change(function() { window[funcname](selector,desiredvalue, target_hidding_selector, SHOW_or_hide);	});
					jQuery(selector).change(function() { PuvoxLibrary.radiobox_onchange_hider(selector,desiredvalue, target_hidding_selector, SHOW_or_hide);	});
				}
				var x = jQuery(target_hidding_selector);
				if( jQuery(selector+':checked').val() == desiredvalue)	{ if(SHOW_or_hide)	x.show(); else x.hide(); } 
				else 													{ if(SHOW_or_hide)	x.hide(); else x.show(); }
			},

			// hide content if chosen radio box not chosen  
			checkbox_onchange_hider : function (selector, when_checked, destination_hidding_selector)
			{
				var x=function(target, destination){
					if(   (when_checked && jQuery(target).is(':checked'))  || (!when_checked && jQuery(target).not(':checked'))  ) {
						jQuery(destination).show();
					} else {
						jQuery(destination).hide();
					}
				};
				x(selector, destination_hidding_selector);
				jQuery(selector).click( function(e){ x(e.target, destination_hidding_selector); } ); 
			}
		};


		</script>
		<?php
	}
	
	//move uploaded addon to it's folder : https://pastebin_com/enqb2GFh

} // class 
#endregion


//==========================================================================================================
//========================================== PLUGIN SPECIFIC PARTS =========================================
//==========================================================================================================
 
} // #NAMESPACE