<?php

abstract class PluginiaBotControlRequest extends BaseiaBotControlRequest
{
  public function resetCredits()
  {
  	if ($this->getAuthorized() === true)
  	{
  	  $max_requests = sfConfig::get('app_ia_bot_control_plugin_max_requests_authorized', 10);
  	}
  	else
  	{
  	  $max_requests = sfConfig::get('app_ia_bot_control_plugin_max_requests', 5);
  	}
    $this->setCredits(intval($max_requests));
    
    // force saving of record because it wouldn't be if the only difference is updated_at
    $this->state(Doctrine_Record::STATE_DIRTY);
  }

  public function removeCredit()
  {
    $this->setCredits($this->getCredits() - 1);
  }
}