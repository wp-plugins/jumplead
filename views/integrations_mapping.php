<div class="wrap">
	<h2>Integrations - Mapping - <?php echo $form['name'] . ' (' . $integration->name . ')'; ?></h3>

    <?php include(JUMPLEAD_PATH_VIEW . 'message.php'); ?>

    <form name="jumplead_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <p>Match up the fields in <?php echo $form['name']; ?> with Jumplead's contact fields.</p>

        <?php
        foreach ($integration::$fields as $jumpleadField) {
            $id = $jumpleadField['id'];
            $fieldMap = isset($mapping->$id) ? $mapping->$id : null;
            ?>

            <label>
                <?php
                echo $jumpleadField['name'];
                if ($jumpleadField['required']) {
                    echo ' *';
                }
                ?>
            </label>
            <br />

            <select name="<?php echo $id; ?>">
                <option></option>
            <?php
            foreach ($form['fields'] as $field) {
                // Pre-select current mapping
                $selected = '';

                if ($fieldMap && $fieldMap == $field['id']) {
                    $selected = 'selected="selected"';
                }
                ?>

                <option value="<?php echo $field['id']; ?>" <?php echo $selected; ?>>
                    <?php echo $field['name']; ?>
                </option>
                <?php

            }
            ?>
            </select>
            <?php
            if (isset($jumpleadField['sub'])) {

                foreach ($jumpleadField['sub'] as $jumpleadField) {
                    $id = $jumpleadField['id'];
                    $fieldMap = isset($mapping->$id) ? $mapping->$id : null;
                ?>
                    +
                    <select name="<?php echo $id; ?>">
                        <option></option>
                    <?php
                    foreach ($form['fields'] as $field) {
                        // Pre-select current mapping
                        $selected = '';

                        if ($fieldMap && $fieldMap == $field['id']) {
                            $selected = 'selected="selected"';
                        }
                        ?>

                        <option value="<?php echo $field['id']; ?>" <?php echo $selected; ?>>
                            <?php echo $field['name']; ?>
                        </option>
                        <?php

                    }
                    ?>
                    </select>
            <?php
                }
            }

            ?>
            <br />
            <?php
        }
        ?>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
            <a class="button-secondary"
                href="<?php echo esc_url(admin_url('admin.php?page=jumplead_integations')); ?>">Cancel</a>
		</p>
	</form>
</div>
