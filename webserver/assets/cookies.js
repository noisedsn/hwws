var setCookie = function(name, value, expiry_days, prefix='ws_') {
	var cookie_name = prefix + name;
	var d = new Date();
	d.setTime(d.getTime() + (expiry_days*24*60*60*1000));
	var expires = "expires=" + d.toUTCString();
	document.cookie = cookie_name + "=" + value + ";" + expires + ";path=/";
	return getCookie(cookie_name);
};

var getCookie = function(name, prefix='ws_') {
	var cookie_name = prefix + name + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(cookie_name) === 0) {
			return c.substring(cookie_name.length, c.length);
		}
	}
	return false;
};

