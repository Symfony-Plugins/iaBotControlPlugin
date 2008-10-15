<?php use_helper('Cryptographp', 'Date', 'I18N') ?>

<h2><?php echo __(' Please enter the Captcha Code'); ?></h2>
<h3><?php echo __('You will be able to return to the previous page in %1%.', array('%1%' => distance_of_time_in_words(time(), time() + $time_to_wait, true))) ?></h3>
<p style="text-align:center">
<?php echo form_tag('iaBotControl/checkAuthorization') ?>
  <?php echo input_tag('crypto') ?><br/>
  <?php echo cryptographp_picture(); ?><br/>
  <?php echo cryptographp_reload(); ?><br/>
  <?php echo submit_tag() ?>
</form>
</p>