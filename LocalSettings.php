<?php

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

$wikis = [
	'wikipesija.org' => 'wikipesija',
	'sona-suli.pona.la' => 'sonasuli',
];
if ( defined( 'MW_DB' ) ) {
	$wgDBname = MW_DB;
} elseif ( isset( $_SERVER[ 'MW_DB' ] ) ) {
	$wgDBname = $_SERVER[ 'MW_DB' ];
} else {
	$wgDBname = $wikis[ $_SERVER['HTTP_HOST'] ?? '' ] ?? null;
}
if ( !$wgDBname ) {
	die( 'ma lipu seme\n' );
}
$wgLocalDatabases = $wgConf->wikis = array_values( $wikis );

###############################################################################
# ijo ni li ante lon ma lipu ante
$wgConf->settings = [
	'wgServer' => [
		'wikipesija' => 'https://wikipesija.org',
		'sonasuli' => 'https://sona-suli.pona.la',
	],
	'wgSitename' => [
		'wikipesija' => 'Wikipesija',
		'sonasuli' => 'sona suli',
	],
	'wgLogos' => [
		'wikipesija' => [
			'svg' => "$wgResourceBasePath/resources/assets/Wikipesija.svg",
			'wordmark' => [
				'src' => "$wgResourceBasePath/resources/assets/Wikipesija-nimi-nimi-taso.svg",
				'width' => 120,
				'height' => 30,
			],
		],
	],
	'wgPasswordSender' => [
		'default' => "$wgDBname@tbodt.com",
		'wikipesija' => 'ilo@wikipesija.org',
	],

	'wgLanguageCode' => [
		'default' => 'tok',
	],
	'wgCapitalLinks' => [
		'default' => false,
	],
];

switch ( $wgDBname ) {
case 'wikipesija':
	define("NS_NIMI", 3000);
	define("NS_TOKI_NIMI", 3001);
	$wgExtraNamespaces[NS_NIMI] = 'nimi';
	$wgExtraNamespaces[NS_TOKI_NIMI] = 'toki_nimi';
	$wgContentNamespaces[] = NS_NIMI;
	break;
}

require_once __DIR__ . '/PrivateSettings.php';

extract( $wgConf->getAll( $wgDBname ) );

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "https://creativecommons.org/licenses/by-sa/3.0/";
$wgRightsText = "Creative Commons Attribution-ShareAlike";
$wgRightsIcon = "$wgResourceBasePath/resources/assets/licenses/cc-by-sa.png";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "";

$wgArticlePath = '/wiki/$1';

# Email

$wgEnableEmail = true;
$wgEnableUserEmail = false;
$wgEnotifUserTalk = true;
$wgEnotifWatchlist = false;
$wgEmailAuthentication = true;

## Database settings
$wgDBtype = "sqlite";
$wgDBserver = "";
$wgDBuser = "";
$wgDBpassword = "";

# SQLite-specific settings
$wgSQLiteDataDir = "/home/mediawiki/data";
$wgObjectCaches[CACHE_DB] = [
	'class' => SqlBagOStuff::class,
	'loggroup' => 'SQLBagOStuff',
	'server' => [
		'type' => 'sqlite',
		'dbname' => 'wikicache',
		'tablePrefix' => '',
		'variables' => [ 'synchronous' => 'NORMAL' ],
		'dbDirectory' => $wgSQLiteDataDir,
		'trxMode' => 'IMMEDIATE',
		'flags' => 0
	]
];
$wgLocalisationCacheConf['storeServer'] = [
	'type' => 'sqlite',
	'dbname' => "{$wgDBname}_l10n_cache",
	'tablePrefix' => '',
	'variables' => [ 'synchronous' => 'NORMAL' ],
	'dbDirectory' => $wgSQLiteDataDir,
	'trxMode' => 'IMMEDIATE',
	'flags' => 0
];
$wgJobTypeConf['default'] = [
	'class' => 'JobQueueDB',
	'claimTTL' => 3600,
	'server' => [
		'type' => 'sqlite',
		'dbname' => "{$wgDBname}_jobqueue",
		'tablePrefix' => '',
		'variables' => [ 'synchronous' => 'NORMAL' ],
		'dbDirectory' => $wgSQLiteDataDir,
		'trxMode' => 'IMMEDIATE',
		'flags' => 0
	]
];

# Shared database table
# This has no effect unless $wgSharedDB is also set.
if ( $wgDBname !== 'wikipesija' ) {
	$wgSharedDB = 'wikipesija';
}
$wgSharedTables[] = "actor";

## Shared memory settings
$wgMainCacheType = CACHE_ACCEL; # changed
$wgParserCacheType = CACHE_DB;
$wgMemCachedServers = [];

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = false;
#$wgUseImageMagick = true;
#$wgImageMagickConvertCommand = "/usr/bin/convert";

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = true;

# Periodically send a pingback to https://www.mediawiki.org/ with basic data
# about this MediaWiki instance. The Wikimedia Foundation shares this data
# with MediaWiki developers to help guide future development efforts.
$wgPingback = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale. This should ideally be set to an English
## language locale so that the behaviour of C library functions will
## be consistent with typical installations. Use $wgLanguageCode to
## localise the wiki.
$wgShellLocale = "C.UTF-8";

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publicly accessible from the web.
$wgCacheDirectory = "$IP/cache/{$wgDBname}";

# Changing this will log out all existing sessions.
$wgAuthenticationTokenVersion = "1";

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

# skins

wfLoadSkins( [
	'MonoBook',
	'Timeless',
	'Vector',
	'MinervaNeue',
] );
$wgDefaultSkin = "vector";

# End of automatically generated settings.
# Add more configuration options below.

$wgExtraLanguageNames['tok'] = 'toki pona';
$wgUseRCPatrol = false;
$wgGroupPermissions['sysop']['patrol'] = false;
$wgGroupPermissions['sysop']['deletelogentry'] = true;
$wgGroupPermissions['sysop']['deleterevision'] = true;
$wgAllowUserCss = true;
$wgJobRunRate = 0;
$wgAutoConfirmAge = 86400;
$wgAutoConfirmCount = 5;

wfLoadExtension( 'TokiPona' );
wfLoadExtension( 'Renameuser' );
wfLoadExtension( 'Cite' );
wfLoadExtension( 'ParserFunctions' );
$wgPFEnableStringFunctions = true;
wfLoadExtension( 'Scribunto' );
$wgScribuntoDefaultEngine = 'luastandalone';
wfLoadExtension( 'WikiEditor' );
wfLoadExtension( 'VisualEditor' );
$wgVisualEditorEnableDiffPage = true;
$wgVisualEditorEnableWikitext = true;
wfLoadExtension( 'TemplateData' );
wfLoadExtension( 'Interwiki' );
$wgGroupPermissions['sysop']['interwiki'] = true;
wfLoadExtension( 'TemplateStyles' );
wfLoadExtension( 'Echo' );
wfLoadExtension( 'PageImages' );
wfLoadExtension( 'CodeEditor' );
wfLoadExtension( 'CodeMirror' );
$wgDefaultUserOptions['usecodemirror'] = 1;
wfLoadExtension( 'MobileFrontend' );
$wgDefaultMobileSkin = 'minerva';
$wgMFAutodetectMobileView = true;
wfLoadExtension( 'TimedMediaHandler' );
$wgFFmpegLocation = '/usr/bin/ffmpeg';
wfLoadExtension( 'CheckUser' );
wfLoadExtension( 'UniversalLanguageSelector' );
$wgULSEnable = false;
$wgULSLanguageDetection = false;
wfLoadExtension( 'TextExtracts' );
wfLoadExtension( 'Thanks' );
wfLoadExtension( 'Gadgets' );
wfLoadExtension( 'Nuke' );
wfLoadExtension( 'AbuseFilter' );
wfLoadExtension( 'StopForumSpam' );
wfLoadExtensions([ 'ConfirmEdit', 'ConfirmEdit/QuestyCaptcha' ]);
wfLoadExtension( 'UserMerge' );
$wgGroupPermissions['bureaucrat']['usermerge'] = true;
wfLoadExtension( 'Discord' );
$wgDiscordUseEmojis = true;
wfLoadExtension( 'Popups' );
wfLoadExtension( 'Linter' );
wfLoadExtension( 'DiscussionTools' );

$wgShowExceptionDetails = true;
$wgShowDBErrorBacktrace = true;
$wgShowSQLErrors = true;

if ( PHP_SAPI === 'cli' ) {
	$wgReadOnly = false;
}

/*
$wgShowExceptionDetails = true;
$wgShowDebug = true;
$wgEnableParserCache = false;
$wgCachePages = false;
$wgLocalisationCacheConf = [
	'class' => LocalisationCache::class,
	'store' => 'detect',
	'storeClass' => false,
	'storeDirectory' => false,
	'storeServer' => [],
	'forceRecache' => true,
	'manualRecache' => false,
];
$wgDebugLogFile = "/home/mediawiki/data/{$wgDBname}.log";
$wgDebugLogGroups = array(
	'dt' => "/home/mediawiki/data/{$wgDBname}-dt.log",
);
*/
