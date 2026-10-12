<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'shop' );

/** Database username */
define( 'DB_USER', 'admin' );

/** Database password */
define( 'DB_PASSWORD', 'opaLkklpo12.6' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ':@M2,~K]JGFD^a 0j(X)WrWE;N:dW)kNL1#K1,zX>B<.((If$D/:NBx{cJ5pjSz^' );
define( 'SECURE_AUTH_KEY',  'l]gG:Q`=3?~v_bCG6$aOIiMCIU1%m0)Ux$VE,>G!Y_TH^Sw@Sc;zTFuqd4(?/Hng' );
define( 'LOGGED_IN_KEY',    'QZhh]fW~w7OO,*0]]=mY%kJm!kPY;H*|0POs2e*R^[a3jD6]qumr6=,jyvxgbUyl' );
define( 'NONCE_KEY',        'N[tR3}1v6lVZ}uhK EyP!g+V1E,24+H%iYMqGarq?sk(bBRm[qO`}y^T,Zku}||M' );
define( 'AUTH_SALT',        'bE/l;`dl$%So!Jbo0OT6gM#@w_P;LS>)?nZL)G)kioGfMzO=}sFZKX38[;h(->@0' );
define( 'SECURE_AUTH_SALT', 'X8gB=g^2-<~!t1nMx:#BfV.ANT[ti7y|kYj$1E%j,~woHUG315- }YQI)qyMV5QK' );
define( 'LOGGED_IN_SALT',   'PxI$Ft@.SOS<5X&l`2hc>`4Ph.<s2&S.dS]`!>24.^QPfX]qbC27U0t+9hjuCp)@' );
define( 'NONCE_SALT',       'YY% G&>YeP_.N)*:/`itC{s.<,bFZM60#8To=NFW^_Wx/KTn1+)R+FKj6@]H)dw&' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
