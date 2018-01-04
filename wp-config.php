<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'dmqgWordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'vY/*YkD>qXwmQ2;To8Z>Q~H~;I 2RM/@S@4Kq3jsfsmiWlEkk3+4)B9J-]fyUaat');
define('SECURE_AUTH_KEY',  'FD~xbwH}3n=#U^q;ha?~^U:LE 9H&i?PzpWZ_ (Yv#,f$U`/y,dZA52]8kcM#^Nq');
define('LOGGED_IN_KEY',    'Q>g2t:xNTc7As#MHNvu/&&4U!m87ukuMNBW(%7eIL<C,TsTa!1mS%eo$RpxR0^~d');
define('NONCE_KEY',        '{E@v>`L0N-NO?+S5p,Z7z[G9rIR`C!jz8mp)]tuCio+k&(F_=xfdB%-/ayHV`&Iw');
define('AUTH_SALT',        '=JPNM%B/!yTF}eG?G`=kd(VCu_qRC$e`&Z_!VU+cY/$Iv?9nW]_G84ij8tskh6M8');
define('SECURE_AUTH_SALT', '9RRvuqi(:@3qR=qsej-w3g+_>AR_$TZI9 TPpr!OG+a&(5Zzc VDyb;p^Z+/SP|)');
define('LOGGED_IN_SALT',   '9ue_F eQ9HH8~MPxd+YTVZ7(-00bl9*Mr!1Et8$gYT[44^,`asDuLhKd6YEnA$!<');
define('NONCE_SALT',       'yX`-U@lRYbw1P(RIah0< arq>mC?BtY[lH!RZHNg:%/iS}w^=#1$%j)aU7@z)y/2');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
