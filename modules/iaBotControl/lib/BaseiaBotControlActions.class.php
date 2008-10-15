<?php

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Sebastian Schulze <sebastian.schulze@inaudito.de>
 */

class BaseiaBotControlActions extends sfActions
{
  public function executeAuthorize()
  {
    $iaBotControlRequest = Doctrine::getTable('iaBotControlRequest')->findOneById($this->getUser()->getAttribute('id', null, 'iaBotControl'));
    $this->last_request = abs(strtotime($iaBotControlRequest->getUpdatedAt()) - time());
    if ($this->last_request > sfConfig::get('app_ia_bot_control_authorization_timeout'))
    {
      $this->grantAccess($iaBotControlRequest->getId());
    }
    else
    {
      $this->time_to_wait = sfConfig::get('app_ia_bot_control_authorization_timeout') - $this->last_request;
    }
  }

  public function executeCheckAuthorization()
  {
    $this->grantAccess($this->getUser()->getAttribute('id', null, 'iaBotControl'));
  }

  public function handleErrorCheckAuthorization()
  {
    $this->redirect('iaBotControl/authorize');
  }

  private function grantAccess($id)
  {
      $botControl = Doctrine::getTable('iaBotControlRequest')->findOneById($id);
      $botControl->resetCredits();
      $this->redirect($this->getUser()->getAttribute('redirect_to', null, 'iaBotControl'));
  }
}