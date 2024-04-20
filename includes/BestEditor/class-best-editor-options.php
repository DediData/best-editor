<?php
/**
 * Advanced Classic Editor Settings Page Class
 * 
 * @package Best_Editor
 */

declare(strict_types=1);

namespace BestEditor;

/**
 * Class for registering settings page under Settings.
 */
final class Best_Editor_Options extends \DediData\Singleton {

	/**
	 * Page Title
	 * 
	 * @var string $page_title
	 */
	protected $page_title;

	/**
	 * Menu Title
	 * 
	 * @var string $menu_title
	 */
	protected $menu_title;

	/**
	 * Plugin Slug
	 * 
	 * @var string $plugin_slug
	 */
	protected $plugin_slug;

	/**
	 * Plugin Hook
	 * 
	 * @var string $plugin_hook
	 */
	protected $plugin_hook;

	/**
	 * Constructor
	 * 
	 * @param mixed $plugin_slug Plugin Slug String.
	 */
	public function __construct( $plugin_slug = null ) {
		$this->plugin_slug = $plugin_slug;
		$this->page_title  = esc_html__( 'Advanced Classic Editor', 'best-editor' );
		$this->menu_title  = esc_html__( 'Advanced Classic Editor', 'best-editor' );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		// Here you can check if plugin is configured (e.g. check if some option is set). If not, add new hook.
		if ( '' === get_option( $this->plugin_slug ) ) {
			add_action( 'admin_notices', array( $this, 'add_admin_notices' ) );
		}
		add_action( 'admin_init',  array( $this, 'register_settings' ) );
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array<bool> $input Contains all settings fields as array keys.
	 * @return array<bool>
	 */
	public function process_inputs( $input ) {
		// sanitize functions:
		// sanitize_email(), sanitize_file_name(), sanitize_html_class(), sanitize_key(), sanitize_meta(), sanitize_mime_type(),
		// sanitize_option(), sanitize_sql_orderby(), sanitize_text_field(), sanitize_textarea_field(), sanitize_title(),
		// sanitize_title_for_query(), sanitize_title_with_dashes(), sanitize_user()
		$options                 = array();
		$options['font-awesome'] = isset( $input['font-awesome'] ) && true === boolval( $input['font-awesome'] );
		$options['bootstrap']    = isset( $input['bootstrap'] ) && true === boolval( $input['bootstrap'] );

		// add error/update messages
		// check if the user have submitted the settings

		/*
		add_settings_error(
			$this->plugin_slug . '_messages', 
			// Slug title of setting
			'wporg_message',
			// Slug-name , Used as part of 'id' attribute in HTML output.
			esc_html__('The entered information is not correct.', 'best-editor'),
			// message text, will be shown inside styled <div> and <p> tags
			'error' 
			// Message type, controls HTML class. Accepts 'error' or 'updated'.
		);
		*/

		return $options;
	}

	/**
	 * Registers a new settings page under Settings.
	 * 
	 * @return void
	 */
	public function add_admin_menu() {
		$this->plugin_hook = add_options_page(
			$this->page_title,
			$this->menu_title,
			// Capability
			'manage_options',
			$this->plugin_slug,
			// Output the content for this page
			array( $this, 'settings_page_content' )
		);

		if ( ! $this->plugin_hook ) {
			return;
		}
		add_action( 'load-' . $this->plugin_hook, array( $this, 'on_plugin_page_load' ) );
	}

	/**
	 * Adds a help tab and a help sidebar to the current screen in the WordPress admin area.
	 * 
	 * @return void
	 */
	public function on_plugin_page_load() {
		remove_action( 'admin_notices', array( $this, 'add_admin_notices' ) );
		// We are in the correct screen because we are taking advantage of the load-* action (below)
		$help_content = '<p>' . esc_html__( 'Use this page to configure Advanced Classic Editor plugin', 'best-editor' ) . '</p>';
		$screen       = get_current_screen();
		// $screen->remove_help_tabs();
		$screen->add_help_tab(
			array(
				'id'      => $this->plugin_slug . '-default',
				'title'   => esc_html__( 'Help', 'best-editor' ),
				'content' => $help_content,
			)
		);
		// add more help tabs as needed with unique id's

		// Help sidebars are optional
		$screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:', 'best-editor' ) . '</strong></p>'
			. '<p><a href="' . esc_url( esc_html__( 'https://dedidata.com', 'best-editor' ) ) . '" target="_blank">' . esc_html__( 'Visit Our Website!', 'best-editor' ) . '</a></p>'
		);
	}

	/**
	 * Display an admin notice on the WordPress admin dashboard if the plugin is not configured.
	 * 
	 * @return void
	 */
	public function add_admin_notices() {
		?>
		<div id="notice" class="update-nag">
			<?php esc_html_e( 'Advanced Classic Editor plugin is not configured', 'best-editor' ); ?>
			<a href="<?php menu_page_url( $this->plugin_slug, true ); ?>"><?php esc_html_e( 'Please configure it now !', 'best-editor' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Register the settings for the plugin.
	 * 
	 * @return void
	 */
	public function register_settings() {
		// whitelist options
		register_setting(
			$this->plugin_slug, 
			// option_group
			$this->plugin_slug, 
			// option_name, for name property of tags
			array( $this, 'process_inputs' ) 
			// sanitize_callback
		);
		add_settings_section(
			'load-libraries',
			// id attribute of tags
			esc_html__( 'Settings for loading libraries', 'best-editor' ),
			// title heading for the section
			static function ( $args ) {
				// callback function to display content at the top of the section
				?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Please specify which libraries to load in your web site front-end', 'best-editor' ); ?></p>
				<?php
			},
			$this->plugin_slug
			// plugin slug, created by add_options_page()
		);
				add_settings_field(
					'font-awesome',
					// id attribute of tag
					esc_html__( 'Load Font Awesome library in your site frontend', 'best-editor' ),
					// Title as label for field
					function () {
						$best_editor        = get_option( $this->plugin_slug );
						$check_font_awesome = $best_editor['font-awesome'] ?? false;
						?>
						<input type="checkbox" name="best-editor[font-awesome]" id="font-awesome" value="true" <?php checked( true, $check_font_awesome ); ?> />
						<?php
					}, // Callback function to echo input tag
					$this->plugin_slug,
					// plugin slug, created by add_options_page()
					'load-libraries',
					// slug-name of the section
					array(
						'label_for' => 'font-awesome',
						// label for => tag id
						'class'     => 'font-awesome',
					// class for <tr>
					)
				);
				add_settings_field(
					'bootstrap',
					// id attribute of tag
					esc_html__( 'Load Bootstrap in your site frontend', 'best-editor' ),
					// Title as label for field
					function () {
						$best_editor     = get_option( $this->plugin_slug );
						$check_bootstrap = $best_editor['bootstrap'] ?? false;
						?>
						<input type="checkbox" name="best-editor[bootstrap]" id="bootstrap" value="true" <?php checked( true, $check_bootstrap ); ?> />
						<?php
					}, // Callback function to echo input tag
					$this->plugin_slug,
					// plugin slug, created by add_options_page()
					'load-libraries',
					// slug-name of the section
					array(
						'label_for' => 'bootstrap',
						// label for => tag id
						'class'     => 'bootstrap',
					// class for <tr>
					)
				);
	}

	/**
	 * Settings page display callback.
	 * 
	 * @return void
	 */
	public function settings_page_content() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// var_dump( wp_load_alloptions() ); // print all options

		// show error/update messages
		// settings_errors( $this->plugin_slug . '_messages' ); // no need, WordPress automatically call this
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( $this->page_title ); ?></h1>
			<form method="post" action="options.php">
				<p><?php esc_html_e( 'To use this plugin, You need to disable Default Block Editor first', 'best-editor' ); ?></p>
				<p><a href="plugin-install.php?tab=plugin-information&plugin=disable-gutenberg"><?php esc_html_e( 'Click here to install a plugin to Disable Default Block Editor', 'best-editor' ); ?></a></p>
				<p><?php esc_html_e( 'Advanced Classic Editor automatically loads FontAwesome and Bootstrap in editor area.', 'best-editor' ); ?></p>
				<p><?php esc_html_e( 'But if you like to use Font Awesome and Bootstrap elements, You should load these libraries in your site frontend. you can enable them in following (if your theme doesn\'t load these libraries)', 'best-editor' ); ?></p>
			<?php
				submit_button();
				settings_fields( $this->plugin_slug );
				// This prints out all hidden setting fields
				do_settings_sections( $this->plugin_slug );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}
}
