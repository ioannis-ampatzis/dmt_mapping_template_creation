<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Class BaseDestination
 */
abstract class BaseDestination {

  /*
   * The taxonomy terms csv file.
   */
  const TAXONOMY_TERMS_CSV = 'taxonomy_terms.csv';

  /**
   * The entity bundles csv file name.
   */
  const ENTITY_BUNDLES_CSV = 'entity_bundles.csv';

  /**
   * The entity properties csv file name.
   */
  const ENTITY_PROPERTIES_CSV = 'entity_properties.csv';

  /**
   * A pointer to the taxonomy term file source.
   *
   * @var resource
   */
  protected $taxonomyTermFileSource;

  /**
   * A pointer to the entity bundle file source.
   *
   * @var resource
   */
  protected $entityBundleFile;

  /**
   * A pointer to the entity properties file source.
   *
   * @var resource
   */
  protected $entityPropertiesFile;

  /**
   * Header format array.
   *
   * @see https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes/#formatting-cells
   */
  const HEADER_FORMAT =  [
    'font' => [
      'bold' => true,
    ],
    'alignment' => [
      'vertical' => Alignment::HORIZONTAL_CENTER,
    ],
    'fill' => [
      'fillType' => Fill::FILL_GRADIENT_LINEAR,
      'color' => [
        'argb' => 'cccccc',
      ],
    ],
  ];

  /**
   * Name of the element that we're generating.
   *
   * To be initialized in the child classes.
   *
   * string @var
   */
  public $name;

  /**
   * The current spreadsheet.
   *
   * @var Spreadsheet
   */
  protected $spreadsheet;

  /**
   * Destination site name.
   *
   * @var string
   */
  protected $siteName;

  /**
   * The current config.
   *
   * @var \ConfigManager
   */
  protected $config;

  /**
   * Site Label.
   *
   * @var string
   */
  protected $siteLabel;

  /**
   * BaseDestination constructor.
   *
   * @param \ConfigManager $config
   *   The current configuration.
   * @param string $siteLabel
   *   The site label.
   * @param string $siteName
   *   The site machine name.
   */
  public function __construct($config, $siteLabel = '', $siteName = '') {
    $this->config = $config;
    $this->siteName = $siteName;
    $this->siteLabel = $siteLabel;
  }

  /**
   * Save the document.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   * @throws \Exception
   */
  public function save() {
    if (!$this->spreadsheet) {
      throw new Exception('The spreadsheet is not properly loaded. It cannot be saved.');
    }

    $destination = $this->config->getDestinationFolder() . $this->siteName . '/';
    if (!file_exists($destination)) {
      mkdir($destination, 0777, true);
    }

    $writer = new Xlsx($this->spreadsheet);
    $writer->save($destination . $this->siteName . '_' . $this->name);
  }

  /**
   * Save the $value in the $cell.
   *
   * @param string $cell
   *   The cell. Eg. A1.
   * @param string $value
   *   The value to be saved.
   *
   * @throws \Exception
   */
  public function saveInCell($cell, $value) {
    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->setCellValue($cell, $value);
  }

  /**
   * Copy the template into the new generated file in the destination.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
   * @throws \Exception
   */
  protected function copy_template() {

    $destination = $this->config->getDestinationFolder() . $this->siteName . '/';
    if (!file_exists($destination)) {
      mkdir($destination, 0777, true);
    }

    $file = $this->config->getTemplateFolder() . $this->name;
    $newfile = $destination . $this->siteName . '_' . $this->name;

    if (!copy($file, $newfile)) {
      throw new Exception('The template cannot be copied.');
    }
    $this->spreadsheet = IOFactory::load($newfile);;
  }

  /**
   * Initialize the process before generate the file.
   * @throws \Exception
   */
  protected function initialize() {

    $this->taxonomyTermFileSource = fopen($this->config->getSourceFolder() . $this->siteName . '/'. self::TAXONOMY_TERMS_CSV, 'r');
    if (!$this->taxonomyTermFileSource) {
      throw new Exception('The source file cannot be opened.');
    }

    $this->entityBundleFile = fopen($this->config->getSourceFolder() . $this->siteName . '/'. self::ENTITY_BUNDLES_CSV, 'r');
    if (!$this->entityBundleFile) {
      throw new Exception('The source file cannot be opened.');
    }

    $this->entityPropertiesFile = fopen($this->config->getSourceFolder() . $this->siteName . '/'. self::ENTITY_PROPERTIES_CSV, 'r');
    if (!$this->entityPropertiesFile) {
      throw new Exception('The source file cannot be opened.');
    }

    $this->copy_template();
  }

  /**
   * Set the header format (bold, colors, etc) to a cell.
   *
   * @param string $cells
   *    The cell or range to apply the format.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  protected function setHeaderFormatToCell($cells) {
    $this->spreadsheet->getActiveSheet()->getStyle($cells)->applyFromArray(self::HEADER_FORMAT);
    $columns = preg_replace("/[0-9]/", "", $cells);
    $this->setColumnSize($columns);
  }

  /**
   * Set auto width to the indicated columns.
   *
   * @param string $columns
   *   Column or range to apply column width.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  protected function setColumnSize($columns) {

    // It's a single column.
    $parts = explode(':', $columns);
    if (!isset($parts[1])) {
      $this->spreadsheet->getActiveSheet()->getColumnDimension($columns)->setAutoSize(true);
      return;
    }

    // It's a range.
    $letters = range($parts[0], $parts[1]);
    foreach ($letters as $letter) {
      $this->spreadsheet->getActiveSheet()->getColumnDimension($letter)->setAutoSize(true);
    }
  }
}