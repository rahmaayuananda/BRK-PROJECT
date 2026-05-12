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

                // Map activity_log row to the format expected by vw_dashboard.php
                $recent_activity[] = [
                    'action' => $act['action'] ?? '',
                    'avatar' => $act['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . rawurlencode($act['name'] ?? $act['username'] ?? 'User'),
                    'created_by' => $act['name'] ?? $act['username'] ?? 'User',
                    'topic_id' => $topic_id ?? '',
                    'topic_title' => $topic_title,
                    'created_at' => strtotime($act['created_at']),
                    'message' => mb_substr($act['description'] ?? '', 0, 200)
                ];
            }
            $data['recent_activity'] = $recent_activity;
        } else {
            $data['recent_activity'] = [];
        }

        // provide a safe return URL so profile page can link back to dashboard
        $data['return_url'] = site_url('dashboard');

        $this->load->view('dashboard/vw_dashboard', $data);
    }
}
