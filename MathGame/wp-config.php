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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'MathGame');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         ')fsJMcZm84,>P(=Jo&QKyc}PDhRfNg.8]<kO?R$Dj.iKQt~-[(pjt!gF9&Kr[N$U');
define('SECURE_AUTH_KEY',  '50w719gze.H!>~?K.^:XRT9&zX5&J%^W)IF<>$3iG(rIm*n?iiV:.<MsUFiRo*}f');
define('LOGGED_IN_KEY',    '78mPvo5jtPg*b~0<d%%p[@%0i1gPG?5+/VFC/SR<LW.?PtU?:^4mP@ARd{5lhGW+');
define('NONCE_KEY',        'Qx]2Cn4v;A@>pajTe7Mak&#<[wf4ddOJKVg$Jr^2G7/:;[61zchf$3TJ(T3lrO#s');
define('AUTH_SALT',        'dK.Qr`Cj:42q0wha>1lZ`1la>%9|.l!4q}%)@K)BJVJm;JT[8bLrSBU<*#tYzzTt');
define('SECURE_AUTH_SALT', 'w:CBp).(IPJ}6rc$8%B:R%q>aN#:scn8qQfS{oc+ T[u}B5z,k>=72PvaNC7]_Lg');
define('LOGGED_IN_SALT',   'cij1NfzCIIo2`oP@j?z4QDd0#EeO<KC*M3L^Oy=/Z Jaz[}`5@WpAGedsA`B6`HR');
define('NONCE_SALT',       'kFn8F0L%DgHD`)A^^CX>CBpan)5y:;RI.[d5CmKRf4^0AAm s]mRiPfh.7J?dZQ}');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_mathgame';

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
