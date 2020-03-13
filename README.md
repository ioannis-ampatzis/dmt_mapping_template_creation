#About

This is a little php-script tool to generate some xlsx files based on some CSV files extracted 
by the drush command `drush dmt-se:export-all --light_version=1` in a drupal 7 site.

## Requeriment
 - Php > 7.0

## Install
No actions required.

## Steps to use this script
 1. Set inside /sources folder a folder with the name of the project, f.e: easme
 2. Set inside this just created folder all extraction CSV provided by the drush command, should be a list like:
   - entity_bundles.csv
   - entity_properties.csv
   - fields.csv*
   - modules.csv*
   - taxonomy_terms.csv   
* Currently these files are useless but they are generated by the command.
  
 3. Execute the command as a php script, with the name of the proyect as a param, f.e.
    `php process_files.php easme` 

 4. Check the /destination folder to check the result.