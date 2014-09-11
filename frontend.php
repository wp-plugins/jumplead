<?php


function jumplead_comment($text) {
    return '<!-- Jumplead: ' . $text . '; Wordpress Plugin v' . JUMPLEAD_VERSION . ' -->' . PHP_EOL;
}

/**
 * Tracking Code
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

// Put it in in <head>
add_action('wp_head', 'jumplead_tracking_code');

/**
 * Trigger Automation Code
 */
function jumplead_automation_trigger_code() {
    // Send data
    if (!empty(Jumplead::$data)) {
        $nameFirst  = trim(Jumplead::$data['name']);
        $nameLast   = trim(Jumplead::$data['name_last']);

        $name       = json_encode(trim($nameFirst . ' ' . $nameLast));
        $email      = json_encode(Jumplead::$data['email']);
        $company    = json_encode(Jumplead::$data['company']);

        // Populate automation, if there is one
        $automation = 'null';
        if (isset(Jumplead::$data['automation_id'])) {
            $automation_id = Jumplead::$data['automation_id'];
            if (!empty($automation_id)) {
                $automation_id = json_encode(Jumplead::$data['automation_id']);
                if ($automation_id) {
                    $automation = $automation_id;
                }
            }
        }

        echo jumplead_comment('Automation Trigger');
        echo <<<HTML
<script type="text/javascript">
    var contact = {
        name: $name,
        email: $email,
        company: $company
    };
    jump('send', 'automation', 'trigger', $automation, contact);
</script>
HTML;
    }
}

add_action('wp_footer', 'jumplead_automation_trigger_code');


/**
 * Short Tags
 */

// Embed form
function jumplead_embed_form($atts) {
    if (isset($atts['id']) && trim($atts['id']) != '') {
        return '<div class="jlcf" data-id="' . $atts['id'] . '"></div>';
    }

	return 'Jumplead Error: Invalid Form ID';
}
// Create shortcode [jumplead_form]
add_shortcode('jumplead_form', 'jumplead_embed_form');
