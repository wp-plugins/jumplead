<?php

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

class JumpleadIntegration {

    var $id        = '';
    var $name      = '';
    var $class     = '';
    var $include   = '';
    var $plugin    = '';
    var $active    = false;

    function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    function getMapping($formId)
    {
        global $wpdb;

        $sql = 'SELECT * FROM ' . Jumplead::$tableFieldMapping . ' ' .
               'WHERE integration_id = %s AND form_id = %d';

        $row = $wpdb->get_row($wpdb->prepare($sql, $this->id, $formId));

        if (!$row) {
            $row = new stdClass();
            $row->automation_id = '';
        }

        return $row;
    }


    function saveMapping($formId, $mappings)
    {
        global $wpdb;
        $wpdb->show_errors();

        $currentMapping = $this->getMapping($formId);

        $where = array(
            'integration_id' => $this->id,
            'form_id' => $formId
        );

        $mappings = (array) $mappings;

        // Update existing
        if (isset($currentMapping->id)) {
            return $wpdb->update(Jumplead::$tableFieldMapping, $mappings, $where);
        }

        // Store a new one
        return $wpdb->insert(Jumplead::$tableFieldMapping, array_merge($mappings, $where));
    }



    static $integrations = array(
        array(
            'id'        => 'formidable',
            'name'      => 'Formidable',
            'class'     => 'JumpleadIntegrationFormidable',
            'include'   => 'Formidable',
            'plugin'    => 'formidable/formidable.php',
            'active'    => false
        ),
        array(
            'id'        => 'jetpack',
            'name'      => 'Jetpack Contact Form',
            'class'     => 'JumpleadIntegrationJetpack',
            'include'   => 'Jetpack',
            'plugin'    => 'jetpack/jetpack.php',
            'active'    => false
        )
    );

    static $integrationObjects = array();

    static $fields = array(
        array(
            'id'        => 'name',
            'name'      => 'Full Name',
            'required'  => true,
            'sub'      => array(
                array(
                    'id'        => 'name_last',
                    'required'  => false,
                )
            ),
        ),
        array(
            'id'        => 'email',
            'name'      => 'Email Address',
            'required'  => true
        ),
        array(
            'id'        => 'company',
            'name'      => 'Company',
            'required'  => false
        )
    );

    static function boot()
    {
        foreach (self::$integrations as $key => $integration) {
            if (is_plugin_active($integration['plugin'])) {
                self::$integrations[$key]['active'] = true;

                // Load
                include(JUMPLEAD_PATH_SRC . '/Integration/' . $integration['include'] . '.php');

                // Instanitate
                $modelName = $integration['class'];
                self::$integrationObjects[$integration['id']] = new $modelName($integration);
            }
        }

        // Check for Data that needs sending to Jumplead
        // - Jetpack
        if ($jetpack = JumpleadIntegration::getById('jetpack')) {
            $jetpack->recoverData();
        }

    }

    static function getAllMappings()
    {
        global $wpdb;

        $sql = 'SELECT * FROM ' . Jumplead::$tableFieldMapping;

        return $wpdb->get_results($sql);
    }

    static function unlinkMappings($ids)
    {
        global $wpdb;

        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $wpdb->delete(Jumplead::$tableFieldMapping, array('id' => $id));
            }
        }
    }

    static function getActive()
    {
        return self::$integrationObjects;
    }

    static function getInactive()
    {
        $inactive = [];

        foreach (self::$integrations as $integration) {
            if (!$integration['active']) {
                $inactive[] = $integration;
            }
        }

        return $inactive;
    }

    static function getById($id)
    {
        if (isset(self::$integrationObjects[$id])) {
            return self::$integrationObjects[$id];
        }
        return null;
    }

}















