<?php
/**
 * Email base class
 *
 * @since   1.0.0
 * @package Awesome9\Email
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Email;

use function Awesome9\Templates\get_template;

/**
 * Email class
 */
class Email {

	/**
	 * Default email type.
	 *
	 * @var string
	 */
	public $email_type = 'html';

	/**
	 * Default heading.
	 *
	 * @var string
	 */
	public $heading = '';

	/**
	 * Default subject.
	 *
	 * @var string
	 */
	public $subject = '';

	/**
	 * Plain text template path.
	 *
	 * @var string
	 */
	public $template_plain;

	/**
	 * HTML template path.
	 *
	 * @var string
	 */
	public $template_html;

	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	public $recipient;

	/**
	 *  List of preg* regular expression patterns to search for,
	 *  used in conjunction with $plain_replace.
	 *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
	 *
	 *  @var array $plain_search
	 *  @see $plain_replace
	 */
	public $plain_search = array(
		"/\r/",                                                  // Non-legal carriage return.
		'/&(nbsp|#0*160);/i',                                    // Non-breaking space.
		'/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i', // Double quotes.
		'/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i',               // Single quotes.
		'/&gt;/i',                                               // Greater-than.
		'/&lt;/i',                                               // Less-than.
		'/&#0*38;/i',                                            // Ampersand.
		'/&amp;/i',                                              // Ampersand.
		'/&(copy|#0*169);/i',                                    // Copyright.
		'/&(trade|#0*8482|#0*153);/i',                           // Trademark.
		'/&(reg|#0*174);/i',                                     // Registered.
		'/&(mdash|#0*151|#0*8212);/i',                           // mdash.
		'/&(ndash|minus|#0*8211|#0*8722);/i',                    // ndash.
		'/&(bull|#0*149|#0*8226);/i',                            // Bullet.
		'/&(pound|#0*163);/i',                                   // Pound sign.
		'/&(euro|#0*8364);/i',                                   // Euro sign.
		'/&(dollar|#0*36);/i',                                   // Dollar sign.
		'/&[^&\s;]+;/i',                                         // Unknown/unhandled entities.
		'/[ ]{2,}/',                                             // Runs of spaces, post-handling.
	);

	/**
	 *  List of pattern replacements corresponding to patterns searched.
	 *
	 *  @var array $plain_replace
	 *  @see $plain_search
	 */
	public $plain_replace = array(
		'',              // Non-legal carriage return.
		' ',             // Non-breaking space.
		'"',             // Double quotes.
		"'",             // Single quotes.
		'>',             // Greater-than.
		'<',             // Less-than.
		'&',             // Ampersand.
		'&',             // Ampersand.
		'(c)',           // Copyright.
		'(tm)',          // Trademark.
		'(R)',           // Registered.
		'--',            // mdash.
		'-',             // ndash.
		'*',             // Bullet.
		'£',             // Pound sign.
		'EUR',           // Euro sign. € ?.
		'$',             // Dollar sign.
		'',              // Unknown/unhandled entities.
		' ',             // Runs of spaces, post-handling.
	);

	/**
	 * Strings to find/replace in subjects/headings.
	 *
	 * @var array
	 */
	protected $placeholders = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );

		// Find/Replace.
		$this->placeholders = array_merge(
			array(
				'{site_address}' => $domain,
				'{site_url}'     => $domain,
				'{site_title}'   => Manager::get()->get_blogname(),
			),
			$this->placeholders
		);
	}

	/**
	 * Format email string.
	 *
	 * @param  mixed $string Text to replace placeholders in.
	 * @return string
	 */
	public function format_string( $string ) {
		$find    = array_keys( $this->placeholders );
		$replace = array_values( $this->placeholders );

		return str_replace( $find, $replace, $string );
	}

	/**
	 * Get email type.
	 *
	 * @return string
	 */
	public function get_email_type() {
		return $this->email_type && class_exists( 'DOMDocument' ) ? $this->email_type : 'plain';
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return $this->format_string( $this->subject );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_heading() {
		return $this->format_string( $this->heading );
	}

	/**
	 * Get email content.
	 *
	 * @return string
	 */
	public function get_content() {
		if ( 'plain' === $this->get_email_type() ) {
			return wordwrap(
				preg_replace(
					$this->plain_search,
					$this->plain_replace,
					wp_strip_all_tags( $this->get_content_plain() )
				),
				70
			);
		}

		return $this->get_content_html();
	}

	/**
	 * Get the email content in plain text format.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return get_template( 'emails', $this->template_plain, $this->args );
	}

	/**
	 * Get the email content in HTML format.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return get_template( 'emails', $this->template_html, $this->args );
	}

	/**
	 * Get email headers.
	 *
	 * @return string
	 */
	public function get_headers() {
		$headers   = array();
		$headers[] = 'Content-Type: ' . $this->get_content_type();
		$headers[] = 'Reply-to: ' . Manager::get()->get_from_name() . ' <' . Manager::get()->get_from_email() . '>';

		return $headers;
	}

	/**
	 * Get email content type.
	 *
	 * @param  string $content_type Default wp_mail() content type.
	 * @return string
	 */
	public function get_content_type( $content_type = '' ) {
		switch ( $this->get_email_type() ) {
			case 'html':
				$content_type = 'text/html';
				break;
			case 'multipart':
				$content_type = 'multipart/alternative';
				break;
			default:
				$content_type = 'text/plain';
				break;
		}

		return $content_type;
	}

	/**
	 * Get email attachments.
	 *
	 * @return array
	 */
	public function get_attachments() {
		return array();
	}

	/**
	 * Send an email.
	 *
	 * @param  string $to   Email to.
	 * @param  array  $args Array of arguments to be used in templates.
	 * @return bool success
	 */
	public function send( $to, $args = array() ) {
		add_filter( 'wp_mail_from', array( Manager::get(), 'get_from_email' ) );
		add_filter( 'wp_mail_from_name', array( Manager::get(), 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		$this->args = wp_parse_args(
			$args,
			array(
				'email'   => $this,
				'heading' => $this->get_heading(),
			)
		);

		$return = wp_mail(
			$to,
			$this->get_subject(),
			$this->wrap_message(),
			$this->get_headers(),
			$this->get_attachments()
		);

		remove_filter( 'wp_mail_from', array( Manager::get(), 'get_from_email' ) );
		remove_filter( 'wp_mail_from_name', array( Manager::get(), 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		return $return;
	}

	/**
	 * Wrap message to send.
	 *
	 * @return string
	 */
	public function wrap_message() {
		$out  = '';
		$out .= get_template( 'emails', 'email-header', $this->args );
		$out .= wpautop( wptexturize( $this->get_content() ) ); // WPCS: XSS ok.
		$out .= get_template( 'emails', 'email-footer', $this->args );

		// Prepare styles to be added.
		$this->style_inline();

		return $this->format_string( $out );
	}

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
	 *
	 * @return string
	 */
	public function style_inline() {
		if ( 'plain' === $this->get_email_type() ) {
			return;
		}

		$css = get_template( 'emails', 'email-styles' );

		$this->placeholders['{styles}'] = '<style type="text/css">' . $css . '</style>';
	}
}
