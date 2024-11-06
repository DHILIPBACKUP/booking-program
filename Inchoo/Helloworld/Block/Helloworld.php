<?php
namespace Inchoo\Helloworld\Block;

use Inchoo\Helloworld\Block\UpdateProductAttributeLabel;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\FileSystemException;

class Helloworld extends \Magento\Framework\View\Element\Template
{
    /**
     * @var UpdateProductAttributeLabel
     */
    protected  $label;

    public function __construct(

        UpdateProductAttributeLabel    $label,
        Context                  $context,
        array $data = []
    ) {
        $this->label = $label;
        parent::__construct($context, $data);
    }

    /**
     * @throws FileSystemException
     */
    public function getHelloWorldTxt()
    {
        $this->label->getDataFromCsv();
        return 'Hello world!';
    }
}
