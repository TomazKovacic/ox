<?php namespace ox\framework;

class mustacheRenderer extends Renderer {

	public $rendereName = 'mustacheRenderer';
	public $engine = 'mustache';
	public $result;
	
	
	function __construct() {

		//$loader = new \SplClassLoader('', realpath(dirname(__FILE__) . '/../Mustache')  );
		//$loader->register();

		//addLog( array('[__construct]: register autoload to mustache',  
        //  'file' => __FILE__, 'line' =>  __LINE__, 'class' => __CLASS__, 'function' => __FUNCTION__ ) ); 

	}
	

	function render($content, $data) {

	
		return 'mustacheRenderer::render forced exit.';

		$r = '';

		$debug = false;
		//$debug = true;

		//print gettype($contentArray);
		//print_r2($content);
		//print_r2($data);

		if( $debug == true ) {
		
			print 'DBG: Content:';
			if(is_array($content) && (count($content)>0)) {
				$r = '[' . implode(', ', $content) .']'; //override;
				//$r = '[' .  gettype($content)  .']'; //override;

			}

			print 'DBG: Data:';
			if(is_array($data) && (count($data)>0)) {
				$r .= '<br>*[' . implode(', ', $data).']'; //append
			}

			$r .= '<hr>';
		}


		//-------------------------------


		$m = new \Mustache_Engine;
		//$r .=  $m->render('Hello {{planet}}', array('planet' => 'World!')); // "Hello World!"

		//-------------------------------

		if( is_array($content)) {

			/*
			* multiple contents
			* 1. check if there is info one element, if it is, use it
			* 2. if not, find 'main' element and compose all
			*/

			if( count($content) == 1) {

				$r .= $m->render(reset($content), $data);

			} else {
				if(isset($content['main'])) {
					$r .= '*mustashe: Content is array, unresolved';

				} else {
					$r .= '*mustashe: Content is array, unresolved';	
				}

			}
			

		} else {
			$r .= $m->render($content, $data);
		}

		$this->result = $r;
		return $r;

	}
}