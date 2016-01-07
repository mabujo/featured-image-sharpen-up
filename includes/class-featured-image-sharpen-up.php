<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Featured_Image_Sharpen_Up {

	/**
	 * The single instance of Featured_Image_Sharpen_Up.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * An array of replacement image ids
	 * @var array
	 * @access private
	 * @since  1.0.0
	 */
	private $sharpen_ups = array();

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'featured_image_sharpen_up';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// add svg file to head
		add_action('wp_head', array($this, 'add_tiny_svg'), 100);

		/* add tiny images post thumbnail size */
		add_image_size( 'sharpen-up-thumbnail', 9999, 40 ); // 40 pixels wide (and unlimited height)

		/* filter post thumbnail */
		add_filter( 'post_thumbnail_html', array($this, 'filter_post_thumbnail_html'), 10, 5 );

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.min.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'featured-image-sharpen-up', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'featured-image-sharpen-up';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Featured_Image_Sharpen_Up Instance
	 *
	 * Ensures only one instance of Featured_Image_Sharpen_Up is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Featured_Image_Sharpen_Up()
	 * @return Main Featured_Image_Sharpen_Up instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

	/**
	 * Called from wp_head action
	 * Generates css rules for each thumbnail image found on the page
	 * and outputs them to head
	 */
	public function add_tiny_svg()
	{
		// post object
		global $post;

		// we need to run through the loop to get access
		// to thumbnail images during wp_head
		if ( have_posts() )
		{
			$css_rules = array();

			// loop through e posts
			while ( have_posts() )
			{
				the_post();

				$post_thumbnail_id = get_post_thumbnail_id($post->ID);

				// gets the small image size url
				$thumbnail_img_src = $this->get_or_generate_sharpen_up_thumbnail($post_thumbnail_id);

				// get the small image and base 64 encode it
				if (!empty($thumbnail_img_src))
				{
					$thumbnail_file = file_get_contents($thumbnail_img_src);
					$thumbnail_file_type = pathinfo($thumbnail_img_src, PATHINFO_EXTENSION);
					$thumbnail_base64 = 'data:image/' . $thumbnail_file_type . ';base64,' . base64_encode($thumbnail_file);

					// get the dimensions of the image
					$dimensions = $this->get_image_dimensions($post_thumbnail_id);

					// generate an SVG containing our encoded image
					$svg = $this->generate_svg($thumbnail_base64, $dimensions);

					// add the image as a uri encoded svg inside a background image css rule
					$css_rules[] = '.sharpen_up_post_thumbnail_' . $post_thumbnail_id . ' { ' .
									'background-image: url(data:image/svg+xml;charset=utf-8,' .
									$svg .
									');' .
									' } ';

					// add this thumbnail to the array of thumbnails
					$this->sharpen_ups[] = $post_thumbnail_id;
				}
			}
		}

		// if we have css images, output them in the header
		if (!empty($css_rules))
		{
			echo '<style type="text/css">';
			foreach ($css_rules as $css_rule) {
				echo $css_rule;
			}
			echo '</style>';
		}
	}

	/**
	 * Replace the post thumbnail img tag with an empty div that will contain our image
	 * @param  [type] $html              [description]
	 * @param  [type] $post_id           [description]
	 * @param  [type] $post_thumbnail_id [description]
	 * @param  [type] $size              [description]
	 * @param  [type] $attr              [description]
	 * @return string                    Lazy load image thumnail div
	 */
	public function filter_post_thumbnail_html ( $html, $post_id, $post_thumbnail_id, $size, $attr )
	{
		// if we have a tiny image in the sharpen ups array for this id
		if (in_array($post_thumbnail_id, $this->sharpen_ups)) {
			// output div
			$replace 	 = '<div class="sharpen_up_post_thumbnail sharpen_up_post_thumbnail_' . $post_thumbnail_id . '" data-src="' . wp_get_attachment_image_src( $post_thumbnail_id, 'post-thumbnail' )[0] . ' " >';
			$replace 	.= '</div>';

			return $replace;
		}
		else {
			// we don't have an image, just return the html untouched
			return $html;
		}
	}

	/**
	 * Get the small thumbnail image type, or if not found, generate one
	 * this removes the need for a 're-generate thumbnails' type function
	 * although will add (some) load time the first time each image is loaded.
	 * @param  int 		$post_thumbnail_id
	 * @return string   Small image url
	 */
	private function get_or_generate_sharpen_up_thumbnail ($post_thumbnail_id)
	{
		$thumbnail = wp_get_attachment_metadata($post_thumbnail_id, 'sharpen-up-thumbnail');

		// we already have the sharpen-up-thumbnail size
		if (isset($thumbnail['sizes']['sharpen-up-thumbnail'])) {
			return wp_get_attachment_image_url($post_thumbnail_id, 'sharpen-up-thumbnail');
		}
		else // we need to generate the image size
		{
			// include the image library functions
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			// full image file
			$original_file = get_attached_file($post_thumbnail_id);

			// generate and update metadata, return img src
			$attach_data = wp_generate_attachment_metadata( $post_thumbnail_id, $original_file );
  			wp_update_attachment_metadata( $post_thumbnail_id,  $attach_data );

			return wp_get_attachment_image_url($post_thumbnail_id, 'sharpen-up-thumbnail');
		}
	}

	/**
	 * Take post_thumbnail_html $html contents and return the image dimensions
	 * @access private
	 * @since  1.0.0
	 * @param  string $html
	 * @return array       The thumbnail image height and width width in pixels
	 */
	private function get_image_dimensions ($post_thumbnail_id)
	{
		$thumbnail = wp_get_attachment_metadata($post_thumbnail_id, 'post-thumbnail');

		// we already have the post-thumbnail size
		if (isset($thumbnail['sizes']['post-thumbnail'])) {
			$dimensions = array('width' => $thumbnail['sizes']['post-thumbnail']['width'], 'height' => $thumbnail['sizes']['post-thumbnail']['height']);
		}
		else {
			// // find width attribute
			// preg_match('/(width)=("[^"]*")/i',$html, $width_matches);
			// preg_match('/(height)=("[^"]*")/i',$html, $height_matches);

			// // get only the number
			// $width = (int) preg_replace("/[^0-9]/","",$matches[0]);
			// $dimensions = array('width' => (int) preg_replace("/[^0-9]/","",$width_matches[0]), 'height' => (int) preg_replace("/[^0-9]/","",$height_matches[0]));
			$dimensions = false;
		}

		return $dimensions;
	}

	/**
	 * Puts the base64 encoded small image file into an SVG
	 * template and uri encodes the result.
	 * @param  string $thumbnail_base64 base64 encoded image
	 * @param  array $dimensions       height and width of image
	 * @return string                   url encoded svg containing image
	 */
	private function generate_svg($thumbnail_base64, $dimensions)
	{
		// svg template
		$svg =
		'<svg xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink"
			width="' . $dimensions['width'] . '" height="' . $dimensions['height'] . '"
			viewBox="0 0 ' . $dimensions['width'] . ' ' . $dimensions['height'] .'">
			<filter id="blur" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
				<feGaussianBlur stdDeviation="20 20" edgeMode="duplicate" />
				<feComponentTransfer>
					<feFuncA type="discrete" tableValues="1 1" />
				</feComponentTransfer>
			</filter>
			<image filter="url(#blur)"
				xlink:href="' . $thumbnail_base64  . '"
				x="0" y="0"
				height="100%" width="100%"/>
		</svg>';

		// encode the svg for browser support
		$svg_encoded = rawurlencode($svg);

		return $svg_encoded;
	}
}