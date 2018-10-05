<?php namespace ox\view;

class View {

  protected $content;
  protected $row_content;
  protected $file_extension;
  protected $error;
  protected $path;
  protected $cachepath;


  function __construct() {

    //print 'View::__construct() <br>';

    $this->content;
    $this->row_content;
    $this->file_extension = '.twig.html';
    $this->path = ROOT_DIR . '/app/views';
    $this->cachepath = ROOT_DIR . '/app/cache/views';

  }

  public function make( $viewname, $parameters = array() ) {

    //print 'View::make('. $viewname .', ['. implode(', ', $parameters) .']) <br>';

    $filename = $viewname . $this->file_extension;

    $this->content = $this->render($filename,  $parameters);

    return $this->getContent();
  }


  public function render($filename, $data = array()) {

    //print 'View::render() <br>';
    $app = app();


    //set helpher variable 'u' to REQUEST_URI
      $data['u'] = url();
      $data['user']   = \Auth::user();
      $data['config'] = $app->config;

    $loader = new \Twig_Loader_Filesystem(  $this->path );
    //$twig = new \Twig_Environment($loader, array( 'cache' => $this->cachepath ) );
    $twig = new \Twig_Environment($loader, array( 'cache' => false ) );

    $template = $twig->loadTemplate( $filename );
    return $template->render( $data );



  }

  public function getPath() {
    return $this->path;
  }

  public function setPath($path) {
    $this->path = $path;
  }

  public function getContent() {
  	return $this->content;
  }

  public function setContent($content) {
  	$this->content = $content;
  }

}
