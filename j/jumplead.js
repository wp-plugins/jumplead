(function() {
	var JumpleadWP = {
		cookiePoll: function() {
			JumpleadWP.triggerAutomation();
			setTimeout( function() { JumpleadWP.cookiePoll(); }, 1000 );
		},
		triggerAutomation: function() {
			var name = JumpleadWP.readCookie( 'jlwp_name' );
			var name_last = JumpleadWP.readCookie( 'jlwp_name_last' );
			var email = JumpleadWP.readCookie( 'jlwp_email' );
			var company = JumpleadWP.readCookie( 'jlwp_company' );
			var automation_id = JumpleadWP.readCookie( 'jlwp_automation_id' );

			if (typeof email == 'string' && typeof name == 'string') {
				var fullname = JumpleadWP.trim( name );
				if (name_last) {
					fullname += ' ' + JumpleadWP.trim( name_last );
				}

				var contact = {
					name: fullname,
					email: email,
					company: company
				};

				jump( 'send', 'automation', 'trigger', automation_id, contact );
				JumpleadWP.clearFormCookies();
			}
		},
		clearFormCookies: function() {
			JumpleadWP.eraseCookie( 'jlwp_name' );
			JumpleadWP.eraseCookie( 'jlwp_name_last' );
			JumpleadWP.eraseCookie( 'jlwp_email' );
			JumpleadWP.eraseCookie( 'jlwp_company' );
			JumpleadWP.eraseCookie( 'jlwp_automation_id' );
		},
		/**
		 * Read, read & erase cookies
		 */
		createCookie: function (name,value,days) {
			var expires = '';
			if (days) {
				var date = new Date();
				date.setTime( date.getTime() + (days * 24 * 60 * 60 * 1000) );
				var expires = '; expires=' + date.toGMTString();
			}

			document.cookie = name + '=' + escape( value ) + expires + '; path=/';
		},
		readCookie: function(name) {
			var nameEQ = name + '=';
			var ca = document.cookie.split( ';' );
			for (var i = 0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt( 0 ) == ' ') { c = c.substring( 1,c.length ); }
				if (c.indexOf( nameEQ ) == 0) { return unescape( c.substring( nameEQ.length,c.length ) ); }
			}
			return null;
		},
		eraseCookie: function(name) {
			this.createCookie( name,'',-1 );
		},
		/**
		 * Helpers
		 */
		trim: function(str) {
			return typeof str == 'string' ? str.replace( /^\s+|\s+$/g, '' ) : str;
		}
	};

	// Triger an automation
	JumpleadWP.triggerAutomation();

	// Look for CF7 form in the page.
	var forms = document.getElementsByTagName( 'form' );
	for (var i in forms) {
		var className = forms[i].className || null;

		if (className && className.indexOf( 'wpcf7-form' ) > -1) {
			JumpleadWP.cookiePoll();
			break;
		}
	}
})();
