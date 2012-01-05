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
    
    public function UpdateIssue($issue_id = 0 , $data = array() )
    {
        $date = date("Y-m-d H:i:s");
    
        $data['issue']['date_modified']     = $date;
        
        $this->db->where('id',$issue_id)->update('fact_issues',$data['issue']);

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
        $result = $this->db->where('issue_id',$data['issue_id'])->order_by('id','DESC')->get('dim_issue_statuses',1);
        
        if ( $result->num_rows() > 0)
        {
            if ( $result->row()->status_id == $data['status_id'] ) {
                return;
            }
        }
        
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
    
    
    
    public function GetAll($page, $limit, $search = 0)
    {
        $where = '';
        
        if ( $search  )
        {
            $where = "AND ( fi.id LIKE '%{$search}%' OR    
                            fi.summary LIKE '%{$search}%' OR
                            fi.description LIKE '%{$search}%' OR
                            fs.name LIKE '%{$search}%' OR
                            fu.first_name LIKE '{$search}' OR
                            ft.name LIKE '%{$search}%' OR
                            status.name LIKE '%{$search}%' OR
                            (SELECT COUNT(*) FROM dim_issue_labels as dl INNER JOIN fact_labels as fl on dl.label_id = fl.id where dl.issue_id = fi.id AND fl.name LIKE '%{$search}%' ) > 0
                            )
            ";
            
        }
        
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
            status.name AS 'status',
            status.color AS 'status_color'
            
            FROM fact_issues AS fi
            INNER JOIN fact_users AS fu ON fi.owner_id = fu.id
            INNER JOIN fact_severity AS fs ON fi.serevity_id = fs.id
            
            LEFT JOIN 
            (SELECT ds.id,ds.issue_id,fss.name,fss.color FROM dim_issue_statuses AS ds INNER JOIN fact_status AS fss ON ds.status_id = fss.id ORDER BY ds.id DESC) AS `status` ON status.issue_id = fi.id
            
            LEFT JOIN fact_types AS ft ON fi.type_id = ft.id
            
            WHERE
            fi.is_active = 1
            {$where}
            GROUP BY fi.id
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
                'color'         => $issue->color,
                'status_color'  => $issue->status_color
            );
        }
        
        return $issue_array;
        
    }
    
    public function GetTotalIssue($search = 0)
    {
        $where = '';
        
        if ( $search  )
        {
            $where = "AND ( fi.id LIKE '%{$search}%' OR    
                            fi.summary LIKE '%{$search}%' OR
                            fi.description LIKE '%{$search}%' OR
                            fs.name LIKE '%{$search}%' OR
                            fu.first_name LIKE '{$search}' OR
                            ft.name LIKE '%{$search}%' OR
                            status.name LIKE '%{$search}%' OR
                            (SELECT COUNT(*) FROM dim_issue_labels as dl INNER JOIN fact_labels as fl on dl.label_id = fl.id where dl.issue_id = fi.id AND fl.name LIKE '%{$search}%' ) > 0
                            )
            ";
        }
        
        $sql = "
            SELECT
            COUNT(*) as `num`
            FROM fact_issues AS fi
            INNER JOIN fact_users AS fu ON fi.owner_id = fu.id
            INNER JOIN fact_severity AS fs ON fi.serevity_id = fs.id
            
            LEFT JOIN 
            (SELECT ds.id,ds.issue_id,fss.name,fss.color FROM dim_issue_statuses AS ds INNER JOIN fact_status AS fss ON ds.status_id = fss.id ORDER BY ds.id DESC) AS `status` ON status.issue_id = fi.id
            
            LEFT JOIN fact_types AS ft ON fi.type_id = ft.id
            
            WHERE
            fi.is_active = 1
            {$where}
            GROUP BY fi.id
        ";
        
        $result = $this->db->query($sql);
        
        return $result->num_rows();
        
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
                fs.color,
                fs.id AS 'severity_id',
                fi.owner_id,
                fi.user_id,
                fi.type_id
                
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
                'color'         => $row->color,
                'severity_id'   => $row->severity_id,
                'user_id'       => $row->user_id,
                'type_id'       => $row->type_id
                
            );
        
        return $data;
        
    }
    
    public function GetStatus( $id = 0 )
    {
        $sql = "SELECT
                *,
                fs.id AS 'status_id'
                FROM dim_issue_statuses AS ds
                INNER JOIN fact_status AS fs ON ds.status_id = fs.id
                INNER JOIN fact_users AS fu ON fu.id = ds.user_id
                WHERE ds.issue_id = '{$id}' ORDER BY ds.id DESC";
                
        $query = $this->db->query($sql);
        
        $status_array = array();
        
        foreach( $query->result() as $row )
        {
            $status_array[] = array('id'=>$row->status_id,'user'=>$row->first_name . ' ' . $row->last_name,'name'=>$row->name,'description'=>$row->description,'color'=>$row->color,'complete_date'=>date("F d, Y h:i a",strtotime($row->date_added)),'date_added'=>date("F d, Y",strtotime($row->date_added)));
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
    
    public function GetLabels( $id = 0 )
    {
        
        $sql = "SELECT fl.id, fl.name FROM dim_issue_labels AS dl INNER JOIN fact_labels AS fl ON dl.label_id = fl.id WHERE dl.issue_id = '{$id}'";
        
        $result = $this->db->query($sql);
        
        $labels = array();
        
        foreach( $result->result() as $label )
        {
            $labels[] = array('id'=>$label->id,'name'=>$label->name);
        }
        
        return $labels;
        
    }
    
    public function GetCC($id = 0)
    {
        $sql = "SELECT 
                fu.id,
                fu.first_name,
                fu.last_name
                FROM dim_issue_cc AS dc
                INNER JOIN fact_users AS fu ON dc.user_id = fu.id
                WHERE dc.issue_id = '{$id}' ";
        
        $result = $this->db->query($sql);
        
        $cc = array();
        
        foreach( $result->result() as $c )
        {
            $cc[] = array('id'=>$c->id,'name'=>$c->first_name. ' '.$c->last_name);
        }
        
        return $cc;
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