<?php
$jumplead_tracker_id_updated = isset($_POST['jumplead_tracker_id']);

if ($jumplead_tracker_id_updated) {
	$jumplead_tracker_id = $_POST['jumplead_tracker_id'];
	update_option('jumplead_tracker_id', trim($jumplead_tracker_id));
}

$jumplead_tracker_id_valid = jumplead_is_tracker_id_valid();
$jumplead_tracker_id = $jumplead_tracker_id_valid ? get_option('jumplead_tracker_id') : '';

if ($jumplead_tracker_id_updated) {
    if ($jumplead_tracker_id_valid) {
?>
        <div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
<?php
    } else {
?>
        <div class="error"><p><strong><?php _e('Tracker ID is not valid.' ); ?></strong></p></div>
<?php
    }
} else if (!$jumplead_tracker_id_valid) {
?>
    <div class="updated"><p><strong>Enter your Tracker ID to get started. </strong></p></div>
<?php
}
?>


<div class="wrap">
	<h2>
	    <a class="button-primary" style="float: right;" href="http://jumplead.com" target="_blank">Jumplead.com</a>
	    Jumplead
    </h2>

<?php
    if ($jumplead_tracker_id_valid) {
?>

	<h3>How To Use</h3>

     <p>
        <strong>Tracking</strong><br />
        Once you've entered your Tracker ID, we'll automatically embed the Jumplead tracking code on every page.
    </p>

    <p>
        <strong>Embedding Conversion Forms</strong><br />

        Conversion Forms can be embedded on any page using the Jumplead short code.
        To obtain the short code for a Conversion Form:
        <ol>
            <li>Login to your <a href="https://app.jumplead.com" target="_blank">Jumplead account</a></li>
            <li>Select the <a href="https://app.jumplead.com/conversion/forms" target="_blank">Conversion Form</a> you wish to embed</li>
            <li>Click Generate Form in the Form builder</li>
            <li>Copy the short code</li>
            <li>Paste into any page</li>
        </ol>
    </p>

    <hr />
<?php
    }
?>
	<form name="jumplead_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <h3>Tracker Settings</h3>
        <p>You can find your Tracker ID in your <a href="https://app.jumplead.com/settings/tracking-code" target="_blank">Jumplead Settings</a>.</p>

		<table class="form-table">
			<tbody>
				<tr>
					<th>
					    <label for="jumplead_tracker_id">Tracker ID</label><br />
					</th>
					<td>
						<input type="text" id="jumplead_tracker_id" name="jumplead_tracker_id" value="<?php echo $jumplead_tracker_id ?>" size="20">
						<span class="description">

						Example: JL-1111111111-1
						</span>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
		</p>
	</form>
</div>



