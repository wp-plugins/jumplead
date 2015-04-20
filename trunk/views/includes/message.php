<?php
if ( isset($errors) && ! empty($errors) ) {
?>
    <div class="error">
        <p>
            <strong>
    <?php
	foreach ( $errors as $error ) {
		echo esc_html( $error ) . ' <br />';
	}
	?>
            </strong>
        </p>
    </div>
<?php
}


if ( isset($info) && ! empty($info) ) {
?>
    <div class="updated">
        <p>
            <strong>
    <?php
	foreach ( $info as $i ) {
		echo esc_html( $i ) . ' <br />';
	}
	?>
            </strong>
        </p>
    </div>
<?php
}
?>