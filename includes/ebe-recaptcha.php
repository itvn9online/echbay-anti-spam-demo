<?php
defined('ABSPATH') or die('Invalid request.'); ?>
<script type="text/javascript" src="<?php echo str_replace(ABSPATH, rtrim(get_home_url(), '/') . '/', __DIR__); ?>/ebe-recaptcha.js?v=<?php echo filemtime(__DIR__ . '/ebe-recaptcha.js'); ?>" defer></script>
<script type="text/javascript">
(function() {
var _run = function(max_i) {
if (max_i < 0) {
console.log('max i:', max_i);
return false;
} else if (typeof action_ebe_anti_spam != 'function') {
setTimeout(function() {
_run(max_i - 1)
}, 100);
return false; }
action_ebe_anti_spam('<?php echo admin_url('admin-ajax.php'); ?>', '<?php echo EBE_ANTI_SPAM_RAND; ?>'); }
_run(99);
})();
</script>