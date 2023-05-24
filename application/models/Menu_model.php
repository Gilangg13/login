<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{

    // ambil data si table user_menu
    public function getMenu()
    {
        $query = $this->db->get('user_menu');
        return $query->result_array();
    }

    // ambil data menu berdasarkan id
    public function getMenuById($id)
    {
        return $this->db->get_where('user_menu', ['id' => $id])->row_array();
    }

    // tambah Menu
    public function tambahMenu()
    {
        $this->db->insert('user_menu', ['menu' => $this->input->post('menu')]);
    }

    // hapus Menu
    public function deleteMenu($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('user_menu');
    }


    // ubah Menu
    public function editMenu($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('user_menu', $data);
    }




    public function getSubMenu()
    {
        $query = "SELECT `user_sub_menu`.*, `user_menu`.`menu` 
                FROM `user_sub_menu` JOIN `user_menu`
                ON `user_sub_menu`.`menu_id` = `user_menu`.`id`";

        return $this->db->query($query)->result_array();
    }

    // public function getMenu($id){

    // }
    public function getSubMenuById($id)
    {
        return $this->db->get_where('user_sub_menu', ['id' => $id])->row_array();
    }

    public function tambahSubMenu($data)
    {
        $this->db->insert('user_sub_menu', $data);
    }

    public function editSubMenu($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('user_sub_menu', $data);
    }

    public function deleteSubMenu($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('user_sub_menu');
    }
}
