<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->template
            ->set_partial('header', 'partials/header')
            ->set_partial('blue_header','partials/blue-header')
            ->set_partial('footer','partials/footer');
    }
    
    public function index()
    {
        try
        {
            
            $this->template
                    ->title('Sassy Dumpling', 'Bug Tracker | Home')
                    ->build('home');
                    
        } catch(Exception $e) {
            
        }
        
    }
    
    public function logout()
    {
        $data = array(
            'id'            => '',
            'first_name'    => '',
            'last_name'     => '',
            'email'         => ''
        );
        
        $this->session->unset_userdata($data);
        
        redirect('home');
    }
    
    public function login( $authenticate = false )
    {
        $this->lang->load('user');
        
        if ( $authenticate != false )
        {
            
            $config = array(
                    array(
                        'field' => 'username', 
                        'label' => 'lang:user_username', 
                        'rules' => 'required'
                    ),
                    array(
                        'field' => 'password', 
                        'label' => 'lang:user_password', 
                        'rules' => 'required'
                    )
            );
            
            $this->form_validation->set_rules($config);
            
            if ( $this->form_validation->run() )
            {
                $this->load->model('user');
                
                $data = array(
                    'username'  => $this->input->post('username'),
                    'password'  => $this->input->post('password')
                );
                
                $user = $this->user->Authenticate($data);
                
                if ( is_array($user) )
                {
                    
                    $this->session->set_userdata($user);
                    redirect('home');
                }
                
            }
            
            $this->session->set_flashdata('msg',lang('user_invalid'));
            redirect('home/login');
            
        }

        $this->template
                    ->title('Sassy Dumpling', 'Bug Tracker | Login')
                    ->build('login');
    }
    
}