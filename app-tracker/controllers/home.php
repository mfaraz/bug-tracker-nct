<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        try
        {
            
            $this->template
                    ->title('Sassy Dumpling', 'Bug Tracker')
                    ->set_partial('header', 'partials/header')
                    ->set_partial('blue_header','partials/blue-header')
                    ->set_partial('footer','partials/footer')
                    ->build('home');
        }
        catch(Exception $e)
        {
            
        }
        
    }
    
    
}