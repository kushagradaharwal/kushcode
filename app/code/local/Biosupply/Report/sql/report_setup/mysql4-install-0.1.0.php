<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('bioreport')};
CREATE TABLE {$this->getTable('bioreport')} (
  `report_id` int(11) unsigned NOT NULL auto_increment,
  `customerid` int(11)  NOT NULL  default '0',
  `account_no` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `report_type` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup(); 