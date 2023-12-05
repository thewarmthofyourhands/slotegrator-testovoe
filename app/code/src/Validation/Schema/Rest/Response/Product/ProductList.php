<?php

declare(strict_types=1);

namespace App\Validation\Schema\Rest\Response\Product;

class ProductList
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
        'type' => 'integer',
        'minimum' => 1,
        'description' => 'eId',
        'example' => 1,
      ),
      'categories' => 
      array (
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
              'type' => 'integer',
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
        'description' => 'List of category',
        'example' => 
        array (
          0 => 
          array (
            'id' => 1,
            'eId' => 1,
            'title' => 'Category 1',
          ),
          1 => 
          array (
            'id' => 2,
            'eId' => 2,
            'title' => 'Category 2',
          ),
        ),
      ),
    ),
    'required' => 
    array (
      0 => 'id',
      1 => 'title',
      2 => 'eId',
      3 => 'categories',
    ),
    'additionalProperties' => false,
  ),
);
}
