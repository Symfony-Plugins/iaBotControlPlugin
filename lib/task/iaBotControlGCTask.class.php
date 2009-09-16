<?php

/**
 * Performs garbage collection of old bot control records.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Maik Riechert <maik.riechert@animey.net>
 */
class iaBotControlGCTask extends sfPluginBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure() 
  {
  	$this->addOption('days', null, sfCommandOption::PARAMETER_OPTIONAL, 
  	  'The days after which non-updated records will be deleted', 10);
    
  	$this->namespace = 'iaBotControl';
    $this->name = 'gc';
    $this->briefDescription = 'Performes garbage collection';

    $this->detailedDescription = <<<EOF
The [iaBotControl:gc|INFO] task cleans old records from the bot protection table
which are no longer needed.
EOF;
  }
  
  protected function execute($arguments = array(), $options = array())
  {
  	$databaseManager = new sfDatabaseManager($this->configuration);
  	
  	$rows = Doctrine_Query::create()
  	  ->delete('iaBotControlRequest r')
  	  ->where('r.updated_at < ?', self::calcDate($options['days']))
  	  ->execute();
  	$this->log($rows.' records deleted which were more than '.$options['days'].' days old');
  }
  
  private static function calcDate($days_ago)
  {
  	return strtotime('-'.$days_ago.' days');
  }
}