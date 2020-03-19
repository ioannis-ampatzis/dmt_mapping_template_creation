<?php

require_once 'BaseDestination.php';

/**
 * Base class for Entity mapping files.
 *
 * Class BaseEntityMapping
 */
class EntityMapping extends BaseDestination {

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
   * The name of the entity in the sheets.
   *
   * @var string
   */
  protected $entityName;

  /**
   * AssetsMapping constructor.
   *
   * @param string $siteName
   */
  public function __construct($config, $name, $entityName, $siteName = '') {
    parent::__construct($config, $siteName);

    $this->name = $name;
    $this->entityName = $entityName;
    $this->currentRow = 3;
  }

  /**
   * Execute the file generation.
   *
   * @throws \Exception
   */
  public function generate() {
    $this->initialize();

    $this->generate_inventory();
    $this->generate_bundle_sheets();
    $this->save();
  }

  /**
   * Generate the inventory sheet.
   *
   * @throws \Exception
   */
  protected function generate_inventory() {
    // Locate the place where the File entity describe the bundles.
    while ($data = fgetcsv($this->entityBundleFile, 1000, ',')) {
      if ($data[0] == $this->entityName){
        break;
      };
    }

    if (!$data) {
      throw new Exception(sprintf('No bundle founds for %s entity', $this->entityName));
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

    // Select the summary worksheet.
    $this->spreadsheet->setActiveSheetIndexByName('D7 Inventory');

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

      /*
       * sheet_name maximum size is 31 char, ensuring that it has a valid length.
       */
      $bundles[$label] = [
        'machine_name' => $machine_name,
        'label' => $label,
        'count' => $count,
        'original' => $bundle[2],
        'sheet_name' => substr('D7 - ' . $bundle[2], 0, 30),
      ];
    }

    // sort it alphabetically.
    sort($bundles);
    $this->bundles = $bundles;
  }

  /**
   * Generate bundle sheets.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \Exception
   */
  protected function generate_bundle_sheets() {

    // First tab is in the position 2.
    $position = 1;
    foreach ($this->bundles as $bundle) {

      // Skip bundles without any content.
      if ($bundle['count'] == 0) {
        continue;
      }

      // Create the sheet in the correct position.
      $worksheet = $this->spreadsheet->createSheet($position);
      $position++;

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
        if ($data[0] == $this->entityName) {
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
}