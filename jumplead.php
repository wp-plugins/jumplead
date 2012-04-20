<?php
/*
Plugin Name: Jumplead
Plugin URI: http://wordpress.org/extend/plugins/jumplead/
Description: This plugin will allow you to quickly insert the Jumplead website tracking and chat code into your website or blog.
Version: 1.1
Author: Jumplead
Author URI: http://www.jumplead.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
*/

/*  Copyright 2012  Adam Curtis  (email : adam@jumplead.com)

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


//Admin page
function jumplead_admin() {
	include('jumplead_admin.php');
}

function jumplead_admin_actions() {
	add_options_page("Jumplead", "Jumplead", 1, "Jumplead", "jumplead_admin");
}

add_action('admin_menu', 'jumplead_admin_actions');


//Insert into footer
function jumplead_footer() {
    $tracker_id = get_option('jumplead_trk_id');
	echo <<< JUMPLEAD
<!-- Start Jumplead Code -->
<script type="text/javascript">
    try {
        window.Jumplead||function(e){function j(){return["<",f,' onload="var d=',c,";d.getElementsByTagName('head')[0].",g,"(d.",d,"('script')).",h,"='",m,"?v=",e.version,"&a=",e.account,"'\"></",f,">"].join("")}var b=document,f="body",k=b[f],d="createElement",g="appendChild",n=b[d]("div")[g](b[d]("m")),a=b[d]("iframe"),h="src",i,c="document",m=("https:"==b.location.protocol?"https://":"http://")+"www.jumplead.com/jumplead.js";a.style.display="none";a.frameBorder="0";a.id="jl-if";a.allowTransparency="true";
        n[g](a);k.insertBefore(a,k.firstChild);try{a.contentWindow[c].open()}catch(o){e.domain=b.domain,i="javascript:var d="+c+".open();d.domain='"+b.domain+"';",a[h]=i+"void(0);"}try{var l=a.contentWindow[c];l.write(j());l.close()}catch(p){a[h]=i+'d.write("'+j().replace(/"/g,'\"')+'");d.close();'}}
        ({account:"{$tracker_id}",version:1});
    } catch(e) {}
</script>
<!-- End Jumplead Code -->
JUMPLEAD;
}
add_action('wp_footer', 'jumplead_footer');