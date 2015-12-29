<?php namespace ox\stack;

class StackedHttpKernel implements HttpKernelInterface, TerminableInterface
  
  public function __construct() {}

  public function handle(Request $request
