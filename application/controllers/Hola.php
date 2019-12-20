<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Hola extends REST_Controller {

  function __construct() {
    parent::__construct();
  }
    public function index_get(){
        $this->response("Welcome to web services site ".base_url(), REST_Controller::HTTP_OK);
    }
}