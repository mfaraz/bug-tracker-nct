<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fact extends CI_Model {
    
    public function GetAllLabels( $param = null )
    {
        try {
            
            if ( $param != null ) {
                if ( is_int($param) ) {
                     $this->db->where('id',$param);
                } elseif ( is_string($param) )  {
                    $this->db->like('name',$param);
                }
            }
                
                
            $result = $this->db
                            ->get('fact_labels');
                            
            $sev_array = array();
            
            foreach( $result->result() as $sev ) {
                $sev_array[] = array(
                    'id'    => $sev->id,
                    'name'  => $sev->name
                );
            }
            
            return $sev_array;
            
        }catch(Exception $e) {
            throw $e;
        }
    }
    
    public function GetAllSeverity()
    {
        try {
            
            $result = $this->db
                            ->where('is_active',1)
                            ->get('fact_severity');
                            
            $sev_array = array();
            
            foreach( $result->result() as $sev ) {
                $sev_array[] = array(
                        
                            'id'    => $sev->id,
                            'name'  => $sev->name
                        );
            }
            
            return $sev_array;
            
        }catch(Exception $e) {
            throw $e;
        }
    }
    
    public function GetAllStatus()
    {
        $result = $this->db
                        ->select('fact_status.id',FALSE)
                        ->select('fact_status.name',FALSE)
                        ->select('fact_status.description',FALSE)
                        ->select('fact_status.status_group_id',FALSE)
                        ->select('fact_status_groups.group_name')
                        ->join('fact_status_groups','fact_status.status_group_id = fact_status_groups.id','inner')
                        ->where('fact_status.is_active',1)
                        ->where('fact_status_groups.is_active',1)
                        ->order_by('fact_status_groups.id','ASC')
                        ->get('fact_status');
        
        $status_array = array();
        
        foreach( $result->result() as $status )
        {
            $status_array[$status->group_name][] = array('id'=>$status->id,'name'=>$status->name,'description'=>$status->description);
        }
        
        return $status_array;
        
    }
    
    public function GetAllType()
    {
        $result = $this->db
                        ->select('fact_types.id',FALSE)
                        ->select('fact_types.name',FALSE)
                        ->select('fact_types.description',FALSE)
                        ->select('fact_types.group_type_id',FALSE)
                        ->select('fact_group_types.name AS `group_name`',FALSE)
                        ->join('fact_group_types','fact_types.group_type_id = fact_group_types.id','inner')
                        ->where('fact_types.is_active',1)
                        ->where('fact_group_types.is_active',1)
                        ->order_by('fact_group_types.id','ASC')
                        ->get('fact_types');
        
        $type_array = array();
        
        foreach( $result->result() as $type )
        {
            $type_array[$type->group_name][] = array('id'=>$type->id,'name'=>$type->name,'description'=>$type->description);
        }
        
        return $type_array;
        
    }
    
    public function GetStatusById($id = 0)
    {
        
        $result = $this->db->where('id',$id)->get('fact_status',1);
        
        if ( $result->num_rows() > 0 ) {
            
            return $result->row();
            
        } else {
            
        }
    }
}