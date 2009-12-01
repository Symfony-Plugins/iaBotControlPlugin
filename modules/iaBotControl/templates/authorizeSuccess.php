<?php use_helper('I18N', 'sfCryptoCaptcha') ?>

<h2><?php echo __('Please enter the Captcha Code'); ?></h2>
<p style="text-align:center">
<?php echo form_tag('iaBotControl/authorize') ?>
  <?php echo $form->renderHiddenFields()?>
  <?php echo $form['captcha']->renderLabel(); ?><br/>
  <?php echo $form['captcha']->renderError(); ?><br />
  <?php echo $form['captcha']->render(); ?>
  <?php echo captcha_image(); ?><br/>
  <?php echo captcha_reload_button(); ?><br/>
  <input type="submit" value="verify" />
</form>
</p>