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
   * A pointer to the entity properties file source.
   *
   * @var resource
   */
  protected $entityPropertiesFile;

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

    $this->entityPropertiesFile = fopen(self::SOURCES . $this->prefix . '/'. self::ENTITY_PROPERTIES_CSV, 'r');
    if (!$this->entityPropertiesFile) {
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
    $this->generate_bundle_sheets();
    $this->save();
  }

  /**
   * Generate bundle sheets.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \Exception
   */
  protected function generate_bundle_sheets() {

    foreach ($this->bundles as $bundle) {
      // Create the sheet always in the first second position (after the summary).
      $worksheet = $this->spreadsheet->createSheet(1);

      // Set the name and set as a current sheet to work.
      $worksheet->setTitle($bundle['sheet_name']);
      $this->spreadsheet->setActiveSheetIndexByName($bundle['sheet_name']);

      // Create the header.
      $this->saveInCell('A1', 'Propery ID');
      $this->saveInCell('B1', 'Property Label');
      $this->saveInCell('C1', 'Property type');
      $this->saveInCell('D1', 'Property translatable');
      $this->saveInCell('E1', 'Property required');
      $this->saveInCell('F1', 'Field cardinality');
      $this->saveInCell('G1', 'Count of Populated fields');
      $this->setHeaderFormatToCell('A1:G1');

      // Read until the File description.
      while ($data = fgetcsv($this->entityPropertiesFile, 1000, ',')) {
        if ($data[0] == 'File (file)') {
          break;
        }
      }

      // Read until the current bundle.
      while ($data = fgetcsv($this->entityPropertiesFile, 1000, ',')) {
        if ($data[2] == $bundle['original']) {
          break;
        }
      }

      /*
       * @todo maybe we can map this elements of a better way.
       *
       * File columns:
       * [0]- Entity
       * [1]- Entity_count
       * [2]- Bundle
       * [3]-	Bundle_count
       * [4]-	Property_id => A
       * [5]-	Property_label => B
       * [6]-	Property_type => C
       * [7]-	Property_translatable => D
       * [8]-	Property_required => E
       * [9]-	Property_field_cardinality => F
       * [10]-	Property_count => G
       */
      $this->currentRow = 2;
      while ($data = fgetcsv($this->entityPropertiesFile, 1000, ',')) {
        // A $data[4] is empty means that the current bundle is finished.
        if ($data[4] == '') {
          break;
        }

        // Fill the document.
        $this->saveInCell('A' . $this->currentRow, $data[4]);
        $this->saveInCell('B' . $this->currentRow, $data[5]);
        $this->saveInCell('C' . $this->currentRow, $data[6]);
        $this->saveInCell('D' . $this->currentRow, $data[7]);
        $this->saveInCell('E' . $this->currentRow, $data[8]);
        $this->saveInCell('F' . $this->currentRow, $data[9]);
        $this->saveInCell('G' . $this->currentRow, $data[10]);

        $this->currentRow++;
      }

      // Rewind the pointer to allow search the next bundle.
      rewind($this->entityPropertiesFile);
    }
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
        'sheet_name' => 'D7 - ' . $bundle[2],
      ];
    }

    $this->bundles = $processed;
  }
}