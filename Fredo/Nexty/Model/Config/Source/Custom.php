<?php

namespace Fredo\Nexty\Model\Config\Source;

class Custom implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'completed' => 'Completed',
            'pending_payment' => 'Pending Payment',
        ];
    }
}
