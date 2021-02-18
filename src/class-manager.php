<?php
/**
 * Email manger class
 *
 * @since   1.0.0
 * @package Awesome9\Email
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Email;

use Awesome9\Templates\Storage;

/**
 * Manager class
 */
class Manager {

	/**
	 * From name.
	 *
	 * @var string
	 */
	private $from_name = false;

	/**
	 * From email.
	 *
	 * @var string
	 */
	private $from_email = false;

	/**
	 * Retrieve main instance.
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return Manager
	 */
	public static function get() {
		static $instance;

		if ( is_null( $instance ) && ! ( $instance instanceof Manager ) ) {
			$instance = new Manager();
			$instance->setup();
		}

		return $instance;
	}

	/**
	 * Setup Manager
	 */
	private function setup() {
		// Default template base.
		Storage::get()->add( 'emails', 'emails' );
	}

	/**
	 * Get the from name for outgoing emails.
	 *
	 * @return string
	 */
	public function get_from_name() {
		if ( false === $this->from_name ) {
			$this->set_from_name( $this->get_blogname() );
		}

		return $this->from_name;
	}

	/**
	 * Set the from name for outgoing emails.
	 *
	 * @param  string $from_name From name.
	 * @return Manager
	 */
	public function set_from_name( $from_name ) {
		$this->from_name = wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );

		return $this;
	}

	/**
	 * Get the from address for outgoing emails.
	 *
	 * @return string
	 */
	public function get_from_email() {
		if ( false === $this->from_email ) {
			$this->set_from_email( get_bloginfo( 'admin_email' ) );
		}

		return $this->from_email;
	}

	/**
	 * Set the from address for outgoing emails.
	 *
	 * @param  string $from_email From email.
	 * @return Manager
	 */
	public function set_from_email( $from_email ) {
		$this->from_email = sanitize_email( $from_email );

		return $this;
	}

	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
}
