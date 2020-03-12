<?php

include 'BaseDestination.php';

/**
 * Class TaxonomyTermsMapping to generate the file xxx_taxonomy_terms_mapping_template.xlsx.
 */
class TaxonomyTermsMapping extends BaseDestination {

  const NAME = 'taxonomy_terms_mapping_template.xlsx';

  public function __construct($prefix = '') {
    parent::__construct();
    $this->name = $prefix . self::NAME;
  }

  public function generate() {

    $template_path = self::TEMPLATES . self::NAME;
    $template_spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($template_path);
    $source_path = self::SOURCES . 'easme/taxonomy_terms.csv';
    $destination_path = self::DESTINATION . self::NAME;


  }
}