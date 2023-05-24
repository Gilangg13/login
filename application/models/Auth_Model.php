<?php
class Auth_MOdel extends CI_Model
{
    public function getUser()
    {

        // ambil email dan password
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        // query - SELECT * FROM user WHERE email = $email
        $user = $this->db->get_where('user', ['email' => $email])->row_array();
    }
}
