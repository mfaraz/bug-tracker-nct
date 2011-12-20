<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model {
    
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
            
        }
        catch(Exception $e)
        {
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
            
            $result = $this->db->where('is_active',1)->get('fact_users');
            
            $user_array = array();
            
            foreach( $result->result() as $user  ) {
                $user_array[] = array(
                                'id'      => $user->id,
                                'name'    => $user->first_name . ' ' .$user->last_name
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