<pre>
<?php
include('app/bootstrap.php');
use Magento\Framework\App\Bootstrap;
$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$registry = $objectManager->get('\Magento\Framework\Registry');
$registry->register('isSecureArea', true);
$page = $objectManager->create('Magento\Framework\App\Request\Http')->getParam('page');
$totalItems = $objectManager->create('Magento\Sales\Model\Order')->getCollection()->addFieldToFilter('coupon_code',array('notnull' => true))->getSize();
$row = 100;
$totalpage=ceil($totalItems/$row);
echo "Total Items: ".$totalItems;
echo "<pre>";
$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$tableName = 'sales_order_grid';
for ($i=1; $i <= $totalpage; $i++) {
	echo "\nUpdateing page $i/$totalpage";
	$ordercollection = $objectManager->create('Magento\Sales\Model\Order')->getCollection()->addFieldToFilter('coupon_code',array('notnull' => true))->setPage($i,$row);
	foreach ($ordercollection as $order) {
		$id = (string)$order->getData('increment_id');
		if (strpos($id, 'WSP') === false) {
			var_dump($order->getData('increment_id'));
			var_dump($order->getCouponCode());
			file_put_contents(BP.'/var/log/testupdateordergrid.log', print_r($order->getData('increment_id'), true) . PHP_EOL ,FILE_APPEND);
			file_put_contents(BP.'/var/log/testupdateordergrid.log', print_r($order->getCouponCode(), true) . PHP_EOL ,FILE_APPEND);
			// UPDATE DATA
			$sql = "UPDATE " . $tableName . " SET coupon_code = '".$order->getCouponCode()."' WHERE increment_id = '" . $order->getData('increment_id')."' AND coupon_code IS NULL";
			$connection->query($sql);
		}
	}
}
echo "</pre>";
// print_r($arrayid);

echo "Done";
