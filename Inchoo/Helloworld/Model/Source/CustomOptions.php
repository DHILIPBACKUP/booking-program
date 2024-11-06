<?php

namespace Inchoo\Helloworld\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CustomOptions implements OptionSourceInterface
{
    /**
     * Get options for dropdown
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('-- Please Select --'), 'value' => ''],
            ['label' => __('Option #1'), 'value' => '1'],
            ['label' => __('Option #2'), 'value' => '2'],
            ['label' => __('Option #3'), 'value' => '3'],
        ];
    }
}
