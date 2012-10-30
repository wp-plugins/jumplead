<?php
if(isset($_POST['jumplead_trk_id'])) {
	//Form data sent
	$jumplead_trk_id = $_POST['jumplead_trk_id'];
	update_option('jumplead_trk_id', $jumplead_trk_id);
	?>
	<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div><?php
}

else {
	//Normal page display
	$jumplead_trk_id = get_option('jumplead_trk_id');
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2>Jumplead Settings</h2>

	<form name="jumplead_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<h3>Tracker Settings</h3>

		<p>Use this page to enter your Jumplead Tracker ID. This information is available in your Jumplead settings.</p>

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="jumplead_trk_id">Tracker ID</label></th>
					<td>
						<input type="text" id="jumplead_trk_id" name="jumplead_trk_id" value="<?php echo $jumplead_trk_id ?>" size="20">
						<span class="description">Example: JL-1111111111-1</span>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
		</p>
	</form>
</div>

<?php
}