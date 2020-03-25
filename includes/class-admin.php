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
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }


    /**
     * Register settings
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_settings() {

        register_setting( 
            $this->plugin->settings->settings_name, 
            $this->plugin->settings->option_name
        );

        add_settings_section(
            'aera_post_types_section',
            __( 'Post Types', 'aera' ),
            array( $this, 'aera_post_types_section_cb' ),
            $this->plugin->settings->settings_name
        );

        add_settings_field(
            'aera_post_types',
            __('Post Types'),
            array( $this, 'post_type_field_cb' ),
            $this->plugin->settings->settings_name,
            'aera_post_types_section'
        );
    }

    public function aera_post_types_section_cb() {
        echo 'Post types';
    }

    public function aera_post_type_field_cb() {

        $option_name = $this->plugin->settings->option_name;
        $options = get_option($option_name);

        $post_types = get_post_types(
            array( 
                'public' => apply_filters( 'aera_post_type_visibility', true ) 
            ),
            'objects'
        );

        $rest_api_fields = array(
            'thumbnail_url' => __( 'Thumbnail URL', 'aera' ),
            'author_name' => __( 'Author Name', 'aera' ),
            'avatar_url' => __( 'Avatar URL', 'aera' ),
        );

        foreach( $post_types as $post_type ) {
    
            foreach($rest_api_fields as $field => $label) {
               ?>
                    <input
                     type="checkbox" 
                     value="<?php echo $option_name . '['. $post_type .']['. $field .']'; ?>"
                     <?php echo  isset( $options[ $post_type ][ $field ]) ? selected( $options[ $post_type ][ $field ], $field, false ) : ''; ?>
                     >
                     <?php echo $label ;?>
                     <br>
               <?php
            }
        }
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
        echo '<div class="wrap">';
        echo '<h1>Extend REST API</h1>';

        ?>

        <form method="post" action="options.php">
            <?php
             settings_fields( $this->plugin->settings->option_name ); 
             
             do_settings_sections( $this->plugin->settings->option_name );

             submit_button(__( 'Save settings', 'aera' )); ?>
        </form>
        </div>

        <?php
    }
}