<?php
/**
 * Sharing
 *
 * @author Doug Axelrod <axelrod@homefrontdc.com>
 */
class Share {

	protected static $_instance;

	/**
	 * Internal plugins
	 * @var array of SharePlugins
	 */
	protected $_plugins;

	/**
	 * Returns an instance of Zend_Auth
	 *
	 * Singleton pattern implementation
	 *
	 * @return Share
	 */
	public static function get_instance() {
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	protected function __construct( ) { }

	/**
	 * Add a plugin
	 *
	 * @param string $type Share plugin type (either class name, or plugin name minus 'Share')
	 * @param string $alias Alias of share plugin (used in get)
	 * @return SharePlugin|boolean Plugin if successful, false if not
	 */
	public function add ( $type, $alias ) {

		$plugin = null;

		if ( class_exists($type) ) {
			$plugin = new $type;
		} elseif ( class_exists('Share' . $type) ) {
			$type = 'Share' . $type;
			$plugin = new $type;
		}

		if ( ! $plugin instanceof SharePlugin ) {
			// Couldn't create the plugin
			return false;
		}

		if ( empty($alias) ) {
			$this->_plugins[] = $plugin;
		} else {
			$this->_plugins[$alias] = $plugin;
		}

		return $plugin;
	}

	/**
	 *
	 * @param string $alias
	 * @return SharePlugin|boolean
	 */
	public function get ( $alias ) {

		if ( ! isset($this->_plugins[$alias]) ) {
			return false;
		}

		return $this->_plugins[$alias];
	}

	/**
	 * Shares something with all the plugins
	 *
	 * @param string $share_text
	 * @param string $share_url
	 * @param string $share_image
	 */
	public function share ( $share_text, $share_url = null, $share_image = null ) {

		foreach ( $this->_plugins as $plugin ) {
			$plugin->share($share_text, $share_url, $share_image);
		}
	}

	/**
	 * Gets the includes for all the plugins
	 *
	 * @return string
	 */
	public function get_includes ( ) {

		$foot = '';
		foreach ( $this->_plugins as $plugin ) {
			$foot .= $plugin->get_includes();
		}

		return $foot;
	}

}

/**
 * Abstract class implemented by Share plugins
 *
 *
 */
abstract class SharePlugin {

	/**
	 * External scripts to include once per page
	 * @var array of src values
	 */
	protected $external_scripts = array();

	/**
	 * Inline scripts to include once per page
	 * @var array of javascript
	 */
	protected $inline_scripts = array();

	/**
	 * External stylesheets to include once per page
	 * @var array of href values or href => media
	 */
	protected $external_styles = array();

	/**
	 * Inline styles to include once per page
	 * @var array of css
	 */
	protected $inline_styles = array();

	/**
	 * Tracks if scripts have been sent
	 * @var bool
	 */
	protected $sent_scripts = false;

	/**
	 * Tracks if styles have been sent
	 * @var bool
	 */
	protected $sent_styles = false;

	/**
	 * Share
	 * @var string
	 */
	protected $share_text, $share_url, $share_image;

	/**
	 * Share
	 *
	 * @param unknown $share_text
	 * @param unknown $share_url
	 * @param string $share_image
	 * @return $this
	 */
	abstract public function share ( $share_text, $share_url = null, $share_image = null );

	/**
	 * Get includes for a page
	 *
	 * @return string
	 */
	abstract public function get_includes ( );

	/**
	 * Get Button
	 *
	 * @param array $attributes Custom attributes for buttons
	 * @return string
	 */
	abstract public function get_button ( $attributes = array() );

	/**
	 * Template replace
	 *
	 * @param string $text
	 * @param array $replacements
	 * @return string
	 */
	public function template ( $text, array $replacements ) {

		return str_replace(array_keys($replacements), $replacements, $text);
	}

	/**
	 * Get scripts for output on page
	 *
	 * @return boolean|string
	 */
	protected function get_scripts ( ) {

		// Return early if scripts have been sent already
		if ( $this->sent_scripts ) return false;

		$scripts = '';

		// Skip if there are no scripts
		if ( count($this->external_scripts) > 0 || count($this->inline_scripts) > 0 ) {

			foreach ( $this->external_scripts as $src ) {
				$scripts .= '<script type="text/javascript" src="' . $src . '"></script>';
			}
			foreach ( $this->inline_scripts as $javascript ) {
				$scripts .= '<script>' . $javascript . '</script>';
			}
		}

		// Raise flag that these have already been sent
		$this->sent_scripts = true;

		// Return scripts
		return $scripts;
	}

	/**
	 * Get styles for output on page
	 *
	 * @return boolean|string
	 */
	protected function get_styles ( ) {

		// Return early if styles have been sent already
		if ( $this->sent_styles ) return false;

		$styles = '';

		// Skip if there are no styles
		if ( count($this->external_styles) > 0 || count($this->inline_styles) > 0 ) {

			foreach ( $this->external_styles as $href => $media ) {
				// In case they didn't specify media
				if ( is_numeric($href) && strlen($media) > 0 ) {
					$href = $media;
					$media = null;
				}
				$media = empty($media) ? 'all': $media;
				$styles .= '<link rel="stylesheet" type="text/css" href="' . $href . '" media="' . $media . '" />';
			}
			foreach ( $this->inline_styles as $css ) {
				$styles .= '<style type="text/css">' . $css . '</style>';
			}
		}

		// Raise flag that these have already been sent
		$this->sent_styles = true;

		// Return styles
		return $styles;
	}
}

/**
 * Pinterest
 *
 *
 */
class SharePinterest extends SharePlugin {

	protected $external_scripts = array('//assets.pinterest.com/js/pinit.js');

	/**
	 * Share
	 * @see SharePlugin::share()
	 */
	public function share( $share_text, $share_url = null, $share_image = null ) {

		if ( empty($share_url) ) die('You cannot share on Pinterest without a url!');
		if ( empty($share_image) ) die('You cannot share on Pinterest without an image!');

		$this->share_text = $share_text;
		$this->share_url = $share_url;
		$this->share_image = $share_image;

		return $this;
	}

	/**
	 * Get Includes
	 * @see SharePlugin::get_includes()
	 */
	public function get_includes ( ) {

		return $this->get_scripts();
	}

	/**
	 * Get Button
	 * @see SharePlugin::get_button()
	 */
	public function get_button ( $attributes = array() ) {

		$attributes = array_merge(
				array(
						'url' => $this->share_url,
						'media' => $this->share_image,
						'description' => $this->share_text,
						'count-layout' => 'vertical',
				), $attributes);

		// Templated description
		$description = $this->template($attributes['description'],
				array(
						'%url%' => $this->share_url,
						'%image%' => $this->share_image,
				));

		// Pinterest URL
		$url = 'http://pinterest.com/pin/create/button/'
				. '?url=' . rawurlencode($attributes['url'])
				. '&media=' . rawurlencode($attributes['media'])
				. '&description=' . rawurlencode($description);

		$button = '<a href="' . htmlentities($url, ENT_QUOTES) . '"'
				. ' class="pin-it-button"'
				. ' count-layout="' . $attributes['count-layout'] . '"'
				. '>'
				. '<img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" />'
				. '</a>';

		return $button;
	}
}

/**
 * Facebook
 *
 *
 */
class ShareFacebook extends SharePlugin {

	protected $inline_scripts = array('(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, \'script\', \'facebook-jssdk\'));');

	private $sent_fb_root = false;

	/**
	 * Share
	 * @see SharePlugin::share()
	 */
	public function share( $share_text, $share_url = null, $share_image = null ) {

		$this->share_text = $share_text;
		$this->share_url = $share_url;
		$this->share_image = $share_image;

		return $this;
	}

	/**
	 * Get Includes
	 * @see SharePlugin::get_includes()
	 */
	public function get_includes ( ) {

		return $this->get_fb_root() . $this->get_scripts();
	}

	/**
	 * Get Button
	 * @see SharePlugin::get_button()
	 */
	public function get_button ( $attributes = array() ) {

		$attributes = array_merge(
				array(
						'data-href' => $this->share_url,
						'data-layout' => 'box_count',
						'data-width' => '44',
				), $attributes);

		$button = '<div class="fb-like"'
				. ' data-href="' . htmlentities($attributes['data-href'], ENT_QUOTES) . '"'
				. ' data-send="false"'
				. ' data-layout="' . $attributes['data-layout'] . '"'
				. ' data-width="' . $attributes['data-width'] . '"'
				. ' data-show-faces="false"'
				. '>'
				. '</div>';

		return $button;
	}

	/**
	 * Get Open Graph tags
	 * @desc Should be called in the <head> of a page
	 * @link https://developers.facebook.com/docs/reference/plugins/like/
	 *
	 *
	 * @param string $site_name Human readable name for the site
	 * @param string $type Open graph type @link https://developers.facebook.com/docs/opengraphprotocol/#types
	 * @param array $fb in the form tag (minus the "fb:") => value
	 * @return string
	 */
	public function get_og_tags ( $title, $site_name, $type, $fb ) {

		// Build list of OG tags
		$og_tags = '';

		$og_tags .= $this->og_tag('title', $title);

		// Templated Description
		$description = $this->template($this->share_text,
				array(
						'%url%' => $this->share_url,
						'%image%' => $this->share_image,
				));
		$og_tags .= $this->og_tag('description', $description);

		// No share url here!

		if ( strlen($this->share_image) ) {
			$og_tags .= $this->og_tag('image', $this->share_image);
		}
		$og_tags .= $this->og_tag('site_name', $site_name);
		$og_tags .= $this->og_tag('type', $type);
		foreach ( $fb as $fb_tag => $fb_value ) {
			$og_tags .= $this->og_tag($fb_tag, $fb_value, 'fb:');
		}
		return $og_tags;
	}

	/**
	 * Get Open Graph redirect
	 * @desc Should be called in the <head> of a page
	 * Redirects users to the shared page while forcing facebook to crawl the meta data
	 *
	 * @return string
	 */
	public function get_og_redirect ( ) {

		return '<script>window.location = "' . htmlentities($this->share_url, ENT_QUOTES) . '";</script>';
	}

	/**
	 * Open Graph tag
	 *
	 * @param string $tag
	 * @param string $value
	 * @param string $prefix
	 * @return string
	 */
	private function og_tag ( $tag, $value, $prefix = 'og:' ) {

		return '<meta property="' . htmlentities($prefix, ENT_QUOTES) . htmlentities($tag, ENT_QUOTES) . '"'
				. ' content="' . htmlentities($value, ENT_QUOTES) . '"/>'
				. "\n";
	}

	/**
	 * Get the FB root element (needed before scripts)
	 * @return boolean|string
	 */
	private function get_fb_root ( ) {

		if ( $this->sent_fb_root ) return false;

		// Set flag that this has already been sent
		$this->sent_fb_root = true;

		return '<div id="fb-root"></div>';
	}
}

/**
 * Twitter
 *
 *
 */
class ShareTwitter extends SharePlugin {

	protected $inline_scripts = array('!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");');

	/**
	 * Share
	 * @see SharePlugin::share()
	 */
	public function share( $share_text, $share_url = null, $share_image = null ) {

		$this->share_text = $share_text;
		$this->share_url = $share_url;
		$this->share_image = $share_image;

		return $this;
	}

	/**
	 * Get Includes
	 * @see SharePlugin::get_includes()
	 */
	public function get_includes ( ) {

		return $this->get_scripts();
	}

	/**
	 * Get Button
	 * @see SharePlugin::get_button()
	 */
	public function get_button ( $attributes = array() ) {

		// data-via="uscensus"
		// data-hashtags="data"
		$attributes = array_merge(
				array(
						'data-url' => $this->share_url,
						'data-text' => $this->share_text,
						'data-count' => 'vertical',
				), $attributes);

		// Templated tweet
		$tweet = $this->template($attributes['data-text'],
				array(
						'%url%' => $this->share_url,
						'%image%' => $this->share_image, // No twitpic here! Just a straight up link to the image
				));

		$button = '<a href="https://twitter.com/share"'
				. ' class="twitter-share-button"'
				. ' data-url="' . htmlentities($attributes['data-url'], ENT_QUOTES) . '"'
				. ' data-text="' . htmlentities($tweet, ENT_QUOTES) . '"'
				. ' data-count="' . $attributes['data-count'] . '"'
				. '>'
				. 'Tweet'
				. '</a>';

		return $button;
	}
}

/**
 * Email
 *
 *
 */
class ShareEmail extends SharePlugin {

	/**
	 * Share
	 * @see SharePlugin::share()
	 */
	public function share( $share_text, $share_url = null, $share_image = null ) {

		$this->share_text = $share_text;
		$this->share_url = $share_url;
		$this->share_image = $share_image;

		return $this;
	}

	/**
	 * Get Includes
	 * @see SharePlugin::get_includes()
	 */
	public function get_includes ( ) {

		return '';
	}

	/**
	 * Get Button
	 * @see SharePlugin::get_button()
	 */
	public function get_button ( $attributes = array() ) {

		$attributes = array_merge(
				array(
						'to' => '',
						'subject' => '',
						'body' => $this->share_text,
				), $attributes);

		// Templated tweet
		$body = $this->template($attributes['body'],
				array(
						'%url%' => $this->share_url,
						'%image%' => $this->share_image,
				));

		$url = 'mailto:'
				. '?to=' . rawurlencode($attributes['to'])
				. '&subject=' . rawurlencode($attributes['subject'])
				. '&body=' . rawurlencode($body);

		$button = '<a href="' . $url . '">Email</a>';

		return $button;
	}
}