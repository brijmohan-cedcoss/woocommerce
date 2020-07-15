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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '4k7RGNKane+OIPyBfKO2DhYDz8jPkN+ESPegWH2tZOtmAH4g2RzthdFb3RZgcgLZg9IWA4bz0RVcqUoDMJ27Nw==');
define('SECURE_AUTH_KEY',  '9JFCktJVBgGnbEQL1UwcHLqKjE05DAeg+oQ39ITb93iLe0gNBvsvmMZ1Fcg6fC+7oQB+7s96LjpuUn5hs6A32Q==');
define('LOGGED_IN_KEY',    'jNTY0uDEj/pVTqAXM5acyK4BERCGndIpyMj9QP8xCLdK+YzVq495AF2Pbsxk1DcP9hbInm1HmYKVUDz2QdrFEw==');
define('NONCE_KEY',        'hAl12UNWWiHuot/VKPsyoZkRD76dRZWBVc1i4xGBDm5M0ImqMa7C5t2a3NiUckyNGoHz17D7heHO8zW4MA9nzg==');
define('AUTH_SALT',        'w0jt2Zy02piNup+ZqXOLkLsOlC3a9AmO/CJtfxa9UYg1tVttxk+EW1kXyjA5JH78TxLzP5emmUD77+9KpIgE4g==');
define('SECURE_AUTH_SALT', 'IOiLjxwx58vX3nm4dAmPHlvGOfLfIkSemBmHjgGx64Vqmm80N1jTRwyOfwjjHUOcgeRdpLkaJU6GVWnXkFvIlQ==');
define('LOGGED_IN_SALT',   'abY2lbzU6WMdbKT9IYw4DKkHPm7YChL0rzEk346Ds/b40AqdITkuBgMaBVnEqBruV/y/Qgnj+FGRDLh2pWrM8g==');
define('NONCE_SALT',       'XoJIsmH43JFp8dxYiyINOXQSMac55AImcSfZgIkusXq+orAg8y63J3+7TwJKPeTAN338RECTljnvcSqoYqNkow==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
