<?php

/**
 * Plugin Name.
 *
 * @package   Twitter Fire
 * @author    Adam Davis <adam@admataz.com>
 * @license   GPL-2.0+
 * @link      http://admataz.com
 * @copyright 2013 Adam Davis
 */
/**
 * @package Twitter_Fire
 * @author  Your Name <adam@admataz.com>
 */
class Twitter_Fire
{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '2.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'twitter-fire';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	var $cache_timer_minutes = 5; 
	var $number_of_tweets = 20;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct()
	{
		// Load plugin text domain
		add_action('init', array($this, 'load_plugin_textdomain'));
		// use this method to provide template tags - see http://nacin.com/2010/05/18/rethinking-template-tags-in-plugins/
		add_filter('twitterfire_get_tweets', array($this, 'output_tweets'), 10, 2);
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug()
	{
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{
		$domain = $this->plugin_slug;
		$locale = apply_filters('plugin_locale', get_locale(), $domain);
		load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
		load_plugin_textdomain($domain, FALSE, basename(dirname(__FILE__)) . '/languages/');
	}

	public function get_tweets()
	{
		$tobj =  new Twitterfier();
		$options = get_option($this->plugin_slug . '_settings', array());
		$tobj->init(
			$options['twitter_consumer_key_text'],
			$options['twitter_consumer_secret_text']
		);
		$tobj->get_user_tweetsV2($options['twitter_screen_name_text'], array(
			'max_results'=>$this->number_of_tweets
		));
		update_option('twitterfire_last_api_fetch', time() + $this->cache_timer_minutes * 60);
	}


	public function output_tweets($val, $num = 5)
	{
		$next_fetch = get_option('twitterfire_last_api_fetch', 0);
		if (!$next_fetch || time() > $next_fetch || $_GET['resetcache']) {
			$this->get_tweets();
		}
		$tobj =  new Twitterfier();
		return $tobj->output($this->number_of_tweets);
	}
}
