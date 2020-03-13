<?php

// @todo include all elements dynamically.
require 'vendor/autoload.php';
require 'src/TaxonomyTermsMapping.php';
require 'src/AssetsMapping.php';

/*
 * $argv[1] => Name of the project. f.e. easme.
 */
if (!isset($argv[1])) {
  print 'You must indicate the name of the project.';
  exit;
}

// Generate the taxonomy term mapping file.
try {
  $taxonomy = new TaxonomyTermsMapping($argv[1]);
  $taxonomy->generate();

  $assets = new AssetsMapping($argv[1]);
  $assets->generate();
}
catch (\Exception $e) {
  print $e->getMessage();
}

// Generate the asset term mapping file.