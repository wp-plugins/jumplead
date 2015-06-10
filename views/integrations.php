<?php
include(JUMPLEAD_PATH_VIEW . 'includes/header.php');
?>
<h3>Web Forms</h3>
<?php
// Active
if ( $formCount > 0 ) {
?>
<form id="posts-filter" method="post">

	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
			<select name="bulkaction">
				<option value="">Bulk Actions</option>
				<option value="unlink">Unlink</option>
			</select>

			<input type="submit" class="button action" value="Apply">
		</div>

        <div class="tablenav-pages one-page">
			<span class="displaying-num">
			    <?php echo esc_html( $formCount ); ?> <?php echo esc_html( ($formCount == 1) ? 'form' : 'forms' ); ?>
			 </span>
		</div>
	</div>

	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-cb check-column">
					<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
					<input id="cb-select-all-1" type="checkbox">
				</th>
				<th scope="col" class="manage-column column-title">
					Form
				</th>
				<th scope="col"></th>
				<th scope="col">
					Plugin
				</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-cb check-column">
					<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
					<input id="cb-select-all-2" type="checkbox">
				</th>
				<th scope="col" class="manage-column column-title">
					Form
				</th>
				<th scope="col"></th>
				<th scope="col">
					Plugin
				</th>
			</tr>
		</tfoot>

		<tbody id="the-list">
<?php
			$i = 0;
foreach ( $activeIntegrations as $integration ) {
	$baseurl = esc_url( admin_url( 'admin.php?page=jumplead_integrations&subpage=mapping&' ) );

	foreach ( $formsLookup[$integration->id] as $form ) {
		$url    = $baseurl . 'integration='. $integration->id .'&form_id=' . $form['id'];
		$name   = $form['name'];

		// Mapping
		$key        = (string) $integration->id . '_' . (string) $form['id'];
		$mapping    = isset($mappingsLookup[$key])
						? $mappingsLookup[$key]
						: null;
		$mappingId  = ($mapping) ? $mapping->id : '-1';

		// Row colouring
		$alternateClass = ($i % 2 == 0) ? 'alternate' : '';
	?>
		<tr class="<?php echo esc_attr( $alternateClass ); ?>">
			<th scope="row" class="check-column">
				<label class="screen-reader-text" for="<?php echo esc_attr( 'cb-select-' . $form['id'] ); ?>">
					Select <?php echo esc_html( $name ); ?>
				</label>
				<input
					id="<?php echo esc_attr( 'cb-select-' . $form['id'] ); ?>"
					type="checkbox"
					name="forms[]"
					value="<?php echo esc_attr( $mappingId ); ?>"
				>
			</th>
			<td>
				<strong>
					<a class="row-title" href="<?php echo esc_attr( $url ); ?>" title="Edit: <?php echo esc_attr( $name ); ?>">
						<?php echo esc_html( $name ); ?>
					</a>
				</strong>

				<div class="row-actions"></div>
			</td>
			<td>
<?php
if ( $mapping ) {
?>
<div class="dashicons dashicons-admin-links"></div>
Linked
<?php
}
?>
			</td>
			<td>
				<?php echo esc_html( $integration->name ); ?>
			</td>
		</tr>
<?php
		$i++;
	} // foreach $integration
} // foreach $activeIntegrations
?>
		</tbody>
	</table>

	<div class="tablenav bottom">
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
			<select name="bulkaction2">
				<option value="">Bulk Actions</option>
				<option value="unlink">Unlink</option>
			</select>

			<input type="submit" class="button action" value="Apply">
		</div>

		<div class="tablenav-pages one-page">
			<span class="displaying-num">
			    <?php echo esc_html( $formCount ); ?> <?php echo esc_html( ($formCount == 1) ? 'form' : 'forms' ); ?>
			 </span>
		</div>
	</div>
</form>

<?php
} else {
?>
	<p>We couldn't find any compatible forms in your WordPress blog.</p>

	<h3>Why not create a form with Jumplead?</h3>
	<p>
		Jumplead forms allow you to create contacts in the Jumplead CRM, trigger marketing automations, and progressively profile your contacts on several forms submissions.
	</p>

	<p>
		<a class="button button-primary" href="http://app.jumplead.com" target="_blank">Login</a>
	</p>
<?php

}

	include(JUMPLEAD_PATH_VIEW . 'includes/footer.php');
?>
