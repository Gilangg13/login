<?php

function cek_login()
{
    // memanggil library CI di fungsi ini
    $ci = get_instance();
    // jika sudah login
    // jika belum ada session atau belum login arahkan kembali ka hal login
    if (!$ci->session->userdata('email')) {
        redirect('auth');
    } else {
        // jika belum login
        // cek role id
        $role_id = $ci->session->userdata('role_id');

        // cek sedang ada di controller/menu mana
        $menu = $ci->uri->segment(1);

        // query menu
        $queryMenu = $ci->db->get_where('user_menu', ['menu' => $menu])->row_array();
        // ambil id
        $menu_id = $queryMenu['id'];

        // cek berdasarkan role_id apakah boleh akses menu yang dipilih
        $userAccess  = $ci->db->get_where('user_access_menu', [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ]);

        // jika userAccessnya 0 maka pindahkan ke halaman blok
        if ($userAccess->num_rows() < 1) {
            redirect('auth/blocked');
        }
    }
}


function check_access($role_id, $menu_id)
{
    // memanggil library CI di fungsi ini
    $ci = get_instance();

    // ambil data dari user_access_menu yang role_id = $role_id dan menu_id = $role_id
    // contoh query 1
    // $ci->db->where('role_id', $role_id);
    // $ci->db->where('menu_id', $menu_id);
    // $ci->db->get('user_access_menu');

    // contoh query 2
    $result = $ci->db->get_where('user_access_menu', [
        'role_id' => $role_id,
        'menu_id' => $menu_id
    ]);

    // jika result ada isinya/barisnya
    if ($result->num_rows() > 0) {
        return "checked = 'checked'";
    }
}
