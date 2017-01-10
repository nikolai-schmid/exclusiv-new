$(window).load(function () {
	navbar();
});

$(window).resize(function () {
	navbar();
});

function navbar() {
	var togglers = $(".navbar-toggle");
	var navbar = $("#navbar");
	var navLinks = $("#nav-links");
	
	
	if ($(window).width() > 720) {
		navbar.show();
		navLinks.show();
		return;
	}
	
	navbar.hide();
	
	var navLinksUp = true;
	var navUp = true;
	
	$("#nav-toggle").click(function () {
	});

	togglers.click(function () {
		toggle(navbar, navUp);
		navUp = !navUp
	});

}

function toggle (navbar, navUp) {
	if (navUp) {
		navbar.slideDown();
		return;
	}
	navbar.slideUp();
}