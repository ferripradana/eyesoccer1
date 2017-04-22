<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Player extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->_prepare_basic_auth(); //Uncomment to use basic
        $this->methods['index_get']['limit'] = 500; 
         $this->load->model('player_model');
    }

    public function index_get()
    {
        $id = $this->get('id');

        $offset = -1;
        $limit = -1;

        $key = '';
        if (!empty($this->get('key'))) {
            $key = (string)$this->get('key');
        }

        $s = '';
        if (!empty($this->get('s'))) {
            $s = (string) $this->get('s');
        }

        if (!empty($this->get('limit'))) {
             $limit = (string)$this->get('limit');
        }
        $page = 1;
        if (!empty($this->get('page'))) {
            $offset = ($this->get('page')  == 1) ? 0 : ($this->get('page')  * $limit) - $limit;
            $page = $this->get('page') ;
        }

        // echo $offset; die();

        if ($id === NULL)
        {
            $players = $this->player_model->get_all_limit_page($limit,$offset, $page, $key, $s);
            if ($players)
            {
                $this->response($players, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No players were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

        else {
            $id = (int) $id;

            if ($id <= 0)
            {
                $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }

        
            $player = $this->player_model->get_by_id($id);


            if (!empty($player))
            {
                $this->set_response($player, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'User could not be found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }

 

   

}
