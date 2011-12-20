<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('ajaxify');
        if ( ! isAjax() ) {
            redirect('bugs');
        }
        
        
    }

    public function label()
    {
        $this->load->model('fact');
        
        $labels   = $this->fact->GetAllLabels( $this->input->post('q') );
        echo json_encode($labels);
    }
    
    public function cc()
    {
        $this->load->model('user');
        
        $users = $this->user->GetAll( $this->input->post('q') );
        
        $u_array = array();
        
        foreach( $users as $u ) {
            $u_array[] = array(
                'id'    => $u['id'],
                'name'  => $u['first_name'] . ' ' . $u['last_name']
            );
        }
        
        echo json_encode($u_array);
        
    }
    
    public function comment()
    {
        $config = array(
            array(
                'field' => 'issue_comment', 
                'label' => 'lang:issue_comment', 
                'rules' => 'required'
            ),
            array(
                'field' => 'issue_id', 
                'label' => 'lang:issue_id', 
                'rules' => 'required|integer'
            )
        );
        
        $this->form_validation->set_rules($config);
        
        if ( $this->form_validation->run() ) {
            
            $this->load->model('issue');
            $date = date("Y-m-d H:i:s");
            $data = array(
                'user_id'   => 1,
                'comment'   => $this->input->post('issue_comment'),
                'date_added'=> $date,
                'issue_id'  => $this->input->post('issue_id')
            );
            
            $id = $this->issue->AddComment( $data );
            $comment = wordwrap(nl2br($this->input->post('issue_comment')),60,'&#8203;',true);
            
            echo json_encode( array('r'=>true,'id'=>$id,'comment'=>$comment,'user'=>'Ervin Musngi','date'=>date("F d, Y g:i a",strtotime($date)) ) );
            
        } else {
            
            echo json_encode( array('r'=>false,'m'=>validation_errors()) );
                
        }
        
    }
    
    
}
