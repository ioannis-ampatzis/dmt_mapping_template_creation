<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

abstract class BaseDestination {

  const DESTINATION = 'destination/';
  const TEMPLATES = 'templates/';
  const SOURCES = 'sources/';

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

  // To be initialized in the child classes.
  public $name;

  /**
   * @var Spreadsheet
   */
  protected $spreadsheet;

  protected $prefix;

  public function __construct($prefix = '') {
    $this->prefix = $prefix;
  }

  /**
   * Save the document.
   */
  public function save() {
    if (!$this->spreadsheet) {
      throw new Exception('The spreadsheet is not propertly loaded. It cannot be saved.');
    }

    $writer = new Xlsx($this->spreadsheet);
    $writer->save(self::DESTINATION . $this->prefix . '_' . $this->name);
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
   */
  protected function copy_template() {
    $file = self::TEMPLATES . $this->name;
    $newfile = self::DESTINATION . $this->prefix . '_' . $this->name;

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
    $this->copy_template();
  }
}