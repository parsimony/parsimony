<?php

//General Config
$config['BASE_PATH'] = '';
$config['DOCUMENT_ROOT'] = __DIR__;

//Themes
$config['THEMEMODULE'] = 'blog';
$config['THEME'] = 'parsidefault';

//Database
$config['db']['host'] = 'localhost';
$config['db']['port'] = '3306';
$config['db']['dbname'] = '';
$config['db']['user'] = '';
$config['db']['pass'] = '';
$config['db']['prefix'] = '';

//Cache
$config['cache']['cache-control'] = 'private';
$config['cache']['max-age'] = '2592000999865';
$config['cache']['active'] = '0';

//Extensions auth
$config['extensions_auth'] = 'js,png,css,jpg,jpeg,gif,swf,ico,xml,mp3,wmv,avi,mpeg,html';

//Security
$config['security']['allowedipadmin'] = '';
$config['security']['salt'] = '';

//Domain
$config['domain']['multisite'] = '0';
$config['domain']['sld'] = '2';

//Devices
$config['devices']['desktop'] = '1';
$config['devices']['mobile'] = '1';
$config['devices']['tablet'] = '1';
$config['devices']['tv'] = '1';
$config['devices']['defaultDevice'] = 'desktop';

//Dev
$config['dev']['status'] = 'dev';
$config['dev']['serialization'] = 'obj';

//Localization
$config['localization']['default_language'] = 'en_EN';
$config['localization']['timezone'] = 'America/Adak';

//Preferences
$config['preferences']['conteneurColor'] = '#ffffff';
$config['preferences']['blockColor'] = '#ffffff';
$config['preferences']['cssPickerColor'] = '#ffffff';
$config['preferences']['translateColor'] = '#ffffff';

//General
$config['general']['ajaxnav'] = '0';

//Mailing conf
$config['mail']['adminMail'] = '';
$config['mail']['type'] = '';
$config['mail']['server'] = '';
$config['mail']['port'] = '25';

/* Modules */
$config['modules']['active']['core'] = '1';
$config['modules']['active']['blog'] = '1';
$config['modules']['default'] = 'blog';

$config['sitename'] = 'My WebSite';
