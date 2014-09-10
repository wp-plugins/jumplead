<?php
include(JUMPLEAD_PATH_VIEW . 'includes/header.php');
?>
    <h3>Web Forms</h3>
<?php
    // Active
    if (count($active) > 0) {
?>
    <form id="posts-filter" method="post">

        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
                <select name="bulkaction">
                    <option>Bulk Actions</option>
                    <option value="unlink">Unlink</option>
                </select>

                <input type="submit" class="button action" value="Apply">
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
                foreach ($active as $integration) {
                    $baseurl = esc_url(admin_url('admin.php?page=jumplead_integations&subpage=mapping&'));

                    foreach ($integration->listForms() as $form) {
                        $id = $form['id'];
                        $url = $baseurl . 'integration='. $integration->id .'&form_id=' . $form['id'];
                        $name = $form['name'];

                        $mapping = isset($mappingsLookup[$integration->id][$id]) ? $mappingsLookup[$integration->id][$id] : null;
                        $mappingId = ($mapping) ? $mapping->id : '-1';

                        $alternateClass = ($i%2 == 0) ? 'alternate' : '';
 ?>

                        <tr class="<?php echo $alternateClass; ?>">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="cb-select-<?php echo $id; ?>">
                                    Select <?php echo $name; ?>
                                </label>
                                <input
                                    id="cb-select-<?php echo $id; ?>" type="checkbox" name="forms[]"
                                    value="<?php echo $mappingId; ?>"
                                >
                            </th>
                            <td>
                                <strong>
                                    <a class="row-title" href="<?php echo $url; ?>" title="Edit “<?php echo $name; ?>”">
                                        <?php echo $name; ?>
                                    </a>
                                </strong>

                                <div class="row-actions"></div>
                            </td>
                            <td>
<?php
                                if ($mapping) {
?>
                                <div class="dashicons dashicons-admin-links"></div>
                                Linked
<?php
                                }
?>
                            </td>
                            <td>
                                <?php echo $integration->name; ?>
                            </td>
                        </tr>
<?php
                        $i++;
                    } // foreach $integration
                } // foreach $active
?>
            </tbody>
        </table>

        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
                <select name="bulkaction">
                    <option>Bulk Actions</option>
                    <option value="unlink">Unlink</option>
                </select>

                <input type="submit" class="button action" value="Apply">
            </div>
        </div>
    </form>

<?php
    } // IF

include(JUMPLEAD_PATH_VIEW . 'includes/footer.php');
?>