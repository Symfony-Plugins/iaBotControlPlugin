<?php

/**
 * Symfony Bot Control Filter
 *
 * @author Sebastian Schulze <sebastian.schulze@inaudito.de>
 */

class iaBotControlFilter extends sfFilter
{
  public function execute($filterChain)
  {
    $action = $this->getContext()->getActionStack()->getLastEntry()->getActionInstance();
    $securityConfiguration = $action->getSecurityConfiguration();

    if (isset($securityConfiguration[strtolower($action->getActionName())]))
    {
      $botControlConfiguration = $securityConfiguration[strtolower($action->getActionName())];
      $max_requests = sfConfig::get('app_ia_bot_control_max_requests');
      $user = $this->getContext()->getUser();
      $ip = $_SERVER['REMOTE_ADDR'];

      if (isset($botControlConfiguration['bot_control']) && (true === $botControlConfiguration['bot_control']))
      {
        $botControl = Doctrine::getTable('iaBotControlRequest')->getOrCreate($ip, $user->getGuardUser());

        $this->getContext()->getUser()->setAttribute('id', $botControl->getId(), 'iaBotControl');

        if (0 < $botControl->getCredits())
        {
          $last_request = abs(strtotime($botControl->getUpdatedAt()) - time());

          if ($last_request > sfConfig::get('app_ia_bot_control_authorization_timeout'))
          {
            $botControl->resetCredits();
            $action->redirect($user->getAttribute('redirect_to', null, 'iaBotControl'));
          }
          else
          {
            $botControl->removeCredit($user);
            $botControl->save();
          }
        }
        else
        {
          $this->getContext()->getUser()->setAttribute('redirect_to', $action->getRequest()->getUri(), 'iaBotControl');
          $action->forward('iaBotControl', 'authorize');
        }
      }
    }

    $filterChain->execute();
  }
}
