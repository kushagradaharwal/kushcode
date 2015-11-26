<?php

class Biosupply_Report_Model_Report extends Mage_Core_Model_Abstract
{
    public function _construct()
    {  
       parent::_construct();
        $this->_init('report/report');
    }
}