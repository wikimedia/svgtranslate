<?php

// Debugging.
$wgShowExceptionDetails = true;
$wgShowDBErrorBacktrace = true;

// Site.
$wgSitename = "SVG Translate Dev Wiki";
$wgMetaNamespace = "Project";
$wgServer = 'http://localhost:8043';
$wgScriptPath = '';
$wgArticlePath = '/index.php?title=$1';
$wgResourceBasePath = $wgScriptPath;

// Database.
$wgDBtype = "sqlite";
$wgDBserver = "";
$wgDBname = "my_wiki";
$wgDBuser = "";
$wgDBpassword = "";
$wgSQLiteDataDir = "/var/www/html/images";
$wgObjectCaches[CACHE_DB] = [
	'class' => SqlBagOStuff::class,
	'loggroup' => 'SQLBagOStuff',
	'server' => [
		'type' => 'sqlite',
		'dbname' => 'wikicache',
		'tablePrefix' => '',
		'dbDirectory' => $wgSQLiteDataDir,
		'flags' => 0
	]
];
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = [];

// Uploads.
$wgEnableUploads = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgFileExtensions[] = 'svg';

// Misc.
$wgPingback = false;
$wgShellLocale = "C.UTF-8";
$wgLanguageCode = "en";
$wgSecretKey = "b1c05924840c1ba95367a0b2aa94a993a997e49dd030518bacaf812e5e79f384";
$wgAuthenticationTokenVersion = "1";
$wgUpgradeKey = "4ccb4ff027e09664";

// Skin and extensions.
wfLoadSkin( 'Vector' );
$wgDefaultSkin = "Vector";

wfLoadExtension('OAuth');
$wgGroupPermissions['sysop']['mwoauthproposeconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthupdateownconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthmanageconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthsuppress'] = true;
$wgGroupPermissions['sysop']['mwoauthviewsuppressed'] = true;
$wgGroupPermissions['sysop']['mwoauthviewprivate'] = true;
$wgGroupPermissions['sysop']['mwoauthmanagemygrants'] = true;
