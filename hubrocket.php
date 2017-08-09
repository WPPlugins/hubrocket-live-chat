<?php
/*
Plugin Name: HubRocket Live Chat
Plugin URI: https://hubrocket.com/
Description: This plugin will integrate HubRocket's live chat software on your website
Author: HubRocket
Version: 1.0.1
*/

function hubrocket_settings_init() {
	register_setting("hubrocket", "hubrocket_options");
	add_settings_section("hubrocket_section_settings", __("HubRocket Live Chat", "hubrocket"), "hubrocket_section_settings_cb", "hubrocket");
	add_settings_field("hubrocket_field_guid", __("Widget Unique ID", "hubrocket"), "hubrocket_field_guid_cb", "hubrocket", "hubrocket_section_settings", [
		"label_for" => "hubrocket_field_guid",
		"class" => "hubrocket_row"
	]);
}

add_action("admin_init", "hubrocket_settings_init");

function hubrocket_section_settings_cb($args) {
	// $args title, id, callback
}

function hubrocket_field_guid_cb($args) {
	// get the value of the setting we've registered with register_setting()
	$options = get_option("hubrocket_options");
	// output the field
	?>
	<input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="hubrocket_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo isset($options[$args['label_for']]) ? ($options[$args['label_for']]) : ''; ?>" placeholder="abcdef00-1234-5678-9012-abcdefabcdef" class="regular-text">
	<p class="description">
		Your <a href="https://hubrocket.com">HubRocket</a> Unique Widget ID. If you do not have an account with <a href="https://hubrocket.com">HubRocket</a> yet, you can <a href="https://hubrocket.com/pricing.html">sign up here.</a> (30-day free trial, no obligation required).<br />
		You can find your <strong>Unique Widget ID</strong> (which looks like: <code>abcdef00-1234-5678-9012-abcdefabcdef</code>) in your Website Panel -&gt; Settings -&gt; Installation.<br />
		<br />
		<strong>Need help?</strong> Feel free to contact us at <code>hello@hubrocket.com</code>.
	</p>
	<?php
}
 
function hubrocket_options_page() {
	// add top level menu page
	add_menu_page(
		'HubRocket',
		'HubRocket Options',
		'manage_options',
		'hubrocket',
		'hubrocket_options_page_html'
 	);
}

function hubrocket_plugin_settings_link($links) {
	$settings_link = '<a href="admin.php?page=hubrocket">' . __('Settings', 'hubrocket') . '</a>';
	array_unshift($links, $settings_link); 
	return $links;
}

add_action('admin_menu', 'hubrocket_options_page');
add_filter('plugin_action_links_' . plugin_basename(plugin_dir_path(__FILE__) . 'hubrocket.php'), 'hubrocket_plugin_settings_link');

function hubrocket_options_page_html() {
	if(!current_user_can('manage_options')) { return; }
	if(isset($_GET["settings-updated"])) {
		add_settings_error("hubrocket_messages", "hubrocket_message", __("Settings Saved!", "hubrocket"), "updated");
	}
	settings_errors("hubrocket_messages");
	?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting
				settings_fields('hubrocket');
				do_settings_sections('hubrocket');
				submit_button('Save Settings');
				?>
			</form>
		</div>
	<?php
}

add_action("wp_head", "hubrocket_widget_code");
function hubrocket_widget_code() {
	$options = get_option("hubrocket_options");
	if(isset($options["hubrocket_field_guid"])) {
		?>
<!-- HubRocket Widget Code -->
<script type="text/javascript" async src="https://cdn.hubrocket.com/js/jquery.min.js"></script>
<script type="text/javascript" async src="https://hubrocketapp.com/tracker/<?php echo $options["hubrocket_field_guid"]; ?>"></script>
<!-- / End HubRocket Widget Code -->
		<?php
	}
}

register_uninstall_hook(__FILE__, 'hubrocket_uninstall');
function hubrocket_uninstall() {
	unregister_setting("hubrocket", "hubrocket_options");
	delete_option("hubrocket_options");
}