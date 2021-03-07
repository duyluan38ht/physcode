<?php

/** The WordPress home URL */

define( 'WP_HOME', 'http://localhost/PHYSCODE/fixLanguage' );

/** The WordPress site URL */

define( 'WP_SITEURL', 'http://localhost/PHYSCODE/fixLanguage' );

/** The name of the database for WordPress */

define( 'DB_NAME', "fixLanguage" );

/** MySQL database username */

define( 'DB_USER', "fixLanguage" );


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

define( 'AUTH_KEY',         'sLRALd2Qy6LfsRF#5%=/6&[Yi>*g/Ev70pBz,#T`<G_0qF=p>k7Nm{50#[|!][kR' );

define( 'SECURE_AUTH_KEY',  'nk1tqH0Rlg^Xbzhyr:iZ~G3!d->6yeF6Fe.`_=__8$wP~sWVc&rB,So$nb2t]d&w' );

define( 'LOGGED_IN_KEY',    '1S@HhvF]r/{<fFq(VB_6h9`&nSG0eXT!fi!c`ZJoUf]LyST+uP&Z@s=(Jt]rp#@|' );

define( 'NONCE_KEY',        '$&]l:mx4oRI?SA^c}uGe?eF;x7*]?*g:m+X~z+qtL[s:iL-sw0mK6FzY Pk:KCdr' );

define( 'AUTH_SALT',        '5 ,.|31mL_#6v534Vo#*U6g6v|JJ])0l9umU]fXkOwB{~;VUY:V?Vj<>#(O/Y-1@' );

define( 'SECURE_AUTH_SALT', '%vzN8>q hQQ4GNbZ2MY[.QRR~`aZ4mqtvr#S{.Ax nrD(O7sEUl6=8^oqN^+f@|P' );

define( 'LOGGED_IN_SALT',   '2oH2Dn~fy4PfM44Oi>gN?49{?dw4{CK-Cdp(z*ov2 T.]c6c5rYT0dzAz KW}50q' );

define( 'NONCE_SALT',       'a=nSFwVN2-`L0=@PwzBRe-L H]V{2sQ>2 B17B=#`*Yni,[J:[SmZ(CCbk`K-N*>' );


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

define( 'WP_DEBUG', true );

define( 'WP_DEBUG_LOG', true );


/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}


/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';

