<?php

use Symfony\Component\Yaml\Yaml;

require_once 'ConfigManager.php';

/**
 * Class configManager
 */
class ConfigManager {

  /**
   * The source folder.
   *
   * @var string
   */
  protected $sourceFolder;

  /**
   * The destination folder.
   *
   * @var string
   */
  protected $destinationFolder;

  /**
   * The template folder.
   *
   * @var string
   */
  protected $templateFolder;

  /**
   * Site to generate the files.
   *
   * @var array
   */
  protected $sites;

  /**
   * Entities to process as a file.
   *
   * @var array
   */
  protected $entities;

  /**
   * configManager constructor.
   */
  public function __construct() {
    $config = Yaml::parseFile('config.yml');

    $this->sourceFolder = $config['source'];
    $this->destinationFolder = $config['destination'];
    $this->templateFolder = $config['templates'];

    $this->sites = $config['sites'];
    $this->entities = $config['entities'];
  }

  /**
   * Return the source folder value.
   *
   * @return string
   */
  public function getSourceFolder() {
    return $this->sourceFolder;
  }

  /**
   * Return the destination folder value.
   *
   * @return string
   */
  public function getDestinationFolder() {
    return $this->destinationFolder;
  }

  /**
   * Return the template folder value.
   *
   * @return string
   */
  public function getTemplateFolder() {
    return $this->templateFolder;
  }

  /**
   * Return the list of sites.
   *
   * @return array
   */
  public function getSites() {
    return $this->sites;
  }

  /**
   * Return the list of entities to generate xlsx.
   *
   * @return array
   */
  public function getEntities() {
    return $this->entities;
  }
}