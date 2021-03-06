<?php
/**
 * Alvan Extend REST API: Main class.
 *
 * @since 1.0.0
 * @package AERA
 */

namespace AERA;

use Exception;

/**
 * Alvan Extend REST API: Plugin
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Version
	 *
	 * @var string
	 * @since 1.0.0
	 */
	const VERSION = '1.0.0';

	/**
	 * Path of the plugin
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $_url = '';

	/**
	 * Path of the plugin
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $_path = '';

	/**
	 * Path of dist files
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $_assets_url = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since 1.0.0
	 */
	private $_basename = '';

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Plugin
	 * @since 1.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Settings
	 *
	 * @var Settings
	 * @since 1.0.0
	 */
	private $_settings;

	/**
	 * Settings
	 *
	 * @var Admin
	 * @since 1.0.0
	 */
	private $_admin;

	/**
	 * Enqueues
	 *
	 * @var Enqueue
	 * @since 1.0.0
	 */
	private $_enqueue;

	/**
	 * Settings
	 *
	 * @var REST_API
	 * @since 1.0.0
	 */
	private $_rest_api;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 1.0.0
	 * @return Plugin A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->basename   = AERA_BASENAME;
		$this->url        = plugin_dir_url( dirname( __FILE__ ) );
		$this->path       = plugin_dir_path( dirname( __FILE__ ) );
		$this->assets_url = $this->url . 'assets/dist';
		$this->_settings  = new Settings( $this );
	}

	/**
	 * Initial Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Init
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Load the text domain.
		$this->load_textdomain();
		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Load text domain
	 *
	 * @since 1.0.0
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'alvan-extend-wp-rest-api', false, dirname( $this->basename ) . '/languages' );
	}

	/**
	 * Load classes
	 *
	 * @since 1.0.0
	 */
	public function plugin_classes() {
		$this->admin    = new Admin( $this );
		$this->enqueue  = new Enqueue( $this );
		$this->rest_api = new REST_API( $this );
	}

	/**
	 * Plugin uninstall hook
	 *
	 * @since 1.0.0
	 */
	public function plugin_uninstall() {
		delete_option( AERA_OPTION );
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since 1.0.0
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'assets_url':
			case '_settings':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

}
