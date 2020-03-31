<?php 

/**
 * Alvan Extend REST API: Enqueue
 * @since 1.0.0
 * @package AERA
 */

namespace AERA;

/**
 * Alvan Extend REST API: Enqueue
 *
 * @since 1.0.0
 */
class Enqueue {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   Plugin
	 */
	private $_plugin = null;

	/**
	 * Constructor.
     * 
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
    }

    /**
     * Hooks.
     * 
     * @since 1.0.0
     */
    public function hooks() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    }

    /**
     * Enqueue styles.
     * 
     * @since 1.0.0
     */
    public function enqueue_styles($hook_suffix) {
        // Check if we are on our settings page
        if( $hook_suffix === 'settings_page_'.$this->plugin->_settings->settings_name ) {
            wp_enqueue_style( 'aera_main_font', 'https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,700&display=swap');
            wp_enqueue_style( 'aera_admin_styles', $this->plugin->assets_url . '/css/admin.css', false, $this->plugin->version );
        }
    }
}