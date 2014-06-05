<?php
/*
Plugin Name: Jumplead
Plugin URI: http://wordpress.org/extend/plugins/jumplead/
Description: This plugin will allow you to quickly insert the Jumplead website tracking and chat code into your website or blog.
Version: 2.0
Author: Jumplead
Author URI: http://www.jumplead.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
*/

/*  Copyright 2013  Adam Curtis  (email : adam@jumplead.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Admin page
 */
function jumplead_admin() {
	include('jumplead_admin.php');
}

function jumplead_admin_actions() {
	add_options_page("Jumplead", "Jumplead", 1, "Jumplead", "jumplead_admin");
}

add_action('admin_menu', 'jumplead_admin_actions');

/**
 * Tracking Code
 */
function jumplead_tracking_code() {
    $tracker_id = get_option('jumplead_trk_id');
	echo <<<JUMPLEAD

<!-- Start Jumplead Code -->
<script type="text/javascript">
    window.Jumplead||function(b,d){function k(){return["<",l,' onload="var d=',c,";d."+m+"('head')[0].",n,"(d.",p,"('script')).",e,"='",q,"'\"></",l,">"].join("")}var c="document",f=b[c],l="body",p="createElement",m="getElementsByTagName",r=f[m]("head")[0],n="appendChild",a=f[p]("iframe"),e="src",g,q="//cdn.jumplead.com/tracking_code.js";b.jump=b.jump||function(){(b.jump.q=b.jump.q||[]).push(arguments)};d.events=b.jump;a.style.display="none";r[n](a);try{a.contentWindow[c].open()}catch(s){d.domain=f.domain,
    g="javascript:var d="+c+".open();d.domain='"+f.domain+"';",a[e]=g+"void(0);"}try{var h=a.contentWindow[c];h.write(k());h.close();h.params=d}catch(t){a[e]=g+'d.write("'+k().replace(/"/g,'\"')+'");d.close();',a[e].contentDocument.params=d}}
    (window,{account:"{$tracker_id}",version:4});
</script>
<!-- End Jumplead Code -->


JUMPLEAD;
}

// Put it in in <head>
add_action('wp_head', 'jumplead_tracking_code');



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