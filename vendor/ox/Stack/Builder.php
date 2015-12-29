<?php namespace ox\stack;

  class Builder {
	  
    public function __construct() {
      
    }
    
    public function unshift() {}
    
    public function push() {}
    
    public function resolve() {
      
      $middlewares = array();
      
      
      return new StackedHttpKernel($middlewares);
    }
    
  } //class