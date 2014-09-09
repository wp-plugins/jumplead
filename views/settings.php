<?php include(JUMPLEAD_PATH_VIEW . 'message.php'); ?>

<div class="wrap">
	<h2>
	    <a class="button-primary" style="float: right;" href="http://jumplead.com" target="_blank">Login</a>
	    Settings
    </h2>

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
						<input type="text" id="tracker_id" name="tracker_id" value="<?php echo $tracker_id ?>" size="20">
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

<?php
    if ($tracker_id_valid) {
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
</div>



