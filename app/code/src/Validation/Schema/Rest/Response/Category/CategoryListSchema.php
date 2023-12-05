<?php

declare(strict_types=1);

namespace App\Validation\Schema\Rest\Response\Category;

class CategoryListSchema
{
    public const SCHEMA = array (
  'type' => 'array',
  'items' => 
  array (
    'type' => 'object',
    'properties' => 
    array (
      'id' => 
      array (
        'type' => 'integer',
        'minimum' => 1,
        'description' => 'Id of category',
        'example' => 1,
      ),
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
      0 => 'id',
      1 => 'title',
      2 => 'eId',
    ),
    'additionalProperties' => false,
  ),
);
}
