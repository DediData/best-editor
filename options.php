<?php

/**
 * Class for registering settings page under Settings.
 */
class BestEditor_Options_Page {

	/**
	 * @var string
	 */
	public $page_title;
	/**
	 * @var string|void
	 */
	public $menu_title;
	/**
	 * @var string
	 */
	public $plugin_slug;
	/**
	 * @var
	 */
	public $plugin_hook;

	/**
	 * Constructor.
	 */
	function __construct() {

		$this->plugin_slug = 'best-editor';
		$this->plugin_name = __('Best Editor', 'best-editor');
		$this->page_title  = sprintf(__('%s Settings', 'best-editor'), $this->plugin_name);
		$this->menu_title  = $this->plugin_name;

		add_action('admin_menu', [$this, 'add_admin_menu']);

		// Here you can check if plugin is configured (e.g. check if some option is set). If not, add new hook.
		if ( empty( get_option($this->plugin_slug) ) ) {
			add_action('admin_notices', array($this, 'add_admin_notices'));
		}

		add_action('admin_init', [$this, 'register_settings']);
	}

	/**
	 * Registers a new settings page under Settings.
	 */
	function add_admin_menu() {
		$this->plugin_hook = add_options_page(
			$this->page_title,
			$this->menu_title,
			'manage_options', // capability
			$this->plugin_slug,
			[$this, 'settings_page_content'] //output the content for this page
		);

		if ( $this->plugin_hook ) {
			add_action('load-' . $this->plugin_hook, [$this, 'on_plugin_page_load']);
		}

	}

	/**
	 *
	 */
	function on_plugin_page_load() {
		remove_action('admin_notices', [$this, 'add_admin_notices']);
		$this->add_setting_page_help();
	}

	/**
	 *
	 */
	function add_admin_notices() {
		?>
		<div id="notice" class="update-nag">
			<?php echo sprintf(__('%s is not configured', 'best-editor'), $this->plugin_name); ?>
			<a href="<?php menu_page_url($this->plugin_slug, true); ?>"><?php _e('Please configure it now!', 'best-editor'); ?></a>
		</div>
		<?php
	}

	/**
	 *
	 */
	function add_setting_page_help() {
		// We are in the correct screen because we are taking advantage of the load-* action (below)
		$help_content = '<p>' . sprintf(__('Use this page to configure %s plugin', 'best-editor'), $this->plugin_name) . '</p>';

		$screen = get_current_screen();
		//$screen->remove_help_tabs();
		$screen->add_help_tab(
			[
				'id'      => $this->plugin_slug . '-default',
				'title'   => __('Help', 'best-editor'),
				'content' => $help_content,
			]
		);
		//add more help tabs as needed with unique id's

		// Help sidebars are optional
		$screen->set_help_sidebar(
			'<p><strong>' . __('For more information:', 'best-editor') . '</strong></p>' .
			'<p><a href="https://dedidata.com" target="_blank">' . __('Visit Our Website!', 'best-editor') . '</a></p>'
		);
	}

	/**
	 *
	 */
	function register_settings() { // whitelist options
		register_setting(
			$this->plugin_slug, // option_group
			$this->plugin_slug, // option_name, for name property of tags
			[$this, 'process_inputs'] // sanitize_callback
		);
		add_settings_section(
			'load-libraries', // id attribute of tags
			__('Settings for loading libraries', 'best-editor'), // title heading for the section
			function ( $args ) { // callback function to display content at the top of the section ?>
				<p id="<?php echo esc_attr($args['id']); ?>"><?php _e( 'Please specify which libraries to load in your web site front-end', 'best-editor' ); ?></p><?php
			},
			$this->plugin_slug // plugin slug, created by add_options_page()
		);
		add_settings_field(
			'fontawesome', // id attribute of tag
			__('Load Font Awesome library in your site frontend', 'best-editor'), // Title as lable for field
			function($args){
				$best_editor = get_option($this->plugin_slug);
				$check_fontawesome = isset( $best_editor['fontawesome'] ) ? $best_editor['fontawesome'] : false;
				?>
				<input type="checkbox" name="best-editor[fontawesome]" id="fontawesome" value="true" <?php checked(true , $check_fontawesome); ?> />
				<?php
			}, // Callback function to echo input tag
			$this->plugin_slug, // plugin slug, created by add_options_page()
			'load-libraries', // slug-name of the section
			[
				'label_for' 	=> 'fontawesome', // label for => tag id
				'class'     	=> 'fontawesome',    // class for <tr>
			]
		);
		add_settings_field(
			'bootstrap', // id attribute of tag
			__('Load Bootstrap 3.3.7 in your site frontend', 'best-editor'), // Title as lable for field
			function($args){
				$best_editor = get_option($this->plugin_slug);
				$check_bootstrap = isset( $best_editor['bootstrap'] ) ? $best_editor['bootstrap'] : false;
				?>
				<input type="checkbox" name="best-editor[bootstrap]" id="bootstrap" value="true" <?php checked(true,$check_bootstrap); ?> />
				<?php
			}, // Callback function to echo input tag
			$this->plugin_slug, // plugin slug, created by add_options_page()
			'load-libraries', // slug-name of the section
			[
				'label_for'	=> 'bootstrap', // label for => tag id
				'class'		=> 'bootstrap',    // class for <tr>
			]
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function process_inputs( $input ) {
		// sanitize functions:
		// sanitize_email(), sanitize_file_name(), sanitize_html_class(), sanitize_key(), sanitize_meta(), sanitize_mime_type(),
		// sanitize_option(), sanitize_sql_orderby(), sanitize_text_field(), sanitize_textarea_field(), sanitize_title(),
		// sanitize_title_for_query(), sanitize_title_with_dashes(), sanitize_user()
		$options						= [];
		$options['fontawesome']	= boolval(isset( $input['fontawesome'] )	and $input['fontawesome']	== true);
		$options['bootstrap']		= boolval(isset( $input['bootstrap'] )		and $input['bootstrap']		== true);

		// add error/update messages
		// check if the user have submitted the settings

		if ( false ) {
			add_settings_error(
				$this->plugin_slug . '_messages',
				// Slug title of setting
				'wporg_message',
				// Slug-name , Used as part of 'id' attribute in HTML output.
				__('Entered options are not valid!', 'best-editor'),
				// message text, will be shown inside styled <div> and <p> tags
				'error' // Message type, controls HTML class. Accepts 'error' or 'updated'.
			);
		}

		return $options;
	}

	/**
	 * Settings page display callback.
	 */
	function settings_page_content() {
		// check user capabilities
		if ( ! current_user_can('manage_options') ) {
			return;
		}

		//var_dump( wp_load_alloptions() ); // print all options

		// show error/update messages
		//settings_errors( $this->plugin_slug . '_messages' ); // no need, wordpress automatically call this
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html($this->page_title); ?></h1>
			<form method="post" action="options.php">
				<p><?php _e('Best Editor automatically loads FontAwesome and Bootstrap 3.3.7 in editor area, But if you like to use Font Awesome and Bootstrap elements,<br />
										You should load these libraries in your site frontend, you can enable them in following (if your theme doesn\'t load these libraries)', 'best-editor'); ?></p>
				<?php
				submit_button();
				settings_fields($this->plugin_slug); // This prints out all hidden setting fields
				do_settings_sections($this->plugin_slug);
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

} // class
new BestEditor_Options_Page;
