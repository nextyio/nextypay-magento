<?php
namespace Fredo\Nexty\Controller\Index;

use Fredo\Nexty\Help\UpdateDB;

class Ajax extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
  protected $helperDB;

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

	public function execute()
	{
      ///////////////CHECK REQUEST
    echo $this->helperDB->order_status_to_complete(64);
    //$checkdb="test";
    /*$prefix=$_REQUEST['prefix'];
    $order_id=$_REQUEST['order_id'];
    echo $prefix." ".$order_id." ".$checkdb;*/
	}
}
