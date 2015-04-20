<?php
/**
 * Jumplead Integations
 */

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

class JumpleadIntegration {

	/**
	 * Integation's ID
	 */
	var $id        = '';

	/**
	 * Integation's Name
	 */
	var $name      = '';

	/**
	 * Integation's Class NAme
	 */
	var $class     = '';

	/**
	 * File where class. Must be in Jumplead/Integration/ folder
	 */
	var $include   = '';

	/**
	 * Namespace and file of plugin dependancy
	 */
	var $plugin    = '';

	/**
	 * Active status
	 */
	var $active    = false;

	function __construct($data)
	{
		foreach ( $data as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * List forms belonging to integration
	 * - To be overwritten by most
	 *
	 * @return mixed List of forms
	 */
	function list_forms()
	{
		return null;
	}

	/**
	 * List forms using WP_Query search
	 *
	 * @param array $queryOptions Criteria for search
	 * @return mixed List of forms
	 */
	protected function list_forms_wp_query($queryOptions)
	{
		$return = array();

		// Add pagination controll to WP_Query criteria
		$queryPage = 1;
		$queryOptions['posts_per_page'] = '10';
		$queryOptions['paged'] = (string) $queryPage;

		// Query
		$query = new WP_Query( $queryOptions );

		// Loop
		while ( $query->have_posts() ) {
			wp_reset_postdata();

			// The Loop
			while ( $query->have_posts() ) {
				$query->the_post();

				$return[] = array(
					'id'        => get_the_ID(),
					'name'      => get_the_title(),
					'fields'    => array(),
				);
			}

			// Load next page's data
			$queryPage++;
			$queryOptions['paged'] = $queryPage;
			$query = new WP_Query( $queryOptions );
			wp_reset_postdata();
		};

		return $return;
	}

	/**
	 * Get a specific form from integration
	 * - To be overwritten by most
	 *
	 * @param mixed $formId Id of integration's form
	 * @return mixed Form, or null
	 */
	function get_form($formId)
	{
		return null;
	}

	/**
	 * Get the mapping settings for a form
	 *
	 * @param mixed $formId Id of integration's form
	 * @return mixed Form, or null
	 */
	function get_mapping($formId)
	{
		global $wpdb;

		$sql = 'SELECT * FROM ' . Jumplead::$tableFieldMapping . ' ' .
			   'WHERE integration_id = %s AND form_id = %d';

		// @codingStandardsIgnoreStart
		return $wpdb->get_row( $wpdb->prepare( $sql, $this->id, $formId ) );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Save mappings
	 *
	 * @param mixed $formId Id of integration's form
	 * @param array $mappings Array with mapping data
	 * @return mixed Out come of insert/update
	 */
	function save_mapping($formId, $mappings)
	{
		global $wpdb;
		$wpdb->show_errors();

		$currentMapping = $this->get_mapping( $formId );

		$where = array(
			'integration_id' => $this->id,
			'form_id' => $formId,
		);

		$mappings = (array) $mappings;

		// Update existing
		if ( isset($currentMapping->id) ) {
			return $wpdb->update( Jumplead::$tableFieldMapping, $mappings, $where );
		}

		// Store a new one
		return $wpdb->insert( Jumplead::$tableFieldMapping, array_merge( $mappings, $where ) );
	}


	// Static Variables

	/**
	 * Had Jumplead already recovered form data form cookies
	 */
	static $recovered = false;

	/**
	 * Jumplead Prefix for Cookies
	 */
	static $cookiePrefix = 'jlwp_';

	/**
	 * Cookies used to store form data
	 */
	static $cookies = array(
		'name',
		'name_last',
		'email',
		'company',
		'automation_id',
	);

	/**
	 * Integrations avalible
	 */
	static $integrations = array(
		array(
			'id'        => 'formidable',
			'name'      => 'Formidable',
			'class'     => 'JumpleadIntegrationFormidable',
			'include'   => 'Formidable',
			'plugin'    => 'formidable/formidable.php',
			'active'    => false,
		),
		array(
			'id'        => 'jetpack',
			'name'      => 'Jetpack Contact Form',
			'class'     => 'JumpleadIntegrationJetpack',
			'include'   => 'Jetpack',
			'plugin'    => 'jetpack/jetpack.php',
			'active'    => false,
		),
		array(
			'id'        => 'cf7',
			'name'      => 'Contact Form 7',
			'class'     => 'JumpleadIntegrationContactForm7',
			'include'   => 'ContactForm7',
			'plugin'    => 'contact-form-7/wp-contact-form-7.php',
			'active'    => false,
		),
	);
	/**
	 * Arary of instances of active integrations
	 */
	static $integrationObjects = array();

	/**
	 * Fields that can be mapped
	 */
	static $fields = array(
		array(
			'id'        => 'name',
			'name'      => 'Full Name',
			'required'  => true,
			'description' => 'The first field should be the first or full name. The second should be last name or blank.',
			'sub'      => array(
				array(
					'id'        => 'name_last',
					'required'  => false,
				),
			),
		),
		array(
			'id'        => 'email',
			'name'      => 'Email Address',
			'required'  => true,
		),
		array(
			'id'        => 'company',
			'name'      => 'Company',
			'required'  => false,
		),
	);

	// Static Functions

	/**
	 * Boot script
	 * - Checks for avaliable plugins
	 * - Instantiates classes for active plugins
	 * - Check for capturing of commets
	 * - Adds hook to recover data from cookies
	 *
	 * @return void
	 */
	static function boot()
	{
		if ( jumplead_is_tracker_id_valid() ) {
			foreach ( self::$integrations as $key => $integration ) {
				if ( is_plugin_active( $integration['plugin'] ) ) {
					self::$integrations[$key]['active'] = true;

					// Load
					include(JUMPLEAD_PATH_SRC . '/Integration/' . $integration['include'] . '.php');

					// Instanitate
					$modelName = $integration['class'];
					self::$integrationObjects[$integration['id']] = new $modelName($integration);
				}
			}

			// Wordpress Commennts
			if ( get_option( 'jumplead_capture_comments', false ) ) {
				// JumpleadIntegrationComment is self contained,
				// so don't need to storge the object
				include(JUMPLEAD_PATH_SRC . '/Integration/Comment.php');
				new JumpleadIntegrationComment(
					array(
						'id'        => 'comment',
						'name'      => 'WordPress Comments',
						'class'     => 'JumpleadIntegrationComment',
						'include'   => null,
						'plugin'    => null,
						'active'    => true,
					)
				);
			}
		}
	}

	/**
	 * Get all mappings
	 *
	 * @return array
	 */
	static function get_all_mappings()
	{
		global $wpdb;

		// @codingStandardsIgnoreStart
		return $wpdb->get_results( 'SELECT * FROM ' . Jumplead::$tableFieldMapping );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Remove mappings
	 *
	 * @param array $ids IDs of mappings to remove
	 * @return void
	 */
	static function unlink_mappings($ids)
	{
		global $wpdb;

		foreach ( $ids as $id ) {
			$id = (int) $id;
			if ( $id > 0 ) {
				$wpdb->delete( Jumplead::$tableFieldMapping, array('id' => $id) );
			}
		}
	}

	/**
	 * Get all active integations
	 *
	 * @return array
	 */
	static function get_active()
	{
		return self::$integrationObjects;
	}

	/**
	 * Get all in active integations
	 *
	 * @return array
	 */
	static function get_inactive()
	{
		$inactive = array();

		foreach ( self::$integrations as $integration ) {
			if ( ! $integration['active'] ) {
				$inactive[] = $integration;
			}
		}

		return $inactive;
	}


	/**
	 * Get a instant of an active integration by it's ID
	 *
	 * @return object|null
	 */
	static function get_by_id($id)
	{
		if ( isset(self::$integrationObjects[$id]) ) {
			return self::$integrationObjects[$id];
		}
		return null;
	}

	/**
	 * Saves data to cookies
	 *
	 * @param array $cookies Key - value array of data to save to cookies.
	 * @return void
	 */
	static function save_cookies($cookies)
	{
		foreach ( self::$cookies as $cookie ) {
			if ( isset($cookies[$cookie]) ) {
				// Use setraw and rawrulencode to prevent PHP replacing spaces with +
				$name = self::$cookiePrefix . $cookie;
				$value = rawurlencode( $cookies[$cookie] );
				setrawcookie( $name, $value, time() + 3600, '/' );
			}
		}
	}
}