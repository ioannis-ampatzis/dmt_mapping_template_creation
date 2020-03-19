<?php

require 'vendor/autoload.php';

// @todo include all elements dynamically.
require_once 'src/TaxonomyTermsMapping.php';
require_once 'src/ConfigManager.php';
require_once 'src/EntityMapping.php';

$config = new ConfigManager();

foreach ($config->getSites() as $site_name => $site_label) {
  try {

    // Generate the taxonomy term mapping file.
    $taxonomy = new TaxonomyTermsMapping($config, $site_label, $site_name);
    $taxonomy->generate();

    // Entity files.
    foreach ($config->getEntities() as $files) {

      $entity_processor = new EntityMapping($config, $files['file name'], $files['entity name'], $site_label, $site_name);
      $entity_processor->generate();
    }
  }
  catch (\Exception $e) {
    print $e->getMessage();
  }
}