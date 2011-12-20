<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Issue extends CI_Model {
    
    public function AddIssue( $data )
    {
        $date = date("Y-m-d H:i:s");
        
        $data['issue']['date_added']        = $date;
        $data['issue']['date_modified']     = $date;
        
        $this->db->insert('fact_issues',$data['issue']);
        
        $issue_id =  $this->db->insert_id();
        
        $data['status']['issue_id']         = $issue_id;
        $data['status']['date_added']       = $date;
        
        $this->AddIssueStatus( $data['status'] );
        
        $data['cc']['issue_id']             = $issue_id;
        
        $this->AddCC($data['cc']);
        
        $data['label']['issue_id']          = $issue_id;
        
        $this->AddLabels($data['label']);
        
        $data['attachment']['issue_id']     = $issue_id;
        $data['attachment']['date_added']   = $date;
        
        $this->AddAttachment($data['attachment']);
        
    }
    
    public function AddIssueStatus( $data )
    {
        $this->db->insert('dim_issue_statuses',$data);
    }
    
    public function AddCC( $data )
    {
        
        $issue_id = $data['issue_id'];
        
        foreach ( $data['user_id'] as $user_id ) {
            $this->db->insert('dim_issue_cc',array('issue_id'=>$issue_id,'user_id'=>$user_id));
        }
        
    }
    
    public function AddLabels( $data )
    {
        $issue_id = $data['issue_id'];
        
        foreach ( $data['label'] as $label ) {
            
            if ( $this->CheckLabel($label) ) {
                
                $this->db->insert('dim_issue_labels',array('issue_id'=>$issue_id,'label_id'=>$label));
                
            } else if( is_string($label)) {
                
                $this->db->insert('fact_labels',array('name'=>$label));
                
                $label_id = $this->db->insert_id();
                
                $this->db->insert('dim_issue_labels',array('issue_id'=>$issue_id,'label_id'=>$label_id));
            }
            
        }
    }
    
    public function CheckLabel( $id = 0 )
    {
        
        $result = $this->db->where('id',$id)->get('fact_labels',1);
        
        if ( $result->num_rows() > 0 ) {
            return true;
        } else {
            return false;
        }
        
    }
    
    
    public function AddAttachment( $data )
    {
        
        foreach( $data['attach'] as $attach  )
        {
            $this->db->insert('fact_attachments',array('name'=>$attach['orig_name'],'type'=>$attach['file_type'],'md5_name'=>$attach['file_name']));
            
            $attachment_id = $this->db->insert_id();
            
            $this->db->insert('dim_issue_attachments',array('attachment_id'=>$attachment_id,'issue_id'=>$data['issue_id'],'date_added'=>$data['date_added']));
            
        }
        
    }
    
    
    
    public function GetAll($page, $limit)
    {
        
        $sql = "
            SELECT
            fi.id,
            fi.summary,
            fi.description,
            fu.first_name AS 'owner_f_name',
            fu.last_name AS 'owner_l_name',
            fs.name AS 'severity',
            fi.date_added,
            ft.name AS 'type',
            fs.color,
            
            (SELECT fss.`name` FROM dim_issue_statuses AS ds INNER JOIN fact_status AS fss ON ds.status_id = fss.id WHERE ds.issue_id = fi.id ORDER BY ds.id DESC LIMIT 1) AS 'status'
            
            FROM fact_issues AS fi
            INNER JOIN fact_users AS fu ON fi.owner_id = fu.id
            INNER JOIN fact_severity AS fs ON fi.serevity_id = fs.id
            LEFT JOIN fact_types AS ft ON fi.type_id = ft.id
            ORDER BY fi.date_added DESC
            LIMIT {$page}, {$limit}
        ";
        
        
        $result = $this->db->query($sql);
        
        $issue_array = array();
        
        foreach ( $result->result() as $issue )
        {
            $issue_array[] = array(
                'id'            => $issue->id,
                'summary'       => $issue->summary,
                'description'   => $issue->description,
                'owner'         => $issue->owner_f_name . ' ' . $issue->owner_l_name,
                'severity'      => $issue->severity,
                'date_added'    => $issue->date_added,
                'status'        => $issue->status,
                'type'          => $issue->type,
                'color'         => $issue->color
            );
        }
        
        return $issue_array;
        
    }
    
    public function GetTotalIssue()
    {
        
        $query = $this->db->select(" COUNT(*) AS `num`",false)->where('is_active',1)->get('fact_issues')->row();
        
        return $query->num;
        
    }
    
    public function GetIssue( $id = 0 )
    {
        $sql = "
                SELECT
                fi.id,
                fi.summary,
                fi.description,
                fu.id AS 'owner_id',
                fu.first_name AS 'owner_f_name',
                fu.last_name AS 'owner_l_name',
                fs.name AS 'severity',
                fi.date_added,
                ft.name AS 'type',
                fs.color
                
                FROM fact_issues AS fi
                INNER JOIN fact_users AS fu ON fi.owner_id = fu.id
                INNER JOIN fact_severity AS fs ON fi.serevity_id = fs.id
                LEFT JOIN fact_types AS ft ON fi.type_id = ft.id
                WHERE fi.id = '{$id}'
        ";
        
        $row = $this->db->query($sql)->row();
        
        $data = array(
            'id'            => $row->id,
            'summary'       => $row->summary,
            'description'   => $row->description,
            'owner_id'      => $row->owner_id,
            'owner'         => $row->owner_f_name . ' '. $row->owner_l_name,
            'severity'      => $row->severity,
            'date_added'    => $row->date_added,
            'type'          => $row->type,
            'color'         => $row->color
        );
        
        return $data;
        
    }
    
    public function GetStatus( $id = 0 )
    {
        $sql = "SELECT
                *
                FROM dim_issue_statuses AS ds
                INNER JOIN fact_status AS fs ON ds.status_id = fs.id
                WHERE ds.issue_id = '{$id}' ORDER BY ds.id DESC";
                
        $query = $this->db->query($sql);
        
        $status_array = array();
        
        foreach( $query->result() as $row )
        {
            $status_array[] = array('name'=>$row->name,'description'=>$row->description,'color'=>$row->color,'date_added'=>date("F d, Y",strtotime($row->date_added)));
        }
        
        return $status_array;
    }
    
    public function GetAttachment( $id = 0 )
    {
        $sql = "
                SELECT
                fa.*
                FROM dim_issue_attachments AS da 
                INNER JOIN fact_attachments AS fa ON fa.id = da.attachment_id
                WHERE da.issue_id = '{$id}'
            ";
        
        $query = $this->db->query($sql);
        
        $attachment_array = array();
        
        foreach( $query->result() as $row )
        {
            $attachment_array[] = array('id'=>$row->id,'name'=>$row->name,'type'=>$row->type,'file_name'=>$row->md5_name);
        }
        
        return $attachment_array;
    }
    
    public function GetComment( $id = 0 )
    {
        $sql = "
                    SELECT
                    dc.*,
                    fu.first_name,
                    fu.last_name
                    FROM dim_issue_comments AS dc
                    INNER JOIN fact_users AS fu ON fu.id = dc.user_id
                    WHERE
                    dc.issue_id = '{$id}' ORDER BY dc.date_added DESC
                ";
        
        $query = $this->db->query($sql);
        
        $comments = array();
        
        foreach( $query->result() as $row )
        {
            $comments[] = array(
                'id'            => $row->id,
                'user_id'       => $row->user_id,
                'user'          => $row->first_name . ' ' . $row->last_name,
                'comment'       => $row->comment,
                'date_added'    => $row->date_added
            );
        }
        
        return $comments;
        
    } 
    
    public function GetLabel( $id = 0 )
    {
        
    }
    
    public function GetAttachmentById($id = 0)
    {
        
        $query = $this->db->where('id',$id)->get('fact_attachments',1);
        
        if ( $query->num_rows() == 0 ) {
            return false;
        } else {
            return $query->row();
        }
        
    }
    
    public function AddComment( $data )
    {
        
        $this->db->insert('dim_issue_comments',$data);
        
        return $this->db->insert_id();
        
    }
    
}