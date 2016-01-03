<?php

namespace Training2\OrderController\Controller\Index;


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
				$itemCollection = $this->_objectManager->create('Magento\Sales\Model\Order\Item')->getCollection();		
				$itemCollection->addFieldToFilter('order_id', ['eq' => $orderid]);
				//$itemCollection->printLogQuery(true);
				
				$ItemDetails .= '<table border=1><tr><td>Order Id</td><td>Order No</td><td>Status</td><td>Grand Total</td>
				<td>Item Id</td><td>Sku</td><td>Price</td><td>Total Invoiced</td></tr>';
					
				foreach($itemCollection as $Data=>$itemData){
					
					$ItemDetails .='<tr>'
					.'<td>'.$orderData['entity_id'].'</td>'
					.'<td>'.$orderData['increment_id'].'</td>'
					.'<td>'.$orderData['status'].'</td>'
					.'<td>'.$orderData['grand_total'].'</td>'
					.'<td>'.$itemData['item_id'].'</td>'
					.'<td>'.$itemData['sku'].'</td>'
					.'<td>'.$itemData['price'].'</td>'
					.'<td>'.$orderData['total_invoiced'].'</td>'
					.'</tr>';
					
				}
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