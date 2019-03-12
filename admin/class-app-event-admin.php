<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://roadmapstudios.com/
 * @since      1.0.0
 *
 * @package    App_Event
 * @subpackage App_Event/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    App_Event
 * @subpackage App_Event/admin
 * @author     Roadmap Studios
 */
class App_Event_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $app_event    The ID of this plugin.
	 */
	private $app_event;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param   string $app_event       The name of this plugin.
	 * @param   string $version    The version of this plugin.
	 */
	public function __construct( $app_event, $version ) {

		$this->app_event = $app_event;
		$this->version   = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->app_event, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->app_event, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Function to be called at activation of hook to activate api
	 *
	 * @param mixed  $args argument passed by event.
	 * @param string $post_type post type.
	 */
	public function app_event_activate_api( $args, $post_type ) {
		if ( 'tc_events' === $post_type ) {
			$args['show_in_rest']          = true;
			$args['rest_base']             = 'tc_events';
			$args['rest_controller_class'] = 'WP_REST_Posts_Controller';
		}
		return $args;
	}

	/**
	 * Function for calling updated api event
	 *
	 * @return void
	 */
	public function app_event_update_api() {
		// Edit content of API.
		register_rest_field(
			'tc_events',
			'content',
			array(
				'get_callback'    => function( $object, $field_name, $request ) {
					return $this->get_event_update( $object, $field_name, $request );
				},
				'update_callback' => null,
				'schema'          => null,
			)
		);

		// Edit features images of API.
		register_rest_field(
			'tc_events',
			'featured_image_urls',
			array(
				'get_callback'    => function( $object, $field_name, $request ) {
					$thumbnail_id = get_post_thumbnail_id( $object['id'] );
					if ( $thumbnail_id ) {
						$size_arr = get_intermediate_image_sizes();
						$thumbnail_urls = array();
						foreach ( $size_arr as $size ) {
							$image_url = wp_get_attachment_image_src( $thumbnail_id, $size );
							if ( $image_url ) {
								$thumbnail_urls[ $size ] = $image_url[0];
							}
						}
						$object['featured_image_urls'] = $thumbnail_urls;
						return $object['featured_image_urls'];
					}
				},
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}

	/**
	 * Function for updating api response data.
	 *
	 * @param mixed $object Event response object.
	 * @param mixed $field_name field_name.
	 * @param mixed $request Request bject.
	 * @return mixed
	 */
	public function get_event_update( $object, $field_name, $request ) {
		$content = $object['content']['rendered'];
		if ( $object['content'] ) {
			preg_match_all(
				'/<a\b(?=\s) # capture the open tag
			(?=(?:[^>=]|=\'[^\']*\'|="[^"]*"|=[^\'"][^\s>]*)*?\shref="(\/wp-json\/wp\/v2\/[^"]*)) # get the href attribute
			(?:[^>=]|=\'[^\']*\'|="[^"]*"|=[^\'"\s]*)*"\s?> # get the entire tag
			.*?<\/a>/imx', $content, $matches
			);
			$site_url = get_site_url();
			foreach ( $matches[1] as $skew ) {
				$content = str_replace( $skew, $site_url . $skew, $content );
			}
			$object['content']['rendered'] = $content;
			return $object['content'];

		}
	}

}
