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
        
        $users = $this->user->GetCC( $this->input->post('q') );
        
        $u_array = array();
        
        if ( count($users) > 0 )
        {
            foreach( $users as $u ) {
                $u_array[] = array(
                    'id'    => $u['id'],
                    'name'  => $u['first_name'] . ' ' . $u['last_name']
                );
            }
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
                'user_id'   => $this->session->userdata('id'),
                'comment'   => $this->input->post('issue_comment'),
                'date_added'=> $date,
                'issue_id'  => $this->input->post('issue_id')
            );
            
            $id = $this->issue->AddComment( $data );
            $comment = wordwrap(nl2br($this->input->post('issue_comment')),60,'&#8203;',true);
            
            $name = $this->session->userdata('first_name'). ' ' . $this->session->userdata('last_name');
            
            $issue_id = $this->input->post('issue_id');
            
            $e_data['name']     = $name;
            $e_data['date']     = $date;
            $e_data['issue_id'] = $issue_id;
            $e_data['comment']  = $comment;
            
            $cc                 = $this->issue->GetCC($issue_id);
            $issue              = $this->issue->GetIssue( $issue_id );
            
            $this->load->library('email');
            
            $this->email->initialize(array('mailtype'=>'html'));
            
            $this->email->from('bugs@sassydumpling.com', 'SassyDumpling Bug Tracker');
            
            $this->email->to($issue['owner_email']);

            foreach( $cc as $c  )
            {
                if ( $this->session->userdata('id') != $c['id'] ) {
                    $this->email->cc($c['email']);
                }
            }
            
            $this->email->subject('New Comment on Issue #'.$issue_id);
            $this->email->message($this->load->view('email/new-comment',$e_data,true));
            $this->email->send();
            
            echo json_encode( array('r'=>true,'id'=>$id,'comment'=>$comment,'user'=>$name,'date'=>date("F d, Y g:i a",strtotime($date)) ) );
            
        } else {
            
            echo json_encode( array('r'=>false,'m'=>validation_errors()) );
                
        }
        
    }
    
    public function deleteattachment()
    {
        $config = array(
            array(
                'field' => 'issue_id', 
                'label' => 'lang:issue_id', 
                'rules' => 'required|integer'
            ),
            array(
                'field' => 'attachment_id',
                'label' => 'lang:issue_attachment',
                'rules' => 'required|integer'
            )
        );
        
        $this->form_validation->set_rules($config);
        
        if ( $this->form_validation->run() ) {
            
            $this->db->where('id',$this->input->post('issue_id'))->update('fact_attachments',array('is_active'=>0));
            $this->db->where('issue_id',$this->input->post('issue_id'))->where('attachment_id',$this->input->post('attachment_id'))->delete('dim_issue_attachments');
            echo json_encode( array('r'=>true) );
            
        } else {
            echo json_encode( array('r'=>false,'m'=>validation_errors()) );
        }
    }
    
    public function deleteissue()
    {
        $config = array(
            array(
                'field' => 'issue_id', 
                'label' => 'lang:issue_id', 
                'rules' => 'required|integer'
            )
        );
        
        $this->form_validation->set_rules($config);
        
        if ( $this->form_validation->run() ) {
            
            $this->db->where('id',$this->input->post('issue_id'))->update('fact_issues',array('is_active'=>0));
            
            echo json_encode( array('r'=>true) );
            
        } else {
            echo json_encode( array('r'=>false,'m'=>validation_errors()) );
        }
    }
    
    public function status()
    {
        $config = array(
            array(
                'field' => 'status_id', 
                'label' => 'lang:issue_status', 
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
            
            $this->load->model('fact');
            $this->load->model('user');
            $this->load->model('issue');
            
            $date = date("Y-m-d H:i:s");
            
            $data = array(
                'issue_id'      => $this->input->post('issue_id'),
                'status_id'     => $this->input->post('status_id'),
                'user_id'       => $this->session->userdata('id'),
                'date_added'    => $date
            );
            
            $res = $this->issue->AddIssueStatus( $data );
            
            if ($res)
            {
                $status = $this->fact->GetStatusById( $this->input->post('status_id') );
                
                $status_info = array(
                    'id'            => $status->id,
                    'user'          => $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name'),
                    'name'          => $status->name,
                    'date'          => date("F d, Y",strtotime($date)),
                    'complete_date' => date("F d, Y h:i a",strtotime($date)),
                    'description'   => $status->description,
                    'color'         => $status->color
                );
                
                $issue_id = $this->input->post('issue_id');
                
                $s_data = $status_info;
                
                $s_data['issue_id'] = $issue_id;
                
                $cc                 = $this->issue->GetCC($issue_id);
                $issue              = $this->issue->GetIssue( $issue_id );
                
                $this->load->library('email');
                
                $this->email->initialize(array('mailtype'=>'html'));
                
                $this->email->from('bugs@sassydumpling.com', 'SassyDumpling Bug Tracker');
                
    
                $this->email->to($issue['owner_email']);
    
                foreach( $cc as $c  )
                {
                    if ( $this->session->userdata('id') != $c['id'] ) {
                        $this->email->cc($c['email']);
                    }
                }
                
                $this->email->subject('Status updated on Issue #'.$issue_id);
                $this->email->message($this->load->view('email/new-status',$s_data,true));
                $this->email->send();
                
                echo json_encode($status_info);
            }
            
        } else {
            
            echo json_encode( array('r'=>false,'m'=>validation_errors()) );
                
        }
        
    }
    
    
}
