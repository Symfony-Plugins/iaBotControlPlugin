<?php

class BaseaiBotControlAuthorizeForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'captcha' => new sfWidgetFormInput()
    ));

    $this->setValidators(array(
      'captcha' => new sfValidatorSfCryptoCaptcha(array('required' => true, 'trim' => true),
                                                  array('wrong_captcha' => 'The code you copied is not valid.',
                                                        'required' => 'You did not copy any code. Please copy the code.'))
    ));

    $this->widgetSchema->setNameFormat('authorize[%s]');
  }
}