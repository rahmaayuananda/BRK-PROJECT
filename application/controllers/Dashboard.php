<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('forum_model');
        $this->load->model('activity_log');
        $this->load->helper('url');
        $this->load->library('session');
        
        // Set timezone ke Asia/Jakarta (UTC+7)
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        $data['total_topics'] = $this->forum_model->count_topics();
        $data['total_faq'] = count($this->forum_model->get_faq_topics());
        $data['total_arsip'] = count($this->forum_model->get_archived_topics());

        $data['latest_topics'] = $this->forum_model->get_latest_topics(5);
        $data['popular_topics'] = $this->forum_model->get_popular_topics(5);

        // Get per-user activity log from activity_log table
        $user_id = $this->session->userdata('id_users');
        if ($user_id) {
            $activities = $this->activity_log->get_user_activities($user_id, 10);
            $recent_activity = [];
            
            foreach ($activities as $act) {
                // Get topic title if this is a topic-related action
                $topic_title = '';
                $topic_id = $act['target_id'] ?? null;
                if (!empty($topic_id)) {
                    $topic = $this->forum_model->find_topic($topic_id);
                    $topic_title = $topic['title'] ?? '';
                }

                // Clean description - remove IP address part for UI display
                $description = $act['description'] ?? '';
                // Remove "dari IP xxx" part for dashboard display
                $description = preg_replace('/\s+dari\s+IP\s+[\d\.:a-f]+\)?$/', '', $description);
                
                // Map activity_log row to the format expected by vw_dashboard.php
                $recent_activity[] = [
                    'action' => $act['action'] ?? '',
                    'avatar' => $act['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . rawurlencode($act['name'] ?? $act['username'] ?? 'User'),
                    'created_by' => $act['name'] ?? $act['username'] ?? 'User',
                    'topic_id' => $topic_id ?? '',
                    'topic_title' => $topic_title,
                    'created_at' => strtotime($act['created_at']),
                    'message' => mb_substr($description, 0, 200)
                ];
            }

            // Juga ambil aktivitas pesan forum (seperti di halaman profile)
            $username = $this->session->userdata('username');
            $fullname = $this->session->userdata('name');
            $forum_acts_1 = $this->forum_model->get_recent_activity_by_user($fullname, 10);
            $forum_acts_2 = ($fullname && $fullname !== $username)
                ? $this->forum_model->get_recent_activity_by_user($username, 10)
                : [];

            foreach (array_merge($forum_acts_1, $forum_acts_2) as $fa) {
                $recent_activity[] = [
                    'action' => 'KIRIM_PESAN',
                    'avatar' => $fa['avatar'] ?? 'https://ui-avatars.com/api/?name=' . rawurlencode($fa['created_by'] ?? 'User'),
                    'created_by' => $fa['created_by'] ?? 'User',
                    'topic_id' => $fa['topic_id'] ?? '',
                    'topic_title' => $fa['topic_title'] ?? '',
                    'created_at' => $fa['created_at'] ?? 0,
                    'message' => mb_substr($fa['message'] ?? '', 0, 200)
                ];
            }

            // Urutkan semua aktivitas berdasarkan waktu terbaru & limit 10
            usort($recent_activity, function ($a, $b) {
                return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
            });
            $data['recent_activity'] = array_slice($recent_activity, 0, 10);
        } else {
            $data['recent_activity'] = [];
        }

        // provide a safe return URL so profile page can link back to dashboard
        $data['return_url'] = site_url('dashboard');

        $this->load->view('dashboard/vw_dashboard', $data);
    }
}
