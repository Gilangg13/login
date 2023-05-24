<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // jika belum ada session atau belum login arahkan kembali ka hal login
        cek_login();

        // $this->load->database();
        $this->load->model('Menu_model');
        $this->load->library('form_validation');
    }


    public function index()
    {
        $data['title'] = 'Menu Management';
        // ambil data email dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // ambil menu dari tabel user_menu
        $data['menu'] = $this->Menu_model->getMenu();

        // $this->form_validation->set_rules('menu', 'Menu', 'required');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah()
    {
        $data['title'] = 'Menu Management';
        // ambil data email dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // ambil menu dari tabel user_menu
        $data['menu'] = $this->Menu_model->getMenu();

        $this->form_validation->set_rules('menu', 'Menu', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/tambah', $data);
            $this->load->view('templates/footer');
        } else {
            // masukan data ke databse
            $this->Menu_model->tambahMenu();

            // jika berhasil
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Menu Ditambahkan</div>');
            redirect('menu');
        }
    }

    public function delete($id)
    {
        $this->Menu_model->deleteMenu($id);
        // jika berhasil
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Menu Berhasil Dihapus</div>');
        redirect('menu/index');
    }

    public function edit($id)
    {
        $data['title'] = 'Menu Management';

        // ambil data email dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // ambil menu dari tabel user_menu
        $data['menu'] = $this->Menu_model->getMenuById($id);

        $this->form_validation->set_rules('editMenu', 'EditMenu', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/edit', $data);
            $this->load->view('templates/footer');
        } else {
            // edit data ke databse
            $data = [
                'menu' => $this->input->post('editMenu')
            ];

            $this->Menu_model->editMenu($id, $data);

            // jika berhasil
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Menu Berhasil Di Edit</div>');
            redirect('menu');
        }
    }




    public function submenu()
    {
        $data['title'] = 'Sub Menu Management';
        // ambil data email dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // ambil menu dari tabel user_menu
        $data['menu'] = $this->Menu_model->getMenu();

        // ambil submenu dari tabel user_sub_menu
        // (Nama model, nama alias)
        $data['subMenu'] = $this->Menu_model->getSubMenu();

        // $data['subMenu'] = $this->db->get('user_sub_menu')->result_array();

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('icon', 'icon', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/submenu', $data);
            $this->load->view('templates/footer');
        } else {
            // masukan data ke databse
            $data = [
                'title' => $this->input->post('title'),
                'menu_id' => $this->input->post('menu_id'),
                'url' => $this->input->post('url'),
                'icon' => $this->input->post('icon'),
                'is_active' => $this->input->post('is_active')
            ];
            //tambah submenu
            $this->Menu_model->tambahSubMenu($data);

            // jika berhasil
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Sub Menu Ditambahkan</div>');
            redirect('menu/submenu');
        }
    }

    public function editSubMenu($id)
    {
        $data['title'] = 'Sub Menu Management';
        // ambil data email dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // ambil menu dari tabel user_menu
        $data['menu'] = $this->Menu_model->getMenu();

        // ambil submenu dari tabel user_sub_menu
        $data['subMenu'] = $this->Menu_model->getSubMenuById($id);


        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('icon', 'icon', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/editSubMenu', $data);
            $this->load->view('templates/footer');
        } else {
            // masukan data ke databse
            $data = [
                'title' => $this->input->post('title'),
                'menu_id' => $this->input->post('menu_id'),
                'url' => $this->input->post('url'),
                'icon' => $this->input->post('icon'),
                'is_active' => $this->input->post('is_active')
            ];
            //tambah submenu
            $this->Menu_model->editSubMenu($id, $data);

            // jika berhasil
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Sub Menu Berhasil Diedit</div>');
            redirect('menu/submenu');
        }
    }

    public function hapusSubMenu($id)
    {
        $this->Menu_model->deleteSubMenu($id);
        // jika berhasil
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Sub Menu Berhasil Dihapus</div>');
        redirect('menu/submenu');
    }













    // public function delete($id)
    // {
    //     // ambil data email dari session
    //     $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    //     // ambil menu dari tabel user_menu
    //     $data['menu'] = $this->db->get('user_menu')->result_array();

    //     // $this->form_validation->set_rules('editmenu', 'EditMenu', 'required');

    //     if ($this->form_validation->run() == false) {
    //         $this->load->view('templates/header', $data);
    //         $this->load->view('templates/sidebar', $data);
    //         $this->load->view('templates/topbar', $data);
    //         $this->load->view('menu/index', $data);
    //         $this->load->view('templates/footer');
    //     } else {
    //         // masukan data ke databse
    //         $this->db->delete('user_menu', $data);

    //         // jika berhasil
    //         $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Menu Berhasil Di Hapus</div>');
    //         redirect('menu');
    //     }
    // }
}
