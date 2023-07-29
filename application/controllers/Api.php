<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *        http://example.com/index.php/welcome
   *    - or -
   *        http://example.com/index.php/welcome/index
   *    - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see https://codeigniter.com/user_guide/general/urls.html
   */

  public function __construct()
  {
    parent::__construct();

    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));
  }

  //DASHBOARD
  public function index()
  {
    $this->load->view('testing');
  }

  function daftar_customer()
  {
    echo "aaaaa";
    // $curl = curl_init();

    // curl_setopt_array($curl, array(
    //   CURLOPT_URL => 'https://one-editor.omas-mfg.com/api/test_get_customers',
    //   CURLOPT_RETURNTRANSFER => true,
    //   CURLOPT_ENCODING => '',
    //   CURLOPT_MAXREDIRS => 10,
    //   CURLOPT_TIMEOUT => 0,
    //   CURLOPT_FOLLOWLOCATION => true,
    //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //   CURLOPT_CUSTOMREQUEST => 'POST',
    //   CURLOPT_HTTPHEADER => array(
    //     'Cookie: ci_session=d0a90f61596b712cee2d4183ee50392fc3df868e'
    //   ),
    // ));

    // $response = curl_exec($curl);

    // curl_close($curl);
    // print_r($response);
  }
}
