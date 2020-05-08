jQuery('#dismiss-site-notice').on('click', dismiss_notice);

if (document.cookie.replace(/(?:(?:^|.*;\s*)display-site-notice\s*\=\s*([^;]*).*$)|^.*$/, "$1") === "false") {
	jQuery('.site-notice').slideUp();
}

function dismiss_notice(){
	jQuery('.site-notice').hide();
	document.cookie = "display-site-notice=false; expires=Fri, 31 Dec 9999 23:59:59 GMT";
}