<?php
/**
* Plugin Name: EchBay Anti-spam: Spam Protection
* Description: Echbay Anti-spam is quite possibly the best way to protect your blog from spam. Echbay Anti-spam keeps your site protected even while you sleep. To get started: install and activate the Echbay Anti-spam plugin.
* Plugin URI: https://www.facebook.com/groups/wordpresseb
* Plugin Facebook page: https://www.facebook.com/webgiare.org
* Author: Dao Quoc Dai
* Author URI: https://www.facebook.com/ech.bay/
* Version: 1.0.0
* Text Domain: webgiareorg
* Domain Path: /languages/
* License: GPLv2 or later
*/
defined('ABSPATH') or die('Invalid request.');
define('EBE_ANTI_SPAM_VERSION', '1.0.0');
define('EBE_ANTI_SPAM_PLUGIN_NAME', 'EchBay Anti-spam');
add_action('init', function () {
if (!session_id()) {
session_start(); }
});
define('EBE_ANTI_SPAM_RAND', '_' . substr(md5(session_id()), 6, 12));
if (!class_exists('EBE_ANTI_SPAM_Actions_Module')) {
class EBE_ANTI_SPAM_Actions_Module
{
public $input_anti_spam = [
'email' => 'email',
'phone' => 'text',
'fname' => 'text',
'lname' => 'text',
'address' => 'text',
'captcha' => 'text',
];
public $plugin_path = '';
public $optionName = 'ebe_anti_spam_options';
public $optionGroup = 'ebe-anti-spam-options-group';
public $defaultOptions = [];
public $defaultNameOptions = [];
public $my_settings = [];
public $plugin_page = 'echbay-anti-spam';
public $time_expired = 3600;
public $minOptions = array(
'time_expired' => 120,
);
public $rand_len_code = 6;
public $checked_status = true;
public $result_error = 'Anti-spam detected in your request!';
public $js_code_fill = '';
public function __construct()
{
$this->defaultOptions = array(
'time_expired' => 3600,
'for_cmt' => '1',
'for_login' => '0',
'for_cf7' => '1',
'for_woo' => '0',
);
$this->defaultNameOptions = array(
'time_expired' => [
'name' => 'Expire time',
'type' => 'number',
'description' => 'Set expire time for each request.'
],
'for_cmt' => [
'name' => 'Enable on comment and woocomerce review',
'type' => 'checkbox',
],
'for_login' => [
'name' => 'Enable on login form',
'type' => 'checkbox',
],
'for_cf7' => [
'name' => 'Enable in contact form 7',
'type' => 'checkbox',
],
'for_woo' => [
'name' => 'Enable in woocomerce checkout',
'type' => 'checkbox',
],
);
$this->js_code_fill = substr(EBE_ANTI_SPAM_RAND, 0, 6);
$this->my_settings = $this->get_my_options();
add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'), 10, 2);
add_action('admin_menu', array($this, 'admin_set_menu'));
add_action('admin_init', array($this, 'register_my_settings')); }
public function add_action_links($links)
{
if (strpos($_SERVER['REQUEST_URI'], '/plugins.php') !== false) {
$settings_link = '<a href="' . admin_url('options-general.php?page=' . $this->plugin_page) . '" title="Settings">Settings</a>';
array_unshift($links, $settings_link); }
return $links; }
public function admin_set_menu()
{
add_options_page(
EBE_ANTI_SPAM_PLUGIN_NAME,
EBE_ANTI_SPAM_PLUGIN_NAME,
'manage_options',
$this->plugin_page,
array(
$this,
'main_page'
)
); }
public function register_my_settings()
{
register_setting($this->optionGroup, $this->optionName); }
public function get_my_options()
{
$result = wp_parse_args(get_option($this->optionName), $this->defaultOptions);
foreach ($result as $k => $v) {
if ($v == '' && isset($this->defaultOptions[$k]) && $v != $this->defaultOptions[$k]) {
$result[$k] = $this->defaultOptions[$k];
} else if (isset($this->minOptions[$k]) && $v < $this->minOptions[$k]) {
$result[$k] = $this->minOptions[$k]; }
}
return $result; }
public function main_page()
{
include __DIR__ . '/includes/main_page.php'; }
public function get_url_static_file($f)
{
if ($this->plugin_path == '') {
$this->plugin_path = plugin_dir_path(__FILE__); }
return str_replace(ABSPATH, get_home_url() . '/', $this->plugin_path) . $f . '?v=' . filemtime($this->plugin_path . $f); }
public function anti_spam_field()
{
include __DIR__ . '/includes/anti_spam.php';
return true; }
public function anti_spam_ajax()
{
if (is_user_logged_in()) {
return $this->anti_spam_field(); }
echo '<div class="ebe-recaptcha"></div>';
include_once __DIR__ . '/includes/ebe-recaptcha.php';
return true; }
public function anti_spam_result($html = '')
{
ob_start();
$this->anti_spam_ajax();
$result = ob_get_contents();
ob_end_clean();
return $html . $result; }
public function antiRequiredSpam($commentdata, $context = 'default')
{
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
return false; }
$has_value = 0;
$no_value = 0;
$i = 0;
$this_spam = false;
foreach ($this->input_anti_spam as $k => $v) {
$k = EBE_ANTI_SPAM_RAND . '_' . $k;
if (!isset($_POST[$k])) {
break; }
if (!empty($_POST[$k])) {
$has_value++;
if (md5($k) != explode('@', $_POST[$k])[0]) {
$this_spam = $k;
break; }
} else {
$no_value++; }
$i++; }
$by_token = 0;
$by_code = 0;
$by_jsf = 0;
$time_out = isset($_POST[EBE_ANTI_SPAM_RAND . '_to']) ? $_POST[EBE_ANTI_SPAM_RAND . '_to'] : 0;
$time_token = isset($_POST[EBE_ANTI_SPAM_RAND . '_token']) ? $_POST[EBE_ANTI_SPAM_RAND . '_token'] : '';
if (empty($time_out) || !is_numeric($time_out) || $time_out < time() || empty($time_token) || md5(EBE_ANTI_SPAM_RAND . $time_out) != $time_token) {
$by_token = 1;
} else {
$rand_code = isset($_POST[EBE_ANTI_SPAM_RAND . '_code']) ? $_POST[EBE_ANTI_SPAM_RAND . '_code'] : '';
if (empty($rand_code) || strlen($rand_code) != $this->rand_len_code || strpos(session_id(), $rand_code) === false) {
$by_code = 1;
} else {
$jsf_code = isset($_POST[EBE_ANTI_SPAM_RAND . '_jsf']) ? $_POST[EBE_ANTI_SPAM_RAND . '_jsf'] : '';
if (empty($jsf_code) || $jsf_code != $this->js_code_fill) {
$by_jsf = 1; }
}
}
$result = $this->afterCheckSpam([
'by_token' => $by_token,
'by_code' => $by_code,
'by_jsf' => $by_jsf,
'has_value' => $has_value > 0 ? $has_value : __LINE__,
'no_value' => $no_value > 0 ? $no_value : __LINE__,
'i' => $i > 0 ? $i : __LINE__,
'this_spam' => $this_spam,
'f' => __FUNCTION__,
'context' => $context,
]);
if ($result !== true) {
return $result; }
return $commentdata; }
public function antiCf7Spam($data)
{
if (!class_exists('WPCF7_Submission')) {
return $data; }
$submission = WPCF7_Submission::get_instance();
if ($this->antiRequiredSpam($data, 'cf7') === false) {
$submission->set_status('spam');
$submission->set_response($this->result_error); }
return $data; }
public function antiWooSpam($data)
{
if (!class_exists('WooCommerce')) {
return $data; }
return $this->antiRequiredSpam($data); }
public function afterCheckSpam($ops)
{
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
return $this->dieIfSpam([
'code' => __LINE__,
'rq' => 'Bad request!',
'f' => strtolower($ops['f']),
'context' => $ops['context'],
]); }
$count_anti = count($this->input_anti_spam);
if ($ops['by_token'] > 0) {
return $this->dieIfSpam([
'code' => __LINE__,
'token' => $ops['by_token'],
'f' => strtolower($ops['f']),
'context' => $ops['context'],
]); }
else if ($ops['by_code'] > 0) {
return $this->dieIfSpam([
'code' => __LINE__,
'sid' => $ops['by_code'],
'f' => strtolower($ops['f']),
'context' => $ops['context'],
]); }
else if ($ops['by_jsf'] > 0) {
return $this->dieIfSpam([
'code' => __LINE__,
'jsf' => $ops['by_jsf'],
'f' => strtolower($ops['f']),
'context' => $ops['context'],
]); }
$ops['has_value'] *= 1;
$ops['no_value'] *= 1;
$by_value = false;
if ($ops['has_value'] > 0 || $ops['no_value'] > 0) {
if ($ops['has_value'] !== 1 || $ops['no_value'] !== ($count_anti - 1)) {
$by_value = true; }
}
if ($by_value !== false) {
return $this->dieIfSpam([
'code' => __LINE__,
'has' => $ops['has_value'],
'no' => $ops['no_value'],
'f' => strtolower($ops['f']),
'context' => $ops['context'],
]); }
else if ($ops['i'] > 0 && $ops['i'] != $count_anti) {
return $this->dieIfSpam([
'code' => __LINE__,
'count' => $ops['i'],
'f' => strtolower($ops['f']),
'context' => $ops['context'],
]); }
else if ($ops['this_spam'] !== false) {
return $this->dieIfSpam([
'code' => __LINE__,
'spamer' => $ops['this_spam'],
'f' => strtolower($ops['f']),
'context' => $ops['context'],
]); }
return true; }
public function dieIfSpam($d)
{
if ($d['context'] == 'cf7') {
return false; }
$d['error'] = $this->result_error;
if (wp_doing_ajax()) {
header('Content-type: application/json; charset=utf-8');
die(json_encode($d)); }
wp_die($d['error'] . ' (' . $d['code'] . '). <br> <a href="javascript:history.back()">« Back</a>', 409); }
public function hide_captcha()
{
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
$this->anti_spam_field(); }
exit(); }
public function js_fill()
{
include_once __DIR__ . '/includes/js-fill.php';
return true; }
}
try {
$EBE_ANTI_SPAM_func = new EBE_ANTI_SPAM_Actions_Module();
$arr_my_options = $EBE_ANTI_SPAM_func->get_my_options();
if ($arr_my_options['for_cmt'] * 1 > 0) {
add_action('comment_form_top', array($EBE_ANTI_SPAM_func, 'anti_spam_ajax'));
add_filter('preprocess_comment', array($EBE_ANTI_SPAM_func, 'antiRequiredSpam'), 1); }
if ($arr_my_options['for_login'] * 1 > 0) {
add_action('login_form', array($EBE_ANTI_SPAM_func, 'anti_spam_ajax'));
add_filter('wp_login', array($EBE_ANTI_SPAM_func, 'antiRequiredSpam'), 1); }
if ($arr_my_options['for_cf7'] * 1 > 0) {
add_filter('wpcf7_form_elements', array($EBE_ANTI_SPAM_func, 'anti_spam_result'));
add_filter('wpcf7_posted_data', array($EBE_ANTI_SPAM_func, 'antiCf7Spam'), 1); }
if ($arr_my_options['for_woo'] * 1 > 0) {
add_filter('woocommerce_before_checkout_billing_form', array($EBE_ANTI_SPAM_func, 'anti_spam_ajax'));
add_filter('woocommerce_after_checkout_validation', array($EBE_ANTI_SPAM_func, 'antiWooSpam'), 1); }
add_action('wp_enqueue_scripts', function () {
if (!wp_script_is('jquery', 'enqueued')) {
wp_enqueue_script('jquery'); }
});
add_action('wp_ajax_' . EBE_ANTI_SPAM_RAND, array($EBE_ANTI_SPAM_func, 'hide_captcha'));
add_action('wp_ajax_nopriv_' . EBE_ANTI_SPAM_RAND, array($EBE_ANTI_SPAM_func, 'hide_captcha'));
add_filter('wp_footer', array($EBE_ANTI_SPAM_func, 'js_fill'));
} catch (Exception $e) {
echo ($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()); }
}