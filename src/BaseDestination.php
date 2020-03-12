<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class BaseDestination {

  const DESTINATION = 'destination/';
  const TEMPLATES = 'templates/';
  const SOURCES = 'sources/';

  public $name;

  protected $spreadsheet;

  public function __construct() {
    $this->spreadsheet = new Spreadsheet();
    $this->name = 'unnamed.xlsl';
  }

  /**
   * Save the document.
   */
  public function save() {
    $writer = new Xlsx($this->spreadsheet);
    $writer->save(self::DESTINATION . $this->name);
  }

  /**
   * Save the $value in the $cell.
   *
   * @param string $cell
   *   The cell. Eg. A1.
   * @param string $value
   *   The value to be saved.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  public function saveInCell($cell, $value) {
    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->setCellValue($cell, $value);
  }
}