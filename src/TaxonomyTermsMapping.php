<?php

require_once 'BaseDestination.php';

/**
 * Class TaxonomyTermsMapping to generate the file xxx_taxonomy_terms_mapping_template.xlsx.
 */
class TaxonomyTermsMapping extends BaseDestination {

  /*
   * Template name and base name for the generated file.
   */
  const NAME = 'taxonomy_terms_mapping_template.xlsx';

  /**
  /**
   * The current row.
   *
   * @var int
   */
  protected $currentRow;

  /**
   * TaxonomyTermsMapping constructor.
   *
   * @param string $siteName
   */
  public function __construct($config, $siteName = '') {
    parent::__construct($config, $siteName);
    $this->name = self::NAME;

    // This template starts in A4.
    $this->currentRow = 4;
  }

  /**
   * @throws \Exception
   */
  public function generate() {

    // All action necessaries to perform the execution.
    $this->initialize();

    // The autosize doesn't work very well in this worksheet.
    // $this->setColumnSize('A:E');

    // Skip header rows.
    fgetcsv($this->taxonomyTermFileSource, 1000, ',');
    fgetcsv($this->taxonomyTermFileSource, 1000, ',');

    /*
     * @todo maybe we can map this elements of a better way.
     *
     * File columns:
     *  - [0] Vocabulary => A
     *  - [1] Term ID => B
     *  - [2] Term name => C
     *  - [3] Term language
     *  - [4] Usage (Published entity) => D
     *  - [5] Entity types (Published)
     *  - [6] Usage (Unpublished entity)
     *  - [7] Entity types (Unpublished)
     *  - [8] Term description => E
     */
    while ($data = fgetcsv($this->taxonomyTermFileSource, 1000, ',')) {
      // Unset useless data;
      $this->saveInCell('A' . $this->currentRow, $data[0]);
      $this->saveInCell('B' . $this->currentRow, $data[1]);
      $this->saveInCell('C' . $this->currentRow, $data[2]);
      $this->saveInCell('D' . $this->currentRow, $data[4]);
      $this->saveInCell('E' . $this->currentRow, $data[8]);

      $this->currentRow++;
    }

    $this->save();
  }

}