<?php 

/**
 * Alvan Extend REST API: Settings
 * @since 1.0.0
 * @package AERA
 */

namespace AERA;

/**
 * Alvan Extend REST API: Settings
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   Plugin
	 */
	private $_plugin = null;

    /**
     * Settings name
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    public $settings_name = '';

    /**
     * Settings name
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    public $posts_types_option_name = '';

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin Main plugin object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $plugin ) {
        $this->plugin = $plugin;
        $this->settings_name = 'aera-settings';
        $this->posts_types_option_name = 'aera_options';
    }
    
    /**
     * The capability need to administrate the plugin
     * Hookable to change the default capabilty needed
     * 
     * @since 1.0.0
     */
    public function capability() {
        return apply_filters('aera_capability', 'manage_options');
    }
}