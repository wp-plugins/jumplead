<?php
// Hooks
add_action('wp_head', 'jumplead_tracking_code');
add_action('wp_footer', 'jumplead_automation_trigger_code');
add_action('wp_enqueue_scripts', 'jumplead_automation_trigger_code');

// Create shortcode [jumplead_form]
add_shortcode('jumplead_form', 'jumplead_embed_form');

/**
 * Echo out the Jumplead Tracking Code
 * Echos the tracking code or just a comment if tracker id is invalid
 *
 * @return void
 */
function jumplead_tracking_code() {
    $tracker_id = get_option('jumplead_tracker_id');

    echo jumplead_comment('Tracking Code');

    if (jumplead_is_tracker_id_valid()) {
	    echo <<<JUMPLEAD
<script type="text/javascript">
    window.Jumplead||function(b,d){function k(){return["<",l,' onload="var d=',c,";d."+m+"('head')[0].",n,"(d.",p,"('script')).",e,"='",q,"'\"></",l,">"].join("")}var c="document",f=b[c],l="body",p="createElement",m="getElementsByTagName",r=f[m]("head")[0],n="appendChild",a=f[p]("iframe"),e="src",g,q="//cdn.jumplead.com/tracking_code.js";b.jump=b.jump||function(){(b.jump.q=b.jump.q||[]).push(arguments)};d.events=b.jump;a.style.display="none";r[n](a);try{a.contentWindow[c].open()}catch(s){d.domain=f.domain,
    g="javascript:var d="+c+".open();d.domain='"+f.domain+"';",a[e]=g+"void(0);"}try{var h=a.contentWindow[c];h.write(k());h.close();h.params=d}catch(t){a[e]=g+'d.write("'+k().replace(/"/g,'\"')+'");d.close();',a[e].contentDocument.params=d}}
    (window,{account:"{$tracker_id}",version:4});
</script>

JUMPLEAD;
    } else {
        echo jumplead_comment('Tracker ID ' . $tracker_id . ' is invalid');
    }
}

/**
 * Queue JavaScript to trigger an automation
 *
 * @return void
 */
function jumplead_automation_trigger_code() {
	wp_enqueue_script('jumplead.js', plugins_url('j/jumplead.js', __FILE__ ), array(), JUMPLEAD_VERSION, true);
}


/**
 * Jumplead Form Short Tag
 *
 * @return void
 */
function jumplead_embed_form($atts) {
    // Ensure ID is set
    if (!isset($atts['id']) || trim($atts['id']) == '') {
	    return 'Jumplead Error: Invalid Form ID';
    }

    $dataStr = 'data-id="' . $atts['id'] . '"';

    // JavaScript callback
    if (isset($atts['callback'])) {
        $dataStr .= ' data-callback="' . $atts['callback'] . '"';
    }

    return '<div class="jlcf" ' . $dataStr . '></div>';
}
