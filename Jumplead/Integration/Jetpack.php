<?php

class JumpleadIntegrationJetpack extends JumpleadIntegration {

    function __construct($data)
    {
        parent::__construct($data);

        // Hooks
        add_action('grunion_pre_message_sent', array($this, 'captureForm'));
    }

    function listForms()
    {
        $return = array();

        $query = new WP_Query(
            array(
                's' => '[contact-form]'
            )
        );

        // The Loop
        if ($query->have_posts()) {
        	while ($query->have_posts()) {

        		$query->the_post();

                $return[] = array(
                    'id'        => get_the_ID(),
                    'name'      => get_the_title(),
                    'fields'    => array()
                );
        	}
        }

        wp_reset_postdata();

        return $return;
    }

    function getForm($id)
    {
        $return = null;

        $form = get_post($id);

        if ($form) {
            $return = array(
                'id'        => $form->ID,
                'name'      => $form->post_title,
                'fields'    => array()
            );

            // Fields
            $content = $form->post_content;
            $pattern = '\[contact\-field([^\]]+)/\]';

            if (preg_match_all('#' . $pattern . '#s', $content, $matches )) {

                if (isset($matches[1])) {
                    foreach ($matches[1] as $attrsSting) {
                        $attrs = shortcode_parse_atts( $attrsSting );

                        $return['fields'][] = array(
                            'id' => 'g' . $id . '-' . str_replace('-', '', sanitize_title($attrs['label'])),
                            'name' => $attrs['label']
                        );
                    }
                }
            }
        }

        return $return;
    }

    function captureForm($submissionId)
    {
        global $wpdb;

        $formId = isset($_POST['contact-form-id']) ? (int) $_POST['contact-form-id'] : null;
        $formData = $_POST;

        if ($formId && $formData) {
            $mapping = $this->getMapping($formId);

            if ($mapping) {
                $data = array(
                    'integration_id'    => $this->id,
                    'submission_id'     => $submissionId,
                    'name'              => isset($formData[$mapping->name])       ? $formData[$mapping->name]       : null,
                    'name_last'         => isset($formData[$mapping->name_last])  ? $formData[$mapping->name_last]  : null,
                    'email'             => isset($formData[$mapping->email])      ? $formData[$mapping->email]      : null,
                    'company'           => isset($formData[$mapping->company])    ? $formData[$mapping->company]    : null,
                    'automation_id'     => $mapping->automation_id
                );

                $wpdb->insert(Jumplead::$tableSubmissions, $data);
            }
        }
    }


    function recoverData()
    {
        if (isset($_GET['contact-form-id']) && isset($_GET['contact-form-sent'])) {
            global $wpdb;

            $sql = 'SELECT * FROM ' . Jumplead::$tableSubmissions . ' ' .
                   'WHERE integration_id = %s AND submission_id = %d';

            $data = $wpdb->get_row($wpdb->prepare($sql, 'jetpack', $_GET['contact-form-sent']));

            if ($data) {
                Jumplead::$data = (array) $data;

/*
                $wpdb->delete(
                    Jumplead::$tableSubmissions,
                    array(
                        'integration_id'    => $data->integration_id,
                        'submission_id'     => $data->submission_id
                    )
                );
*/
            }
        }
    }
}
