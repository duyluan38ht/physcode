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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
define ( 'FS_METHOD', 'direct');
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'project-wordpress' );

/** MySQL database username */
define( 'DB_USER', 'project-wordpress' );

/** MySQL database password */
define( 'DB_PASSWORD', '123456@Abc' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'l O,=d-:aw,*6X%be8+-{{$~; lJWZWs0hk={R=3f_;-v@~ByTS/E<KG-8 P380x' );
define( 'SECURE_AUTH_KEY',  'bOcg_:g&w$e*-;2$FC2r8Y47q^IK(@Ni5WOg[-KeIs9L&] E&!rvuG=3)L$4~q-.' );
define( 'LOGGED_IN_KEY',    'zHMi=_Vw+ZI;VrHbV62|8bA^&B#*#sRE/8X?d`+HF8t,m+/Q{H}LuB)o>0gn,y*M' );
define( 'NONCE_KEY',        ')|l}w-jS$tY-4[[BE?Q?>]Q.2h+~j%d ,,6d*~v/4|f%:GhXk2:*&T?VHD(d0`sL' );
define( 'AUTH_SALT',        '88assQ><KpeTL::keS,)Oqq/0eO/sy9Da?HB}t{5<ns[:VQmmv6yc7E/oa4f]4.L' );
define( 'SECURE_AUTH_SALT', 'z}(.Hr[C4B`z^+Z/w~3lDLp[!_&D} MrG*- NEmyH&~Ic$PBR_TmZu4CFq`%~lGv' );
define( 'LOGGED_IN_SALT',   'F7 D2Yv;%O{KfiJ.?TA(ks]N__1>7. cQA.fx1g: RY.IP0$C5_iBFwH;!|A-o}@' );
define( 'NONCE_SALT',       'bKzTGc8nwa9.N/{;!Y$}db(O04xI,)0F~[n*ygv:Y4Sy]*,DY>Stl4~rxzM~/_6*' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

