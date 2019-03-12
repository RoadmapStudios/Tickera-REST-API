<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://roadmapstudios.com/
 * @since      1.0.0
 *
 * @package    App_Event
 * @subpackage App_Event/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    App_Event
 * @subpackage App_Event/public
 * @author     Roadmap Studios
 */
class App_Event_Public {

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
	 * @param      string    $app_event       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $app_event, $version ) {

		$this->app_event = $app_event;
		$this->version   = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->app_event, plugin_dir_url( __FILE__ ) . 'css/plugin-name-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->app_event, plugin_dir_url( __FILE__ ) . 'js/plugin-name-public.js', array( 'jquery' ), $this->version, false );

	}

}
