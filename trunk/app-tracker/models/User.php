<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model {
    
    public function Authenticate($data)
    {
        
        try
        {
            
            $this->db->where('email',$data['username']);
            $this->db->where('password',$data['password']);
            
            $result = $this->db->get('fact_users',1);
            
            $user = array();
            
            if ( $result->num_rows() > 0 ) {
                
                $user_row = $result->row();
                
                $user = array(
                    
                    'id'            => $user_row->id,
                    'first_name'    => $user_row->first_name,
                    'last_name'     => $user_row->last_name,
                    'email'         => $user_row->email,
                    'is_admin'      => $user_row->is_admin
                );
                
                return $user;
                
            } else {
                return false;
            }
            
        }
        catch(Exception $e)
        {
            throw $e;
        }
        
    }
    
    public function checkEmail( $id = 0 , $email = '' )
    {
        try
        {
            
            $query = $this->db->where('id !=',$id)->where('email',$email)->get('fact_users',1);
            
            if ( $query->num_rows() > 0 )
            {
                return false;
            }
            else
            {
                return true;
            }
            
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }
    
    public function GetById( $id = 0 )
    {
        try
        {
            
            $this->db->where('id',$id);
                        $result = $this->db->get('fact_users',1);
            
            $user = array();
            
            if ( $result->num_rows() > 0 ) {
                
                $user_row = $result->row();
                
                $user = array(
                    
                    'id'            => $user_row->id,
                    'first_name'    => $user_row->first_name,
                    'last_name'     => $user_row->last_name,
                    'email'         => $user_row->email,
                    'is_admin'      => $user_row->is_admin
                );
                
                return $user;
                
            } else {
                return false;
            }
            
        }
        catch(Exception $e)
        {
            throw $e;
        }
        
    }
    
    public function GetAll( $param = null )
    {
        try
        {
            if ( $param != null ) {
                if( is_int($param) ) {
                    $this->db->where('id',$param);
                } elseif( is_string($param) ) {
                    $this->db->like('first_name',$param)->or_like('last_name',$param);
                }
            }
            
            $result = $this->db->where('is_active',1)->get('fact_users');
            
            $user_array = array();
            
            foreach( $result->result() as $user  ) {
                $user_array[] = array(
                                'id'            => $user->id,
                                'first_name'    => $user->first_name,
                                'last_name'     => $user->last_name
                            );
            }
            
            return $user_array;
            
        } catch(Exception $e) {
            throw $e;
        }
    }
    
    public function GetCC( $param = null )
    {
        try
        {
            if ( $param != null ) {
                if( is_int($param) ) {
                    $this->db->where('id',$param);
                } elseif( is_string($param) ) {
                    $this->db->like('first_name',$param)->or_like('last_name',$param);
                }
            }
            
            $this->db->where('id !=',$this->session->userdata('id'));
            $result = $this->db->where('is_active',1)->get('fact_users');
            
            $user_array = array();
            
            foreach( $result->result() as $user  ) {
                $user_array[] = array(
                                'id'      => $user->id,
                                'first_name'    => $user->first_name,
                                'last_name'     => $user->last_name
                        );
            }
            
            return $user_array;
            
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }
    
    public function GetTotalUser($search = null)
    {
        if ( $search )
        {
            $this->db->or_like('first_name',$search)->or_like('last_name',$search)->or_like('email',$search);
        }
        
        $query = $this->db->select(' COUNT(*) AS `num` ', false)->where('is_active',1)->get('fact_users');
        
        return $query->row()->num;
    }
    
    public function GetAllListing($page = 0, $limit = 20, $search = null)
    {
        
        if ( $search )
        {
            $this->db->or_like('first_name',$search)->or_like('last_name',$search)->or_like('email',$search);
        }
        
        
        $query = $this->db->where('is_active',1)->get('fact_users',$limit,$page);
        
        $users = array();
        
        foreach( $query->result() as $u )
        {
            
            $users[] = array(
                'id'            => $u->id,
                'first_name'    => $u->first_name,
                'last_name'     => $u->last_name,
                'email'         => $u->email,
                'is_admin'      => $u->is_admin
            );
            
        }
        
        return $users;
        
        
    }
    
    
    public function insert( $data = array() )
    {
        
        if ( !is_array($data) )
        {
            return false;
        }
        
        
        $this->db->insert('fact_users',$data);
        
    }
    
    public function update( $id, $data = array() )
    {
        if ( !is_array($data) )
        {
            return false;
        }
        
        $this->db->where('id',$id)->update('fact_users',$data);
    }
}