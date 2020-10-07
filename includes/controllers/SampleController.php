<?php

class SampleController extends MosAffiliateController {

  protected $variables = [
    'name',
    'age',
    'ingredients',
  ];

  
  protected function name() {
    return 'Martin';
  }


  protected function age() {
    return 42;
  }


  protected function ingredients() {
    return [
      'milk',
      'bananan',
      'peanuts',
      'soap',
    ];
  }

}