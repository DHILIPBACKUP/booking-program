<?php

namespace Inchoo\Helloworld\Block;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Webforce\QuickAdd\Setup\Patch\Data\AdditionalProductAttributes;
use Webforce\QuickAdd\Setup\Patch\Data\FrameAndWheelSize;
use Webforce\IdhProductAttributes\Logger\Logger;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Product;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Backend\Block\Template\Context;




/**
 * Class to update attribute labels based on a CSV file.
 */
class UpdateProductAttributeLabel extends \Magento\Framework\View\Element\Template
{
    public const MODULE_NAME = 'Webforce_IdhProductAttributes';
    public const CSV_FILE = '/Helper/attributes.csv';

    protected $moduleDirReader;

    protected $idhLogger;

    protected $file;

    protected $driver;

    /**
     * Constructor for updating product attribute labels.
     *
     * @param Logger $idhLogger
     * @param Reader $moduleDirReader
     * @param File $file
     * @param DriverInterface $driver
     */
    public function __construct(
         Logger                   $idhLogger,
         Reader                   $moduleDirReader,
         File                     $file,
         DriverInterface          $driver,
         Context                  $context,
        array $data = []
    ) {
        $this->idhLogger = $idhLogger;
        $this->moduleDirReader = $moduleDirReader;
        $this->file = $file;
        $this->driver = $driver;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     * @throws FileSystemException
     */
    public function getDataFromCsv(): void
    {
        $modulePath = $this->moduleDirReader->getModuleDir('', self::MODULE_NAME);
        $csvFile = $modulePath . self::CSV_FILE;

        if (!$this->driver->isFile($csvFile)) {
            $this->idhLogger->info("CSV file not found: $csvFile");
            return;
        }
       $english = [];
        $attributesToUpdate = $this->getAttributesFromCSV($csvFile);

        foreach ($attributesToUpdate as $attributeCode => $newLabel) {
            $english[$attributeCode] = $newLabel['french'];
        }
        $this->idhLogger->info('updated attribute for test' . json_encode($english));
    }



    /**
     * Extract attributes from CSV file and prepare them for update.
     *
     * @param string $csvFile
     * @return array Associative array of attribute codes and their new labels.
     * @throws FileSystemException
     */
    private function getAttributesFromCSV(string $csvFile): array
    {
        $attributes = [];
        if (($handle = $this->file->fileOpen($csvFile, 'r'))) {
            $header = $this->file->fileGetCsv($handle);
            $attributeNameIndex = array_search('attribute_name', $header);
            $attributeStatusIndex = array_search('migration_status', $header);
            $attributeLabelEnglishIndex = array_search('attribue_label_english', $header);
            $attributeLabelFrenchIndex = array_search('attribute_label_french', $header);

            if ($attributeNameIndex !== false && $attributeStatusIndex !== false && $attributeLabelEnglishIndex !== false && $attributeLabelFrenchIndex !==false) {

                $attributes = $this->processCsvAttributes(
                    $handle,
                    $attributeStatusIndex,
                    $attributeNameIndex,
                    $attributeLabelEnglishIndex,
                    $attributeLabelFrenchIndex
                );

            } else {
                $this->idhLogger->info("Required columns 'attribute_name' or 'migration_status' not found in CSV.");
            }
        }
        return $attributes;
    }



    /**
     * Processes attributes from a CSV file to generate a mapping of attribute codes to their labels.
     *
     * @param resource $handle
     * @param string $attributeStatusIndex
     * @param string $attributeNameIndex
     * @param array $specialAttributes
     * @return array
     * @throws FileSystemException
     */
    private function processCsvAttributes(
        $handle,
        string $attributeStatusIndex,
        string $attributeNameIndex,
        string $attributeLabelEnglishIndex,
        string $attributeLabelFrenchIndex
    ): array
    {
        $attributes = [];

        while (($data = $this->file->fileGetCsv($handle)) !== false) {
            if (strtolower($data[$attributeStatusIndex] ?? '') === 'y') {
                $attributeCode = strtolower($data[$attributeNameIndex]);
                $labelEnglish = $data[$attributeLabelEnglishIndex];
                $labelFrench = $data[$attributeLabelFrenchIndex];
                $attributes[$attributeCode] = [
                    'english' => $labelEnglish,
                    'french' => $labelFrench
                ];
            }
        }

        return $attributes;
    }










}
