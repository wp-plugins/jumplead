<?php
class JumpleadIntegrationContactForm7 extends JumpleadIntegration {

	private $formTypesToIgnore = array(
		'quiz',
		'file',
		'captchar',
		'submit',
	);

	function __construct($data)
	{
		parent::__construct( $data );

		// Hooks
		add_action( 'wpcf7_before_send_mail', array($this, 'capture') );
	}

	/**
	 * @inherit
	 */
	function list_forms()
	{
		$queryOptions = array(
			'post_type' => array('wpcf7_contact_form'),
		);

		return $this->list_forms_wp_query( $queryOptions );
	}

	/**
	 * @inherit
	 */
	function get_form($id)
	{
		$form = wpcf7_contact_form( $id );

		if ( $form ) {

			$return = array(
				'id'        => $id,
				'name'      => $form->name(),
				'fields'    => array(),
			);

			$properties = $form->get_properties();

			// Fields
			$content = $properties['form'];
			$pattern = '\[([^\]]+)\]';

			if ( preg_match_all( '#' . $pattern . '#s', $content, $matches ) ) {
				if ( isset($matches[1]) ) {
					foreach ( $matches[1] as $attrsSting ) {
						// Split short code into parts
						$attrs = shortcode_parse_atts( $attrsSting );

						// Get field type and name
						list($type, $name) = $attrs;
						$type = trim( $type, '*' );

						// Ignore some types of fields
						if ( ! in_array( $type, $this->formTypesToIgnore ) ) {
							// Add fields
							$return['fields'][] = array(
								'id' => $name,
								'name' => ucwords( str_replace( '-', ' ', $name ) ),
							);
						}
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Capture and recoved the data from a form
	 *
	 * @return void
	 */
	function capture($cf7)
	{
		$formId = $cf7->id();
		// @codingStandardsIgnoreStart
		$formData = $_POST;
		// @codingStandardsIgnoreEnd

		if ( $formId && $formData && $mapping = $this->get_mapping( $formId ) ) {
			$data = array(
				'name'          => isset($formData[$mapping->name])        ? $formData[$mapping->name]          : null,
				'name_last'     => isset($formData[$mapping->name_last])   ? $formData[$mapping->name_last]     : null,
				'email'         => isset($formData[$mapping->email])       ? $formData[$mapping->email]         : null,
				'company'       => isset($formData[$mapping->company])     ? $formData[$mapping->company]       : null,
				'automation_id' => $mapping->automation_id,
			);

			JumpleadIntegration::save_cookies( $data );
		}
	}
}
