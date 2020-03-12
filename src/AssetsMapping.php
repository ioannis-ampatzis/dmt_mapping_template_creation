<?php

require_once 'src/BaseDestination.php';

/**
 * Class AssetsMapping
 */
class AssetsMapping extends BaseDestination {

  /*
 * Template name and base name for the generated file.
 */
  const NAME = 'assets_mapping_template.xlsx';

  /**
   * A pointer to the entity bundle file source.
   *
   * @var resource
   */
  protected $entityBundleFile;

  /**
   * The current row.
   *
   * @var int
   */
  protected $currentRow;

  /**
   * Array of bundles.
   *
   * @var array
   */
  protected $bundles;

  /**
   * AssetsMapping constructor.
   *
   * @param string $prefix
   */
  public function __construct($prefix = '') {
    parent::__construct($prefix);
    $this->name = self::NAME;


    $this->currentRow = 3;
  }

  /**
   * @throws \Exception
   */
  public function initialize() {
    parent::initialize();

    $this->entityBundleFile = fopen(self::SOURCES . $this->prefix . '/'. self::ENTITY_BUNDLES_CSV, 'r');
    if (!$this->entityBundleFile) {
      throw new Exception('The source file cannot be oppened.');
    }
  }

  /**
   * @throws \Exception
   */
  public function generate() {
    $this->initialize();

    // Complete the inventory sheet.
    $this->generate_inventory();
  }

  /**
   * Complete the inventory page. Always has to be the step 1.
   * @throws \Exception
   */
  protected function generate_inventory() {
    // Locate the place where the File entity describe the bundles.
    while ($data = fgetcsv($this->entityBundleFile, 1000, ',')) {
      if ($data[0] == 'File (file)'){
        break;
      };
    }

    if (!$data) {
      throw new Exception('No bundle founds for File (filE) entity');
    }

    // Save the total count of File entities.
    $this->saveInCell('A2', $data[1]);
    $this->bundles = [];
    while ($data = fgetcsv($this->entityBundleFile, 1000, ',')) {
      if ($data[0] != ''){
        break;
      };
      $this->bundles[] = $data;
    }

    // Process bundles and prepare to be processed.
    $this->processBundles();

    // Write the data inside the excel.
    foreach ($this->bundles as $bundle) {
        $this->saveInCell('B' . $this->currentRow, $bundle['label']);
        $this->saveInCell('C' . $this->currentRow, $bundle['machine_name']);
        $this->saveInCell('E' . $this->currentRow, $bundle['count']);
        $this->currentRow++;
    }
  }

  /**
   * Process bundle and extract the data from the CSV.
   */
  protected function processBundles() {
    $processed = [];
    foreach ($this->bundles as $bundle) {
      $parts = explode(' (', $bundle[2]);
      $label = $parts[0];
      preg_match('#\((.*?)\)#', $bundle[2], $match);
      $machine_name = $match[1];
      $count = $bundle[3];
      $processed[$machine_name] = [
        'machine_name' => $machine_name,
        'label' => $label,
        'count' => $count,
        'original' => $bundle[2],
      ];
    }

    $this->bundles = $processed;
  }
}