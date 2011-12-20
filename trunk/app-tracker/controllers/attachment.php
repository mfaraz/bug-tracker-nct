<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Attachment extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function download( $id = 0 )
    {
        
        $this->load->model('issue');
        
        $attachment = $this->issue->GetAttachmentById( $id );
        if ( $attachment ) {
            
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=".$attachment->name);
            header("Content-Type: ".$attachment->type);
            header("Content-Transfer-Encoding: binary");
            
            readfile(base_url().'resources/uploads/'.$attachment->file_name);
                
        } else {
            redirect('home');
        }
        
    }
    
}