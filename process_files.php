<?php

require 'vendor/autoload.php';
// @todo include all elements dynamically.
require 'src/TaxonomyTermsMapping.php';

$taxonomy = new TaxonomyTermsMapping('easme_');
$taxonomy->saveInCell('A1', 'test');
$taxonomy->save();