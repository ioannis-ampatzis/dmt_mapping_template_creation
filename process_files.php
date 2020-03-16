<?php

// @todo include all elements dynamically.
require 'vendor/autoload.php';
require 'src/TaxonomyTermsMapping.php';
require 'src/AssetsMapping.php';
require 'src/ContentTypeMapping.php';

/*
 * $argv[1] => Name of the project. f.e. easme.
 */
if (!isset($argv[1])) {
  print 'You must indicate the name of the project.';
  exit;
}

// Generate the taxonomy term mapping file.
try {
  //@todo uncomment this.
//  $taxonomy = new TaxonomyTermsMapping($argv[1]);
//  $taxonomy->generate();
//
//  $assets = new AssetsMapping($argv[1]);
//  $assets->generate();

  $content = new ContentTypeMapping($argv[1]);
  $content->generate();
}
catch (\Exception $e) {
  print $e->getMessage();
}

// Generate the asset term mapping file.