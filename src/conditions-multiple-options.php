<?php

add_filter('if_menu_conditions', 'ifMenuAdvancedConditions');

function ifMenuAdvancedConditions($conditions) {
	$activePlugins = apply_filters('active_plugins', get_option('active_plugins'));


	// User location
	$conditions[] = array(
		'id'		=>	'user-location',
		'name'		=>	__('User from country:', 'if-menu'),
		'options'	=>	array(
			'AF' => __( 'Afghanistan', 'if-menu' ),
			'AX' => __( '&#197;land Islands', 'if-menu' ),
			'AL' => __( 'Albania', 'if-menu' ),
			'DZ' => __( 'Algeria', 'if-menu' ),
			'AS' => __( 'American Samoa', 'if-menu' ),
			'AD' => __( 'Andorra', 'if-menu' ),
			'AO' => __( 'Angola', 'if-menu' ),
			'AI' => __( 'Anguilla', 'if-menu' ),
			'AQ' => __( 'Antarctica', 'if-menu' ),
			'AG' => __( 'Antigua and Barbuda', 'if-menu' ),
			'AR' => __( 'Argentina', 'if-menu' ),
			'AM' => __( 'Armenia', 'if-menu' ),
			'AW' => __( 'Aruba', 'if-menu' ),
			'AU' => __( 'Australia', 'if-menu' ),
			'AT' => __( 'Austria', 'if-menu' ),
			'AZ' => __( 'Azerbaijan', 'if-menu' ),
			'BS' => __( 'Bahamas', 'if-menu' ),
			'BH' => __( 'Bahrain', 'if-menu' ),
			'BD' => __( 'Bangladesh', 'if-menu' ),
			'BB' => __( 'Barbados', 'if-menu' ),
			'BY' => __( 'Belarus', 'if-menu' ),
			'BE' => __( 'Belgium', 'if-menu' ),
			'PW' => __( 'Belau', 'if-menu' ),
			'BZ' => __( 'Belize', 'if-menu' ),
			'BJ' => __( 'Benin', 'if-menu' ),
			'BM' => __( 'Bermuda', 'if-menu' ),
			'BT' => __( 'Bhutan', 'if-menu' ),
			'BO' => __( 'Bolivia', 'if-menu' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'if-menu' ),
			'BA' => __( 'Bosnia and Herzegovina', 'if-menu' ),
			'BW' => __( 'Botswana', 'if-menu' ),
			'BV' => __( 'Bouvet Island', 'if-menu' ),
			'BR' => __( 'Brazil', 'if-menu' ),
			'IO' => __( 'British Indian Ocean Territory', 'if-menu' ),
			'VG' => __( 'British Virgin Islands', 'if-menu' ),
			'BN' => __( 'Brunei', 'if-menu' ),
			'BG' => __( 'Bulgaria', 'if-menu' ),
			'BF' => __( 'Burkina Faso', 'if-menu' ),
			'BI' => __( 'Burundi', 'if-menu' ),
			'KH' => __( 'Cambodia', 'if-menu' ),
			'CM' => __( 'Cameroon', 'if-menu' ),
			'CA' => __( 'Canada', 'if-menu' ),
			'CV' => __( 'Cape Verde', 'if-menu' ),
			'KY' => __( 'Cayman Islands', 'if-menu' ),
			'CF' => __( 'Central African Republic', 'if-menu' ),
			'TD' => __( 'Chad', 'if-menu' ),
			'CL' => __( 'Chile', 'if-menu' ),
			'CN' => __( 'China', 'if-menu' ),
			'CX' => __( 'Christmas Island', 'if-menu' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'if-menu' ),
			'CO' => __( 'Colombia', 'if-menu' ),
			'KM' => __( 'Comoros', 'if-menu' ),
			'CG' => __( 'Congo (Brazzaville)', 'if-menu' ),
			'CD' => __( 'Congo (Kinshasa)', 'if-menu' ),
			'CK' => __( 'Cook Islands', 'if-menu' ),
			'CR' => __( 'Costa Rica', 'if-menu' ),
			'HR' => __( 'Croatia', 'if-menu' ),
			'CU' => __( 'Cuba', 'if-menu' ),
			'CW' => __( 'Cura&ccedil;ao', 'if-menu' ),
			'CY' => __( 'Cyprus', 'if-menu' ),
			'CZ' => __( 'Czech Republic', 'if-menu' ),
			'DK' => __( 'Denmark', 'if-menu' ),
			'DJ' => __( 'Djibouti', 'if-menu' ),
			'DM' => __( 'Dominica', 'if-menu' ),
			'DO' => __( 'Dominican Republic', 'if-menu' ),
			'EC' => __( 'Ecuador', 'if-menu' ),
			'EG' => __( 'Egypt', 'if-menu' ),
			'SV' => __( 'El Salvador', 'if-menu' ),
			'GQ' => __( 'Equatorial Guinea', 'if-menu' ),
			'ER' => __( 'Eritrea', 'if-menu' ),
			'EE' => __( 'Estonia', 'if-menu' ),
			'ET' => __( 'Ethiopia', 'if-menu' ),
			'FK' => __( 'Falkland Islands', 'if-menu' ),
			'FO' => __( 'Faroe Islands', 'if-menu' ),
			'FJ' => __( 'Fiji', 'if-menu' ),
			'FI' => __( 'Finland', 'if-menu' ),
			'FR' => __( 'France', 'if-menu' ),
			'GF' => __( 'French Guiana', 'if-menu' ),
			'PF' => __( 'French Polynesia', 'if-menu' ),
			'TF' => __( 'French Southern Territories', 'if-menu' ),
			'GA' => __( 'Gabon', 'if-menu' ),
			'GM' => __( 'Gambia', 'if-menu' ),
			'GE' => __( 'Georgia', 'if-menu' ),
			'DE' => __( 'Germany', 'if-menu' ),
			'GH' => __( 'Ghana', 'if-menu' ),
			'GI' => __( 'Gibraltar', 'if-menu' ),
			'GR' => __( 'Greece', 'if-menu' ),
			'GL' => __( 'Greenland', 'if-menu' ),
			'GD' => __( 'Grenada', 'if-menu' ),
			'GP' => __( 'Guadeloupe', 'if-menu' ),
			'GU' => __( 'Guam', 'if-menu' ),
			'GT' => __( 'Guatemala', 'if-menu' ),
			'GG' => __( 'Guernsey', 'if-menu' ),
			'GN' => __( 'Guinea', 'if-menu' ),
			'GW' => __( 'Guinea-Bissau', 'if-menu' ),
			'GY' => __( 'Guyana', 'if-menu' ),
			'HT' => __( 'Haiti', 'if-menu' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'if-menu' ),
			'HN' => __( 'Honduras', 'if-menu' ),
			'HK' => __( 'Hong Kong', 'if-menu' ),
			'HU' => __( 'Hungary', 'if-menu' ),
			'IS' => __( 'Iceland', 'if-menu' ),
			'IN' => __( 'India', 'if-menu' ),
			'ID' => __( 'Indonesia', 'if-menu' ),
			'IR' => __( 'Iran', 'if-menu' ),
			'IQ' => __( 'Iraq', 'if-menu' ),
			'IE' => __( 'Ireland', 'if-menu' ),
			'IM' => __( 'Isle of Man', 'if-menu' ),
			'IL' => __( 'Israel', 'if-menu' ),
			'IT' => __( 'Italy', 'if-menu' ),
			'CI' => __( 'Ivory Coast', 'if-menu' ),
			'JM' => __( 'Jamaica', 'if-menu' ),
			'JP' => __( 'Japan', 'if-menu' ),
			'JE' => __( 'Jersey', 'if-menu' ),
			'JO' => __( 'Jordan', 'if-menu' ),
			'KZ' => __( 'Kazakhstan', 'if-menu' ),
			'KE' => __( 'Kenya', 'if-menu' ),
			'KI' => __( 'Kiribati', 'if-menu' ),
			'KW' => __( 'Kuwait', 'if-menu' ),
			'KG' => __( 'Kyrgyzstan', 'if-menu' ),
			'LA' => __( 'Laos', 'if-menu' ),
			'LV' => __( 'Latvia', 'if-menu' ),
			'LB' => __( 'Lebanon', 'if-menu' ),
			'LS' => __( 'Lesotho', 'if-menu' ),
			'LR' => __( 'Liberia', 'if-menu' ),
			'LY' => __( 'Libya', 'if-menu' ),
			'LI' => __( 'Liechtenstein', 'if-menu' ),
			'LT' => __( 'Lithuania', 'if-menu' ),
			'LU' => __( 'Luxembourg', 'if-menu' ),
			'MO' => __( 'Macao S.A.R., China', 'if-menu' ),
			'MK' => __( 'Macedonia', 'if-menu' ),
			'MG' => __( 'Madagascar', 'if-menu' ),
			'MW' => __( 'Malawi', 'if-menu' ),
			'MY' => __( 'Malaysia', 'if-menu' ),
			'MV' => __( 'Maldives', 'if-menu' ),
			'ML' => __( 'Mali', 'if-menu' ),
			'MT' => __( 'Malta', 'if-menu' ),
			'MH' => __( 'Marshall Islands', 'if-menu' ),
			'MQ' => __( 'Martinique', 'if-menu' ),
			'MR' => __( 'Mauritania', 'if-menu' ),
			'MU' => __( 'Mauritius', 'if-menu' ),
			'YT' => __( 'Mayotte', 'if-menu' ),
			'MX' => __( 'Mexico', 'if-menu' ),
			'FM' => __( 'Micronesia', 'if-menu' ),
			'MD' => __( 'Moldova', 'if-menu' ),
			'MC' => __( 'Monaco', 'if-menu' ),
			'MN' => __( 'Mongolia', 'if-menu' ),
			'ME' => __( 'Montenegro', 'if-menu' ),
			'MS' => __( 'Montserrat', 'if-menu' ),
			'MA' => __( 'Morocco', 'if-menu' ),
			'MZ' => __( 'Mozambique', 'if-menu' ),
			'MM' => __( 'Myanmar', 'if-menu' ),
			'NA' => __( 'Namibia', 'if-menu' ),
			'NR' => __( 'Nauru', 'if-menu' ),
			'NP' => __( 'Nepal', 'if-menu' ),
			'NL' => __( 'Netherlands', 'if-menu' ),
			'NC' => __( 'New Caledonia', 'if-menu' ),
			'NZ' => __( 'New Zealand', 'if-menu' ),
			'NI' => __( 'Nicaragua', 'if-menu' ),
			'NE' => __( 'Niger', 'if-menu' ),
			'NG' => __( 'Nigeria', 'if-menu' ),
			'NU' => __( 'Niue', 'if-menu' ),
			'NF' => __( 'Norfolk Island', 'if-menu' ),
			'MP' => __( 'Northern Mariana Islands', 'if-menu' ),
			'KP' => __( 'North Korea', 'if-menu' ),
			'NO' => __( 'Norway', 'if-menu' ),
			'OM' => __( 'Oman', 'if-menu' ),
			'PK' => __( 'Pakistan', 'if-menu' ),
			'PS' => __( 'Palestinian Territory', 'if-menu' ),
			'PA' => __( 'Panama', 'if-menu' ),
			'PG' => __( 'Papua New Guinea', 'if-menu' ),
			'PY' => __( 'Paraguay', 'if-menu' ),
			'PE' => __( 'Peru', 'if-menu' ),
			'PH' => __( 'Philippines', 'if-menu' ),
			'PN' => __( 'Pitcairn', 'if-menu' ),
			'PL' => __( 'Poland', 'if-menu' ),
			'PT' => __( 'Portugal', 'if-menu' ),
			'PR' => __( 'Puerto Rico', 'if-menu' ),
			'QA' => __( 'Qatar', 'if-menu' ),
			'RE' => __( 'Reunion', 'if-menu' ),
			'RO' => __( 'Romania', 'if-menu' ),
			'RU' => __( 'Russia', 'if-menu' ),
			'RW' => __( 'Rwanda', 'if-menu' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'if-menu' ),
			'SH' => __( 'Saint Helena', 'if-menu' ),
			'KN' => __( 'Saint Kitts and Nevis', 'if-menu' ),
			'LC' => __( 'Saint Lucia', 'if-menu' ),
			'MF' => __( 'Saint Martin (French part)', 'if-menu' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'if-menu' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'if-menu' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'if-menu' ),
			'SM' => __( 'San Marino', 'if-menu' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'if-menu' ),
			'SA' => __( 'Saudi Arabia', 'if-menu' ),
			'SN' => __( 'Senegal', 'if-menu' ),
			'RS' => __( 'Serbia', 'if-menu' ),
			'SC' => __( 'Seychelles', 'if-menu' ),
			'SL' => __( 'Sierra Leone', 'if-menu' ),
			'SG' => __( 'Singapore', 'if-menu' ),
			'SK' => __( 'Slovakia', 'if-menu' ),
			'SI' => __( 'Slovenia', 'if-menu' ),
			'SB' => __( 'Solomon Islands', 'if-menu' ),
			'SO' => __( 'Somalia', 'if-menu' ),
			'ZA' => __( 'South Africa', 'if-menu' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'if-menu' ),
			'KR' => __( 'South Korea', 'if-menu' ),
			'SS' => __( 'South Sudan', 'if-menu' ),
			'ES' => __( 'Spain', 'if-menu' ),
			'LK' => __( 'Sri Lanka', 'if-menu' ),
			'SD' => __( 'Sudan', 'if-menu' ),
			'SR' => __( 'Suriname', 'if-menu' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'if-menu' ),
			'SZ' => __( 'Swaziland', 'if-menu' ),
			'SE' => __( 'Sweden', 'if-menu' ),
			'CH' => __( 'Switzerland', 'if-menu' ),
			'SY' => __( 'Syria', 'if-menu' ),
			'TW' => __( 'Taiwan', 'if-menu' ),
			'TJ' => __( 'Tajikistan', 'if-menu' ),
			'TZ' => __( 'Tanzania', 'if-menu' ),
			'TH' => __( 'Thailand', 'if-menu' ),
			'TL' => __( 'Timor-Leste', 'if-menu' ),
			'TG' => __( 'Togo', 'if-menu' ),
			'TK' => __( 'Tokelau', 'if-menu' ),
			'TO' => __( 'Tonga', 'if-menu' ),
			'TT' => __( 'Trinidad and Tobago', 'if-menu' ),
			'TN' => __( 'Tunisia', 'if-menu' ),
			'TR' => __( 'Turkey', 'if-menu' ),
			'TM' => __( 'Turkmenistan', 'if-menu' ),
			'TC' => __( 'Turks and Caicos Islands', 'if-menu' ),
			'TV' => __( 'Tuvalu', 'if-menu' ),
			'UG' => __( 'Uganda', 'if-menu' ),
			'UA' => __( 'Ukraine', 'if-menu' ),
			'AE' => __( 'United Arab Emirates', 'if-menu' ),
			'GB' => __( 'United Kingdom (UK)', 'if-menu' ),
			'US' => __( 'United States (US)', 'if-menu' ),
			'UM' => __( 'United States (US) Minor Outlying Islands', 'if-menu' ),
			'VI' => __( 'United States (US) Virgin Islands', 'if-menu' ),
			'UY' => __( 'Uruguay', 'if-menu' ),
			'UZ' => __( 'Uzbekistan', 'if-menu' ),
			'VU' => __( 'Vanuatu', 'if-menu' ),
			'VA' => __( 'Vatican', 'if-menu' ),
			'VE' => __( 'Venezuela', 'if-menu' ),
			'VN' => __( 'Vietnam', 'if-menu' ),
			'WF' => __( 'Wallis and Futuna', 'if-menu' ),
			'EH' => __( 'Western Sahara', 'if-menu' ),
			'WS' => __( 'Samoa', 'if-menu' ),
			'YE' => __( 'Yemen', 'if-menu' ),
			'ZM' => __( 'Zambia', 'if-menu' ),
			'ZW' => __( 'Zimbabwe', 'if-menu' ),
		),
		'condition'	=>	function($item, $selectedOptions = array()) {
			return in_array(If_Menu::getUserCountryCode(), $selectedOptions);
		},
		'group'		=>	__('User', 'if-menu')
	);


	// Third-party plugin integration - Groups
	if (in_array('groups/groups.php', $activePlugins) && class_exists('Groups_Group')) {
		$groupOptions = array();
		foreach (Groups_Group::get_groups() as $group) {
			$groupOptions[$group->group_id] = $group->name;
		}

		$conditions[] = array(
			'id'		=>	'user-in-group',
			'name'		=>	__('User is in group:', 'if-menu'),
			'condition'	=>	function($item, $selectedGroups = array()) {
				$isInGroup = false;
				$groupsUser = new Groups_User(get_current_user_id());
				foreach ($selectedGroups as $groupId) {
					if ($groupsUser->is_member($groupId)) {
						$isInGroup = true;
					}
				}
				return $isInGroup;
			},
			'options'	=>	$groupOptions,
			'group'		=>	__('User', 'if-menu')
		);
	}


	// Third-party plugin integration - WooCommerce Subscriptions
	if (in_array('woocommerce-subscriptions/woocommerce-subscriptions.php', $activePlugins)) {
		$subscriptionsOptions = array();

		$subscriptions = get_posts(array(
			'numberposts'	=>	-1,
			'post_type'		=>	array('product', 'product-variation'),
			'post_status'	=>	'publish',
			'tax_query'		=>	array(array(
				'taxonomy'		=>	'product_type',
				'field'			=>	'slug',
				'terms'			=>	array('subscription', 'variable-subscription')
			))
		));

		foreach ($subscriptions as $subscription) {
			$subscriptionsOptions[$subscription->ID] = $subscription->post_title;
		}

		$conditions[] = array(
			'id'		=>	'woocommerce-subscriptions',
			'name'		=>	__('Has active subscription:', 'if-menu'),
			'condition'	=>	function($item, $selectedSubscriptions = array()) {
				$hasSubscription = false;

				foreach ($selectedSubscriptions as $subscriptionId) {
					if (wcs_user_has_subscription(0, $subscriptionId, 'active')) {
						$hasSubscription = true;
					}
				}

				return $hasSubscription;
			},
			'options'	=>	$subscriptionsOptions,
			'group'		=>	__('User', 'if-menu')
		);
	}


	// Third-party plugin integration - WishList Member
	if (function_exists('wlmapi_the_levels')) {
		$membershipLevelOptions = array();
		$wishlistMembershipLevels = wlmapi_the_levels();

		foreach ($wishlistMembershipLevels['levels']['level'] as $level) {
			$membershipLevelOptions[$level['id']] = $level['name'];
		}

		$conditions[] = array(
			'id'		=>	'wishlist-member',
			'name'		=>	__('WishList Membership Level', 'if-menu'),
			'condition'	=>	function($item, $membershipLevels = array()) {
				$hasAccess = false;
				$userId = get_current_user_id();

				foreach ($membershipLevels as $level) {
					if (wlmapi_is_user_a_member($level, $userId)) {
						$hasAccess = true;
					}
				}

				return $hasAccess;
			},
			'group'		=>	__('User', 'if-menu')
		);
	}


	return $conditions;
}
