<?php 

/**
 * Alvan Extend REST API: Admin
 * @since 1.0.0
 * @package AERA
 */

namespace AERA;

/**
 * Alvan Extend REST API: Admin
 *
 * @since 1.0.0
 */
class Admin {

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
	 * @param Plugin $plugin Main plugin object.
	 *
	 * @since 1.0.0
     * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
    }
    

    /**
     * Hooks
     * 
     * @since 1.0.0
     * @return void
     */
    public function hooks() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    /**
     * Add admin menu page
     * 
     * @since 1.0.0
     * @return void
     */
    public function admin_menu() {
        add_submenu_page(
            'options-general.php',
            __('Extend REST API', 'aera'),
            __('Extend REST API', 'area'),
            $this->plugin->settings->capability(),
            $this->plugin->settings->settings_name,
            array($this, 'plugin_admin_page')
        );
    }

    /**
     * Render the menu page
     * @since 1.0.0
     * @return void
     */
    public function plugin_admin_page() {
        echo 'TEST';
    }
}