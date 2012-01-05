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
                    'email'         => $user_row->email
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
    
}