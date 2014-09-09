<div class="wrap">
	<h2>Integrations</h2>

    <?php
    // Active
    if (count($active) > 0) {
        echo '<h3>Active Integrations</h3>';

        foreach ($active as $integration) {
            echo '<h4>' . $integration->name . '</h4>';

            $baseurl = esc_url(admin_url('admin.php?page=jumplead_integations&subpage=mapping&'));

            foreach ($integration->listForms() as $form) {
                echo '<a href="' . $baseurl . 'integration='. $integration->id .'&form_id=' . $form['id'].'">';
                echo $form['name'];
                echo '</a> ';
            }
            echo '<hr />';
        }
    }

    // Inactive
    if (count($inactive) > 0) {
        echo '<hr />';
        echo '<h3>Available Integrations</h3>';

        foreach ($inactive as $integration) {
            echo '<h4>' . $integration['name'] . '</h4>';
        }
    }
    ?>
</div>