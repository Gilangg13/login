<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // jika belum ada session atau belum login arahkan kembali ka hal login
        cek_login();
        $this->load->model('Admin_model');
    }
    public function index()
    {
        $data['title'] = 'Dashboard';
        // ambil data email dari session
        // $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $data['user'] = $this->Admin_model->getUser();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function role()
    {
        $data['title'] = 'Role';
        // ambil data email dari session
        // $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['user'] = $this->Admin_model->getUser();

        $data['role'] = $this->Admin_model->getRole();
        // $data['role'] = $this->db->get('user_role')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/role', $data);
        $this->load->view('templates/footer');
    }

    public function roleaccess($role_id)
    {
        $data['title'] = 'Role';
        // ambil data email dari session
        // $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['user'] = $this->Admin_model->getUser();

        // ambil data role berdasarkan id
        $data['role'] = $this->Admin_model->getRoleById($role_id);
        // $data['role'] = $this->db->get('user_role')->result_array();

        // ambil data menu
        $data['menu'] = $this->Admin_model->getMenu();


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/role-access', $data);
        $this->load->view('templates/footer', $data);
    }

    public function ubahaccess()
    {
        // ambil data yg dikirim dari ajak
        $menu_id = $this->input->post('menuId');
        $role_id = $this->input->post('roleId');

        $data = [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ];

        $result = $this->db->get_where('user_access_menu', $data);

        // jika tidak ada isinya, terus di ceklis maka ditambahkan
        if ($result->num_rows() < 1) {
            $this->db->insert('user_access_menu', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Access Diubah Ditambahkan</div>');
        } else {
            // jika sudah ada, terus di unchecklist, maka hapus accessnya
            $this->db->delete('user_access_menu', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Access Diubah Dihapus</div>');
        }
    }
}
