<?php 

/**
 * Alvan Extend REST API: REST_API
 * @since 1.0.0
 * @package AERA
 */

namespace AERA;

use Exception;

/**
 * Alvan Extend REST API: REST_API
 *
 * @since 1.0.0
 */
class REST_API {

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
        add_action( 'rest_api_init', array( $this, 'add_custom_fields' ) );
    }

    /**
     * Add custom fields to REST API
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_custom_fields() {

       /**
        * Get the post types additional fields
        */
        $post_types_fields = get_option($this->plugin->_settings->posts_types_option_name);

        foreach($post_types_fields as $post_type => $fields) {
            foreach($fields as $field => $value) {

                $method_name = 'get_'.$field;

                if( !method_exists($this, $method_name) ) {
                    throw new Exception('The method ' . $method_name . ' of the class ' . get_class($this) . ' does not exists');
                }
                
                register_rest_field( 
                    $post_type, 
                    $field,
                    array(
                        'get_callback'    => array( $this, $method_name),
                        'schema'          => null,
                    )
                );
            }
        }
    }

    /**
     * Get post thumbnail url
     * 
     * @since 1.0.0
     * @return string Post thumbnail url
     */
    public function get_featured_media_url($post) {

        return get_the_post_thumbnail_url($post['id']);
    }

    /**
     * Get post thumbnail url
     * 
     * @since 1.0.0
     * @return string Author's (User) avatar url
     */
    public function get_avatar_url($post) {
        return get_avatar_url($post['author']);
    }

    /**
     * Get post thumbnail url
     * 
     * @since 1.0.0
     * @return string Author's (User) name (display_name)
     */
    public function get_author_name($post) {
        return get_the_author_meta('display_name', $post['author']);
    }

    
}