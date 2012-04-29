<?php

//General Config
define('PARSIMONY_VERSION','0.1');
$base = dirname($_SERVER['PHP_SELF']);
if($base=='/' || $base=='\\' ) $base = '';
define('BASE_PATH',$base.'/');

//Themes
$config['THEMEMODULE'] = 'core';
$config['THEME'] = 'parsidefault';  

//BDD
$config['db']['host'] = 'localhost';
$config['db']['port'] = '3306';
$config['db']['dbname'] = '';
$config['db']['user'] = '';
$config['db']['pass'] = '';


//Cache
$config['cache']['cache-control'] = 'private';
$config['cache']['max-age'] = '2592000999865';

//Extenssions auth
$config['extensions_auth'] = 'js,png,css,jpg,jpeg,gif,swf,ico,xml,mp3,wmv,avi,mpeg,html';

//security
$config['security']['allowedipadmin'] = '';
$config['security']['salt'] = '1187c105';

//domain
$config['domain']['multisite'] = '0';
$config['domain']['sld'] = '2';

//Dev
$config['dev']['status'] = 'dev';
$config['dev']['serialization'] = 'obj';

//localization
$config['localization']['default_language'] = 'en_EN';
$config['localization']['timezone'] = 'America/Adak';

//preferences
$config['preferences']['conteneurColor'] = '#ffffff';
$config['preferences']['blockColor'] = '#ffffff';
$config['preferences']['cssPickerColor'] = '#ffffff';
$config['preferences']['translateColor'] = '#ffffff';

$config['cache']['active'] = '0';

//mailing conf
$config['mail']['adminMail'] = '';
$config['mail']['type'] = 'smtp';
$config['mail']['server'] = '';
$config['mail']['port'] = '25';

$config['activeModules']['core'] = '1';
$config['activeModules']['admin'] = '0';

$config['sitename'] = 'My WebSite';