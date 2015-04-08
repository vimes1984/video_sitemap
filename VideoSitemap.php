<?php
/**
 * Video sitemap
 *
 * @package   video-sitemap
 * @author    bawd <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 4-4-2015 BAWD
 */

/**
 * Video sitemap class.
 *
 * @package VideoSitemap
 * @author  bawd <churchill.c.j@gmail.com>
 */
class VideoSitemap{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = "1.0.0";

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = "video-sitemap";

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action("init", array($this, "load_plugin_textdomain"));

		// Add the options page and menu item.
		add_action("admin_menu", array($this, "add_plugin_admin_menu"));


		// Load admin style sheet and JavaScript.
		add_action("admin_enqueue_scripts", array($this, "enqueue_admin_styles"));
		add_action("admin_enqueue_scripts", array($this, "enqueue_admin_scripts"));

		// Load public-facing style sheet and JavaScript.
		add_action("wp_enqueue_scripts", array($this, "enqueue_styles"));
		add_action("wp_enqueue_scripts", array($this, "enqueue_scripts"));

		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		add_action("TODO", array($this, "action_method_name"));
		add_filter("TODO", array($this, "filter_method_name"));

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn"t been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate($network_wide) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate($network_wide) {
		// TODO: Define deactivation functionality here
	}


	/**
	* Checks if a file is writable and tries to make it if not.
	*
	* @since 3.05b
	* @access private
	* @author  VJTD3 <http://www.VJTD3.com>
	* @return bool true if writable
	*/
	public function IsVideoSitemapWritable($filename) {
			//can we write?
			if(!is_writable($filename)) {
					//no we can't.
					if(!@chmod($filename, 0666)) {
							$pathtofilename = dirname($filename);
							//Lets check if parent directory is writable.
							if(!is_writable($pathtofilename)) {
									//it's not writeable too.
									if(!@chmod($pathtoffilename, 0666)) {
											//darn couldn't fix up parrent directory this hosting is foobar.
											//Lets error because of the permissions problems.
											return false;
									}
							}
					}
			}
			//we can write, return 1/true/happy dance.
			return true;
	}
	/**
	 *
	 */
	public function video_sitemap_output() {

			$getsitemap = $this->create_sitemap($_POST['post_slug']);
			if($getsitemap != false){
				return $getsitemap;
			}else{
				$class = "update-nag";
				$message = "<p>We've check all the selected post types and there meta values and found no youtube videos...</p>";
				$this->my_admin_error_notice($message, $class);
			}
	}
	/**
	 *
	 */
	public function get_post_types(){

		$args = array(
		   'public'   => true,
		);

		$posttypes 	= get_post_types($args, 'objects');

		return $posttypes;
	}
	/**
	 * Errors
		* @param $message string to display,  $class string class updated or error or update-nag
	 */
	public function my_admin_error_notice($message = '', $class = 'update-nag') {
				echo"<div class=\"$class\">$message</div>";
	}
	/**
	 *$posttypes = array or post sluges to check
	 */
	public function  create_sitemap($posttypes){
		global $wpdb;

		//Check if the post's array atleast is set...
		if($posttypes === NULL){
			$class 		= "update-nag";
			$message 	= "<p>Nothing selected please go back and try selecting a few post types..</p>";
			$this->my_admin_error_notice($message, $class);

			exit();
		}

			$q1 = new WP_Query( array(
			    'post_type' => $posttypes,
			    'posts_per_page' => -1,
			    's' => 'youtube.com'
			));

			$q2 = new WP_Query( array(
			    'post_type' => $posttypes,
			    'posts_per_page' => -1,
			    'meta_query' => array(
			        array(
								'value' => 'youtube.com',
								'compare' => 'LIKE',
			        )
			     )
			));

			$result 						= new WP_Query();
			//mark where we have got this from the search or the metakeys
			//Search
			foreach($q1->posts  as $post){ $post->type 					= 'youtube_content'; }
			//metaquery
			foreach($q2->posts  as $post){ $post->type 					= 'youtube__in_meta'; }
			$result->posts 			= array_unique( array_merge( $q1->posts, $q2->posts ), SORT_REGULAR );
			$result->post_count = count( count( $result->posts ) );




		if (empty ($result->posts)) {
				return false;

		} else {

				$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
				$xml .= '<!-- Created by (Accrue http://www.accruemarketing.com/) -->' . "\n";
				$xml .= '<!-- Generated-on="' . date("F j, Y, g:i a") .'" -->' . "\n";
				$xml .= '<?xml-stylesheet type="text/xsl" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/youtube-video-sitemap-generator/video-sitemap.xsl"?>' . "\n" ;
				$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . "\n";

				$videos = array();

				foreach ($result->posts as $post) {
						$c = 0;
										$excerpt = ($post->post_excerpt != "") ? $post->post_excerpt : $post->post_title ;
										$permalink = $this->video_EscapeXMLEntities(get_permalink($post->ID));

										if($post->type == 'youtube_content'){
												preg_match_all ("/youtube.com\/(v\/|watch\?v=|embed\/)([a-zA-Z0-9\-_]*)/", $post->post_content, $matches, PREG_SET_ORDER);


												$vidid = $matches[0][2];
												$fix =  $c++==0?'':' [Video '. $c .'] ';

												array_push($videos, $vidid);

												$xml .= "\n <url>\n";
												$xml .= " <loc>$permalink</loc>\n";
												$xml .= " <video:video>\n";
												$xml .= "  <video:player_loc allow_embed=\"yes\" autoplay=\"autoplay=1\">http://www.youtube.com/v/$vidid</video:player_loc>\n";
												$xml .= "  <video:thumbnail_loc>http://i.ytimg.com/vi/$vidid/hqdefault.jpg</video:thumbnail_loc>\n";
												$xml .= "  <video:title>" . htmlspecialchars($post->post_title) . $fix . "</video:title>\n";
												$xml .= "  <video:description>" . $fix . htmlspecialchars($excerpt) . "</video:description>\n";
												$xml .= "  <video:publication_date>".date (DATE_W3C, strtotime ($post->post_date_gmt))."</video:publication_date>\n";
												$xml .= " </video:video>\n </url>";
									}elseif($post->type == 'youtube__in_meta'){

											$meta = get_post_meta($post->ID);
											$get_vaues =	 $this->recursive_array_search('youtube.com', $meta);

											preg_match_all ("/youtube.com\/(v\/|watch\?v=|embed\/)([a-zA-Z0-9\-_]*)/", $get_vaues[0], $matches, PREG_SET_ORDER);

											$vidid = $matches[0][2];
											$fix =  $c++==0?'':' [Video '. $c .'] ';


											array_push($videos, $vidid);

											$xml .= "\n <url>\n";
											$xml .= " <loc>$permalink</loc>\n";
											$xml .= " <video:video>\n";
											$xml .= "  <video:player_loc allow_embed=\"yes\" autoplay=\"autoplay=1\">http://www.youtube.com/v/$vidid</video:player_loc>\n";
											$xml .= "  <video:thumbnail_loc>http://i.ytimg.com/vi/$vidid/hqdefault.jpg</video:thumbnail_loc>\n";
											$xml .= "  <video:title>" . htmlspecialchars($post->post_title) . $fix . "</video:title>\n";
											$xml .= "  <video:description>" . $fix . htmlspecialchars($excerpt) . "</video:description>\n";
											$xml .= "  <video:publication_date>".date (DATE_W3C, strtotime ($post->post_date_gmt))."</video:publication_date>\n";
											$xml .= " </video:video>\n </url>";


									}

				}

				$xml .= "\n</urlset>";
		}
		$path = get_home_path();
		$video_sitemap_url = $path . '/sitemap-video.xml';
		if ($this->IsVideoSitemapWritable($path) || $this->IsVideoSitemapWritable($video_sitemap_url)) {
				if (file_put_contents ($video_sitemap_url, $xml)) {
							$class = "updated";
							$message = '<div class="wrap">
														<h2>Sitemap Created Correctly</h2>
														<p>The XML sitemap was generated successfully and has been saved in your WordPress root folder at <strong>' . $video_sitemap_url . '</strong></p>
														<p> You can view a copy of the sitemap here: <a href="/sitemap-video.xml" target="_blank">here</a></p>
														<h3>Or edit the raw version here and manually save the file:</h3>
														<textarea rows="30" cols="150" style="font-family:verdana; font-size:11px;color:#666;background-color:#f9f9f9;padding:5px;margin:5px">' . $xml . '</textarea>
														<br />
												</div>';
							$this->my_admin_error_notice($message, $class);
						return true;
				}
		}

			$class = "update-nag";
			$message = '<div class="wrap">
										<h2>Error writing the file</h2>
										<p>The XML sitemap was generated successfully but the  plugin was unable to save the xml to your WordPress root folder at <strong>' . $path . '</strong> probably because the folder doesn\'t have appropriate <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">write permissions</a>.</p>
										<p>You can however manually copy-paste the following text into a file and save it as video-sitemap.xml in your WordPress root folder. </p>
										<br />
									<textarea rows="30" cols="150" style="font-family:verdana; font-size:11px;color:#666;background-color:#f9f9f9;padding:5px;margin:5px">' . $xml . '</textarea>
								</div>';
			$this->my_admin_error_notice($message, $class);
		exit();
	}

	/**
	 *
	 */
		public function recursive_array_search($needle,$haystack) {
	    foreach($haystack as $key=>$value) {
	        $current_key = $value;
					$searchvalue = strstr($value, $needle);
	        if(strstr($value, $needle) OR (is_array($value) && $this->recursive_array_search($needle,$value) !== false)) {
	            return $current_key;
	        }
	    }
	    return false;
	}
	/**
	 *
	 */
	public function video_EscapeXMLEntities($xml) {
	    return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $xml);
	}









	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters("plugin_locale", get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR . "/" . $domain . "/" . $domain . "-" . $locale . ".mo");
		load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . "/lang/");
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_style($this->plugin_slug . "-admin-styles", plugins_url("css/admin.css", __FILE__), array(),$this->version);
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_script($this->plugin_slug . "-admin-script", plugins_url("js/video-sitemap-admin.js", __FILE__),
				array("jquery"), $this->version);
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_slug . "-plugin-styles", plugins_url("css/public.css", __FILE__), array(),
			$this->version);
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_slug . "-plugin-script", plugins_url("js/public.js", __FILE__), array("jquery"),
			$this->version);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		$page_title											 	= __("Video sitemap - Administration", $this->plugin_slug);
		$menu_title												= __("Video sitemap", $this->plugin_slug);
		$capability												= "read";
		$menu_slug												= $this->plugin_slug;
		$function													= array($this, "display_plugin_admin_page");
		$icon_url													= 'dashicons-randomize';
		$position													= 200;
		$this->plugin_screen_hook_suffix 	= add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once("views/admin.php");
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

}
