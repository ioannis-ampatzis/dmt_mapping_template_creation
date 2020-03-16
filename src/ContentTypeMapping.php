<?php

require_once 'BaseEntityMapping.php';

/**
 * Class ContentTypeMapping
 */
class ContentTypeMapping extends BaseEntityMapping {

  /*
   * Template name and base name for the generated file.
   */
  const NAME = 'content_type_mapping_template.xlsx';

  /**
   * Entity name in the sheets.
   */
  const ENTITY_NAME = 'Content (node)';

  /**
   * ContentTypeMapping constructor.
   *
   * @param string $prefix
   */
  public function __construct($prefix = '') {
    parent::__construct($prefix);

    $this->name = self::NAME;
    $this->entityName = self::ENTITY_NAME;
  }
}