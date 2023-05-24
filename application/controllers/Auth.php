<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
    }

    public function index()
    {
        // jika ada session email maka tidak bisa kembali ke auth melalui url
        if ($this->session->userdata('email')) {
            redirect('user');
        }

        // rule validasi email dan password
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');


        // jika login gagal maka tampilkan form login
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Halaman Login';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            // jika validasi sukses jalankan fungsi login
            $this->_login();
        }
    }



    private function _login()
    {
        // ambil email dan password
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        // query - SELECT * FROM user WHERE email = $email
        $user = $this->db->get_where('user', ['email' => $email])->row_array();
        // var_dump($user);
        // die;
        // jika usernya ada
        if ($user) {
            // jika usernya aktif
            if ($user['is_active'] == 1) {
                // cek password
                if ($password == $user['password']) { //password_verify - untuk menyamakan password yg diinput dengan password yg sudah di hash

                    // data untuk session
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];

                    // set session data
                    $this->session->set_userdata($data);
                    // cek jika role id nya 1 maka akan ke halaman admin
                    if ($user['role_id'] == 1) {
                        redirect('admin');
                        // jika bukan (0) maka akan ke halaman user
                    } else {
                        redirect('user');
                    }
                } else {
                    // jika gagal maka tampilkan pesan error
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Password Salah</div>');
                    redirect('auth');
                }
            } else {
                // jika gagal maka tampilkan pesan error
                // var_dump($user);
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email belum diaktivasi.</div>');
                redirect('auth');
            }
        } else {
            // jika gagal maka tampilkan pesan error
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email tidak terdaftar.</div>');
            redirect('auth');
        }
    }





    public function registrasi()
    {

        // jika ada session email maka tidak bisa kembali ke regist melalui url
        if ($this->session->userdata('email')) {
            redirect('user');
        }

        // nama atribut, nama lain/alias, harus diisi
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        // nama atribut, nama lain/alias, harus diisi|harus format email|ngecek apakah ada email yg sama di db
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'Email sudah pernah terdaftar'
        ]);

        // nama atribut, nama lain/alias, harus diisi|harus format email|minimal jumlah password|agar sama dengan password2
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password tidak sama!',
            'min_length' => 'Password terlalu pendek!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|min_length[3]|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Halaman Registrasi';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registrasi');
            $this->load->view('templates/auth_footer');
        } else {
            // ambil data
            $email = $this->input->post('email', true);
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($email),
                'image' => 'default.jpg',
                // password di enkripsi dulu dengan function password_hash(), algoritma security
                // 'password' => password_hash($this->input->post('password1', true)),
                'password' => $this->input->post('password1', true),
                'role_id' => 2,
                'is_active' => 0,
                'date_created' => time()
            ];

            // siapkan token
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $email,
                'token' => $token,
                'date_created' => time()
            ];



            // masukan data ke database
            $this->db->insert('user', $data);
            $this->db->insert('user_token', $user_token);

            // fitur kirim email
            $this->_sendEmail($token, 'verify');

            // jika berhasil maka akan pindah ke halaman login
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Selamat! Akun anda telah dibuat. Silahkan Aktivasi Akunmu.</div>');
            redirect('auth');
        }
    }

    public function verify()
    {
        // ambil data di URL
        $email =  $this->input->get('email');
        $token =  $this->input->get('token');

        // ambil email dari db
        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        // cek apakah ada user di db
        if ($user) {
            // ambil token dari db
            $user_token =  $this->db->get_where('user_token', ['token' => $token])->row_array();

            // cek apakah token sama dengan yg di db
            if ($user_token) {
                // validasi, apakah aktivasi dilakukan sebelum 24 jam
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {

                    //update status menjadi 1 (aktif)
                    $this->db->set('is_active', 1);
                    $this->db->where('email', $email);
                    $this->db->update('user');

                    // hapus token jika sudah digunakan
                    $this->db->delete('user_token', ['email' => $email]);
                    //
                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">' . $email . ' Telah Aktif!, Silahkan Login</div>');
                    redirect('auth');
                } else {

                    // hapus user dan token
                    $this->db->delete('user', ['email' => $email]);
                    $this->db->delete('user_token', ['email' => $email]);


                    // jika lebih dari 1 hari
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Aktivasi Akun Gagal! Token Expired</div>');
                    redirect('auth');
                }
            } else {
                // jika token salah
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Aktivasi Akun Gagal! Token Invalid</div>');
                redirect('auth');
            }
        } else {
            // tampilkan pesan error
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Aktivasi Akun Gagal! Email Salah</div>');
            redirect('auth');
        }
    }



    // function fitur kirim email
    private function _sendEmail($token, $type)
    {
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'arfanhasif88@gmail.com',
            'smtp_pass' => 'ihhjewuistfnvcev',
            'smtp_port' => 465,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];

        // $config = array(
        //     'protocol' => 'smtp',
        //     'smtp_host' => 'smtp.mail.ac.id', // ganti dengan alamat SMTP yang digunakan oleh institusi
        //     'smtp_port' => 587, // ganti dengan port yang digunakan oleh SMTP
        //     'smtp_user' => 'arfanhasif88@gmail.com',
        //     'smtp_pass' => 'zeinlbeeyfljvfur',
        //     'mailtype' => 'html',
        //     'charset' => 'utf-8'
        // );
        $this->load->library('email', $config);
        $this->email->initialize($config);

        $this->email->from('arfanhasif88@gmail.com', 'Gilang Gumelar');
        $this->email->to($this->input->post('email'));

        // jika typenya verify
        if ($type == 'verify') {
            $this->email->subject('Account Verification');
            $this->email->message('Click this link to verify your account : <a href="' . base_url() . 'auth/verify?email=' .
                $this->input->post('email') . '&token=' . urlencode($token) . '">Active</a>');
        } else if ($type == 'lupapassword') {
            // jika typenya lupa password
            $this->email->subject('Reset Password');
            $this->email->message('Click this link to reset your password : <a href="' . base_url() . 'auth/resetpassword?email=' .
                $this->input->post('email') . '&token=' . urlencode($token) . '">Reset Password</a>');
        }

        // kirim email
        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }



    public function logout()
    {
        // hapus session email dan role id
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        // tampilkan pesan berhasil logout
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Anda telah logout</div>');
        redirect('auth');
    }


    public function blocked()
    {
        $data['title'] =  "Access Blocked";

        $this->load->view('templates/header', $data);
        $this->load->view('auth/blocked');
    }






    public function lupapassword()
    {

        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Lupa Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/lupa-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');
            $user =  $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();

            if ($user) {
                // siapkan token
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                $this->db->insert('user_token', $user_token);
                $this->_sendEmail($token, 'lupapassword');

                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Silahkan cek email untuk reset password</div>');
                redirect('auth/lupapassword');
            } else {
                // jika email belum registrasi
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email belum registrasi atau aktivasi</div>');
                redirect('auth/lupapassword');
            }
        }
    }


    public function resetpassword()
    {
        // ambil data di URL
        $email =  $this->input->get('email');
        $token =  $this->input->get('token');

        // ambil email dari db
        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        // cek apakah ada user di db
        if ($user) {
            // ambil token dari db
            $user_token =  $this->db->get_where('user_token', ['token' => $token])->row_array();
            // cek apakah token sama dengan yg di db
            if ($user_token) {
                // validasi, apakah aktivasi dilakukan sebelum 24 jam
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    $this->session->set_userdata('reset_email', $email);
                    $this->changePassword();
                } else {
                    // hapus user dan token jika sudah lebih dari 24 jam
                    $this->db->delete('user', ['email' => $email]);
                    $this->db->delete('user_token', ['email' => $email]);


                    // jika lebih dari 1 hari
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Aktivasi Akun Gagal! Token Expired</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Reset Password Gagal! Token Invalid</div>');
                redirect('auth/lupapassword');
            }
        } else {
            // jika email belum registrasi
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Reset Password Gagal! Email Salah</div>');
            redirect('auth/lupapassword');
        }
    }



    public function changePassword()
    {
        // jika tidak ada session reset_email
        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
        }

        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Repeat Password', 'required|trim|min_length[3]|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change-password');
            $this->load->view('templates/auth_footer');
        } else {
            $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');

            $this->db->set('password', $password);
            $this->db->where('email', $email);
            $this->db->update('user');

            $this->session->unset_userdata('reset_email');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Password Berhasil Diubah, Silahkan login!</div>');
            redirect('auth');
        }
    }
}
