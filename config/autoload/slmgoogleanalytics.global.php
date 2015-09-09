<?php
/**
 * SlmGoogleAnalytics Configuration
 *
 * If you have a ./configs/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$googleAnalytics = array(
    /**
     * Web property ID (something like UA-xxxxx-x)
     */
    'id' => 'UA-50945302-1',

    /**
     * Tracking across multiple (sub)domains
     * @see https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingSite
     */
    'domain_name' 	=> 'http://dev.clavax.us/ovessence',
    'allow_linker' => true,

    /**
     * Disable/enable page tracking
     *
     * It is adviced to turn off tracking in a development/staging environment. Put this
     * configuration option in your local.php in the autoload folder and set "enable" to
     * false.
     */
     'enable' => true,
);

/**
 * You do not need to edit below this line
 */
return array('google_analytics' => $googleAnalytics);
