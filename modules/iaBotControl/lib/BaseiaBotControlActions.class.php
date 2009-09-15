<?php

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Sebastian Schulze <sebastian.schulze@inaudito.de>
 */

class BaseiaBotControlActions extends sfActions
{
  public function executeAuthorize(sfWebRequest $request)
  {
    $class = sfConfig::get('app_ia_bot_control_plugin_authorize_form', 'aiBotControlAuthorizeForm');
    $this->form = new $class();
    
    $botControlId = $this->getUser()->getAttribute('id', null, 'iaBotControl');
    if (is_null($botControlId))
    {
      // occurs if client accesses this action without calling a bot-controlled action before
      // -> must be a bot, so kick him out
      $this->getResponse()->setStatusCode(403);
      return sfView::NONE;
    }

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('authorize'));
      
      if ($this->form->isValid())
      {
      	$botControl = Doctrine::getTable('iaBotControlRequest')->findOneById($botControlId);
      	if ($botControl !== false)
      	{
          $botControl->setAuthorized(true);
      	  $botControl->resetCredits();
      	  $botControl->save();
      	}
      	else
      	{
      	  // occurs if client posts form after the bot control table has been garbage collected
      	  // -> this is probably not a bot, so do nothing, captcha was solved anyway
      	}
        
      	$redirect_url = $this->getUser()->getAttribute('redirect_to', null, 'iaBotControl');
      	if (is_null($redirect_url))
      	{
      	  $this->logMessage(
      	    'iaBotControlPlugin could\'t redirect user after authorizing because url was missing.'.
      	    'Please inform the plugin developers about this error because this shouldn\'t happen!', 'err');
      	  $this->getResponse()->setStatusCode(500);
      	  return sfView::NONE;
      	}
      	else
      	{
      	  $this->redirect($redirect_url);
      	}
      }
    }
  }
}