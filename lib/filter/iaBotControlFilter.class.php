<?php

/**
 * Symfony Bot Control Filter
 * 
 * This filter is the main part of the plugin. It does the following:
 * 
 * First, it checks if the called action has the bot control enabled.
 * If it hasn't then the filter does nothing. But if it has then
 * it checks how many credits the client has. With every request
 * that happens too fast after the one before the client loses one credit.
 * The amount of start credits can be customized through app.yml.
 * If the client is out of credits he gets forwarded to a plugin action
 * to prove he is not a bot. This action has a captcha by default but it
 * can be customized. If the client can't prove that he is a human then
 * he sticks with 0 credits and can't access any page which is bot controlled
 * by this plugin. But if he solves the puzzle then his credits get resetted
 * and he gets redirected to the url at which he was before.
 *
 * @author Sebastian Schulze <sebastian.schulze@inaudito.de>
 * @author Maik Riechert <maik.riechert@animey.net>
 */
class iaBotControlFilter extends sfFilter
{
  public function execute(sfFilterChain $filterChain)
  {
  	$context = $this->getContext();
    $action = $context->getActionStack()->getLastEntry()->getActionInstance();
	$user = $context->getUser();
    
    // (must not be enabled on internal iaBotControl module)
    if (self::botControlEnabled($action, $user))
    {
      $max_requests = sfConfig::get('app_ia_bot_control_plugin_max_requests', 5);
      $ip = $_SERVER['REMOTE_ADDR'];

      $botControl = Doctrine::getTable('iaBotControlRequest')->getOrCreate($ip, $user->getGuardUser());

      $user->setAttribute('id', $botControl->getId(), 'iaBotControl');
      
      $ignore_authorized = sfConfig::get('app_ia_bot_control_plugin_ignore_authorized', false);
      
      if ($ignore_authorized === true && $botControl->getAuthorized() === true)
      {
      	// do nothing
      }
      else if ($botControl->getCredits() > 0)
      {
        $last_request = time() - strtotime($botControl->getUpdatedAt());

        if ($last_request > sfConfig::get('app_ia_bot_control_plugin_timeout', 5))
        {
          $botControl->resetCredits();
        }
        else
        {
          $botControl->removeCredit();
        }
        
        $botControl->save();
      }
      else
      {
        $user->setAttribute('redirect_to', $action->getRequest()->getUri(), 'iaBotControl');
        $action->forward('iaBotControl', 'authorize');
      }
    }

    $filterChain->execute();
  }
  
  /**
   * Checks if bot control is enabled for the action or if the user is logged in
   * and the plugin should ignore signed-in users.
   * 
   * @param sfAction $action
   * @param sfSecurityUser $user
   * @return boolean
   */
  private static function botControlEnabled(sfAction $action, sfSecurityUser $user)
  {
  	$ignore_signed_in = sfConfig::get('app_ia_bot_control_plugin_ignore_signed_in', false);
  	if ($ignore_signed_in && $user->isAuthenticated())
  	{
  	  return false;
  	}
  	
  	$securityConfiguration = $action->getSecurityConfiguration();
  	$actionName = $action->getActionName();
  	if (isset($securityConfiguration['all']['ia_bot_control']))
  	{
  	  return $securityConfiguration['all']['ia_bot_control'];
  	}
  	else if (isset($securityConfiguration[strtolower($actionName)]['ia_bot_control']))
  	{
  	  return $securityConfiguration[strtolower($actionName)]['ia_bot_control'];
  	}
  	else
  	{
  	  return false;
  	} 
  }
}
