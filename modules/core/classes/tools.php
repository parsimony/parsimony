<?php

/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 *  @authors Julien Gras et Benoît Lorillot
 *  @copyright  Julien Gras et Benoît Lorillot
 *  @version  Release: 1.0
 * @category  Parsimony
 * @package core\classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * Tools Class
 * Here are useful methods of Parsimony
 */
class tools {

    /**
     * Convert relative URL to Absolute URL 
     * @static function
     * @param string $txt
     * @param string $basePath
     * @return string
     */
    public static function absolute_url($txt, $basePath) {
	$needles = array('href="', 'src="', 'url("', 'background="');
	$new_txt = '';
	if (substr($basePath, -1) != '/')
	    $basePath .= '/';
	$new_base_url = $basePath;
	$base_url_parts = parse_url($basePath);
	foreach ($needles as $needle) {
	    while ($pos = strpos($txt, $needle)) {
		$pos += strlen($needle);
		if (substr($txt, $pos, 7) != 'http://' && substr($txt, $pos, 8) != 'https://' && substr($txt, $pos, 6) != 'ftp://' && substr($txt, $pos, 9) != 'mailto://') {
		    if (substr($txt, $pos, 1) == '/')
			$new_base_url = $base_url_parts['scheme'] . '://' . $base_url_parts['host'];
		    $new_txt .= substr($txt, 0, $pos) . $new_base_url;
		} else {
		    $new_txt .= substr($txt, 0, $pos);
		}
		$txt = substr($txt, $pos);
	    }
	    $txt = $new_txt . $txt;
	    $new_txt = '';
	}
	return $txt;
    }

    /**
     * Copy an entire dir to another
     * @static function
     * @param string $dir2copy
     * @param string $dir_paste
     */
    public static function copy_dir($dir2copy, $dir_paste) {
	$dir2copy = './' . $dir2copy;
	$dir_paste = './' . $dir_paste;
	if (is_dir($dir2copy)) {
	    if ($dh = opendir($dir2copy)) {
		while (($file = readdir($dh)) !== false) {
		    if (!is_dir($dir_paste))
			mkdir($dir_paste, 0777
			);
		    if (is_dir($dir2copy . $file) && $file != '..' &&
			    $file != '.')
			self::copy_dir($dir2copy . $file . '/', $dir_paste . $file . '/');
		    elseif ($file != '..' && $file != '.')
			copy($dir2copy . $file, $dir_paste . $file);
		}
		closedir($dh);
	    }
	}
    }

    /**
     * Sanitize a String in order to generate clean URL
     * @static function
     * @param string $url
     * @return string
     */
    public static function sanitizeString($url) {
        $url = mb_strtolower($url, 'UTF-8');
        $url = iconv('UTF-8', 'ASCII//TRANSLIT', $url);
        $url = str_replace(array("'", '"', ';', ',', ':', ' ', '_'), '-', $url);
        $url = preg_replace("/[^a-z0-9-]/", '', $url);
	$url = str_replace('---', '-', $url);
        $url = str_replace('--', '-', $url);
	$url = trim($url, '-');
	return $url;
    }
    
        /**
     * Sanitize a String in order to generate clean Name
     * @static function
     * @param string $name
     * @return string
     */
    public static function sanitizeTechString($name) {
        $name = preg_replace('@[^a-zA-Z0-9_-]@', '', $name);
	return $name;
    }

    /**
     * Create Directory event if parent directory doesn't exist
     * @static function
     * @param string $directory
     * @param integer $mask optional
     * @return bool
     */
    public static function createDirectory($directory, $mask=0755) {
	if (!file_exists($directory))
	    return mkdir($directory, $mask, TRUE);
    }
    
    /**
     * Test Syntax Error via exec or eval
     * @static function
     * @param string $code
     * @return bool
     */
    public static function testSyntaxError($code,$vars = array()){
        ob_start();
        if(!empty($vars)) extract($vars);
        /* Test for parse or syntax error  (ex: dgedgbsggb )  */
        $return = @eval('return TRUE;?>' . $code . '<?php ');
	if ( $return === false && ( $error = error_get_last()) ) {
		return $error;
	}else{
            /* If there's no parse or syntax error, Test for E_PARSE error (ex: require 'inexistant file'; ) */
            $return = @eval('?>' . $code . '<?php ');
            if ( $return === false && ( $error = error_get_last()) ) {
		return $error;
            }else{
                /* If there's no E_PARSE error, Test for warning or notice error (ex: strstr(); )  */
                try {
                     $return = eval('?>' . $code . '<?php ');
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
               
                if ( $return === false && ( $error = error_get_last()) ) {
                    return $error;
                }
            }
	}
        ob_clean();
        return FALSE;
    }

    /**
     * Replace native file_put_contents function in order to add backup feature
     * @static function
     * @param string $file
     * @param string $content
     * @param bool $backup optional
     * @return bool
     */
    public static function file_put_contents($file, $content, $backup=true) {
	if ($backup && defined('PROFILE')) {
	    $dir_backup = 'var/backup/' . PROFILE . '/' . dirname($file) . '/';
	    if (!is_dir($dir_backup))
		self::createDirectory($dir_backup);
	    file_put_contents($dir_backup . basename($file) . '-' . time() . '.bak', $content);
            $delest = glob($dir_backup.'*.bak');
            if(is_array($delest)){
                foreach ($delest as $filename) {
                    if ( filemtime($filename) <= time()-60*60*24*4 ) {
                        unlink($filename);
                    }
                }
            }
	}
	self::createDirectory(dirname($file));
	if(empty($content)) $content = ' ';
	if (file_put_contents($file, $content)) { 
	    return TRUE;
	} else {
            throw new \Exception(t('Can\'t write this file : '.$file, FALSE));
        }
    }

    /**
     * Remove a dir and all his child dir ...etc
     * @static function
     * @param string $dir
     * @return bool
     */
    public static function rmdir($dir) {
	if (is_dir($dir)) {
	    $objects = scandir($dir);
	    foreach ($objects as $object) {
		if ($object != '.' && $object != '..') {
		    if (filetype($dir . '/' . $object) == 'dir')
			self::rmdir($dir . '/' . $object); else
			unlink($dir . '/' . $object);
		}
	    }
	    reset($objects);
	    return rmdir($dir);
	}
    }

    /**
     * Truncate a string
     * @static function
     * @param string $str
     * @param integer $maxCharacter
     * @param string $after optional
     * @return string
     */
    public static function truncate($str, $maxCharacter, $after = '...') {
	if (strlen($str) > $maxCharacter) {
	    $str = substr($str, 0, $maxCharacter);
	    return $str . $after;
	}
	return $str;
    }
    
    /**
     * Serialize an object in a file
     * @return bool
     */
    public static function serialize($filename,$obj) {
       return \tools::file_put_contents($filename. '.obj', serialize($obj));
    }
    
    /**
     * Unserialize an object of a file
     * @return bool
     */
    public static function unserialize($filename) {
        return unserialize(file_get_contents($filename. '.obj'));
    }
    
    public static function getClassInfos($reflect) {
        if(!($reflect instanceof \ReflectionClass)) $reflect = new \ReflectionClass($reflect);
        $com = $reflect->getDocComment();
        preg_match_all("/@([^\s]+) (.*)\n/", $com, $matchs, PREG_SET_ORDER); //capture comments
	$infos = array();
	foreach ($matchs as $match) {
	    $infos[$match[1]] = $match[2];
	}
        return $infos;
    }
    
    /**
     * Reset a new password to an user and send by email
     * @param $userMail mail
     */
    public static function sendMail($to, $from, $replyTo, $subject, $body) {
        include 'lib/phpmailer/class.phpmailer.php';
        $mailer = new \PHPMailer();

        if(\app::$config['mail']['type'] == 'smtp'){
            $mailer->IsSMTP();
            $mailer->Host = \app::$config['mail']['server'];
            $mailer->Port = \app::$config['mail']['port'];
        }elseif ( \app::$config['mail']['type']=='sendmail') {
            $mailer->IsSendmail();
        }elseif ( \app::$config['mail']['type']=='qmail') {
            $mailer->IsQmail();
        }
	if(strstr($to,',') !== FALSE){
	    $multi = explode(',',$to);
	    foreach($multi AS $addr){
		$mailer->AddAddress(trim($addr));
	    }
	}else{
	    $mailer->AddAddress($to);
	}
        $mailer->SetFrom($from);
        $mailer->AddReplyTo($replyTo);
        $mailer->Subject = $subject;
        $mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        $mailer->MsgHTML($body);

        return $mailer->Send();
        
    }
    
    /**
     * Sanitize a string come from fied WYSIWYG or block WYSIWYG
     * @param $str string to sanitize
     * @param $plugins string list of wysiwyg's plugins separated by comma
     * @return string sanitized html
     */
    public static function sanitize($str, $plugins = 'bold,underline,italic,justifyLeft,justifyCenter,justifyRight,strikeThrough,subscript,superscript,orderedList,unOrderedList,undo,redo,outdent,indent,removeFormat,fontName,fontSize,createLink,unlink,formatBlock,foreColor,hiliteColor,insertImage') {
    $allowedTags = array('div' => array( 'dir', 'lang', 'style', 'title'),
	'span' => array( 'dir', 'lang', 'style', 'title'),
	'p' => array( 'dir', 'lang', 'style', 'title'),
	'pre' => array( 'dir', 'lang', 'style', 'title'),
	'br' => array( 'style'));

    $allowedStyles = array();

    $pluginsConf = array('bold' => array('allowedStyles' => array('font-weight' => '.*')),
	'underline' => array('allowedStyles' => array('text-decoration' => '.*')),
	'italic' => array('allowedStyles' => array('font-style' => '.*')),
	'justifyLeft' => array('allowedStyles' => array('text-align' => '.*')),
	'justifyCenter' => array('allowedStyles' => array('text-align' => '.*')),
	'justifyRight' => array('allowedStyles' => array('text-align' => '.*')),
	'strikeThrough' => array('allowedStyles' => array('text-decoration' => '.*')),
	'subscript' => array('allowedStyles' => array('vertical-align' => '.*')),
	'superscript' => array('allowedStyles' => array('vertical-align' => '.*')),
	'orderedList' => array('allowedTags' => array('ol' => array( "style", "dir", "lang", "title"),'li' => array( "style", "dir", "lang", "title")),
	    'allowedStyles' => array('list-style' => '.*', 'list-style-image' => '.*', 'list-style-position' => '.*', 'list-style-type' => '.*')),
	'unOrderedList' => array('allowedTags' => array('ul' => array("style", "dir", "lang", "title"),'li' => array( "style", "dir", "lang", "title")),
	    'allowedStyles' => array('list-style' => '.*', 'list-style-image' => '.*', 'list-style-position' => '.*', 'list-style-type' => '.*')),
	'outdent' => array('allowedTags' => array('blockquote' => array("style", "dir", "lang", "title")),
	    'allowedStyles' => array('margin' => '.*', 'border' => '.*', 'padding' => '.*')),
	'indent' => array('allowedTags' => array('blockquote' => array("style", "dir", "lang", "title")),
	    'allowedStyles' => array('margin' => '.*', 'border' => '.*', 'padding' => '.*')),
	'createLink' => array('allowedTags' => array('a' => array( "style", "dir", "lang", "title", "accesskey", "tabindex", "charset", "coords", "href", "hreflang", "name", "rel", "rev", "shape", "target"))),
	'formatBlock' => array('allowedTags' => array('h1' => array("style","dir","lang","title"),
							'h2' => array("style","dir","lang","title"),
							'h3' => array("style","dir","lang","title"),
							'h4' => array("style","dir","lang","title"),
							'h5' => array("style","dir","lang","title"),
							'h6' => array("style","dir","lang","title"))),
	'foreColor' => array('allowedStyles' => array('color' => '.*')),
	'hiliteColor' => array('allowedStyles' => array('background-color' => '.*')),
	'fontName' => array('allowedStyles' => array('font-family' => '.*')),
	'fontSize' => array('allowedStyles' => array('font-size' => '.*')),
	'insertImage' => array('allowedTags' => array('img' => array("style", "dir", "lang", "title", "src", "alt", "title")),'allowedStyles' => array('padding' => '.*','float' => '.*')),
	'code' => array('allowedTags' => array('code' => array("style", "dir", "lang", "title", "src", "alt", "title", "data-language")))
    );
    $cut = explode(',', $plugins);

    foreach ($cut AS $plug) {
	if (isset($pluginsConf[$plug]['allowedTags']))
	    $allowedTags = array_merge($pluginsConf[$plug]['allowedTags'], $allowedTags);
	if (isset($pluginsConf[$plug]['allowedStyles']))
	    $allowedStyles = array_merge($pluginsConf[$plug]['allowedStyles'], $allowedStyles);
    }

    $html = '';
    $str = preg_replace('/ +/', ' ', $str);
    $tab = preg_split('/(<[^>]*>)/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    foreach ($tab AS $val) {
	$innerTag = '';
	$tag = preg_split('/^(<\/?)([^\s]+)[^a-z]/', $val, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	if (isset($tag[1])) {
	    $currentTag = $tag[1];
	    if (!isset($allowedTags[$currentTag])) {
		$currentTag = 'span';
	    }
	    $innerTag = $tag[0] . $currentTag;
	    if (isset($tag[2])) {
		$attrs = preg_split('/([\w\-.:]+)\s*=\s*"([^"]*)"/', $tag[2], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0; $i < count($attrs) - 1; $i = $i + 3) {
		    $name = $attrs[$i];
		    $value = $attrs[$i + 1];
		    if (in_array($name, $allowedTags[$currentTag])) {
			$newValue = '';
			if ($name == 'style') {
			    $styles = explode(';', $value);
			    foreach ($styles AS $style) {
				$cutStyle = explode(':', $style);
				if (isset($allowedStyles[trim($cutStyle[0])])) {
				    $newValue .= trim($style).';';
				}
			    }
			    $value = $newValue;
			}
			$innerTag .= ' ' . $name . '="' . $value . '"';
		    }
		}
	    }
	    $html .= $innerTag . '>';
	} else {
	    $html .= $val;
	}
    }
    return $html;
}
}

?>
