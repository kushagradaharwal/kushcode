<?php

namespace Training3\OrderInfo\Controller\Index;


class Orderdetails extends \Magento\Framework\App\Action\Action
{
    /**
     * Index action
     *
     * @return $this
     */
	
	 
	 
    public function execute()
    {
		$orderid = $this->getRequest()->getParam('orderid');
		$orderData = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderid);
		
		$Flag = 'Wrong Order Id !!';
		$ItemDetails = "";
		if ($orderData->getEntityId()) {
				$Flag = '';
				$ItemDetails .= '<table border=1><tr><td>Order Id</td><td>Order No</td><td>Status</td><td>Grand Total</td>
				<td>Total Invoiced</td></tr>';
					$ItemDetails .='<tr>'
					.'<td>'.$orderData['entity_id'].'</td>'
					.'<td>'.$orderData['increment_id'].'</td>'
					.'<td>'.$orderData['status'].'</td>'
					.'<td>'.$orderData['grand_total'].'</td>'					
					.'<td>'.$orderData['total_invoiced'].'</td>'
					.'</tr>';
			
				$ItemDetails .='</table>' ;
		}			
		
		if(empty($Flag)){
			$jsonData = json_encode(array($ItemDetails));
			
		}else{
			$jsonData = json_encode(array($Flag));
		}
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($jsonData);
		
    }
	
	
	
}