<?php
include(JUMPLEAD_PATH_VIEW . 'includes/header.php');

// @codingStandardsIgnoreStart
$formAction = $_SERVER['REQUEST_URI'];
// @codingStandardsIgnoreEnd
?>
	<h3><?php echo esc_html( $form['name'] . ' (' . $integration->name . ')' ); ?></h3>
    <p>
        Match up the fields in <em><?php echo esc_html( $form['name'] ); ?></em> with Jumplead's contact fields to create contacts.
    </p>

    <form name="jumplead_form" method="post" action="<?php echo esc_attr( $formAction ); ?>">
        <table class="form-table">
            <tbody>
<?php
foreach ( JumpleadIntegration::$fields as $jumpleadField ) {
	$id = $jumpleadField['id'];
	$fieldMap = isset($mapping->$id) ? $mapping->$id : null;
?>
		<tr>
			<th>
				<label><?php echo esc_html( $jumpleadField['name'] ); ?></label>
				<p class="description"><?php echo esc_html( ($jumpleadField['required']) ? 'Required' : '' ); ?></p>
			</th>
			<td>
				<select name="<?php echo esc_attr( $id ); ?>">
					<option></option>
<?php
foreach ( $form['fields'] as $field ) {
	// Pre-select current mapping
	$selected = '';

	if ( $fieldMap && $fieldMap == $field['id'] ) {
		$selected = 'selected="selected"';
	}
?>

	<option value="<?php echo esc_attr( $field['id'] ); ?>" <?php echo wp_kses( $selected, true ); ?>>
		<?php echo esc_html( $field['name'] ); ?>
	</option>
<?php

}
?>
				</select>
<?php
if ( isset($jumpleadField['sub']) ) {
	foreach ( $jumpleadField['sub'] as $jumpleadFieldSub ) {
		$id = $jumpleadFieldSub['id'];
		$fieldMap = isset($mapping->$id) ? $mapping->$id : null;
?>
		<select name="<?php echo esc_attr( $id ); ?>">
			<option></option>
<?php
foreach ( $form['fields'] as $field ) {
	// Pre-select current mapping
	$selected = '';

	if ( $fieldMap && $fieldMap == $field['id'] ) {
		$selected = 'selected="selected"';
	}
?>

	<option value="<?php echo esc_attr( $field['id'] ); ?>" <?php echo wp_kses( $selected, true ); ?>>
		<?php echo esc_attr( $field['name'] ); ?>
	</option>
<?php
}
?>
		</select>
<?php
	}
}

				// Field description?
if ( isset($jumpleadField['description']) && $jumpleadField['description'] ) {
	echo wp_kses( '<p class="description">' . $jumpleadField['description'] . '</p>', true );
}
?>                  </td>
		</tr>
<?php
}
?>
            </tbody>
        </table>

        <h3>Adding an Automation</h3>
        <p>
            Add a Jumplead Automation ID to trigger the automation on form completion.
        </p>

        <h4>You Can Use Automations To</h4>
        <ul>
            <li>Send campaign (Choose from one of your Autoresponder campaigns)</li>
            <li>Issue notification (Send an email to a Jumplead user)</li>
            <li>Change stage (This is the Contact’s stage)</li>
            <li>Add contact tag (Use tags to group your Contacts into Lists)</li>
        </ul>

        <h4>Getting an Automation ID</h4>
        <ol>
            <li>Login to your <a href="https://app.jumplead.com" target="_blank">Jumplead account</a></li>
            <li>Select the <a href="https://app.jumplead.com/automations" target="_blank">Automation</a> you wish to trigger</li>
            <li>Copy and paste the Automation ID below</li>
        </ol>




        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <label for="jumplead_tracker_id">Automation ID</label>
                    </th>
                    <td>
                        <input
                            type="text" id="tracker_id" name="automation_id"
                            value="<?php echo esc_attr( $mapping->automation_id ); ?>"
                        >
                    </td>
                </tr>
            </tbody>
        </table>


        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
            <a class="button-secondary"
                href="<?php echo esc_url( admin_url( 'admin.php?page=jumplead_integrations' ) ); ?>">Cancel</a>
        </p>
    </form>
<?php
include(JUMPLEAD_PATH_VIEW . 'includes/footer.php');
?>
