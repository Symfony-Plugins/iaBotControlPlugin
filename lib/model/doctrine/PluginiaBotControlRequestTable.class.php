<?php

class PluginiaBotControlRequestTable extends Doctrine_Table
{
  public function getOrCreate($ip, sfGuardUser $user = null)
  {
    $botControl = $this->findOneByIp($ip);
    if ($botControl === false)
    {
      $botControl = new iaBotControlRequest();
      $botControl->setIp($ip);
      $botControl->setAuthorized(false);
      $botControl->setUser($user);
      $botControl->resetCredits();
      $botControl->save();
    }
    else if (!self::sameUsers($botControl, $user))
    {
      // user signed in or logged out
      $botControl->setUser($user);
      $botControl->save(); // TODO: refreshes updated_at but shouldn't make big problems
    }
    return $botControl;
  }
  
  private static function sameUsers(iaBotControlRequest $botControl, sfGuardUser $user = null)
  {
  	// TODO: don't know how to check properly if two doctrine objects are the same
    if (is_null($botControl->getUserId()) && is_null($user))
    {
      return true;
    }
    if (!is_null($botControl->getUserId()) && !is_null($user) && $botControl->getUserId() === $user->getId())
    {
      return true;
    }
    return false;
  }
}