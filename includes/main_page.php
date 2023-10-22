<?php
defined('ABSPATH') or die('Invalid request.'); ?>
<style>
.eas-wrap img {
max-width: 999px;
background-color: #fff;
padding: 20px; }
</style>
<div class="wrap eas-wrap">
<h1><?php echo EBE_ANTI_SPAM_PLUGIN_NAME; ?> version <?php echo EBE_ANTI_SPAM_VERSION; ?></h1>
<p>* Keep on mind, that in case of emergency, you can disable this plugin via FTP access, just rename the plugin folder: <strong><?php echo basename(dirname(__DIR__)); ?></strong></p>
<form method="post" action="<?php echo admin_url('options.php'); ?>" novalidate="novalidate">
<?php settings_fields($this->optionGroup); ?>
<table class="form-table eoi-table">
<tbody>
<?php
foreach ($this->defaultOptions as $k => $v) {
if (!isset($this->defaultNameOptions[$k]['type']) || $this->defaultNameOptions[$k]['type'] == '') {
$this->defaultNameOptions[$k]['type'] = 'text';
} ?>
<tr>
<th scope="row">
<label for="<?php echo $k; ?>"><?php echo $this->defaultNameOptions[$k]['name']; ?></label>
</th>
<td>
<?php
if ($this->defaultNameOptions[$k]['type'] == 'checkbox') { ?>
<input type="checkbox" id="<?php echo $k; ?>" value="1" <?php checked('1', $this->my_settings[$k]); ?> data-for="<?php echo $k; ?>" /> Active
<input type="hidden" value="1" data-k="<?php echo $k; ?>" name="<?php echo $this->optionName; ?>[<?php echo $k; ?>]" />
<?php } else { ?>
<input type="<?php echo $this->defaultNameOptions[$k]['type']; ?>" id="<?php echo $k; ?>" value="<?php echo esc_attr($this->my_settings[$k]); ?>" name="<?php echo $this->optionName; ?>[<?php echo $k; ?>]">
<?php } ?>
<?php
if (isset($this->defaultNameOptions[$k]['description'])) { ?>
<p class="description"><?php echo $this->defaultNameOptions[$k]['description']; ?></p>
<?php } ?>
</td>
</tr>
<?php } ?>
</tbody>
</table>
<?php
do_settings_fields($this->optionGroup, 'default');
do_settings_sections($this->optionGroup, 'default'); ?>
<p>* Note: Default values will be used if custom values are not set.</p>
<table class="form-table">
<tbody>
<tr>
<th scope="row">&nbsp;</th>
<td>
<?php
submit_button(); ?>
</td>
</tr>
</tbody>
</table>
</form>
</div>
<br>
<script>
var arr_my_settings = <?php echo json_encode($this->my_settings); ?>
</script>
<script src="<?php echo $this->get_url_static_file('admin.js'); ?>" defer></script>
<p>* Other <a href="<?php echo admin_url('plugin-install.php'); ?>?s=itvn9online&tab=search&type=author" target="_blank">WordPress Plugins</a> written by the same author. Thanks for choose us!</p>