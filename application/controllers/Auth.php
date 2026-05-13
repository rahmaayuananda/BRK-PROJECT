<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('forum_model');
        $this->load->model('activity_log');
        $this->load->helper(array('url', 'form', 'security'));
        $this->load->library('session');
        // load database if available (will use at runtime with checks)
        $this->load->database();
        
        // Set timezone ke Asia/Jakarta (UTC+7)
        date_default_timezone_set('Asia/Jakarta');
    }

    public function login()
    {
        // kalau sudah login → langsung ke forum
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }

        $data = [];
        $data['error'] = $this->session->flashdata('login_error');
        $data['success'] = $this->session->flashdata('register_success');

        $this->load->view('auth/login', $data);
    }

    public function register()
    {
        $data = [];
        $data['error'] = $this->session->flashdata('register_error');
        $data['success'] = $this->session->flashdata('register_success');
        $this->load->view('auth/register', $data);
    }

    public function authenticate()
    {
        $username = trim($this->input->post('username', true));
        $password = $this->input->post('password', true);

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('login_error', 'NIP dan password harus diisi');
            redirect('auth/login');
            return;
        }

        // cek ke database
        if ($this->db && $this->db->table_exists('users')) {

            $q = $this->db->get_where('users', ['username' => $username]);

            if ($q->num_rows() > 0) {
                $row = $q->row_array();

                // cek password (support hash & plain)
                if (password_verify($password, $row['password']) || $row['password'] === $password) {

                    $fullname = $row['name'] ?? $username;

                    // 🔥 ambil avatar langsung saat login
                    $avatar = null;
                    $exts = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

                    foreach ($exts as $e) {
                        $path = FCPATH . 'assets/avatars/' . $row['username'] . '.' . $e;
                        if (file_exists($path)) {
                            $avatar = base_url('assets/avatars/' . $row['username'] . '.' . $e);
                            break;
                        }
                    }

                    // fallback avatar
                    if (!$avatar) {
                        $avatar = 'https://ui-avatars.com/api/?name=' . rawurlencode($fullname) . '&size=64&background=0D8ABC&color=fff';
                    }

                    // ✅ set session lengkap
                    $this->session->set_userdata([
                        'username' => $row['username'],
                        'id_users' => $row['id_users'] ?? null,
                        'name' => $fullname,
                        'avatar_url' => $avatar, // 🔥 FIX UTAMA ADA DI SINI
                        'role' => $row['role'], // ✅ TAMBAHAN PENTING
                        'logged_in' => true
                    ]);

                    // � LOG ACTIVITY - LOGIN
                    $user_id = $row['id_users'] ?? null;
                    if ($user_id) {
                        $description = "{$fullname} berhasil login dari IP " . $this->input->ip_address();
                        $this->activity_log->log_activity(
                            $user_id,
                            'LOGIN',
                            null,
                            $description
                        );
                    }

                    // �🔥 SIMPAN NOTIF KE FILE (GLOBAL)
                    $this->save_notification([
                        'type' => 'user_login',
                        'topic_id' => 'login', // 🔥 TAMBAHAN
                        'topic_title' => 'Login System',
                        'created_by' => $fullname,
                        'created_at' => time()
                    ]);

                    // 🔥 KIRIM NOTIF LOGIN
                    $this->notify_ws('user_login', [
                        'topic_id' => 'login', // 🔥 WAJIB
                        'topic_title' => 'Login System',
                        'user' => $fullname,
                        'created_at' => time()
                    ]);

                    // welcome message
                    $this->session->set_flashdata('welcome', 'Selamat Datang ' . $fullname);

                    redirect('dashboard');
                    return;
                }
            }
        }

        // gagal login
        $this->session->set_flashdata('login_error', 'Username atau password salah');
        redirect('auth/login');
    }
    public function register_submit()
    {
        $fullname = trim($this->input->post('fullname', true));
        $nip = trim($this->input->post('username', true));
        $password = $this->input->post('password', true);
        $repassword = $this->input->post('repassword', true);

        if (empty($fullname) || empty($nip) || empty($password) || $password !== $repassword) {
            $this->session->set_flashdata('register_error', 'Data registrasi tidak valid');
            redirect('auth/register');
            return;
        }

        if (strlen($password) < 4) {
            $this->session->set_flashdata('register_error', 'Password minimal 4 karakter');
            redirect('auth/register');
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 1) Try to insert into DB users table if available
        if (isset($this->db) && $this->db) {
            try {
                if ($this->db->table_exists('users')) {
                    // check existing username
                    $q = $this->db->get_where('users', ['username' => $nip]);
                    if ($q->num_rows() > 0) {
                        $this->session->set_flashdata('register_error', 'Username sudah terdaftar');
                        redirect('auth/register');
                        return;
                    }

                    $ins = [
                        'name' => $fullname,
                        'username' => $nip,
                        'password' => $hash
                    ];
                    if ($this->db->insert('users', $ins)) {
                        $this->session->set_flashdata('register_success', 'Registrasi berhasil, silakan login');
                        redirect('auth/login');
                        return;
                    } else {
                        log_message('error', 'DB insert failed: ' . $this->db->last_query());
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'DB register failed: ' . $e->getMessage());
            }
        }

        // 2) fallback to flatfile storage
        if (!is_dir(APPPATH . 'data')) {
            @mkdir(APPPATH . 'data', 0755, true);
        }
        $users_file = APPPATH . 'data/users.json';
        $users = [];
        if (file_exists($users_file)) {
            $json = @file_get_contents($users_file);
            $users = json_decode($json, true);
            if (!is_array($users))
                $users = [];
        }

        if (isset($users[$nip])) {
            $this->session->set_flashdata('register_error', 'Username sudah terdaftar');
            redirect('auth/register');
            return;
        }

        $users[$nip] = ['fullname' => $fullname, 'password_hash' => $hash, 'created_at' => date('c')];
        if (@file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $this->session->set_flashdata('register_success', 'Registrasi berhasil, silakan login');
            redirect('auth/login');
            return;
        }

        $this->session->set_flashdata('register_error', 'Gagal menyimpan data.');
        redirect('auth/register');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    public function profile()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        $username = $this->session->userdata('username');

        $data = [
            'username' => $username,
            'fullname' => $this->session->userdata('name')
        ];

        // ambil dari DB
        if ($this->db && $this->db->table_exists('users')) {
            $q = $this->db->get_where('users', ['username' => $username]);

            if ($q->num_rows() > 0) {
                $row = $q->row_array();
                $data['fullname'] = $row['name'];
                $data['email'] = $row['email'] ?? '';
            }
        }

        // avatar
        $avatar = null;
        foreach (['png', 'jpg', 'jpeg', 'gif', 'webp'] as $e) {
            $path = FCPATH . 'assets/avatars/' . $username . '.' . $e;
            if (file_exists($path)) {
                $avatar = base_url('assets/avatars/' . $username . '.' . $e) . '?t=' . time();
                break;
            }
        }
        //fallback 
        if (!$avatar) {
            $avatar = 'https://ui-avatars.com/api/?name=' . rawurlencode($data['fullname']);
        }

        $data['avatar_url'] = $avatar;

        // ✅ update session biar konsisten di semua halaman
        $this->session->set_userdata('avatar_url', $avatar);

        // bio: try DB field 'bio' then flatfile
        $data['bio'] = '';
        if ($this->db && $this->db->table_exists('users')) {
            try {
                if ($this->db->field_exists('bio', 'users')) {
                    $q = $this->db->get_where('users', ['username' => $username]);
                    if ($q && $q->num_rows() > 0) {
                        $row = $q->row_array();
                        $data['bio'] = $row['bio'] ?? '';
                    }
                }
            } catch (Exception $e) {
            }
        }
        if ($data['bio'] === '') {
            $users_file = APPPATH . 'data/users.json';
            if (file_exists($users_file)) {
                $json = @file_get_contents($users_file);
                $local = json_decode($json, true);
                if (is_array($local) && isset($local[$username]) && isset($local[$username]['bio'])) {
                    $data['bio'] = $local[$username]['bio'];
                }
            }
        }

        // user topics and recent activity (match by fullname and username)
        $userTopics = [];
        $recentActs = [];
        $fullname = $data['fullname'] ?? '';
        $t1 = $this->forum_model->get_topics_by_user($fullname, 200);
        $t2 = ($fullname && $fullname !== $username) ? $this->forum_model->get_topics_by_user($username, 200) : [];
        $merged = [];
        foreach (array_merge($t1, $t2) as $tt) {
            $merged[$tt['id']] = $tt;
        }
        $userTopics = array_values($merged);

        $a1 = $this->forum_model->get_recent_activity_by_user($fullname, 20);
        $a2 = ($fullname && $fullname !== $username) ? $this->forum_model->get_recent_activity_by_user($username, 20) : [];
        $acts = array_merge($a1, $a2);
        usort($acts, function ($a, $b) {
            return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
        });
        $recentActs = array_slice($acts, 0, 20);

        $data['user_topics'] = $userTopics;
        $data['recent_activity'] = $recentActs;

        $this->load->view('auth/profile', $data);
    }


    public function upload_avatar()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            redirect('auth/profile');
            return;
        }

        $file = $_FILES['avatar'];

        // ✅ batasi ukuran (2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            $this->session->set_flashdata('error', 'File terlalu besar (max 2MB)');
            redirect('auth/profile');
            return;
        }

        $tmp = $file['tmp_name'];

        // ✅ validasi gambar
        $info = getimagesize($tmp);
        if (!$info) {
            redirect('auth/profile');
            return;
        }

        $allowed = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        if (!isset($allowed[$info['mime']])) {
            redirect('auth/profile');
            return;
        }

        $ext = $allowed[$info['mime']];
        $username = $this->session->userdata('username');

        $dir = FCPATH . 'assets/avatars/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // ✅ hapus avatar lama
        foreach (['png', 'jpg', 'jpeg', 'gif', 'webp'] as $e) {
            $old = $dir . $username . '.' . $e;
            if (file_exists($old)) {
                unlink($old);
            }
        }

        $dest = $dir . $username . '.' . $ext;

        // ✅ validasi upload
        if (!is_uploaded_file($tmp) || !move_uploaded_file($tmp, $dest)) {
            redirect('auth/profile');
            return;
        }

        // ✅ cache busting FIX
        $avatar_url = base_url('assets/avatars/' . $username . '.' . $ext) . '?t=' . time();

        $this->session->set_userdata('avatar_url', $avatar_url);

        redirect('auth/profile');
    }
    public function update_profile()
    {
        if (!$this->session->userdata('logged_in')) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Anda belum login']));
            return;
        }
        $fullname = trim($this->input->post('fullname', true));
        $email = trim($this->input->post('email', true));
        $bio = trim($this->input->post('bio', true));
        $old_password = $this->input->post('old_password', true);
        $new_password = $this->input->post('new_password', true);
        $confirm_password = $this->input->post('confirm_password', true);

        if (empty($fullname)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Nama lengkap harus diisi']));
            return;
        }

        $username = $this->session->userdata('username');
        $updated = false;
        $password_changed = false;

        // Handle password change if requested
        if (!empty($new_password) || !empty($confirm_password) || !empty($old_password)) {
            if ($new_password === '' || $confirm_password === '' || $old_password === '') {
                $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Untuk mengganti password, isi semua kolom password']));
                return;
            }
            if ($new_password !== $confirm_password) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Konfirmasi password tidak cocok']));
                return;
            }
            if (strlen($new_password) < 4) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Password baru minimal 4 karakter']));
                return;
            }
        }

        // Try DB users table update (and password verify/change)
        if (isset($this->db) && $this->db) {
            try {
                if ($this->db->table_exists('users')) {
                    // if password change requested, verify current password first
                    if (!empty($new_password)) {
                        $q = $this->db->get_where('users', ['username' => $username]);
                        if ($q && $q->num_rows() > 0) {
                            $row = $q->row_array();
                            $curpw = isset($row['password']) ? $row['password'] : null;
                            $ok = false;
                            if ($curpw) {
                                if (password_verify($old_password, $curpw) || $curpw === $old_password)
                                    $ok = true;
                            }
                            if (!$ok) {
                                $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Password lama salah']));
                                return;
                            }
                            $password_changed = true;
                        } else {
                            // no DB entry to verify against
                            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Tidak bisa memverifikasi password saat ini']));
                            return;
                        }
                    }

                    $upd = ['name' => $fullname];
                    if ($email !== '')
                        $upd['email'] = $email;
                    if ($password_changed)
                        $upd['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                    // include bio if the column exists
                    try {
                        if ($this->db->field_exists('bio', 'users')) {
                            $upd['bio'] = $bio;
                        }
                    } catch (Exception $e) {
                    }

                    // ensure there's an existing DB row to update; if none, don't mark as updated so flatfile fallback runs
                    $qexist = $this->db->get_where('users', ['username' => $username]);
                    if ($qexist && $qexist->num_rows() > 0) {
                        $this->db->where('username', $username);
                        $this->db->update('users', $upd);
                        $updated = ($this->db->affected_rows() > 0);
                    } else {
                        // no DB row found — leave $updated=false to trigger flatfile fallback
                        $updated = false;
                    }
                }
            } catch (Exception $e) {
                $updated = false;
            }
        }

        // Fallback to flatfile (and password change)
        if (!$updated) {
            $users_file = APPPATH . 'data/users.json';
            $local = [];
            if (file_exists($users_file)) {
                $json = @file_get_contents($users_file);
                $local = json_decode($json, true);
                if (!is_array($local))
                    $local = [];
            }
            $entry = isset($local[$username]) ? $local[$username] : [];

            // If password change requested, verify against stored hash if available
            if (!empty($new_password)) {
                if (isset($entry['password_hash']) && $entry['password_hash'] !== '') {
                    if (!password_verify($old_password, $entry['password_hash'])) {
                        $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Password lama salah']));
                        return;
                    }
                } else {
                    // cannot verify if no password_hash in flatfile; treat as failure
                    $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Tidak dapat memverifikasi password di storage saat ini']));
                    return;
                }
                $entry['password_hash'] = password_hash($new_password, PASSWORD_DEFAULT);
                $password_changed = true;
            }

            $entry['fullname'] = $fullname;
            if ($email !== '')
                $entry['email'] = $email;
            // store bio in flatfile
            $entry['bio'] = $bio;
            $local[$username] = $entry;
            if (@file_put_contents($users_file, json_encode($local, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                $updated = true;
            }
        }

        // Handle avatar file upload if present in the same request
        $avatar_uploaded = false;
        if (!empty($_FILES) && isset($_FILES['avatar']) && isset($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            if ($file['size'] <= 2 * 1024 * 1024) {
                $tmp = $file['tmp_name'];
                $info = @getimagesize($tmp);
                if ($info !== false) {
                    $mime = isset($info['mime']) ? $info['mime'] : '';
                    $allowed = array('image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/webp' => 'webp');
                    if (isset($allowed[$mime])) {
                        $ext = $allowed[$mime];
                        $dest_dir = FCPATH . 'assets/avatars';
                        if (!is_dir($dest_dir))
                            @mkdir($dest_dir, 0755, true);
                        foreach (array('png', 'jpg', 'jpeg', 'gif', 'webp') as $e) {
                            $p = $dest_dir . '/' . $username . '.' . $e;
                            if (file_exists($p) && $e !== $ext)
                                @unlink($p);
                        }
                        $dest = $dest_dir . '/' . $username . '.' . $ext;
                        if (is_uploaded_file($tmp) && move_uploaded_file($tmp, $dest)) {
                            $avatar_url = base_url('assets/avatars/' . $username . '.' . $ext);
                            $this->session->set_userdata('avatar_url', $avatar_url);
                            $avatar_uploaded = true;
                        }
                    }
                }
            }
        }

        if ($updated || $avatar_uploaded || $password_changed) {
            // update session fullname
            $this->session->set_userdata('name', $fullname);

            // determine avatar URL if not already set by upload
            $avatar_url = $this->session->userdata('avatar_url');
            if (!$avatar_url) {
                $exts = array('png', 'jpg', 'jpeg', 'gif', 'webp');
                foreach ($exts as $e) {
                    $path = FCPATH . 'assets/avatars/' . $username . '.' . $e;
                    if (file_exists($path)) {
                        $avatar_url = base_url('assets/avatars/' . $username . '.' . $e);
                        break;
                    }
                }
            }
            if (!$avatar_url) {
                if (!empty($email)) {
                    $hash = md5(strtolower(trim($email)));
                    $avatar_url = 'https://www.gravatar.com/avatar/' . $hash . '?s=200&d=identicon';
                } else {
                    $avatar_url = 'https://ui-avatars.com/api/?name=' . rawurlencode($fullname) . '&size=200&background=0D8ABC&color=fff';
                }
            }
            $this->session->set_userdata('avatar_url', $avatar_url);

            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'message' => 'Profile berhasil di update', 'fullname' => $fullname, 'avatar_url' => $avatar_url, 'email' => $email, 'bio' => $bio]));
            return;
        }

        $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Gagal memperbarui profil']));
    }

    protected function notify_ws($type, $data)
    {
        $ws_host = '127.0.0.1';
        $ws_port = '8080';
        $url = 'http://' . $ws_host . ':' . $ws_port . '/notify?type=' . rawurlencode($type) . '&data=' . rawurlencode(json_encode($data, JSON_UNESCAPED_UNICODE));

        $opts = ['http' => ['method' => 'GET', 'timeout' => 0.5]];
        $context = stream_context_create($opts);

        @file_get_contents($url, false, $context);
    }
    private function save_notification($notif)
    {
        $file = FCPATH . 'data/notifications.json';

        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }

        $data = json_decode(file_get_contents($file), true);
        $data[] = $notif;

        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

}
