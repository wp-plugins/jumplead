<?php

/**
 * Helpers
 */
function jumplead_is_tracker_id_valid($tracker_id = null) {
    $tracker_id = trim($tracker_id === null ? get_option('jumplead_tracker_id', '') : $tracker_id);

    if ($tracker_id && is_string($tracker_id) && strlen($tracker_id) > 10) {
        return true;
    }

    return false;
}


