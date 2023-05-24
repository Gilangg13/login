<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
{
    public function getUser()
    {
        $data = [
            'email' => $this->session->userdata('email')
        ];
        return $this->db->get_where('user', $data)->row_array();
    }

    // ambil data dari table user_menu yang id nya bukan 1 (admin)
    public function getMenu()
    {
        $this->db->where('id !=', 1);
        $query = $this->db->get('user_menu')->result_array();
        return $query;
    }


    public function getRole()
    {
        $query = $this->db->get('user_role')->result_array();
        return $query;
    }


    public function getRoleById($role_id)
    {
        return $this->db->get_where('user_role', ['id' => $role_id])->row_array();
    }
}
