<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forum_model extends CI_Model
{
    protected $storage_path;
    protected $topics_file;

    public function __construct()
    {
        parent::__construct();

        // load session biar bisa ambil user login
        $this->load->library('session');

        $this->storage_path = FCPATH . 'forums_data/';
        if (!is_dir($this->storage_path)) {
            mkdir($this->storage_path, 0755, true);
            file_put_contents($this->storage_path . 'index.html', '<!-- hidden -->');
        }

        $this->topics_file = $this->storage_path . 'topics.json';

        if (!file_exists($this->topics_file)) {
            file_put_contents($this->topics_file, json_encode([]));
        }

        //arsip topik
        $this->archived_file = $this->storage_path . 'archived_topics.json';

        if (!file_exists($this->archived_file)) {
            file_put_contents($this->archived_file, json_encode([]));
        }
    }

    // ========================
    // GET ALL TOPICS
    // ========================
    public function get_topics()
    {
        $json = @file_get_contents($this->topics_file);
        $topics = json_decode($json, true);

        if (!is_array($topics)) {
            $topics = [];
        }

        // 🔥 FILTER: buang yang FAQ
        $topics = array_filter($topics, function ($t) {
            return empty($t['is_faq']); // hanya yang bukan FAQ
        });

        // 🔥 REINDEX (PENTING)
        $topics = array_values($topics);

        // 🔥 SORTING terbaru
        usort($topics, function ($a, $b) {
            return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
        });

        return $topics;
    }

    // ========================
    // ADD TOPIC (FIX UTAMA DI SINI)
    // ========================
    public function add_topic($title, $created_by = null)
    {
        // ✅ VALIDASI TITLE
        if (!$title || trim($title) === '') {
            return false;
        }

        // ✅ AMBIL RAW DATA (BUKAN get_topics)
        $json = @file_get_contents($this->topics_file);
        $topics = json_decode($json, true);

        if (!is_array($topics)) {
            $topics = [];
        }

        // ✅ AMBIL USER SESSION DENGAN AMAN
        if (!$created_by) {
            $created_by = $this->session->userdata('nip');
            if (!$created_by) {
                $created_by = 'Guest';
            }
        }

        // ✅ BUAT DATA TOPIC
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = trim($slug, '-');
        if (empty($slug)) {
            $slug = 'topic';
        }
        $id = $slug . '-' . substr(uniqid(), -5);

        $topic = [
            'id' => $id,
            'title' => trim($title),
            'created_at' => time(),
            'created_by' => $created_by
        ];

        // ✅ MASUKKAN KE AWAL
        array_unshift($topics, $topic);

        // ✅ SIMPAN DENGAN LOCK (PENTING)
        $save = file_put_contents(
            $this->topics_file,
            json_encode($topics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );

        // ❌ CEK GAGAL SIMPAN
        if ($save === false) {
            return false;
        }

        // ✅ BUAT FILE MESSAGE OTOMATIS
        $messages_file = $this->get_messages_file($id);
        if (!file_exists($messages_file)) {
            file_put_contents($messages_file, json_encode([]));
        }

        return $topic;
    }
    // ========================
    // FIND TOPIC
    // ========================
    // public function find_topic($id)
    // {
    //     $topics = $this->get_topics();
    //     foreach ($topics as $t) {
    //         if ($t['id'] == $id)
    //             return $t;
    //     }
    //     return null;
    // }

    public function find_topic($id)
    {
        // 🔍 cek di semua topic (termasuk FAQ)
        $json = @file_get_contents($this->topics_file);
        $topics = json_decode($json, true);
        if (is_array($topics)) {
            foreach ($topics as $t) {
                if ($t['id'] == $id) {
                    return $t;
                }
            }
        }

        // 🔍 cek di arsip
        $archived = json_decode(@file_get_contents($this->archived_file), true);
        if (is_array($archived)) {
            foreach ($archived as $t) {
                if ($t['id'] == $id) {
                    $t['archived'] = true; // 🔥 tandai
                    return $t;
                }
            }
        }

        return null;
    }

    // ========================
    // FILE MESSAGE
    // ========================
    protected function get_messages_file($id)
    {
        return $this->storage_path . 'topic_' . $id . '.json';
    }

    // ========================
    // GET MESSAGES
    // ========================
    public function get_messages($id, $limit = 200)
    {
        $file = $this->get_messages_file($id);
        if (!file_exists($file))
            return [];

        $json = @file_get_contents($file);
        $messages = json_decode($json, true);

        if (!is_array($messages))
            $messages = [];

        $user_id = $this->session->userdata('id_users');
        $filtered = [];

        foreach ($messages as $m) {

            // 🔥 filter pesan yg sudah dihapus user ini
            if (!empty($m['deleted_by'])) {
                $deleted = explode(',', $m['deleted_by']);
                if (in_array($user_id, $deleted)) {
                    continue;
                }
            }

            $filtered[] = $m;
        }

        if (count($filtered) > $limit) {
            $filtered = array_slice($filtered, -$limit);
        }

        return $filtered;
    }

    // ========================
    // ADD MESSAGE (BONUS: SIMPAN USER)
    // ========================
    public function add_message($id, $data)
    {
        if (!isset($data['message']) || trim($data['message']) === '') {
            return false;
        }

        $file = $this->get_messages_file($id);

        // 🔥 ambil RAW data, jangan pakai get_messages()
        $messages = [];
        if (file_exists($file)) {
            $json = @file_get_contents($file);
            $messages = json_decode($json, true);
        }

        if (!is_array($messages)) {
            $messages = [];
        }

        $entry = [
            'id' => $data['id'] ?? uniqid(),
            'message' => trim($data['message']),
            'created_at' => time(),
            'created_by' => $data['created_by'] ?? 'Unknown',
            'avatar' => $data['avatar'] ?? null
        ];

        $messages[] = $entry;

        // 🔥 WAJIB pakai LOCK_EX
        file_put_contents(
            $file,
            json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );

        return $entry;
    }
    public function delete_topic($id)
    {
        if (!$id)
            return false;

        $topics = json_decode(@file_get_contents($this->topics_file), true);
        if (!is_array($topics))
            $topics = [];

        $found = false;
        foreach ($topics as $k => $t) {
            if ($t['id'] == $id) {
                $found = true;
                unset($topics[$k]);
                break;
            }
        }

        if (!$found)
            return false;

        $topics = array_values($topics);

        if (false === file_put_contents($this->topics_file, json_encode($topics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX)) {
            return false;
        }

        $msgfile = $this->get_messages_file($id);
        if (file_exists($msgfile)) {
            @unlink($msgfile);
        }

        return true;
    }
    // ========================
    // DASHBOARD HELPERS
    // ========================
    public function count_topics()
    {
        $topics = $this->get_topics();
        return count($topics);
    }

    public function get_latest_topics($limit = 5)
    {
        $topics = $this->get_topics();
        return array_slice($topics, 0, $limit);
    }

    public function get_popular_topics($limit = 5)
    {
        $topics = $this->get_topics();
        foreach ($topics as &$t) {
            $file = $this->get_messages_file($t['id']);
            if (file_exists($file)) {
                $msgs = json_decode(@file_get_contents($file), true);
                $count = is_array($msgs) ? count($msgs) : 0;
            } else {
                $count = 0;
            }
            $t['messages_count'] = $count;
        }
        usort($topics, function ($a, $b) {
            if (($b['messages_count'] ?? 0) === ($a['messages_count'] ?? 0)) {
                return $b['created_at'] - $a['created_at'];
            }
            return ($b['messages_count'] ?? 0) - ($a['messages_count'] ?? 0);
        });
        return array_slice($topics, 0, $limit);
    }

    public function get_recent_activity($limit = 10)
    {
        $activities = [];
        $topics = $this->get_topics();
        foreach ($topics as $t) {
            $msgs = $this->get_messages($t['id']);
            foreach ($msgs as $m) {
                $activities[] = [
                    'topic_id' => $t['id'],
                    'topic_title' => $t['title'],
                    'message' => $m['message'] ?? '',
                    'created_at' => $m['created_at'] ?? 0,
                    'created_by' => $m['created_by'] ?? ($m['fullname'] ?? 'Unknown'),
                    'avatar' => $m['avatar'] ?? null,
                ];
            }
        }
        usort($activities, function ($a, $b) {
            return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
        });
        return array_slice($activities, 0, $limit);
    }

    // get topics created by a specific user (match by username or fullname)
    public function get_topics_by_user($user, $limit = 50)
    {
        if (!$user)
            return [];
        $topics = $this->get_topics();
        $out = [];
        foreach ($topics as $t) {
            if (isset($t['created_by']) && ($t['created_by'] === $user)) {
                $out[] = $t;
            }
        }
        return array_slice($out, 0, $limit);
    }

    // get recent messages posted by a specific user across all topics
    public function get_recent_activity_by_user($user, $limit = 10)
    {
        if (!$user)
            return [];
        $acts = [];
        $topics = $this->get_topics();
        foreach ($topics as $t) {
            $msgs = $this->get_messages($t['id']);
            foreach ($msgs as $m) {
                $created_by = $m['created_by'] ?? ($m['fullname'] ?? '');
                if ($created_by === $user) {
                    $acts[] = [
                        'topic_id' => $t['id'],
                        'topic_title' => $t['title'],
                        'message' => $m['message'] ?? '',
                        'created_at' => $m['created_at'] ?? 0,
                        'created_by' => $created_by,
                        'avatar' => $m['avatar'] ?? null,
                    ];
                }
            }
        }
        usort($acts, function ($a, $b) {
            return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
        });
        return array_slice($acts, 0, $limit);
    }

    // ========================
    // USER JOINED TOPICS (flatfile storage)
    // ========================
    public function join_topic($username, $topic_id)
    {
        if (!$username || !$topic_id)
            return false;

        $file = APPPATH . 'data/user_topics.json';
        if (!is_dir(APPPATH . 'data'))
            @mkdir(APPPATH . 'data', 0755, true);

        $local = [];
        if (file_exists($file)) {
            $json = @file_get_contents($file);
            $local = json_decode($json, true);
            if (!is_array($local))
                $local = [];
        }

        if (!isset($local[$username]) || !is_array($local[$username])) {
            $local[$username] = [];
        }

        if (!in_array($topic_id, $local[$username])) {
            $local[$username][] = $topic_id;
        }

        return (bool) @file_put_contents($file, json_encode($local, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    public function get_user_topics($username)
    {
        if (!$username)
            return [];

        $file = APPPATH . 'data/user_topics.json';
        if (!file_exists($file))
            return [];

        $json = @file_get_contents($file);
        $local = json_decode($json, true);
        if (!is_array($local) || !isset($local[$username]) || !is_array($local[$username]))
            return [];

        $out = [];
        foreach ($local[$username] as $tid) {
            $t = $this->find_topic($tid);
            if ($t)
                $out[] = $t;
        }

        usort($out, function ($a, $b) {
            return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
        });

        return $out;
    }

    public function get_user_topics_with_count($username)
    {
        $topics = $this->get_user_topics($username);

        foreach ($topics as &$t) {
            $messages = $this->get_messages($t['id']);
            $t['total_messages'] = is_array($messages) ? count($messages) : 0;
        }

        return $topics;
    }

    public function delete_message_by_id($id)
    {
        if (!$id)
            return false;

        // ambil semua topic
        $topics = $this->get_topics();

        foreach ($topics as $t) {

            $file = $this->get_messages_file($t['id']);

            if (!file_exists($file))
                continue;

            $messages = json_decode(file_get_contents($file), true);

            if (!is_array($messages))
                continue;

            $newMessages = [];

            foreach ($messages as $m) {

                // simpan data lama yg belum punya id
                if (!isset($m['id'])) {
                    $newMessages[] = $m;
                    continue;
                }

                // simpan semua kecuali yang dihapus
                if ($m['id'] != $id) {
                    $newMessages[] = $m;
                }
            }

            // simpan ulang file
            file_put_contents(
                $file,
                json_encode($newMessages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }

        return true;
    }

    public function delete_message_for_user($message_id, $user_id)
    {
        if (!$message_id || !$user_id)
            return false;

        $topics = $this->get_topics();

        foreach ($topics as $t) {

            $file = $this->get_messages_file($t['id']);
            if (!file_exists($file))
                continue;

            $messages = json_decode(file_get_contents($file), true);
            if (!is_array($messages))
                continue;

            $updated = false;

            foreach ($messages as &$m) {
                if (isset($m['id']) && $m['id'] == $message_id) {

                    $deleted = isset($m['deleted_by']) ? explode(',', $m['deleted_by']) : [];

                    if (!in_array($user_id, $deleted)) {
                        $deleted[] = $user_id;
                    }

                    $m['deleted_by'] = implode(',', $deleted);
                    $updated = true;
                    break;
                }
            }

            if ($updated) {
                file_put_contents(
                    $file,
                    json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );
                return true;
            }
        }

        return false;
    }

    // Dashboard Model Mulai dari sini 
    public function add_log($data)
    {
        return $this->db->insert('activity_log', $data);
    }

    public function get_topics_by_creator($username)
    {
        $topics = $this->get_topics();
        $result = [];

        foreach ($topics as $t) {
            $creator = strtolower(trim($t['created_by'] ?? ''));
            $user = strtolower(trim($username));

            if ($creator === $user) {

                // hitung jumlah message
                $messages = $this->get_messages($t['id']);
                $t['total_messages'] = is_array($messages) ? count($messages) : 0;

                $result[] = $t;
            }
        }

        // sorting terbaru
        usort($result, function ($a, $b) {
            return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
        });

        return $result;
    }

    // ============================ARSIP TOPIK============================
    public function archive_topic($id)
    {
        $topics = json_decode(@file_get_contents($this->topics_file), true);
        if (!is_array($topics)) $topics = [];

        $archived = json_decode(@file_get_contents($this->archived_file), true);
        if (!is_array($archived))
            $archived = [];

        $found = null;

        foreach ($topics as $k => $t) {
            if ($t['id'] == $id) {
                $found = $t;
                unset($topics[$k]);
                break;
            }
        }

        if (!$found)
            return false;

        // tambahkan flag arsip
        $found['archived_at'] = time();

        $archived[] = $found;

        // simpan ulang
        file_put_contents($this->topics_file, json_encode(array_values($topics), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($this->archived_file, json_encode($archived, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return true;
    }

    public function get_archived_topics()
    {
        $data = json_decode(@file_get_contents($this->archived_file), true);
        if (!is_array($data))
            return [];

        usort($data, function ($a, $b) {
            return ($b['archived_at'] ?? 0) - ($a['archived_at'] ?? 0);
        });

        return $data;
    }

    //=========================FAQ=================================
    public function set_faq($id)
    {
        // 🔥 ambil RAW data
        $json = @file_get_contents($this->topics_file);
        $topics = json_decode($json, true);

        if (!is_array($topics)) {
            return false;
        }

        foreach ($topics as &$t) {
            if ($t['id'] == $id) {
                $t['is_faq'] = 1;
                break;
            }
        }

        file_put_contents(
            $this->topics_file,
            json_encode($topics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );

        return true;
    }

    public function get_faq_topics()
    {
        // 🔥 ambil langsung dari file (RAW)
        $json = @file_get_contents($this->topics_file);
        $topics = json_decode($json, true);

        if (!is_array($topics)) {
            return [];
        }

        // 🔥 filter yang is_faq = 1
        $faq = array_filter($topics, function ($t) {
            return !empty($t['is_faq']);
        });

        // 🔥 reindex
        return array_values($faq);
    }
}