<?php
namespace Fredo\Nexty\Controller\Index;

use Fredo\Nexty\Help\UpdateDB;

class Ajax extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
  private $helperDB;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
    \Fredo\Nexty\Helper\UpdateDB $helperDB
    )
	{
		$this->_pageFactory = $pageFactory;
    $this->helperDB=$helperDB;
		return parent::__construct($context);
	}

//return 1 if order_id paid enough; else return 0
	public function execute()
	{
			$this->helperDB->update_nexty_db();
			if (isset($_REQUEST['order_id']))
			$order_id=$_REQUEST['order_id'];
		//	echo $order_id."sdhgyhs";
			$is_paid_sum_enough=$this->helperDB->is_paid_sum_enough($order_id);
		//	echo $is_paid_sum_enough."test";
			//ajax result 1 if paid enough; else 0
			if ($is_paid_sum_enough) echo "1"; else echo "0";
	}
}
