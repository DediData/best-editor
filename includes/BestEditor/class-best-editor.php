<?php
/**
 * Advanced Classic Editor Main Class
 * 
 * @package Best_Editor
 */

declare(strict_types=1);

namespace BestEditor;

/**
 * Class Best_Editor
 */
final class Best_Editor extends \DediData\Singleton {
	
	/**
	 * Plugin URL
	 * 
	 * @var string $plugin_url
	 */
	protected $plugin_url;

	/**
	 * Plugin Folder
	 * 
	 * @var string $plugin_folder
	 */
	protected $plugin_folder;

	/**
	 * Plugin Name
	 * 
	 * @var string $plugin_name
	 */
	protected $plugin_name;

	/**
	 * Plugin Version
	 * 
	 * @var string $plugin_version
	 */
	protected $plugin_version;
	
	/**
	 * Plugin Slug
	 * 
	 * @var string $plugin_slug
	 */
	protected $plugin_slug;

	/**
	 * Plugin File
	 * 
	 * @var string $plugin_file
	 */
	protected $plugin_file;

	/**
	 * Row1 Buttons
	 * 
	 * @var array<string> $row1_buttons
	 */
	protected $row1_buttons = array(
		'formatselect',
		'fontselect',
		'fontsizeselect',
		'styleselect',
		'removeformat',
		'visualchars',
		'pastetext',
	);

	/**
	 * Row2 Buttons
	 * 
	 * @var array<string> $row2_buttons
	 */
	protected $row2_buttons = array(
		'bold',
		'italic',
		'underline',
		'strikethrough',
		'blockquote',
		'alignleft',
		'aligncenter',
		'alignright',
		'alignjustify',
		'outdent',
		'indent',
		'forecolor',
		'backcolor',
		'bullist',
		'numlist',
		'searchreplace',
	);

	/**
	 * Row3 Buttons
	 * 
	 * @var array<string> $row3_buttons
	 */
	protected $row3_buttons = array(
		'link',
		'unlink',
		'anchor',
		'hr',
		'table',
		'wp_page',
		'wp_more',
		'bootstrap',
		'template',
		'fontawesome',
		'codesample',
		'toc',
		'emoticons',
		'charmap',
		'media',
		'wp_help',
		'visualblocks',
	);

	/**
	 * Row4 Buttons
	 * 
	 * @var array<string> $row4_buttons
	 */
	protected $row4_buttons = array();

	/**
	 * All Buttons
	 * 
	 * @var array<string> $all_buttons
	 */
	protected $all_buttons = array();

	/**
	 * Exception Buttons
	 * 
	 * @var array<string> $exception_buttons
	 */
	protected $exception_buttons = array(
		'newdocument',
		'cut',
		'copy',
		'paste',
		'undo',
		'redo',
		'visualaid',
		'code',
		'fullscreen',
		'help',
	);

	/**
	 * Constructor
	 * 
	 * @param mixed $plugin_file Plugin File Name.
	 * @see https://developer.wordpress.org/reference/functions/register_activation_hook
	 * @see https://developer.wordpress.org/reference/functions/register_deactivation_hook
	 * @see https://developer.wordpress.org/reference/functions/register_uninstall_hook
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	protected function __construct( $plugin_file = null ) {
		$this->plugin_file = $plugin_file;
		$this->set_plugin_info();
		register_activation_hook( $plugin_file, array( $this, 'activate' ) );
		register_deactivation_hook( $plugin_file, array( $this, 'deactivate' ) );
		register_uninstall_hook( $plugin_file, self::class . '::uninstall' );
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ), 11 );
			$this->admin();
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ), 11 );
			$this->run();
		}
	}

	/**
	 * The function is used to load frontend scripts and styles in a WordPress plugin, with support for
	 * RTL (right-to-left) languages.
	 * 
	 * @return void
	 */
	public function load_frontend_scripts() {
		/*
		if ( is_rtl() ) {
			wp_register_style( $this->plugin_slug . '-rtl', $this->plugin_url . '/assets/public/css/style.rtl.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug . '-rtl' );
		} else {
			wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/public/css/style.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug );
		}

		wp_register_script( $this->plugin_slug, $this->plugin_url . '/assets/public/js/script.js', array(), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_slug );
		*/

		$font_awesome = get_option( $this->plugin_slug )['font-awesome'];
		if ( true === $font_awesome /* or !isset( $font_awesome ) */ ) {
			wp_enqueue_style( 'font-awesome', $this->plugin_url . '/assets/fontawesome-6.5.1/css/all.min.css', null, '6.5.1' );
			// wp_enqueue_script( $this->plugin_slug , $this->plugin_url . '/js/script.js', array(), $this->plugin_version, true );
		}
		$bootstrap = get_option( $this->plugin_slug )['bootstrap'];
		$rtl       = is_rtl() ? '-rtl' : '';
		$rtl_ext   = is_rtl() ? '.rtl' : '';
		if ( false === $bootstrap /* || ! isset( $bootstrap ) */ ) {
			return;
		}
		wp_enqueue_style( 'bootstrap' . $rtl, $this->plugin_url . '/assets/bootstrap-5.3.3/css/bootstrap' . $rtl_ext . '.min.css', null, '5.3.3' );
		wp_enqueue_script( 'bootstrap', $this->plugin_url . '/assets/bootstrap-5.3.3/js/bootstrap.min.js', array(), '5.3.3', true );
	}

	/**
	 * Styles for Admin
	 * 
	 * @return void
	 */
	public function load_admin_scripts() {
		/*
		if ( is_rtl() ) {
			wp_register_style( $this->plugin_slug . '-rtl', $this->plugin_url . '/assets/admin/css/style.rtl.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug . '-rtl' );
		} else {
			wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/admin/css/style.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug );
		}

		wp_register_script( $this->plugin_slug, $this->plugin_url . '/assets/admin/js/script.js', array(), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_slug );
		*/

		// Load Font-Awesome
		wp_enqueue_style( 'font-awesome', $this->plugin_url . '/assets/fontawesome-6.5.1/css/all.min.css', null, '6.5.1' );

		$rtl_ext = is_rtl() ? '.rtl' : '';
		// Load editor styles for both rtl and ltr
		add_editor_style(
			array(
				'css/editor-style.css',
				$this->plugin_url . '/assets/fontawesome-6.5.1/css/all.min.css',
				$this->plugin_url . '/assets/bootstrap-5.3.3/css/bootstrap' . $rtl_ext . '.min.css',
			)
		);
	}

	/**
	 * Activate the plugin
	 * 
	 * @return void
	 * @see https://developer.wordpress.org/reference/functions/add_option
	 */
	public function activate() {
		add_option( $this->plugin_slug );
	}

	/**
	 * Run when plugins deactivated
	 * 
	 * @return void
	 */
	public function deactivate() {
		// Clear any temporary data stored by plugin.
		// Flush Cache/Temp.
		// Flush Permalinks.
	}

	/**
	 * Add Buttons
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function add_buttons() {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$typenow = $GLOBALS['typenow'];
		// We check that the user has edit posts / pages
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		// We verify the type of post
		if ( ! in_array( $typenow, array( 'post', 'page' ), true ) ) {
			return;
		}
		// We check that the user has WYSIWYG enabled
		if ( 'true' === get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_plugins_to_tinymce' ) );
		}
		$this->all_buttons = array_merge(
			$this->row1_buttons,
			$this->row2_buttons,
			$this->row3_buttons,
			$this->row4_buttons,
			$this->exception_buttons
		);
		add_filter( 'mce_buttons', array( $this, 'mce_buttons_1' ), 999999 );
		add_filter( 'mce_buttons_2', array( $this, 'mce_buttons_2' ), 999999 );
		add_filter( 'mce_buttons_3', array( $this, 'mce_buttons_3' ), 999999 );
		// add_filter( 'mce_buttons_4', array( $this, 'mce_buttons_4' ), 999999 );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_init' ) );
	}

	/**
	 * TinyMCE Init
	 * 
	 * @param array<mixed> $init Init array.
	 * @return mixed
	 */
	public function tinymce_init( $init ) {
		$init['extended_valid_elements'] = 'i[style|id|name|class|lang],span[style|id|name|class|lang]';

		if ( isset( $init['extended_valid_elements'] ) ) {
			$init['extended_valid_elements'] .= ',i[style|id|name|class|lang],span[style|id|name|class|lang]';
		}

		$init['image_advtab'] = true;
		$init['menubar']      = true;
		// Disable advanced row
		$init['wordpress_adv_hidden'] = false;
		$init['fontsize_formats']     = '8px 10px 12px 14px 16px 20px 24px 28px 32px 36px 48px 60px 72px 96px';
		$init['paste_data_images']    = true;
		$init['contextmenu']          = 'copy cut paste selectall link media';
		// $init['font_formats']       = 'Lato=Lato;Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats';
		$init['importcss_append'] = true;
		$init['templates']        = "[
			{title: 'Alert Success', description: 'Insert a success alert message box', url: '" . plugins_url( 'assets/tinymce-plugins/template/templates/alert-success.html', $this->plugin_file ) . "'},
			{title: 'Alert Info', description: 'Insert an info alert message box', url: '" . plugins_url( 'assets/tinymce-plugins/template/templates/alert-info.html', $this->plugin_file ) . "'},
			{title: 'Alert Warning', description: 'Insert a warning alert message box', url: '" . plugins_url( 'assets/tinymce-plugins/template/templates/alert-warning.html', $this->plugin_file ) . "'},
			{title: 'Alert Danger', description: 'Insert a danger alert message box', url: '" . plugins_url( 'assets/tinymce-plugins/template/templates/alert-danger.html', $this->plugin_file ) . "'},
		]";
		$init['verify_html']      = false;
		// $init['noneditable_noneditable_class'] = 'fa';

		/*
		// Add new styles to the TinyMCE "formats" menu dropdown
		// Create array of new styles
		$new_styles = array(
			array(
				'title'	=> __( 'Custom Styles', 'wpex' ),
				'items'	=> [
					array(
						'title'	   => __('Theme Button','wpex'),
						'selector' => 'a',
						'classes'  => 'theme-button'
					),
					array(
						'title'   => __('Highlight','wpex'),
						'inline'  => 'span',
						'classes' => 'text-highlight',
					),
				),
			),
		);
		// Merge old & new styles
		$init['style_formats_merge'] = true;
		// Add new styles
		$init['style_formats'] = json_encode( $new_styles );
		*/

		return $init;
	}

	/**
	 * Modifies the buttons displayed in the WordPress editor toolbar.
	 * 
	 * @param array<string> $original An array of buttons that are currently displayed in the first row of the TinyMCE editor toolbar.
	 * @return array<string> Returns an array of buttons for the first row of the TinyMCE editor toolbar.
	 */
	public function mce_buttons_1( $original ) {
		$buttons_1 = $this->row1_buttons;
		if ( is_array( $original ) ) {
			$original  = array_diff( $original, $this->all_buttons );
			$buttons_1 = array_merge( $buttons_1, $original );
		}

		return $buttons_1;
	}

	/**
	 * Modifies the second row of buttons in a WordPress editor toolbar.
	 * 
	 * @param array<string> $original An array of buttons that are currently displayed in the second row of the TinyMCE editor toolbar.
	 * @return array<string> An array of buttons.
	 */
	public function mce_buttons_2( $original ) {
		$buttons_2 = $this->row2_buttons;
		if ( is_array( $original ) ) {
			$original  = array_diff( $original, $this->all_buttons );
			$buttons_2 = array_merge( $buttons_2, $original );
		}

		return $buttons_2;
	}

	/**
	 * Modifies the third row of buttons in a WordPress editor toolbar.
	 * 
	 * @param array<string> $original An array of buttons that are currently displayed in the third row of the TinyMCE editor toolbar.
	 * @return array<string> An array of buttons.
	 */
	public function mce_buttons_3( $original ) {
		$buttons_3 = $this->row3_buttons;
		if ( is_array( $original ) ) {
			$original  = array_diff( $original, $this->all_buttons );
			$buttons_3 = array_merge( $buttons_3, $original );
		}

		return $buttons_3;
	}
	
	/**
	 * Add Plugins To TinyMCE
	 * 
	 * @param array<mixed> $plugin_array Plugin Array.
	 * @return mixed
	 */
	public function add_plugins_to_tinymce( $plugin_array ) {
		$active_plugins = array(
			'fontawesome',
			'visualchars',
			'advlist',
			'anchor',
			'code',
			'contextmenu',
			'emoticons',
			'importcss',
			'insertdatetime',
			'print',
			'searchreplace',
			'table',
			'visualblocks',
			'autolink',
			'autoresize',
			'codesample',
			'preview',
			'template',
			'toc',
			'noneditable',
			'bootstrap',
		);
		foreach ( $active_plugins as $active_plugin ) {
			$plugin_array[ $active_plugin ] = plugins_url( 'assets/tinymce-plugins/' . $active_plugin . '/plugin.js', $this->plugin_file );
		}

		return $plugin_array;
	}

	/**
	 * Uninstall plugin
	 * 
	 * @return void
	 * @see https://developer.wordpress.org/reference/functions/delete_option
	 */
	public static function uninstall() {
		delete_option( 'best-editor' );
		// Remove Tables from wpdb
		// global $wpdb;
		// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}best-editor");
		// Clear any cached data that has been removed.
		wp_cache_flush();
	}

	/**
	 * Set Plugin Info
	 * 
	 * @return void
	 */
	private function set_plugin_info() {
		$this->plugin_slug = basename( $this->plugin_file, '.php' );
		$this->plugin_url  = plugins_url( '', $this->plugin_file );

		if ( ! function_exists( 'get_plugins' ) ) {
			include_once \ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugin_folder  = plugin_dir_path( $this->plugin_file );
		$plugin_info          = get_plugins( '/' . plugin_basename( $this->plugin_folder ) );
		$plugin_file_name     = basename( $this->plugin_file );
		$this->plugin_version = $plugin_info[ $plugin_file_name ]['Version'];
		$this->plugin_name    = $plugin_info[ $plugin_file_name ]['Name'];
	}

	/**
	 * The function "run" is a placeholder function in PHP with no code inside.
	 * 
	 * @return void
	 */
	private function run() {
		// nothing for now
	}

	/**
	 * The admin function includes the options.php file and registers the admin menu.
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	private function admin() {
		// add_action( 'admin_menu', 'AparatFeed\Admin_Menus::register_admin_menu' );
		add_action( 'admin_head', array( $this, 'add_buttons' ) );
		\BestEditor\Best_Editor_Options::get_instance( $this->plugin_slug );
	}
}
