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
	protected $plugin = null;

	/**
	 * Constructor.
     * 
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin Main plugin object.
     * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
    }

    /**
     * Hooks.
     * 
     * @since 1.0.0
     * @return void
     */
    public function hooks() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    }

    /**
     * Enqueue styles.
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_styles($hook_suffix) {

        if( $hook_suffix === 'settings_page_'.$this->plugin->settings->settings_name ) {
            wp_enqueue_style( 'aera_main_font', 'https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,700&display=swap');
            wp_enqueue_style( 'aera_admin_styles', $this->plugin->assets_url . '/css/admin.css', false, $this->plugin->version );
        }
    }
}