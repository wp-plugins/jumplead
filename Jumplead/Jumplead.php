<?php
/**
 * Jumplead Admin Setup
 */

class Jumplead
{
    static $plugin = 'jumplead/jumplead.php';
    static $tableFieldMapping = '';
    static $tableSubmissions = '';
    static $data = array();

    static function boot()
    {
        global $wpdb;

        // Install
        register_activation_hook(self::$plugin, 'Jumplead::activate');
        register_deactivation_hook(self::$plugin, 'Jumplead::deactivate');
        add_action('plugins_loaded', 'Jumplead::activate');

        // Jumplead Admin
        add_action('admin_menu', 'Jumplead::adminMenu');

        // Tables
        self::$tableFieldMapping = $wpdb->prefix . 'jumplead_mapping';
        self::$tableSubmissions  = $wpdb->prefix . 'jumplead_submissions';
    }

    static function adminMenu()
    {
        $icon = plugins_url('jumplead/assets/jumplead-icon.png');
    	add_menu_page('Jumplead', 'Jumplead', 1, 'jumplead', 'Jumplead::showPageJumplead', $icon);

    	add_submenu_page('jumplead', 'Integrations', 'Integrations', 1, 'jumplead_integations', 'Jumplead::showPageIntegrations');
    	add_submenu_page('jumplead', 'Settings', 'Settings', 1, 'jumplead_settings', 'Jumplead::showPageSettings');
    }

    static function showPageJumplead()
    {
	    include(JUMPLEAD_PATH_VIEW . 'jumplead.php');
    }

    static function showPageSettings()
    {
        $errors = [];
        $info = [];

        // Update?
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        	$tracker_id = $_POST['tracker_id'];

        	if (jumplead_is_tracker_id_valid($tracker_id)) {
        	    update_option('jumplead_tracker_id', trim($tracker_id));
        	    $info[] = 'Tracker ID saved!';
        	} else {
        	    $errors[] = 'Tracker ID is not valid.';
        	}
        }


        $tracker_id = get_option('jumplead_tracker_id', null);
        $tracker_id_valid = jumplead_is_tracker_id_valid($tracker_id);

        if (!$tracker_id) {
            $info[] = 'Enter your Tracker ID to get started.';
        }

        // View
	    include(JUMPLEAD_PATH_VIEW . 'settings.php');
    }

    static function showPageIntegrations()
    {
        $page = null;

        if (isset($_GET['subpage'])) {
            $page = $_GET['subpage'];
        }

        switch ($page) {
            case 'mapping':
                self::showPageIntegrationsMapping();
                break;
            default:
                self::showPageIntegrationsIndex();
        }
    }

    static function showPageIntegrationsIndex()
    {
        $active     = JumpleadIntegration::getActive();
        $inactive   = JumpleadIntegration::getInactive();

	    include(JUMPLEAD_PATH_VIEW . 'integrations.php');
    }

    static function showPageIntegrationsMapping()
    {
        $errors = [];
        $info = [];

        $integrationId  = sanitize_key($_GET['integration']);
        $formId         = (int) $_GET['form_id'];

        $integration = JumpleadIntegration::getById($integrationId);

        // We found integration
        if ($integration) {
            $form = $integration->getForm($formId);

            // We found form
            if ($form) {
                $mapping = $integration->getMapping($formId);

                if (!$mapping) {
                    $mapping = new stdClass();
                }

                // Update mapping?
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    foreach ($integration::$fields as $field) {
                        $id = $field['id'];
                        $mapping->$id = isset($_POST[$id]) ? $_POST[$id] : null;

                        if ($field['required']) {
                            if (!$mapping->$id || strlen($mapping->$id) < 1) {
                                $errors[] = $field['name'] . ' is required.';
                            }
                        }

                        // Sub Fields
                        if (isset($field['sub'])) {
                            foreach ($field['sub'] as $subField) {
                                $id = $subField['id'];
                                $mapping->$id = isset($_POST[$id]) ? $_POST[$id] : null;
                            }
                        }
                    }

                    // No Errors, then save
                    if (empty($errors)) {
                        $response = $integration->saveMapping($formId, $mapping);

                        if ($response === false) {
                            $errors[] = 'Could not save integration.';
                        } else {
                            $info[] = 'Integration saved!';
                        }
                    }
                }

    	        include(JUMPLEAD_PATH_VIEW . 'integrations_mapping.php');
    	    }
        }
    }



    static function activate()
    {
        // Install
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        if (JUMPLEAD_VERSION != get_site_option('jumplead_db_version')) {
    	    global $wpdb;

        	$charset_collate = '';

        	if (!empty($wpdb->charset)) {
        	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        	}

        	if (!empty( $wpdb->collate)) {
        	  $charset_collate .= " COLLATE {$wpdb->collate}";
        	}

            // Mappings
            $sql = "CREATE TABLE " . self::$tableFieldMapping . " (
                `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `integration_id` varchar(100) NOT NULL,
                `form_id` int NOT NULL,
                `name` varchar(255),
                `name_last` varchar(255),
                `email` varchar(255),
                `company` varchar(255)
            ) $charset_collate;";

        	dbDelta($sql);

        	// Submission Storage
            $sql = "CREATE TABLE " . self::$tableSubmissions. " (
                `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `integration_id` varchar(100) NOT NULL,
                `submission_id` int NOT NULL,
                `name` varchar(255),
                `name_last` varchar(255),
                `email` varchar(255),
                `company` varchar(255)
            ) $charset_collate;";

        	dbDelta($sql);


            update_option('jumplead_db_version', JUMPLEAD_VERSION);
        }
    }

    static function deactivate()
    {
        delete_option('jumplead_db_version');
    }
}
