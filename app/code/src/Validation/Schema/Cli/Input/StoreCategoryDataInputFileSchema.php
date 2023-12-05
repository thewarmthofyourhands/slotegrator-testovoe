<?php

declare(strict_types=1);

namespace App\Validation\Schema\Cli\Input;

class StoreCategoryDataInputFileSchema
{
    public const SCHEMA = array (
  'type' => 'array',
  'items' => 
  array (
    'type' => 'object',
    'properties' => 
    array (
      'title' => 
      array (
        'type' => 'string',
        'minLength' => 3,
        'maxLength' => 12,
        'description' => 'Title of category',
        'example' => 'Category 1',
      ),
      'eId' => 
      array (
        'type' => 
        array (
          0 => 'integer',
          1 => 'null',
        ),
        'minimum' => 1,
        'description' => 'eId',
        'example' => 1,
      ),
    ),
    'required' => 
    array (
      0 => 'title',
      1 => 'eId',
    ),
    'additionalProperties' => false,
  ),
);
}
