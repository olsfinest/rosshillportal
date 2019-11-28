<?php

define( 'GD_VIP', '166.62.109.86' );
define( 'GD_RESELLER', 1 );
define( 'GD_ASAP_KEY', 'b8971e598816a00bdb3e0d95bf957b53' );
define( 'GD_STAGING_SITE', false );
define( 'GD_EASY_MODE', false );
define( 'GD_SITE_CREATED', 1536671829 );
define( 'GD_ACCOUNT_UID', '2b2fe2b1-63d2-4d2b-bdef-115ccbf20167' );
define( 'GD_SITE_TOKEN', '209a9a0b-c9b0-4459-af3b-e9afc290a3cc' );
define( 'GD_TEMP_DOMAIN', 'wmg.c5e.myftpupload.com' );
define( 'GD_CDN_ENABLED', TRUE );
define( 'GD_PLAN_NAME', 'Ultimate Managed WordPress' );
define( 'GD_RUM_ENABLED', FALSE );

// Newrelic tracking
if ( function_exists( 'newrelic_set_appname' ) ) {
	newrelic_set_appname( '2b2fe2b1-63d2-4d2b-bdef-115ccbf20167;' . ini_get( 'newrelic.appname' ) );
}

/**
 * Is this is a mobile client?  Can be used by batcache.
 * @return array
 */
function is_mobile_user_agent() {
	return array(
	       "mobile_browser"             => !in_array( $_SERVER['HTTP_X_UA_DEVICE'], array( 'bot', 'pc' ) ),
	       "mobile_browser_tablet"      => false !== strpos( $_SERVER['HTTP_X_UA_DEVICE'], 'tablet-' ),
	       "mobile_browser_smartphones" => in_array( $_SERVER['HTTP_X_UA_DEVICE'], array( 'mobile-iphone', 'mobile-smartphone', 'mobile-firefoxos', 'mobile-generic' ) ),
	       "mobile_browser_android"     => false !== strpos( $_SERVER['HTTP_X_UA_DEVICE'], 'android' )
	);
}