<?php

require 'vendor/autoload.php';

// @todo include all elements dynamically.
require 'src/TaxonomyTermsMapping.php';

/*
 * $argv[1] => Name of the project. f.e. easme.
 */
if (!isset($argv[1])) {
  print 'You must indicate the name of the project.';
  exit;
}

$taxonomy = new TaxonomyTermsMapping($argv[1]);
$taxonomy->generate();