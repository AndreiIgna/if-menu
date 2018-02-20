<?php


// Get visitor's IP

if (!function_exists('get_user_ip')) {
	function get_user_ip() {
		return apply_filters('user_ip', '');
	}
}

add_filter('user_ip', 'if_menu_user_ip');

function if_menu_user_ip($ip = '') {
	if (empty($ip)) {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
						return $ip;
					}
				}
			}
		}
	}

	return $ip;
}



// Get visitor's Country Code, ex: US, ES, etc     /    XX or empty = Unknown

if (!function_exists('get_user_country_code')) {
	function get_user_country_code() {
		return strtoupper(apply_filters('user_country_code', ''));
	}
}

add_filter('user_country_code', 'if_menu_user_country_code_woocommerce');
add_filter('user_country_code', 'if_menu_user_country_code_cloudflare');
add_filter('user_country_code', 'if_menu_user_country_code_appengine');
add_filter('user_country_code', 'if_menu_user_country_code_cloudfront');
add_filter('user_country_code', 'if_menu_user_country_code_geoip');
add_filter('user_country_code', 'if_menu_user_country_code_blueapis');

function if_menu_user_country_code_woocommerce($countryCode = '') {
	if (!$countryCode && class_exists('WC_Geolocation')) {
		$location = WC_Geolocation::geolocate_ip();
		if ($location['country'] && !in_array($location['country'], array('A1', 'A2', 'EU', 'AP'))) {
			$countryCode = $location['country'];
		}
	}

	return $countryCode;
}

function if_menu_user_country_code_cloudflare($countryCode = '') {
	if (!$countryCode && isset($_SERVER['HTTP_CF_IPCOUNTRY']) && $_SERVER['HTTP_CF_IPCOUNTRY'] && $_SERVER['HTTP_CF_IPCOUNTRY'] !== 'XX') {
		$countryCode = $_SERVER['HTTP_CF_IPCOUNTRY'];
	}

	return $countryCode;
}

function if_menu_user_country_code_appengine($countryCode = '') {
	if (!$countryCode && isset($_SERVER['X-AppEngine-country']) && $_SERVER['X-AppEngine-country'] && $_SERVER['X-AppEngine-country'] !== 'ZZ') {
		$countryCode = $_SERVER['X-AppEngine-country'];
	}

	return $countryCode;
}

function if_menu_user_country_code_cloudfront($countryCode = '') {
	if (!$countryCode && isset($_SERVER['CloudFront-Viewer-Country']) && $_SERVER['CloudFront-Viewer-Country']) {
		$countryCode = $_SERVER['CloudFront-Viewer-Country'];
	}

	return $countryCode;
}

function if_menu_user_country_code_geoip($countryCode = '') {
	if (!$countryCode && isset($_SERVER['GEOIP_COUNTRY_CODE']) && $_SERVER['GEOIP_COUNTRY_CODE'] && !in_array($_SERVER['GEOIP_COUNTRY_CODE'], array('A1', 'A2', 'EU', 'AP'))) {
		$countryCode = $_SERVER['GEOIP_COUNTRY_CODE'];
	}

	if (!$countryCode && isset($_SERVER['HTTP_X_COUNTRY_CODE']) && $_SERVER['HTTP_X_COUNTRY_CODE']) {
		$countryCode = $_SERVER['HTTP_X_COUNTRY_CODE'];
	}

	return $countryCode;
}

function if_menu_user_country_code_blueapis($countryCode = '') {
	if (!$countryCode) {
		$ip = get_user_ip();

		if (false === ($countryCode = get_transient('ip-country-code-' . sanitize_key($ip)))) {
			$request = wp_remote_get('https://apis.blue/ip/' . $ip . '?key=layered-if-menu');
			$data = json_decode(wp_remote_retrieve_body($request) ?: '[]');
			if (isset($data->country) && $data->country) {
				$countryCode = $data->country;
				set_transient('ip-country-code-' . sanitize_key($ip), $countryCode, WEEK_IN_SECONDS);
			} else {
				$countryCode = '';
			}
		}
	}

	return $countryCode;
}
