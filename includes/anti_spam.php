<?php
defined('ABSPATH') or die('Invalid request.');
$anti_div_id_spam = '_' . EBE_ANTI_SPAM_RAND . rand(99, 999); ?>
<style>
<?php echo 'div.' . $anti_div_id_spam; ?> {
position: absolute;
left: -9999px;
z-index: -1;
opacity: 0; }
</style>
<div class="<?php echo $anti_div_id_spam; ?>">
<?php
$i = 1;
$j = rand($i, count($this->input_anti_spam));
foreach ($this->input_anti_spam as $k => $v) {
$val = '';
$attr_required = '';
if ($j == $i) {
$val = EBE_ANTI_SPAM_RAND . '_' . $k;
$val = md5($val);
if ($v == 'email') {
$val .= '@' . $_SERVER['HTTP_HOST']; }
$attr_required = 'aria-required="true" required'; }
$i++; ?>
<input type="<?php echo $v; ?>" name="<?php echo EBE_ANTI_SPAM_RAND; ?>_<?php echo $k; ?>" placeholder="<?php echo $k; ?>" value="<?php echo $val; ?>" <?php echo $attr_required; ?> />
<?php
}
$time_expired = $this->my_settings['time_expired'] * 1;
if ($time_expired < 1) {
$time_expired = 24 * 3600;
$time_expired -= rand(0, 99);
} else {
$time_expired += rand(0, 33); }
$time_expired = $time_expired + time();
$rand_code = session_id();
$rand_code = substr($rand_code, rand(0, strlen($rand_code) - $this->rand_len_code), $this->rand_len_code);
foreach ([
'to' => $time_expired,
'token' => md5(EBE_ANTI_SPAM_RAND . $time_expired),
'code' => $rand_code,
'jsf' => '',
] as $k => $v) { ?>
<input type="text" name="<?php echo EBE_ANTI_SPAM_RAND; ?>_<?php echo $k; ?>" placeholder="<?php echo $k; ?>" value="<?php echo $v; ?>" aria-required="true" required />
<?php } ?>
</div>