<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

if($_SERVER['SERVER_NAME']=="blog.ovessence.in" || $_SERVER['SERVER_NAME']=='ovessence.in') {
        //lnserver
        
        $DBNAME = 'OvEssenCe';
        $DBUSER = 'clavax';
        $DBPASSWORD = 'tech';
        $DBHOST = '192.168.2.129';
        $COOKIE_DOMAIN = '.ovessence.in';

    } else if($_SERVER['SERVER_NAME']=="dev.clavax.us"  || $_SERVER['SERVER_NAME']=='dev.clavax.us') {
        //dev.clavax.us development server
        $DBNAME = 'OvEssenCe';
        $DBUSER = 'dbclavax_ovessen';
        $DBPASSWORD = 'aZl6[JO,2C4X';
        $DBHOST = 'localhost';
        $COOKIE_DOMAIN = '.clavax.us';

    } else {
        // local setting      
        $DBNAME = 'OvEssenCe';
        $DBUSER = 'root';
        $DBPASSWORD = 'tech';
        $DBHOST = 'localhost';
        $COOKIE_DOMAIN = '.ovessence.loc';
    }


// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', $DBNAME);

/** MySQL database username */
define('DB_USER', $DBUSER);

/** MySQL database password */
define('DB_PASSWORD' , $DBPASSWORD);


/** MySQL hostname */
define('DB_HOST', $DBHOST);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'DH+9=/}c64T#B(h?Fp}Jyl>zs~Q%roR#5N9iwGJBsNRJ~*:7h-AT~YO%;JJ@5LQ^');
define('SECURE_AUTH_KEY',  '6*X)9YM&CtIA.j9%@bn|M%-P2$/!<|XLB9IUjHM8Q:|$&ou(5d!Xl>3-=[-hK,_z');
define('LOGGED_IN_KEY',    'J,E+cRKm=9T9TzoFwPi QDZE8Cz:8w;MDQ`l%*~2JRcGuR*a=-MLx)_TF]B:]Zdl');
define('NONCE_KEY',        '5-5ojcm1-%E8LKZz|CO>/l_]uwM1/XB:n}v.|;+G6IHcK.|PfCzhBK[beXYtP|U9');
define('AUTH_SALT',        'XRb-%5?^X.3a!vlFaYu:@#`0%[LB:QELGtBe*n_KFL/mLyDx(u|QZp9nyLq 3%r_');
define('SECURE_AUTH_SALT', 'VC(:V#%%p;|;RP?Y_EuLDy9t[N(FWL2=GEAzl<;YWdN.`/e$+(:7IJ2{x_+3+]-D');
define('LOGGED_IN_SALT',   'X{-zzZDR]/|3?F`wl|]#f#6F@uA++|b7a-D5y?}|00D{r~ox%l |Ya?E|++(kB),');
define('NONCE_SALT',       'ek15zbS1I0>/1sP1 LsDm+hP%||v+|@]nJy-FR@K,S,~3?WKN2WSc;K`v|097h2+');

/**#@-*/
define('ADMIN_COOKIE_PATH', '/');
define('COOKIE_DOMAIN', $COOKIE_DOMAIN);


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

add_filter('xmlrpc_enabled', '__return_false');
