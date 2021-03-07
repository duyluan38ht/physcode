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
define( 'DB_NAME', "oncoSchool" );

/** MySQL database username */
define( 'DB_USER', "oncoSchool" );

/** MySQL database password */
define( 'DB_PASSWORD', "123456@Abc" );

/** MySQL hostname */
define( 'DB_HOST', "localhost" );

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
define( 'AUTH_KEY',         'boe&<jrnlEhR(3T;~fw6wy]s<$O5#.eU2!GnPkj*z7E6+krSvC]@,qY54z7=V((<' );
define( 'SECURE_AUTH_KEY',  'uZ0;p>=;tx>@rPZ.$ @m8~#>|p#ET}t$1gBhx`N6|5[yKYs7*W$_mxo::NFFNm%,' );
define( 'LOGGED_IN_KEY',    'g*D6)? !+hD:3(B0iz,{-mfa.mhtNEXQ=%]imIepG^=TL+s]{^M:,j8e2/>mr{mA' );
define( 'NONCE_KEY',        'uXGzrWe3XhV;=kzIf};Jgy`%X2-3WoR4r|v:Yt51F|+AL_BhCXz-+rn}42zuvkc^' );
define( 'AUTH_SALT',        'dhXH&>C3`}Ei+0MX]&#AfME?brv%|H10}5|p3feOMe6b$Rw)$?>6H=(/<r+HVwGy' );
define( 'SECURE_AUTH_SALT', 'sL+2V[8&*XK$bwM.*S6+N#J^qF2P?m[JJkpg<gG==Z1<4bP0XMQFl^2Pq!_Xqb9r' );
define( 'LOGGED_IN_SALT',   'ze}rs%8polBLR_nst?]6,C[G{#~O^J|2Q%Svby%squT7b,qft%NVd7l3:90Cn}(#' );
define( 'NONCE_SALT',       '~CO?vt-knv<B6ti4Y9AfY`Ioq6#WVe+1w@-[G7ZJddf>Ya/]C{;,y24Kv.K,*{yW' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
