<?php
define( 'WP_CACHE', false ); // Added by WP Rocket
 // Added by WP Rocket

//define('JWT_AUTH_SECRET_KEY', '?`;6$K~QM.+YT~U*% r<Gg2}IlWZwlO}*l~@73$pgbn{0,~c<Phi)r8)|+8j)9.%');
//define('JWT_AUTH_CORS_ENABLE', true);

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL

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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cfi' );

/** MySQL database username */
define( 'DB_USER', 'goldenflash' );

/** MySQL database password */
define( 'DB_PASSWORD', 'BUz.t[xq_7D/V~Y3' );

/** MySQL hostname */
define( 'DB_HOST', 'wordpress-cfi.mysql.database.azure.com' );

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
define( 'AUTH_KEY',         'jbtjzbwsms8pj2o65zcwloag7hqfxjat2l5oez8yzghumifcfeifccmcdbhbsc5u' );
define( 'SECURE_AUTH_KEY',  'vvejjiqux3gzjdxk1ucmztrh0lg7ukq0f5a5pflpozpqunsitvgvt1veabhwb9kk' );
define( 'LOGGED_IN_KEY',    'inrte8xnd8ss3fgxb6ef9vgluopsp5pt5yeqcrevds3beqaky22jwi2lhkrt6xaa' );
define( 'NONCE_KEY',        'qt19vluez7gufhk0nccsy49e96teq1xcesgfmp7qmt4fdl69j1eimmn63qfgiiqg' );
define( 'AUTH_SALT',        'qzxw0jsc0gmd6y65sawycanjhr5uk6gcurim7qci9jajah5mr04o9rc2a3jonz5g' );
define( 'SECURE_AUTH_SALT', 'omurm97afdwkxcux0f7looalolnlia9mef7ahklxnu2e8efrbkanexuo29yra0vq' );
define( 'LOGGED_IN_SALT',   'cjtidqgks1b3oiftoozxgveokdsftafltx5uslqmovoab0syr4wdzjvhwdtm7vhi' );
define( 'NONCE_SALT',       '3jqzosmxz2d4flxhowu3m6lqcjrdkdbicucyrshnbugcpv0eok7n0tbhigvnmxdm' );
define( 'JWT_AUTH_SECRET_KEY', 
'rahasia-umum' );
define( 'JWT_AUTH_CORS_ENABLE', true );
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'GRP_';

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
