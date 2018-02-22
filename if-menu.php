<?php
/*
Plugin Name: If Menu
Plugin URI: https://wordpress.org/plugins/if-menu/
Description: Display tailored menu items to each visitor with visibility rules
Version: 0.8.3
Text Domain: if-menu
Author: Layered
Author URI: https://layered.studio
License: GPL2
*/

/*  Copyright 2012 Andrei Igna (email: andrei@rokm.ro)

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


class If_Menu {

	public static function init() {
		global $pagenow;

		load_plugin_textdomain('if-menu', false, dirname(plugin_basename(__FILE__)) . '/languages');

		add_action('rest_api_init', 'If_Menu::restApi');

		if (is_admin()) {
			add_action('admin_enqueue_scripts', 'If_Menu::admin_init');
			add_action('wp_update_nav_menu_item', 'If_Menu::wp_update_nav_menu_item', 10, 2);
			add_filter('wp_edit_nav_menu_walker', 'If_Menu::customWalker', 500);
			add_action('wp_nav_menu_item_custom_fields', 'If_Menu::menu_item_fields');
			add_action('wp_nav_menu_item_custom_title', 'If_Menu::menu_item_title');
			add_action('init', 'If_Menu::saveSettings');
			add_action('admin_footer', 'If_Menu::adminFooter');
			add_action('admin_menu', function() {
				add_submenu_page('themes.php', 'If Menu', 'If Menu', 'edit_theme_options', 'if-menu', 'If_Menu::page');
			});

			if ($pagenow !== 'nav-menus.php') {
				add_filter( 'wp_get_nav_menu_items', 'If_Menu::wp_get_nav_menu_items' );
			}
		} else {
			add_filter( 'wp_get_nav_menu_items', 'If_Menu::wp_get_nav_menu_items' );
			add_action('wp_enqueue_scripts', 'If_Menu::addAssets');
		}
	}

	public static function get_conditions( $for_testing = false ) {
		$conditions = apply_filters( 'if_menu_conditions', array() );

		if ($for_testing) {
			$c2 = array();
			foreach ($conditions as $condition) {
				$c2[$condition['id']] = $condition;
				$c2[$condition['name']] = $condition;
				if (isset($condition['alias'])) {
					$c2[$condition['alias']] = $condition;
				}
			}
			$conditions = $c2;
		}

		return $conditions;
	}

	public static function wp_get_nav_menu_items($items) {
		$conditions = If_Menu::get_conditions($for_testing = true);
		$hidden_items = array();

		$canPeek = is_user_logged_in() && get_option('if-menu-peak') && current_user_can('edit_theme_options');

		foreach ($items as $key => $item) {

			if (in_array($item->menu_item_parent, $hidden_items)) {
				if ($canPeek) {
					$item->classes[] = 'if-menu-peek';
				} else {
					unset($items[$key]);
				}
				$hidden_items[] = $item->ID;
			} else {
				$enabled = get_post_meta($item->ID, 'if_menu_enable');

				if ($enabled && $enabled[0] !== '0') {
					$if_condition_types = get_post_meta($item->ID, 'if_menu_condition_type');
					$if_conditions = get_post_meta($item->ID, 'if_menu_condition');
					$ifMenuOptions = get_post_meta($item->ID, 'if_menu_options');

					$eval = array();

					foreach ($enabled as $index => $operator) {
						$singleCondition = '';

						if ($index) {
							$singleCondition .= $operator . ' ';
						}

						$bit1 = $if_condition_types[$index] === 'show' ? 1 : 0;
						$bit2 = $if_condition_types[$index] === 'show' ? 0 : 1;

						$params = array($item);

						if ($ifMenuOptions && isset($ifMenuOptions[$index])) {
							$params[] = $ifMenuOptions[$index];
						}

						$singleCondition .= call_user_func_array($conditions[$if_conditions[$index]]['condition'], $params) ? $bit1 : $bit2;

						$eval[] = $singleCondition;
					}

					if ((count($eval) === 1 && $eval[0] == 0) || (count($eval) > 1 && !eval('return ' . implode(' ', $eval) . ';'))) {
						if ($canPeek) {
								$item->classes[] = 'if-menu-peek';
							} else {
								unset($items[$key]);
							}
						$hidden_items[] = $item->ID;
					}
				}
			}
		}

		return $items;
	}

	public static function admin_init() {
		global $pagenow;

		if ($pagenow == 'nav-menus.php') {
			wp_enqueue_script('select2', plugins_url('assets/select2.min.js', __FILE__), array('jquery'), '4.0.5');
			wp_enqueue_script('if-menu', plugins_url('assets/if-menu.js', __FILE__), array('select2', 'jquery-ui-dialog'), '0.8');

			wp_enqueue_style('select2', plugins_url('assets/select2.min.css', __FILE__), array(), '4.0.5');
			wp_enqueue_style('if-menu', plugins_url('assets/if-menu.css', __FILE__), array('wp-jquery-ui-dialog'), '0.8');

			wp_localize_script('if-menu', 'IfMenu', array(
				'plan'					=>	self::getPlan(),
				'conflictErrorMessage'  =>  sprintf(
					wp_kses(
						__('<strong>If Menu</strong> detected a conflict with another plugin or theme (%s) and may not work as expected. <a href="%s" target="_blank">Read more about the issue here</a>', 'if-menu'),
						array('a' => array('href' => array()), 'strong' => array())
					),
					apply_filters('wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit'),
					esc_url('https://wordpress.org/plugins/if-menu/faq/')
				)
			));
		}

		if ($pagenow == 'themes.php') {
			wp_enqueue_style('if-menu', plugins_url('assets/if-menu.css', __FILE__), '0.8');
		}
	}

	public static function saveSettings() {
		global $pagenow;

		if ($pagenow == 'themes.php' && isset($_POST['if-menu-settings'])) {
			update_option('if-menu-peak', $_POST['if-menu-peek']);
		}
	}

	public static function adminFooter() {
		?>
		<div class="if-menu-dialog-premium hidden" title="<?php _e('That\'s a Premium feature', 'if-menu') ?>">
			<p><?php _e('Get <strong>If Menu Premium</strong> plan to enable integrations with third-party plugins, user location detection and priority support', 'if-menu') ?></p><br>
			<p>
				<a href="<?php echo admin_url('themes.php?page=if-menu') ?>" class="button button-primary pull-right if-menu-dialog-btn" data-action="get-premium"><?php _e('Get If Menu Premium', 'if-menu') ?></a>
				<button class="button close if-menu-dialog-btn" data-action="close"><?php _e('Use Basic plan', 'if-menu') ?></button>
			</p>
		</div>
		<?php
	}

	public static function page() {
		$ifMenuPeek = get_option('if-menu-peak');
		$plan = self::getPlan();
		?>

		<div class="wrap about-wrap if-menu-wrap">
			<h1>If Menu</h1>
			<p class="about-text"><?php _e('Thanks for using <strong>If Menu</strong>! Now you can display tailored menu items to each visitor, based on visibility rules. Here are a few examples:', 'if-menu') ?></p>
			<ul class="list">
				<li><?php _e('Hide Login or Register links for logged-in users:', 'if-menu') ?> <code><span class="if-menu-red"><?php _e('Hide', 'if-menu') ?></span> <?php _e('if', 'if-menu') ?> <span class="if-menu-purple"><?php _e('User is logged in', 'if-menu') ?></span></code></li>
				<li><?php _e('Display Logout link for logged-in users:', 'if-menu') ?> <code><span class="if-menu-green"><?php _e('Show', 'if-menu') ?></span> <?php _e('if', 'if-menu') ?> <span class="if-menu-purple"><?php _e('User is logged in', 'if-menu') ?></span></code></li>
				<li><?php _e('Hide menu item on mobile devices:', 'if-menu') ?> <code><span class="if-menu-red"><?php _e('Hide', 'if-menu') ?></span> <?php _e('if', 'if-menu') ?> <span class="if-menu-purple"><?php _e('Mobile', 'if-menu') ?></span></code></li>
				<li><?php _e('Display menu item for users in US and UK:', 'if-menu') ?> <code><span class="if-menu-green"><?php _e('Show', 'if-menu') ?></span> <?php _e('if', 'if-menu') ?> <span class="if-menu-purple"><?php _e('User from country: US, UK', 'if-menu') ?></span></code></li>
			</ul>
			<hr class="wp-header-end">

			<div class="feature-section pricing-plan-section two-col">
				<div class="col">
					<div class="pricing-cell <?php if (!$plan || $plan['plan'] == 'free') echo 'selected' ?>">
						<span class="price"><small><?php _e('Free', 'if-menu') ?></small></span>
						<h3><?php _e('Basic', 'if-menu') ?></h3>
						
						<ul>
							<li>
								<?php _e('Basic visibility rules:', 'if-menu') ?>
								<ul>
									<li><?php _e('User role - is Admin, Editor, Author or Shop Manager', 'if-menu') ?></li>
									<li><?php _e('User state - visitor is logged in or out', 'if-menu') ?></li>
									<li><?php _e('Visitor device - detect mobile or desktop', 'if-menu') ?></li>
								</ul>
							</li>
							<li><?php _e('Support on WordPress forum', 'if-menu') ?></li>
						</ul>
						
						<p>
							<?php if (!$plan || $plan['plan'] == 'free') : ?>
								<button class="button disabled"><?php _e('Current plan', 'if-menu') ?></button>
							<?php endif ?>
						</p>
					</div>
				</div>

				<div class="col">
					<div class="pricing-cell <?php if ($plan && $plan['plan'] == 'premium') echo 'selected' ?>">
						<span class="price">$15<small>/<?php _e('annually', 'if-menu') ?></small></span>
						<h3><?php _e('Premium', 'if-menu') ?></h3>
						
						<ul>
							<li>
								<?php _e('Advanced visibility rules:', 'if-menu') ?>
								<ul>
									<li><?php _e('Visitor location - detect visitor\'s country', 'if-menu') ?></li>
								</ul>
							</li>
							<li>
								<?php _e('3rd-party plugin integrations:', 'if-menu') ?>
								<ul>
									<li><a href="https://woocommerce.com/products/woocommerce-subscriptions" target="_blank">WooCommerce Subscriptions</a> - <?php _e('Customer has active subscription', 'if-menu') ?></li>
									<li><a href="https://wordpress.org/plugins/groups" target="_blank">Groups</a> - <?php _e('Users are in a Group', 'if-menu') ?></li>
									<li><a href="https://member.wishlistproducts.com" target="_blank">WishList Member</a> - <?php _e('Users above a Membership Level', 'if-menu') ?></li>
								</ul>
							</li>
							<li><?php _e('Priority support', 'if-menu') ?></li>
						</ul>

						<p class="description">
							<?php if ($plan && $plan['plan'] == 'premium') : ?>
								<button class="button disabled"><?php _e('Current plan', 'if-menu') ?></button>
								<br><br><small class="text-muted"><?php printf(__('Active until %s', 'if-menu'), date(get_option('date_format'), strtotime($plan['until']))) ?></small>
							<?php else : ?>
								<a href="https://wordpress.layered.studio/get-premium?site=<?php echo urlencode(site_url()) ?>&_nonce=<?php echo self::apiNonce('get-premium') ?>" class="button button-primary"><?php _e('Get premium', 'if-menu') ?></a>
							<?php endif ?>
						</p>
					</div>
				</div>
			</div>

			<hr>

			<h3 class="title"><?php _e('Settings', 'if-menu') ?></h3>

			<form method="post" action="">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e('If Menu peek', 'if-menu') ?></th>
							<td>
								<fieldset>
									<label><input type="checkbox" name="if-menu-peek" value="1" <?php checked($ifMenuPeek, 1) ?>> <?php _e('Enable If Menu peek', 'if-menu') ?></label><br>
								</fieldset>
								<p class="description"><?php _e('Let administrators preview hidden menu items on website (useful for testing)', 'if-menu') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"></th>
							<td>
								<p class="submit"><input type="submit" name="if-menu-settings" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'if-menu') ?>"></p>
							</td>
						</tr>
					</tbody>
				</table>
			</form>

			<hr>

			<p class="text-right">
				<strong>If Menu</strong>:
				<a href="https://wordpress.org/plugins/if-menu/#faq" target="wpplugins"><?php _e('FAQs', 'if-menu') ?></a> &middot;
				<a href="https://wordpress.org/plugins/if-menu/#reviews" target="wpplugins"><?php _e('Reviews', 'if-menu') ?></a> &middot;
				<a href="https://wordpress.org/support/plugin/if-menu" target="wpplugins"><?php _e('Support', 'if-menu') ?></a>
			</p>

		</div>

		<?php
	}

	public static function addAssets() {
		wp_enqueue_style('if-menu-site-css', plugins_url('assets/if-menu-site.css', __FILE__));
	}

	public static function menu_item_fields($item_id) {
		$conditions = If_Menu::get_conditions();
		$if_menu_enable = get_post_meta( $item_id, 'if_menu_enable' );
		$if_menu_condition_type = get_post_meta( $item_id, 'if_menu_condition_type' );
		$if_menu_condition = get_post_meta( $item_id, 'if_menu_condition' );
		$ifMenuOptions = get_post_meta($item_id, 'if_menu_options');

		if (!count($if_menu_enable)) {
			$if_menu_enable[] = 0;
			$if_menu_condition_type[] = '';
			$if_menu_condition[] = '';
		}

		$groupedConditions = array();
		foreach ($conditions as $condition) {
			$groupedConditions[isset($condition['group']) ? $condition['group'] : 'Custom'][] = $condition;
		}
		?>

		<p class="if-menu-enable description description-wide">
			<a href="<?php echo admin_url('themes.php?page=if-menu') ?>" class="if-menu-help" title="<?php esc_attr_e('Visibility rule examples', 'if-menu') ?>"><span class="dashicons dashicons-editor-help"></span></a>
			<label>
				<input <?php if (isset($if_menu_enable[0])) checked( $if_menu_enable[0], 1 ) ?> type="checkbox" value="1" class="menu-item-if-menu-enable" name="menu-item-if-menu-enable[<?php echo esc_attr( $item_id ); ?>][]" />
				<?php esc_html_e( 'Change menu item visibility', 'if-menu' ) ?>
			</label>
		</p>

		<div class="if-menu-conditions" style="display: <?php echo $if_menu_enable[0] ? 'block' : 'none' ?>">
			<?php for ($index = 0; $index < count($if_menu_enable); $index++) : ?>

				<p class="if-menu-condition description description-wide" data-menu-item-id="<?php echo $item_id ?>">
					<?php
					$selectedCondition = null;
					?>
					<span class="if-menu-condition-rule">
						<select class="menu-item-if-menu-condition-type" id="edit-menu-item-if-menu-condition-type-<?php echo esc_attr( $item_id ); ?>" name="menu-item-if-menu-condition-type[<?php echo esc_html( $item_id ); ?>][]" data-val="<?php echo esc_html($if_menu_condition_type[$index]) ?>">
							<option <?php selected( 'show', $if_menu_condition_type[$index] ) ?> value="show"><?php esc_html_e( 'Show', 'if-menu' ) ?></option>
							<option <?php selected( 'hide', $if_menu_condition_type[$index] ) ?> value="hide"><?php esc_html_e( 'Hide', 'if-menu' ) ?></option>
						</select>
						<?php esc_html_e( 'if', 'if-menu' ); ?>
						<select class="menu-item-if-menu-condition" id="edit-menu-item-if-menu-condition-<?php echo esc_attr( $item_id ); ?>" name="menu-item-if-menu-condition[<?php echo esc_attr( $item_id ); ?>][]">
							<?php foreach ($groupedConditions as $group => $conditions) : ?>
								<optgroup label="<?php echo esc_attr($group) ?>">
									<?php foreach($conditions as $condition) : ?>
										<?php
										if ($condition['id'] === $if_menu_condition[$index]) {
											$selectedCondition = $condition;
										}
										?>
										<option value="<?php echo $condition['id'] ?>" <?php selected($condition['id'], $if_menu_condition[$index]) ?> <?php selected($condition['name'], $if_menu_condition[$index]) ?> data-options='<?php if (isset($condition['options'])) echo json_encode($condition['options']) ?>'><?php echo esc_html($condition['name']) . (isset($condition['options']) ? ' &raquo;' : '') ?></option>
									<?php endforeach ?>
								</optgroup>
							<?php endforeach ?>
						</select>
					</span>
					<select class="menu-item-if-menu-enable-next" name="menu-item-if-menu-enable[<?php echo esc_attr( $item_id ); ?>][]">
						<option value="false">+</option>
						<option value="and" <?php if (isset($if_menu_enable[$index + 1])) selected( 'and', $if_menu_enable[$index + 1] ) ?>><?php esc_html_e( 'AND', 'if-menu' ) ?></option>
						<option value="or" <?php if (isset($if_menu_enable[$index + 1])) selected( 'or', $if_menu_enable[$index + 1] ) ?>><?php esc_html_e( 'OR', 'if-menu' ) ?></option>-->
					</select>
					<?php if (isset($selectedCondition['options'])) : ?>
						<select class="menu-item-if-menu-options" name="menu-item-if-menu-options[<?php echo esc_attr($item_id) ?>][<?php echo esc_attr($index) ?>][]" style="width: 305px" multiple>
							<?php foreach ($selectedCondition['options'] as $value => $label) : ?>
								<option value="<?php echo esc_attr($value) ?>" <?php if (in_array($value, $ifMenuOptions[$index])) echo 'selected' ?>><?php echo $label ?></option>
							<?php endforeach ?>
						</select>
					<?php endif ?>
				</p>

			<?php endfor ?>
		</div>

		<?php
	}

  public static function menu_item_title( $item_id ) {
    $if_menu_enabled = get_post_meta( $item_id, 'if_menu_enable' );

    if ( count( $if_menu_enabled ) && $if_menu_enabled[0] !== '0' ) {
      $conditionTypes = get_post_meta( $item_id, 'if_menu_condition_type' );
      $conditions = get_post_meta( $item_id, 'if_menu_condition' );
      $rules = If_Menu::get_conditions($for_testing = true);

      if ( $conditionTypes[0] === 'show' ) {
        $conditionTypes[0] = '';
      }

      echo '<span class="is-submenu">';
      printf( __( '%s if %s', 'if-menu' ), $conditionTypes[0], $rules[$conditions[0]]['name'] );
      if ( count( $if_menu_enabled ) > 1 ) {
        printf( ' ' . _n( 'and %d more rule', 'and %d more rules', count( $if_menu_enabled ) - 1, 'if-menu' ), count( $if_menu_enabled ) - 1 );
      }
      echo '</span>';
    }
  }

  public static function customWalker($walker) {
    global $wp_version;

    if (version_compare( $wp_version, '4.7.0', '>=')) {
      require_once(plugin_dir_path(__FILE__) . 'src/if-menu-nav-menu-4.7.php');
    } elseif ( version_compare( $wp_version, '4.5.0', '>=' ) ){
      require_once(plugin_dir_path(__FILE__) . 'src/if-menu-nav-menu-4.5.php');
    } else {
      require_once(plugin_dir_path(__FILE__) . 'src/if-menu-nav-menu.php');
    }

    return 'If_Menu_Walker_Nav_Menu_Edit';
  }

	public static function wp_update_nav_menu_item( $menu_id, $menu_item_db_id ) {
		if (isset($_POST['menu-item-if-menu-enable'])) {

			delete_post_meta($menu_item_db_id, 'if_menu_enable');
			delete_post_meta($menu_item_db_id, 'if_menu_condition_type');
			delete_post_meta($menu_item_db_id, 'if_menu_condition');
			delete_post_meta($menu_item_db_id, 'if_menu_options');

			foreach ($_POST['menu-item-if-menu-enable'][$menu_item_db_id] as $index => $value) {
				if (in_array( $value, array('1', 'and', 'or'))) {
					add_post_meta($menu_item_db_id, 'if_menu_enable', $value);
					add_post_meta($menu_item_db_id, 'if_menu_condition_type', $_POST['menu-item-if-menu-condition-type'][$menu_item_db_id][$index]);
					add_post_meta($menu_item_db_id, 'if_menu_condition', $_POST['menu-item-if-menu-condition'][$menu_item_db_id][$index]);
					if (isset($_POST['menu-item-if-menu-options']) && isset($_POST['menu-item-if-menu-options'][$menu_item_db_id]) && isset($_POST['menu-item-if-menu-options'][$menu_item_db_id][$index])) {
						add_post_meta($menu_item_db_id, 'if_menu_options', array_unique($_POST['menu-item-if-menu-options'][$menu_item_db_id][$index]));
					} else {
						add_post_meta($menu_item_db_id, 'if_menu_options', 0);
					}
				} else {
					break;
				}
			}
		}
	}

	public static function apiNonce($action) {
		$nonce = uniqid();
		set_transient('if-menu-nonce-' . $action, $nonce, 600);
		return $nonce;
	}

	public static function getPlan() {
		if (true || isset($_REQUEST['if-menu-recheck-plan']) || false === ($plan = get_transient('if-menu-plan'))) {
			$plan = false;
			$request = wp_remote_get('https://wordpress.layered.studio/get-plan?site=' . urlencode(site_url()) . '&for=if-menu&_nonce=' . self::apiNonce('plan-check'));

			if (!is_wp_error($request)) {
				$data = json_decode(wp_remote_retrieve_body($request), true);
				if (isset($data['plans'])) {
					$plan = $data['plans']['if-menu'];
					set_transient('if-menu-plan', $plan, 500);
				}
			}
		}

		return $plan;
	}

	public static function restApi() {
		register_rest_route('if-menu/v1', '/did-you-made-this-request', array(
			'methods'	=>	'GET',
			'callback'	=>	function() {
				$action = isset($_REQUEST['action']) ? sanitize_key($_REQUEST['action']) : false;
				$nonce = isset($_REQUEST['nonce']) ? sanitize_key($_REQUEST['nonce']) : false;
				return array('valid' => $action && $nonce && $nonce === get_transient('if-menu-nonce-' . $action));
			}
		) );
	}

	public static function pluginActivate() {
		add_option('if-menu-peak', 0);
	}

}



/* ------------------------------------------------
	Include default visibility rules for menu items
------------------------------------------------ */

include 'src/user-info.php';
include 'src/conditions-basic.php';
include 'src/conditions-multiple-options.php';



/* ------------------------------------------------
	Run the plugin
------------------------------------------------ */

register_activation_hook(__FILE__, array('If_Menu', 'pluginActivate'));
add_action('plugins_loaded', 'If_Menu::init');
