<?php
/**
 * Plugin Name:           AutomatorWP - GamiPress integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-gamipress-integration/
 * Description:           Connect AutomatorWP with GamiPress.
 * Version:               1.0.9
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-gamipress-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.9
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\GamiPress
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_GamiPress_Integration {

    /**
     * @var         AutomatorWP_GamiPress_Integration $instance The one true AutomatorWP_GamiPress_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_GamiPress_Integration self::$instance The one true AutomatorWP_GamiPress_Integration
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_GamiPress_Integration();

            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();
                self::$instance->load_textdomain();

            }

            self::$instance->hooks();

        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_GAMIPRESS_VER', '1.0.9' );

        // Plugin file
        define( 'AUTOMATORWP_GAMIPRESS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_GAMIPRESS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_GAMIPRESS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() && ! $this->pro_installed() ) {

            // Includes
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/triggers/earn-points.php';
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/triggers/earn-achievement.php';
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/triggers/reach-rank.php';

            // Actions
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/actions/user-points.php';
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/actions/user-achievement.php';
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/actions/user-rank.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'gamipress', array(
            'label' => 'GamiPress',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/gamipress.svg',
        ) );

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'AutomatorWP - GamiPress requires %s and %s in order to work. Please install and activate them.', 'automatorwp-gamipress-integration' ),
                        '<a href="https://wordpress.org/plugins/automatorwp/" target="_blank">AutomatorWP</a>',
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php elseif ( $this->pro_installed() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php echo __( 'You can uninstall AutomatorWP - GamiPress Integration because you already have the pro version installed and includes all the features of the free version.', 'automatorwp-gamipress-integration' ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'GamiPress' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_GamiPress' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = AUTOMATORWP_GAMIPRESS_DIR . '/languages/';
        $lang_dir = apply_filters( 'automatorwp_gamipress_integration_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'automatorwp-gamipress-integration' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'automatorwp-gamipress-integration', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/automatorwp-gamipress-integration/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/automatorwp-gamipress-integration/ folder
            load_textdomain( 'automatorwp-gamipress-integration', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/automatorwp-gamipress-integration/languages/ folder
            load_textdomain( 'automatorwp-gamipress-integration', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'automatorwp-gamipress-integration', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_GamiPress_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_GamiPress_Integration The one true AutomatorWP_GamiPress_Integration
 */
function AutomatorWP_GamiPress_Integration() {
    return AutomatorWP_GamiPress_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_GamiPress_Integration' );
