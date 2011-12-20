<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bugs extends CI_Controller {
    
    private $_limit;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('ajaxify');
        
        $this->_limit = 5;
    }
    
    public function index()
    {
        $this->listing();
    }
    
    public function listing( $page = 0, $search = '' )
    {
        $this->lang->load('form');
        $this->load->model('issue');
        $this->load->library('pagination');
        

        $config['base_url']         = site_url('bugs/listing/');
        $config['total_rows']       = $this->issue->GetTotalIssue();
        $config['per_page']         = $this->_limit; 
        $data['currentpage']        = $page;
        $this->pagination->initialize($config);
        
    
        $data['issues'] = $this->issue->GetAll($page,$this->_limit);
        
        $data['originalUrl']    = $this->uri->uri_string();
        
        $this->_load_listings( $data );
    }
    
    private function _load_listings( $data )
    {
         $this->template
                ->title('Sassy Dumpling', 'Bug Tracker')
                ->prepend_metadata('<script src="'.base_url().'resources/js/jquery.tokeninput.js"></script>')
                ->set_partial('header', 'partials/header')
                ->set_partial('blue_header','partials/blue-header')
                ->set_partial('footer','partials/footer')
                ->build('list-issue',$data);
    }
    
    public function issue( $issue_id = 0, $page = 0 )
    {
        $this->load->model('issue');
        $this->lang->load('form');
        
        $data['issue']          = $this->issue->GetIssue( $issue_id );
        $data['status']         = $this->issue->GetStatus( $issue_id );
        $data['attachment']     = $this->issue->GetAttachment( $issue_id );
        $data['comment']        = $this->issue->GetComment( $issue_id );
        $data['currentpage']    = $page;
        
        if ( $this->input->post('p_url') ) {
            $data['p_url'] = $this->input->post('p_url');
        } else {
            $data['p_url'] = 'bugs';
        }
        
        if ( ! isAjax() ) {
            
            $this->load->library('pagination');

            $config['uri_segment']      = 4;
            
            $config['base_url']         = site_url('bugs/listing/');
            $config['total_rows']       = $this->issue->GetTotalIssue();
            $config['per_page']         = $this->_limit; 
            
            $this->pagination->initialize($config);
            
            $data['originalUrl']        = site_url('bugs/listing/'.$page);
            
            $data['issues'] = $this->issue->GetAll($page, $this->_limit);
            
            $data['issue_view'] = $this->load->view('view-issue',$data,true);
            
            $this->_load_listings( $data );
            
        } else {
            
            $this->load->view('view-issue',$data);
            
        }
        
    }
    
    public function create()
    {
        try
        {
            $this->load->model('user');
            $this->load->model('fact');
            $this->load->model('issue');
            $this->lang->load('form');
            
            
            $data = array();
            $data['users']      = $this->user->GetAll();
            $data['status']     = $this->fact->GetAllStatus();
            $data['severity']   = $this->fact->GetAllSeverity();
            $data['type']       = $this->fact->GetAllType();
            $data['p_url']          = $this->input->post('p_url');
            
            if ( ! isAjax() ) {
                
                $data['issue_view'] = $this->load->view('create-issue',$data,true);
                $data['currentpage'] = 0;
                
                $data['issues']             = $this->issue->GetAll(0,$this->_limit);
                $data['originalUrl']        = site_url('bugs/listing/');
                $this->load->library('pagination');

                $config['uri_segment']      = 3;
                
                $config['base_url']         = site_url('bugs/listing/');
                $config['total_rows']       = $this->issue->GetTotalIssue();
                $config['per_page']         = $this->_limit; 
                
                $this->pagination->initialize($config);
                
                $this->template
                        ->title('Sassy Dumpling', 'Bug Tracker')
                        ->prepend_metadata('<script src="'.base_url().'resources/js/jquery.tokeninput.js"></script>')
                        ->set_partial('header', 'partials/header')
                        ->set_partial('blue_header','partials/blue-header')
                        ->set_partial('footer','partials/footer')
                        ->build('list-issue',$data);
                        
            } else {
                $this->load->view('create-issue',$data);
            }
        }
        catch(Exception $e)
        {
            
        }
    }
    
    public function insert()
    {
        
        
        $this->load->model('issue');
        
        $this->lang->load('form');
        
        $config = array(
                array(
                    'field' => 'issue_summary', 
                    'label' => 'lang:issue_summary', 
                    'rules' => 'required|min_length[5]'
                ),
                array(
                    'field' => 'issue_description', 
                    'label' => 'lang:issue_description', 
                    'rules' => 'required'
                ),
                array(
                    'field' => 'issue_status', 
                    'label' => 'lang:issue_status', 
                    'rules' => 'required'
                ),   
                array(
                    'field' => 'issue_owner', 
                    'label' => 'lang:issue_owner', 
                    'rules' => 'required'
                ),
                array(
                    'field' => 'issue_severity',
                    'label' => 'lang:issue_severity',
                    'rules' => 'required'
                )
            );
        
        $this->form_validation->set_rules($config);
        
        if ( $this->form_validation->run() ) {
            
            $cc         = $this->input->post('issue_cc');
            $label      = $this->input->post('issue_label');
            $issue_cc       = array();
            $issue_label    = array(); 
            if ( $cc ) {
                $issue_cc = explode(',',$cc);
            }
            
            if ( $label ) {
                $issue_label = explode(',',$label);
            }
            
            
            
            $data = array(
                'issue'         => array(
                                    'summary'       => $this->input->post('issue_summary'),
                                    'description'   => $this->input->post('issue_description'),
                                    'owner_id'      => $this->input->post('issue_owner'),
                                    'serevity_id'   => $this->input->post('issue_severity'),
                                    'type_id'       => $this->input->post('issue_type')
                                ),
                
                'status'        => array('status_id'=>$this->input->post('issue_status')),
                'cc'            => array('user_id'  =>$issue_cc),
                'label'         => array('label'    =>$issue_label),
                'attachment'    => array('attach'   =>$this->_upload_files())
            );
            
            $this->issue->AddIssue($data);
            
            redirect('bugs');
            
        } else {
            $this->create();
        }
        
        
    }
    
    
    private function _upload_files()
    {
        $uploaded_data = array();
        
        $config['upload_path'] = './resources/uploads/';
        $config['allowed_types'] = '*';
        $config['encrypt_name'] = true;
		$this->load->library('upload', $config);
        
        foreach($_FILES as $key=>$value)
        {
            $this->upload->initialize($config);
            $this->upload->do_upload($key);
            
            $uploaded_data[] = $this->upload->data();
            
        }
        
        return $uploaded_data;

    }
    
}