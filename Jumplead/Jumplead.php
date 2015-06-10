<?php
/**
 * Jumplead
 */

class Jumplead
{
	/**
	 * Plugin's name
	 */
	static $plugin = 'jumplead/jumplead.php';

	/**
	 * Path to Jumplead Plugin's folder
	 */
	static $path = '';

	/**
	 * DB table name for field mapping
	 */
	static $tableFieldMapping = '';

	/**
	 * Data to be used for triggering automations
	 */
	static $data = array();


	/**
	 * Boot up Jumplead Plugin
	 * Sets up static variables, hooks and styles
	 */
	static function boot()
	{
		global $wpdb;

		// Static Variables
		self::$path = plugins_url( '/', self::$plugin );
		self::$tableFieldMapping = $wpdb->prefix . 'jumplead_mapping';

		// Install
		register_activation_hook( self::$plugin, 'Jumplead::handle_activate' );
		register_deactivation_hook( self::$plugin, 'Jumplead::handle_deactivate' );
		register_uninstall_hook( self::$plugin, 'Jumplead::handle_uninstall' );

		// Admin
		add_action( 'admin_menu', 'Jumplead::admin_menu' );

		// Styles
		add_action( 'admin_enqueue_scripts', 'Jumplead::styles' );

		// Filters
		add_action( 'load-jumplead_page_jumplead_integrations', 'Jumplead::filter_has_tracker_id' );
	}

	/**
	 * Add Jumple to Admin menu, along with subpages
	 *
	 * @return void
	 */
	static function admin_menu()
	{
		$icon = plugins_url( 'jumplead/assets/jumplead-icon.png' );

		// Main Menu
		add_menu_page( 'Jumplead', 'Jumplead', 'edit_pages', 'jumplead', 'Jumplead::show_page_jumplead', $icon );

		// Subpages
		add_submenu_page( 'jumplead', 'Integrations', 'Integrations', 'edit_pages', 'jumplead_integrations', 'Jumplead::show_page_integrations' );
		add_submenu_page( 'jumplead', 'Settings', 'Settings', 'edit_pages', 'jumplead_settings', 'Jumplead::show_page_settings' );
	}

	/**
	 * Add Jumplead admin styles
	 *
	 * @return void
	 */
	static function styles()
	{
		wp_enqueue_style( 'jumplead_styles', self::$path . 'c/jumplead.css', array('dashicons'), JUMPLEAD_VERSION );
	}


	/**
	 * Check for tracker ID, redirect to settings if not set
	 *
	 * @return void
	 */
	static function filter_has_tracker_id()
	{

		if ( ! jumplead_is_tracker_id_valid() ) {
			wp_redirect( admin_url( 'admin.php?page=jumplead_settings' ) );
			exit;
		}
	}

	// Page Controllers

	/**
	 * Jumplead Landing Page
	 *
	 * @return void
	 */
	static function show_page_jumplead()
	{
		$h2 = 'Jumplead';
		include(JUMPLEAD_PATH_VIEW . 'jumplead.php');
	}

	/**
	 * Jumplead Settings Page
	 *
	 * @return void
	 */
	static function show_page_settings()
	{
		$h2 = 'Jumplead Settings';

		$errors = array();
		$info = array();

		// Update?
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

			// Comments
			$capture_comments = isset($_POST['capture_comments']) ? (bool) $_POST['capture_comments'] : false;
			update_option( 'jumplead_capture_comments', $capture_comments );

			// Tracker
			$invalidTrackerId = true;
			if ( isset($_POST['tracker_id']) ) {
				$tracker_id = trim( esc_html( $_POST['tracker_id'] ) );

				if ( jumplead_is_tracker_id_valid( $tracker_id ) ) {
					update_option( 'jumplead_tracker_id', $tracker_id );
					$info[] = 'Settings Saved!';
					$invalidTrackerId = false;
				}
			}

			if ( $invalidTrackerId ) {
				$errors[] = 'Tracker ID is not valid.';
			}
		}

		// Settings for view
		$tracker_id         = get_option( 'jumplead_tracker_id', null );
		$capture_comments   = get_option( 'jumplead_capture_comments', false );
		$tracker_id_valid   = jumplead_is_tracker_id_valid( $tracker_id );

		// Prompt for Tracker ID
		if ( empty($errors) && empty($info) && ! $tracker_id_valid ) {
			$info[] = 'Please enter your Jumplead Tracker ID to use Jumplead.';
		}

		// View
		include(JUMPLEAD_PATH_VIEW . 'settings.php');
	}

	/**
	 * Jumplead Integrations Pages
	 * - Acts as a route for Jumplead Integration pages.
	 * - $_GET['subpage'] defines what page to load
	 *
	 * @return void
	 */
	static function show_page_integrations()
	{
		$page = isset($_GET['subpage']) ? esc_html( $_GET['subpage'] ) : null;

		switch ( $page ) {
			case 'mapping':
				self::show_page_integrations_mapping();
				break;
			default:
				self::show_page_integrations_index();
		}
	}

	/**
	 * Jumplead Integrations Page
	 * - Lists forms from active integrated plugins.
	 * - Handles unlinking of forms.
	 *
	 * @return void
	 */
	static function show_page_integrations_index()
	{
		$h2 = 'Jumplead Integrations';

		// Bulk Actions
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			// Top Bulkaction dropdown
			$bulkaction = isset($_POST['bulkaction']) ? esc_html( $_POST['bulkaction'] ) : null;
			if ( ! $bulkaction ) {
				// Bottom Bulkaction dropdown
				$bulkaction = isset($_POST['bulkaction2']) ? esc_html( $_POST['bulkaction2'] ) : null;
			}

			// @codingStandardsIgnoreStart
			$forms = isset($_POST['forms']) ? $_POST['forms'] : null;
			// @codingStandardsIgnoreEnd

			if ( $bulkaction ) {
				switch ( $bulkaction ) {
					case 'unlink':
						JumpleadIntegration::unlink_mappings( $forms );
				}
			}
		}

		// Show list
		$activeIntegrations     = JumpleadIntegration::get_active();
		$mappings               = JumpleadIntegration::get_all_mappings();

		// Set up array of mappings for easy use
		$mappingsLookup = array();

		foreach ( $mappings as $mapping ) {
			$key = (string) $mapping->integration_id . '_' . (string) $mapping->form_id;
			$mappingsLookup[$key] = $mapping;
		}

		// Count the forms
		$formCount = 0;
		$formsLookup = array();
		foreach ( $activeIntegrations as $integration ) {
			$forms = $integration->list_forms();
			$formsLookup[$integration->id] = $forms;
			$formCount += count( $forms );
		}

		include(JUMPLEAD_PATH_VIEW . 'integrations.php');
	}

	/**
	 * Jumplead Integrations Mapping Page (Subpage of Jumplead Integrations)
	 * - Sets up mappings
	 * - Sets automation
	 *
	 * @return void
	 */
	static function show_page_integrations_mapping()
	{
		$h2 = 'Jumplead Integrations Mapping';

		$errors = array();
		$info = array();

		$integrationId  = isset( $_GET['integration']) ? sanitize_key( $_GET['integration'] ) : null;
		$formId         = isset( $_GET['form_id']) ? esc_html( $_GET['form_id'] ) : null;

		$integration = JumpleadIntegration::get_by_id( $integrationId );

		// We found integration
		if ( $integration ) {
			$form = $integration->get_form( $formId );

			// We found form
			if ( $form ) {
				$mapping = $integration->get_mapping( $formId );

				if ( ! $mapping ) {
					$mapping = new stdClass();
					$mapping->automation_id = '';
				}

				// Update mapping?
				if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
					// Get automation_id
					$mapping->automation_id = isset($_POST['automation_id']) ? esc_html( $_POST['automation_id'] ) : null;

					// Track fields from form that are matched
					$fieldsInUse = array();

					// Loop the fields
					foreach ( JumpleadIntegration::$fields as $field ) {
						$id = $field['id'];
						$mapping->$id = isset($_POST[$id]) ? esc_html( $_POST[$id] ) : null;

						if ( $mapping->$id ) {
							$fieldsInUse[] = $mapping->$id;
						}

						if ( $field['required'] ) {
							if ( ! $mapping->$id || strlen( $mapping->$id ) < 1 ) {
								$errors[] = $field['name'] . ' is required.';
							}
						}

						// Sub Fields
						if ( isset($field['sub']) ) {
							foreach ( $field['sub'] as $subField ) {
								$id = $subField['id'];
								$mapping->$id = isset($_POST[$id]) ? esc_html( $_POST[$id] ) : null;

								if ( $mapping->$id ) {
									$fieldsInUse[] = $mapping->$id;
								}
							}
						}
					}

					// Check no field is mapped more than once
					if ( empty($errors) ) {
						$lengthBefore   = count( $fieldsInUse );
						// Remove duplicates from array to see if count changes
						$lengthAfter    = count( array_unique( $fieldsInUse ) );

						if ( $lengthBefore != $lengthAfter ) {
							$errors[] = 'A form field may only mapped to one Jumplead field.';
						}
					}

					// No Errors, then save
					if ( empty($errors) ) {
						$response = $integration->save_mapping( $formId, $mapping );

						if ( false === $response ) {
							$errors[] = 'Could not save integration.';
						} else {
							$info[] = 'Integration saved!';
						}
					}
				}

				include(JUMPLEAD_PATH_VIEW . 'integrations-mapping.php');
			}
		}
	}

	// Plugin Management

	/**
	 * Plugin activation hook.
	 *
	 * @return void
	 */
	static function handle_activate()
	{
		self::handle_multi_site( 'activate' );
	}

	/**
	 * Plugin deactivation hook.
	 *
	 * @return void
	 */
	static function handle_deactivate()
	{
		self::handle_multi_site( 'deactivate' );
	}

	/**
	 * Plugin uninstall hook.
	 *
	 * @return void
	 */
	static function handle_uninstall()
	{
		self::handle_multi_site( 'uninstall' );
	}

	/**
	 * Activatation script
	 * - Creates DB tables
	 * - Sets Jumplead verion in DB
	 *
	 * @return void
	 */
	static function activate()
	{
		if ( JUMPLEAD_VERSION != get_site_option( 'jumplead_version' ) ) {
			global $wpdb;

			$charset_collate = '';

			if ( ! empty($wpdb->charset) ) {
				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			}

			if ( ! empty( $wpdb->collate) ) {
				$charset_collate .= " COLLATE {$wpdb->collate}";
			}

			// Mappings
			$sql = 'CREATE TABLE ' . self::$tableFieldMapping . " (
				`id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`integration_id` varchar(100) NOT NULL,
				`form_id` int NOT NULL,
				`automation_id` varchar(100),
				`name` varchar(255),
				`name_last` varchar(255),
				`email` varchar(255),
				`company` varchar(255)
			) $charset_collate;";

			dbDelta( $sql );

			update_option( 'jumplead_version', JUMPLEAD_VERSION );
		}
	}
	/**
	 * Deactivation script
	 *
	 * @return void
	 */
	static function deactivate()
	{
		// Nothing, right now.
	}

	/**
	 * Installation script
	 * - Drops DB tables
	 * - Unsets Jumplead options
	 *
	 * @return void
	 */
	static function uninstall()
	{
		global $wpdb;

		delete_option( 'jumplead_version' );
		delete_option( 'jumplead_tracker_id' );
		delete_option( 'jumplead_capture_comments' );

		// @codingStandardsIgnoreStart
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::$tableFieldMapping );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Runs an aution on single and multi-site installs
	 *
	 * @param string $action The account's name.
	 * @return void
	 */
	static function handle_multi_site($action)
	{
		// Active
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			// Is it networkwide?
			if ( isset($_GET['networkwide']) && (1 == $_GET['networkwide']) ) {

				$current = $wpdb->blogid;

				// Get all blog ids
				// @codingStandardsIgnoreStart
				$blogIds = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs" ) );
				// @codingStandardsIgnoreEnd

				foreach ( $blogIds as $blogId ) {

					// @codingStandardsIgnoreStart
					switch_to_blog( $blogId );
					// @codingStandardsIgnoreEnd

					// Run the action on this site
					self::handle_multi_site_action( $action );
				}

				// @codingStandardsIgnoreStart
				// Switch back to current WordPress
				switch_to_blog( $current );
				// @codingStandardsIgnoreEnd
				return;
			}
		}

		// Run action on Current WordPress site
		self::handle_multi_site_action( $action );
	}

	/**
	 * Runs an aution on single and multi-site installs
	 *
	 * @param string $action The account's name.
	 * @return void
	 */
	static function handle_multi_site_action($action)
	{
		switch ( $action ) {
			case 'activate':
				self::activate();
				break;
			case 'deactivate':
				self::deactivate();
				break;
			case 'uninstall':
				self::uninstall();
				break;
		}
	}
}
