<?php

declare(strict_types=1);

namespace App\Validation\Schema\Rest\Request\Product;

class EditProductSchema
{
    public const SCHEMA = array (
  'type' => 'object',
  'properties' => 
  array (
    'id' => 
    array (
      'type' => 'integer',
      'minimum' => 1,
      'description' => 'Id of product',
      'example' => 1,
    ),
    'title' => 
    array (
      'type' => 'string',
      'minLength' => 3,
      'maxLength' => 12,
      'description' => 'Title of product',
      'example' => 'Product 1',
    ),
    'price' => 
    array (
      'type' => 'number',
      'multipleOf' => 0.01,
      'minimum' => 0,
      'maximum' => 200,
      'description' => 'Price',
      'example' => 150.21,
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
    'categoriesEId' => 
    array (
      'type' => 'array',
      'uniqueItems' => true,
      'items' => 
      array (
        'type' => 'integer',
        'minimum' => 1,
        'description' => 'eId',
        'example' => 1,
      ),
      'description' => 'List of category eId',
      'example' => 
      array (
        0 => 1,
        1 => 2,
      ),
    ),
  ),
  'required' => 
  array (
    0 => 'id',
    1 => 'title',
    2 => 'price',
    3 => 'eId',
    4 => 'categoriesEId',
  ),
  'additionalProperties' => false,
);
}
