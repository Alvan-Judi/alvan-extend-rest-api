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
        add_filter( 'plugin_action_links_' . $this->plugin->basename, array( $this, 'plugin_settings_page_link' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    /**
     * Add link to our settings page, on the plugin list page.
     * 
     * @since 1.0.0
     * @return void
     */
    public function plugin_settings_page_link( $links ) {
        $links[] = '<a href="'. esc_url( admin_url( 'options-general.php?page='.$this->plugin->_settings->settings_name ) ) .'">'. __('Settings', 'alvan-extend-wp-rest-api') .'</a>';
        return $links;
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
            __('Extend REST API', 'alvan-extend-wp-rest-api'),
            __('Extend REST API', 'area'),
            $this->plugin->_settings->capability(),
            $this->plugin->_settings->settings_name,
            array($this, 'plugin_admin_page')
        );
    }

    /**
     * List post types
     * @since 1.0.0
     * @return array of WP_Post_Types objects
     */
    public function list_posts_types() {

        $args = array(
            'public' => true,
            'show_in_rest' => true
        );

        $post_types = get_post_types(
           $args,
            'objects',
            'and'
        );

        return $post_types;
    }

    /**
     * Register settings
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_settings() {

        /**
         * Register the setting
         * The first parameters is need to link settings section or settings field to it
         * Second parameters is the option name. Every fields will be stored inside this single option.
         * When our settings form is submited, WordPress serialized all the settings in the option.
         */
        register_setting( 
            $this->plugin->_settings->settings_name, 
            $this->plugin->_settings->posts_types_option_name
        );

        /**
         * A settings section
         * This is optionnal, but it allow us to organize our fields settings. In this case, all the post types 
         * settings will be grouped in it
         */
        add_settings_section(
            'aera_post_types_section',
            __( 'Post types additional data', 'alvan-extend-wp-rest-api' ),
            array( $this, 'post_types_section_cb' ),
            $this->plugin->_settings->settings_name
        );

        // Get all the post types
        $post_types = $this->list_posts_types();

        // Loop over the posts types to create the settings fields
        foreach($post_types as $post_type) {
            add_settings_field(
                'aera_post_type_' . $post_type->name,
                $post_type->label,
                array( $this, 'post_type_field_cb' ),
                $this->plugin->_settings->settings_name,
                'aera_post_types_section',
                array(
                    'post_type_name' => $post_type->name,
                    'rest_base' => $post_type->rest_base ? $post_type->rest_base : $post_type->name
                )
            );
        }

    }

    /**
     * The call to the post types section
     * 
     * @since 1.0.0
     * @return void
     * 
     */
    public function post_types_section_cb() {
        echo '<p>' . __( 'Select the additional data you want to add to the endpoint of each public post type. Post types must also have show_in_rest set to true.', 'alvan-extend-wp-rest-api' ) . '</p>';
    }

    /**
     * The call back for post type field
     * 
     * @since 1.0.0
     * @return void
     */
    public function post_type_field_cb( $args ) {

        $option_name = $this->plugin->_settings->posts_types_option_name;
        $options = get_option($option_name);

        $post_rest_api_fields = array(
            'featured_media_url' => array(
                'label' => __( 'Featured media URL', 'alvan-extend-wp-rest-api' ),
                'support' => 'thumbnail'
            ),
            'author_name' => array(
               'label' => __( 'Author\'s name', 'alvan-extend-wp-rest-api' ),
            ),
            'avatar_url' => array(
                'label' => __( 'Author\'s avatar URL', 'alvan-extend-wp-rest-api' ),
             ),
        );

        $endpoint = get_rest_url( null, 'wp/v2/' . $args['rest_base'] );

        foreach($post_rest_api_fields as $field => $params ) :

            $id = $option_name . '-' . $args['post_type_name'] . '-' . $field;

            if( !empty( $params['support'] ) && !post_type_supports( $args['post_type_name'], $params['support'] ) ) {
                continue;
            }
          
            ?>
                <div class="input-wrap">
                    <input
                        id="<?php echo esc_attr( $id ); ?>"
                        type="checkbox" 
                        name="<?php echo esc_attr( $option_name . '['. $args['post_type_name'] .']['. $field .']' ); ?>"
                        
                        <?php echo isset( $options[ $args['post_type_name'] ][ $field ]) ? checked( $options[ $args['post_type_name'] ][ $field ], 'on', false ) : ''; ?>
                    />
                    <label for="<?php echo esc_attr( $id ); ?>">
                        <?php echo esc_html( $params['label'] ) ;?>
                    </label>
                </div>
        <?php endforeach; ?>
            <p class="aera-information">
                <a target="_blank" href="<?php echo $endpoint ?>"><?php echo $endpoint; ?></a>
            </p>
        <?php
    }
   
    /**
     * Render the menu page
     * @since 1.0.0
     * @return void
     */
    public function plugin_admin_page() {
       
        ?>
        <div class="wrap" id="aera-settings">
            <h1 id="aera-main-title"><?php _e( 'Extend REST API', 'alvan-extend-wp-rest-api' ); ?></h1>

            <form id="aera-settings-form" class="aera-settings-form" method="post" action="options.php">
                <?php
                settings_fields( $this->plugin->_settings->settings_name ); 
                
                do_settings_sections( $this->plugin->_settings->settings_name );

                submit_button( __( 'Save settings', 'alvan-extend-wp-rest-api' ) ); ?>
            </form>
        </div>

        <?php
    }
}