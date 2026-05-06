<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forum extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('forum_model');
        $this->load->helper('url');
        $this->load->helper('security');
    }

    public function index()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        $username = $this->session->userdata('nip'); // ✅ pakai nip

        // $data['topics'] = $this->forum_model->get_user_topics_with_count($username);
        $data['topics'] = $this->forum_model->get_topics();

        $this->load->view('forum/index', $data);
    }

    public function topic($id = null)
    {
        if (!$id)
            show_404();

        // ✅ PAKAI find_topic (bisa ambil FAQ & arsip)
        $topic = $this->forum_model->find_topic($id);

        if (!$topic)
            show_404();

        $data['topic'] = $topic;

        $data['seg1'] = $this->uri->segment(1);
        $data['seg2'] = $this->uri->segment(2);
        $data['from'] = $this->input->get('from');

        $this->load->view('forum/topic', $data);
    }

    // API: get topics as JSON
    public function topics()
    {
        $topics = $this->forum_model->get_topics();
        foreach ($topics as &$t) {
            $messages = $this->forum_model->get_messages($t['id']);
            $t['total_messages'] = is_array($messages) ? count($messages) : 0;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($topics));
    }

    // API: get user topics as JSON
    public function user_topics()
    {
        $username = $this->session->userdata('nama_lengkap') ?: $this->session->userdata('nip');
        $topics = $this->forum_model->get_topics_by_creator($username);
        $this->output->set_content_type('application/json')->set_output(json_encode($topics));
    }

    // API: create topic
    public function create_topic()
    {
        $title = $this->input->post('title', true);
        $title = trim($title);
        if ($title === '') {
            $this->output->set_status_header(400)->set_content_type('application/json')->set_output(json_encode(['error' => 'Judul kosong']));
            return;
        }
        $fullname = $this->session->userdata('nama_lengkap');

        if (!$fullname) {
            $fullname = $this->session->userdata('nip');
        }

        if (!$fullname) {
            $fullname = 'Guest'; // fallback terakhir
        }
        $topic = $this->forum_model->add_topic($title, $fullname);
        // notify websocket server (best-effort, non-blocking)
        $this->notify_ws('topic', $topic);
        $this->output->set_content_type('application/json')->set_output(json_encode($topic));
        log_message('error', 'SESSION: ' . json_encode($this->session->userdata()));
    }

    public function messages($id = null)
    {
        if (!$id) {
            echo json_encode([]);
            return;
        }

        $messages = $this->forum_model->get_messages($id, 200);

        foreach ($messages as &$m) {

            if (empty($m['avatar'])) {

                $name = $m['created_by'] ?? 'User';

                $m['avatar'] = 'https://ui-avatars.com/api/?name=' . rawurlencode($name);
            }
        }

        echo json_encode($messages);
    }

    public function post_message()
    {
        $id = $this->input->post('topic_id', true);
        $message = $this->input->post('message', true);

        if (!$id || $message === null) {
            $this->output->set_status_header(400)
                ->set_output(json_encode(['error' => 'Invalid']));
            return;
        }

        $message = trim($message);
        if (mb_strlen($message) > 50) {
            $message = mb_substr($message, 0, 50);
        }

        $message = $this->security->xss_clean($message);

        // ✅ ambil user
        $username = $this->session->userdata('nip');
        $fullname = $this->session->userdata('nama_lengkap') ?: $username;

        // ✅ ambil avatar
        $avatar = null;
        foreach (['png', 'jpg', 'jpeg', 'gif', 'webp'] as $e) {
            $p = FCPATH . 'assets/avatars/' . $username . '.' . $e;
            if (file_exists($p)) {
                $avatar = base_url('assets/avatars/' . $username . '.' . $e);
                break;
            }
        }

        if (!$avatar) {
            $avatar = 'https://ui-avatars.com/api/?name=' . rawurlencode($fullname);
        }

        // ✅ format data message
        $entry = [
            'id' => uniqid(),
            'topic_id' => $id,
            'message' => $message,
            'created_by' => $fullname,
            'avatar' => $avatar,
            'created_at' => time()
        ];

        // ✅ simpan ke model
        $saved = $this->forum_model->add_message($id, $entry);

        // 🔥🔥🔥 INI YANG KURANG DI KODE KAMU
        $this->notify_ws('message', [
            'topic_id' => $id,
            'message' => $entry
        ]);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($entry));
    }

    public function my_topics()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login?return=' . urlencode(site_url('forum/my_topics')));
            return;
        }

        // ❌ blok admin masuk halaman My Topic
        if ($this->session->userdata('role') !== 'user') {
            redirect('forum'); // atau dashboard admin
            return;
        }

        $username = $this->session->userdata('nama_lengkap')
            ?: $this->session->userdata('nip');

        $topics = $this->forum_model->get_topics_by_creator($username);

        $data['topics'] = $topics;

        $this->load->view('forum/my_topic', $data);
    }

    public function notifications()
    {
        if (!$this->session->userdata('logged_in')) {
            $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $username = $this->session->userdata('nip');
        $fullname = $this->session->userdata('nama_lengkap') ?: $username;

        $topics = $this->forum_model->get_user_topics($username);

        $notifs = [];
        foreach ($topics as $t) {
            $msgs = $this->forum_model->get_messages($t['id']);
            foreach ($msgs as $m) {
                $created_by = $m['created_by'] ?? ($m['fullname'] ?? '');
                if ($created_by === $fullname || $created_by === $username)
                    continue;
                $notifs[] = [
                    'topic_id' => $t['id'],
                    'topic_title' => $t['title'],
                    'message' => $m['message'] ?? '',
                    'created_by' => $created_by,
                    'created_at' => $m['created_at'] ?? 0,
                    'avatar' => $m['avatar'] ?? null
                ];
            }
        }

        usort($notifs, function ($a, $b) {
            return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
        });
        $notifs = array_slice($notifs, 0, 50);

        $this->output->set_content_type('application/json')->set_output(json_encode($notifs));
    }

    protected function notify_ws($type, $data)
    {
        $ws_host = '127.0.0.1';
        $ws_port = '8080';
        $url = 'http://' . $ws_host . ':' . $ws_port . '/notify?type=' . rawurlencode($type) . '&data=' . rawurlencode(json_encode($data, JSON_UNESCAPED_UNICODE));
        $opts = array('http' => array('method' => 'GET', 'timeout' => 0.5));
        $context = stream_context_create($opts);
        @file_get_contents($url, false, $context);
    }

    public function delete_topic($id = null)
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            return $this->output->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Method not allowed']));
        }

        if (!$id) {
            return $this->output->set_status_header(400)
                ->set_output(json_encode(['error' => 'Missing id']));
        }

        $topic = $this->forum_model->find_topic($id);
        if (!$topic) {
            return $this->output->set_status_header(404)
                ->set_output(json_encode(['error' => 'Topic not found']));
        }

        // 🔥 ambil session
        $username = $this->session->userdata('nip');
        $fullname = $this->session->userdata('nama_lengkap');
        $role = $this->session->userdata('role');

        // 🔥 NORMALISASI (hindari mismatch)
        $creator = trim(strtolower($topic['created_by'] ?? ''));
        $user1 = trim(strtolower($username ?? ''));
        $user2 = trim(strtolower($fullname ?? ''));

        $isOwner = ($creator === $user1 || $creator === $user2);
        $isAdmin = ($role === 'admin');

        // 🔥 LOG DEBUG (WAJIB buat cek)
        log_message('error', 'ROLE: ' . $role);
        log_message('error', 'CREATOR: ' . $creator);
        log_message('error', 'USER: ' . $user1 . ' / ' . $user2);

        if (!$isAdmin && !$isOwner) {
            return $this->output->set_status_header(403)
                ->set_output(json_encode(['error' => 'Tidak punya akses']));
        }

        $deleted = $this->forum_model->delete_topic($id);

        if (!$deleted) {
            return $this->output->set_status_header(500)
                ->set_output(json_encode(['error' => 'Gagal menghapus topik']));
        }

        $this->notify_ws('topic_deleted', ['topic_id' => $id]);

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode(['success' => true]));
    }


    public function delete_message()
    {
        $id = $this->input->post('id');
        $user_id = $this->session->userdata('id_users');
        $role = $this->session->userdata('role');

        if (!$id) {
            echo json_encode(['success' => false]);
            return;
        }

        if ($role === 'admin') {
            // 🔥 admin hapus permanen
            $deleted = $this->forum_model->delete_message_by_id($id);
        } else {
            // 🔥 user hanya hapus untuk dirinya sendiri
            $deleted = $this->forum_model->delete_message_for_user($id, $user_id);
        }

        echo json_encode(['success' => $deleted]);
    }

    public function fix_message_id()
    {
        $file = FCPATH . 'data/messages.json';

        if (!file_exists($file)) {
            echo "File tidak ditemukan";
            return;
        }

        $data = json_decode(file_get_contents($file), true);

        foreach ($data as &$m) {
            if (!isset($m['id'])) {
                $m['id'] = uniqid(); // 🔥 tambahkan id
            }
        }

        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

        echo "Berhasil tambah ID ke semua message";
    }

    public function delete_message_for_me()
    {
        $id = $this->input->post('id');
        $user_id = $this->session->userdata('id_users');

        if (!$id || !$user_id) {
            echo json_encode(['success' => false]);
            return;
        }

        $deleted = $this->forum_model->delete_message_for_user($id, $user_id);

        echo json_encode(['success' => $deleted]);
    }

    // =====================ARSIP TOPIK=====================
    public function archive_topic($id = null)
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            return $this->output->set_status_header(405)
                ->set_output(json_encode(['error' => 'Method not allowed']));
        }

        if (!$id) {
            return $this->output->set_status_header(400)
                ->set_output(json_encode(['error' => 'Missing id']));
        }

        $success = $this->forum_model->archive_topic($id);

        if (!$success) {
            return $this->output->set_status_header(500)
                ->set_output(json_encode(['error' => 'Gagal arsipkan topik']));
        }

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode(['success' => true]));
    }

    public function arsip()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        $topics = $this->forum_model->get_archived_topics();
        foreach ($topics as &$t) {
            $messages = $this->forum_model->get_messages($t['id']);
            $t['total_messages'] = is_array($messages) ? count($messages) : 0;
        }
        unset($t);
        $data['topics'] = $topics;

        $this->load->view('forum/arsip', $data);
    }

    public function close_topic($id)
    {
        $json = @file_get_contents(FCPATH . 'forums_data/topics.json');
        $topics = json_decode($json, true);
        if (!is_array($topics)) $topics = [];

        foreach ($topics as &$t) {
            if ($t['id'] == $id) {
                $t['archived'] = true;
                break;
            }
        }

        // simpan kembali ke JSON
        file_put_contents(FCPATH . 'forums_data/topics.json', json_encode($topics, JSON_PRETTY_PRINT));

        echo json_encode(['success' => true]);
    }
    public function faq()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        $topics = $this->forum_model->get_faq_topics();
        foreach ($topics as &$t) {
            $messages = $this->forum_model->get_messages($t['id']);
            $t['total_messages'] = is_array($messages) ? count($messages) : 0;
        }
        unset($t);
        $data['topics'] = $topics;

        $this->load->view('forum/faq', $data);
    }

    public function set_faq($id)
    {
        $success = $this->forum_model->set_faq($id);

        echo json_encode(['success' => $success]);
    }
}
