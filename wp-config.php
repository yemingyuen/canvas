<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'db');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         'FB#IJ+$ >~$ktH`LuL`R~&P~htnq{9798[AWO{{~:|n c-c7i/:~6-f(jZRlX4|$');
define('SECURE_AUTH_KEY',  '{n-^z9Ty-M:_,#}s=iO_XT0/S+?h0L[IjVtUs2FE(!-UI$Kt(Z>U*?`Rr,OkmV=!');
define('LOGGED_IN_KEY',    'z/W*fN$qZ|2$,;*1@2mxDsYgXX|A=pNL#&2q<|K.o>};5T.$h;0@ai4eNO|-L<nv');
define('NONCE_KEY',        'wo-,nb[.H%ZN3qYgT =:d-ts_-1>F-Et&*~?-<*|pi;(>gW7OrWQoGM&1-{Y|B4t');
define('AUTH_SALT',        '-XxvO^%1%a._J3}]xwk>glGXZGHvSRO}|l6F(wq`Iy3FF8.B%,Z!<A:|;uoPgj:!');
define('SECURE_AUTH_SALT', 'jI^1tO;})z|LC  yvch`,>c-m;Q&Fp_.c>QEQrMGG_S]*=M9lU6>{b:wh,G}M$|:');
define('LOGGED_IN_SALT',   'r?[r#8XXhOfmK8-=LKJ/x,D3Cl-$^L#ZUV+jV~r|m} |e(-r+sodA32^}O$))mh$');
define('NONCE_SALT',       '+I-d7~pQFZW_6n;oW(JUw&80AT%`0xGGoIv+Bh|,ru-xJ|Pf*HX,a@;|CPWOnn*%');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
