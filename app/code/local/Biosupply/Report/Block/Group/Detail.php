<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Report to newer
 * versions in the future.
 *
 * @category    Biosupply
 * @package     Biosupply_Report
  * @author     Extlab Team
 * @copyright   Copyright (c) 2015 Extlab Team (http://www.extlab.com)
 */
class Biosupply_Report_Block_Group_Detail  extends Biosupply_Report_Block_Group_Report_Export {
	public function __construct() {
		$this->setCollection($this->getCollection());
	}
}
