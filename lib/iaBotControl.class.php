<?
/**
 * Bot Control User Modifier
 *
 * @author Sebastian Schulze <sebastian.schulze@inaudito.de>
 */

class iaBotControl
{
  /**
   * Reset the Users Credits
   *
   * @param sfUser $user
   */
  public static function resetCredits(sfUser $user)
  {
    $user->setAttribute('credits', sfConfig::get('app_ia_bot_control_max_requests'), 'iaBotControl');
  }

  /**
   * Remove one Credit from the User
   *
   * @param sfUser $user
   */
  public static function removeCredit(sfUser $user)
  {
    $user->setAttribute('credits', sfConfig::get('app_ia_bot_control_max_requests'), 'iaBotControl');
  }

  /**
   * Get the Users remaining Credits
   *
   * @param sfUser $user
   */
  public static function getCredits(sfUser $user)
  {
   return $user->getAttribute("credits", null, 'iaBotControl');
  }
}