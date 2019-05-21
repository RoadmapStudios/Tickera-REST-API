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
	 * @since    1.0.0define_public_hooks
	 * @access   privadefine_public_hooks
	 * @var      strindefine_public_hooks version of this plugin.
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
	 * Function to be called before query is executed
	 *
	 * @param mixed  $query_vars generated for rest api.
	 * @param string $request resquested parameters for the api call.
	 */
	public function app_event_meta_vars( $query_vars, $request ) {
		$orderby = $request->get_param( 'filter' );
		if ( isset( $orderby ) && $orderby['meta_key'] != '' ) {
			$query_vars['orderby']  = 'meta_value';
			$query_vars['meta_key'] = $orderby['meta_key'];
		}
		return $query_vars;
	}

	/**
	 * Function to be called at activation of hook to activate api
	 *
	 * @param mixed  $args argument passed by event.
	 * @param string $post_type post type.
	 */
	public function app_event_rest_end_point( $routes ) {
		foreach ( [ 'tc_events' ] as $type ) {
			if ( ! ( $route =& $routes[ '/wp/v2/' . $type ] ) ) {
					continue;
			}

			// Allow ordering by my meta value
			$route[0]['args']['orderby']['enum'][] = 'meta_value_num';

			// Allow only the meta keys that I want
			$route[0]['args']['meta_key'] = array(
				'type' => 'datetime',
				'enum' => [ 'event_date_time' ],
			);
		}

		return $routes;
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
		// if ( 'product' === $post_type ) {
		// $args['show_in_rest']          = true;
		// $args['rest_base']             = 'product';
		// $args['rest_controller_class'] = 'WP_REST_Posts_Controller';
		// }
		return $args;
	}

	/**
	 * Function for calling updated api event
	 *
	 * @return void
	 */
	public function app_event_update_api() {
		// Edit content of API.
		// register_rest_field(
		// 'product',
		// 'content'
		// );
		register_rest_route(
			'appevent/v2',
			'/my-tickets/(?P<id>\d+)/(?P<platform>[a-zA-Z]+)',
			array(
				'methods'  => 'GET',
				'callback' => function( $request ) {
					global $tc, $wp;
					$upload_dir = wp_upload_dir();
					$user_id = $request['id'];
					$platform = $request['platform'];
					$customer_orders = get_posts(
						array(
							'numberposts' => -1,
							'meta_key'    => '_customer_user',
							'meta_value'  => $user_id,
							'post_type'   => wc_get_order_types(),
							'post_status' => array_keys( wc_get_order_statuses() ),
						)
					);
					$allTickets      = array();
					$i               = 0;

					foreach ( $customer_orders as $post ) {
						$order = new TC_Order( $post->ID );
						// $args            = array(
						// 'posts_per_page' => -1,
						// 'orderby'        => 'post_date',
						// 'order'          => 'ASC',
						// 'post_type'      => 'tc_tickets_instances',
						// 'post_parent'    => $order->details->ID,
						// );
						// $tickets         = get_posts( $args );
						$order_attendees = TC_Orders::get_tickets_ids( $post->ID );
						foreach ( $order_attendees as $order_attendee_id ) {

							$ticket_type_id   = get_post_meta( $order_attendee_id, 'ticket_type_id', true );
							$ticket_type_name = get_the_title( $ticket_type );

							$ticket_type       = new TC_Ticket( $ticket_type_id );
							$ticket_type_title = $ticket_type->details->post_title;
							$ticket_type_title = apply_filters( 'tc_checkout_owner_info_ticket_title', $ticket_type_title, $ticket_type_id, array(), $order_attendee_id );

							$event_id   = get_post_meta( $order_attendee_id, 'event_id', true );
							$event      = new TC_Event( $event_id );
							$event_name = $event->details->post_title;

							$first_name  = get_post_meta( $order_attendee_id, 'first_name', true );
							$last_name   = get_post_meta( $order_attendee_id, 'last_name', true );
							$email       = get_post_meta( $order_attendee_id, 'owner_email', true );
							$ticket_code = get_post_meta( $order_attendee_id, 'ticket_code', true );

							$order_key    = isset( $wp->query_vars['tc_order_key'] ) ? $wp->query_vars['tc_order_key'] : strtotime( $order->details->post_date );
							$download_url = apply_filters( 'tc_download_ticket_url_front', wp_nonce_url( trailingslashit( $tc->get_order_slug( true ) ) . $order->details->post_title . '/' . $order_key . '/?download_ticket=' . $ticket_id . '&order_key=' . $order_key, 'download_ticket_' . $ticket_id . '_' . $order_key, 'download_ticket_nonce' ), $order_key, $order_attendee_id );

							$displayFileName = $upload_dir['url'] . '/' . $ticket_code . '.pkpass';
							if ( $platform == 'android' ) {
								$walletURL = 'https://walletpass.io?u=' . $displayFileName;
								$walletImg = 'https://www.walletpasses.io/badges/badge_web_generic_en@2x.png';
							} elseif ( $platform == 'ios' ) {
								$walletURL = $displayFileName;
								$walletImg = plugin_dir_url( __DIR__ ) . 'assets/img/add-to-apple-wallet.jpg';
							}

							$allTickets[ $i ]['ticket_id']        = $order_attendee_id;
							$allTickets[ $i ]['event_name']       = $event_name;
							$allTickets[ $i ]['ticket_type'] = $ticket_type_title;
							$allTickets[ $i ]['first_name']       = $first_name;
							$allTickets[ $i ]['last_name']        = $last_name;
							$allTickets[ $i ]['email']            = $email;
							$allTickets[ $i ]['ticket_code']      = $ticket_code;
							$allTickets[ $i ]['download_url']     = str_replace( '&amp;', '&', $download_url );
							$allTickets[ $i ]['walletURL']      = str_replace( '&amp;', '&', $walletURL );
							$allTickets[ $i ]['walletImg']      = $walletImg;
							$i++;
						}

						// echo 'count($tickets): ' . count( $tickets );
						// print_r( $tickets );
						// foreach ( $tickets as $ticket ) {
						// $ticket_id      = $ticket->ID;
						// $ticket_type_id = get_post_meta( $ticket_id, 'ticket_type_id', true );
						// $ticket_type    = new TC_Ticket( $ticket_type_id );
						// $event_id       = $ticket_type->get_ticket_event( apply_filters( 'tc_ticket_type_id', $ticket_type_id ) );
						// $event          = new TC_Event( $event_id );
						// $event_name     = $event->details->post_title;
						// $ticket_type_title = $ticket_type->details->post_title;
						// echo '<br /> $ticket_type_title: ' . $ticket_type_title;
						// $ticket_type_title = apply_filters( 'tc_checkout_owner_info_ticket_title', $ticket_type_title, $ticket_type_id, array(), $ticket_id );
						// $cart_info  = get_post_meta( $order->details->ID, 'tc_cart_info', true );
						// $buyer_data = $cart_info['buyer_data'];
						// $buyer_name = $buyer_data['first_name_post_meta'] . ' ' . $buyer_data['last_name_post_meta'];
						// echo '$order->details->ID: ' . $order->details->ID;
						// echo '<br /> $event_id: ' . $event_id;
						// echo '<br /> $event_name: ' . $event_name;
						// echo '<br /> $ticket_type_title: ' . $ticket_type_title;
						// echo '<br /> $buyer_name: ' . $buyer_name;
						// $order_key = isset( $wp->query_vars['tc_order_key'] ) ? $wp->query_vars['tc_order_key'] : strtotime( $order->details->post_date );
						// $download_url = apply_filters( 'tc_download_ticket_url_front', wp_nonce_url( trailingslashit( $tc->get_order_slug( true ) ) . $order->details->post_title . '/' . $order_key . '/?download_ticket=' . $ticket_id . '&order_key=' . $order_key, 'download_ticket_' . $ticket_id . '_' . $order_key, 'download_ticket_nonce' ), $order_key, $ticket_id );
						// echo '<br /> $download_url: ' . $download_url;
						// }
					}

					echo json_encode( $allTickets );
					die();
				},
			)
		);

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
			.*?<\/a>/imx',
				$content,
				$matches
			);
			$site_url = get_site_url();
			foreach ( $matches[1] as $skew ) {
				$nskew   = str_replace( '/wp-json/wp/v2/tc_events', '', $skew );
				$link    = $object['link'] . $nskew;
				$content = str_replace( $skew, $link, $content );
			}
			$object['content']['rendered'] = $content;
			return $object['content'];
		}
	}

	/**
	 * Function to enable taxonomy with rest API for custom event.
	 *
	 * @return void
	 */
	public function app_event_add_tax_to_api() {
		$mytax               = get_taxonomy( 'event_category' );
		$mytax->show_in_rest = true;
	}
}
