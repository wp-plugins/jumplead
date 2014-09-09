<?php

class JumpleadIntegrationFormidable extends JumpleadIntegration {

    function __construct($data)
    {
        parent::__construct($data);

        // Hooks
        add_action('frm_after_create_entry', array($this, 'captureForm'));
    }

    function listForms()
    {
        $modelForm = new FrmForm();
        $forms = $modelForm->getAll();

        $modelFields = new FrmField();

        $return = array();

        foreach ($forms as $form) {
            $return[] = array(
                'id' => $form->id,
                'name' => $form->name,
                'fields' => array()
            );
        }

        return $return;
    }

    function getForm($id)
    {
        $return = null;

        $modelForm = new FrmForm();
        $form = $modelForm->getOne($id);

        if ($form) {
            $modelFields = new FrmField();

            $return = array(
                'id' => $form->id,
                'name' => $form->name,
                'fields' => array()
            );

            // Fields
            $where = array('form_id' => $form->id);
            $fields = $modelFields->getAll($where);

            foreach ($fields as $field) {
                $return['fields'][] = array(
                    'id' => $field->id,
                    'name' => $field->name
                );
            }
        }

        return $return;
    }

    function captureForm()
    {
        $formId = isset($_POST['form_id']) ? $_POST['form_id'] : null;
        $formData = isset($_POST['item_meta']) ? $_POST['item_meta'] : null;

        if ($formId && $formData) {
            $mapping = $this->getMapping($formId);

            if ($mapping) {
                Jumplead::$data = array(
                    'name'          => isset($formData[$mapping->name])        ? $formData[$mapping->name]          : null,
                    'name_last'     => isset($formData[$mapping->name_last])   ? $formData[$mapping->name_last]     : null,
                    'email'         => isset($formData[$mapping->email])       ? $formData[$mapping->email]         : null,
                    'company'       => isset($formData[$mapping->company])     ? $formData[$mapping->company]       : null
                );
            }
        }
    }
}
