<?php
defined('ABSPATH') or die('Invalid request.'); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
setTimeout(function() {
jQuery('input[name="<?php echo EBE_ANTI_SPAM_RAND; ?>_jsf"]').val('<?php echo $this->js_code_fill; ?>');
}, 3000);
});
</script>