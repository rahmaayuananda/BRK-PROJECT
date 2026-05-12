<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log extends CI_Model
{
    protected $table = 'activity_log';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_table_exists();
    }

    /**
     * Memastikan tabel activity_log ada di database
     */
    private function ensure_table_exists()
    {
        if (!$this->db->table_exists($this->table)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS {$this->table} (
                    id_log_activity INT(10) AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(10),
                    action VARCHAR(50),
                    target_id VARCHAR(100),
                    description TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user_id (user_id),
                    INDEX idx_action (action),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    /**
     * Log activity ke database
     * 
     * @param int $user_id - ID pengguna yang melakukan action
     * @param string $action - Tipe action (LOGIN, CREATE_TOPIC, ARCHIVE_TOPIC, MARK_FAQ)
     * @param string $target_id - ID target (topic_id atau null untuk login)
     * @param string $description - Deskripsi lengkap activity
     * @return bool
     */
    public function log_activity($user_id, $action, $target_id = null, $description = null)
    {
        // Validasi input
        if (empty($user_id) || empty($action)) {
            return false;
        }

        $data = [
            'user_id' => $user_id,
            'action' => $action,
            'target_id' => $target_id,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert($this->table, $data);
    }

    /**
     * Ambil semua activity log dengan pagination
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_all_activities($limit = 50, $offset = 0)
    {
        $this->db->select('al.*, u.username, u.name')
                 ->from($this->table . ' al')
                 ->join('users u', 'al.user_id = u.id_users', 'left')
                 ->order_by('al.created_at', 'DESC')
                 ->limit($limit, $offset);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Ambil activity log berdasarkan user_id
     * 
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public function get_user_activities($user_id, $limit = 50)
    {
        $this->db->select('*')
                 ->from($this->table)
                 ->where('user_id', $user_id)
                 ->order_by('created_at', 'DESC')
                 ->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Ambil activity log berdasarkan action
     * 
     * @param string $action
     * @param int $limit
     * @return array
     */
    public function get_activities_by_action($action, $limit = 50)
    {
        $this->db->select('al.*, u.username, u.name')
                 ->from($this->table . ' al')
                 ->join('users u', 'al.user_id = u.id_users', 'left')
                 ->where('al.action', $action)
                 ->order_by('al.created_at', 'DESC')
                 ->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Ambil activity log untuk topic tertentu
     * 
     * @param string $topic_id
     * @return array
     */
    public function get_topic_activities($topic_id)
    {
        $this->db->select('al.*, u.username, u.name')
                 ->from($this->table . ' al')
                 ->join('users u', 'al.user_id = u.id_users', 'left')
                 ->where('al.target_id', $topic_id)
                 ->order_by('al.created_at', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Total count semua activity
     * 
     * @return int
     */
    public function count_all_activities()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Total count activity by action
     * 
     * @param string $action
     * @return int
     */
    public function count_activities_by_action($action)
    {
        $this->db->where('action', $action);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Ambil activity log dengan filter custom
     * 
     * @param array $filters - ['action' => 'LOGIN', 'user_id' => 1, 'target_id' => 'topic-123']
     * @param int $limit
     * @return array
     */
    public function get_activities_with_filters($filters = [], $limit = 50)
    {
        $this->db->select('al.*, u.username, u.name')
                 ->from($this->table . ' al')
                 ->join('users u', 'al.user_id = u.id_users', 'left');

        if (!empty($filters['action'])) {
            $this->db->where('al.action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $this->db->where('al.user_id', $filters['user_id']);
        }

        if (!empty($filters['target_id'])) {
            $this->db->where('al.target_id', $filters['target_id']);
        }

        $this->db->order_by('al.created_at', 'DESC')
                 ->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Ambil activity berdasarkan date range
     * 
     * @param string $start_date - format: Y-m-d H:i:s
     * @param string $end_date - format: Y-m-d H:i:s
     * @param int $limit
     * @return array
     */
    public function get_activities_by_date_range($start_date, $end_date, $limit = 100)
    {
        $this->db->select('al.*, u.username, u.name')
                 ->from($this->table . ' al')
                 ->join('users u', 'al.user_id = u.id_users', 'left')
                 ->where('al.created_at >=', $start_date)
                 ->where('al.created_at <=', $end_date)
                 ->order_by('al.created_at', 'DESC')
                 ->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Delete activity log berdasarkan ID
     * 
     * @param int $id_log_activity
     * @return bool
     */
    public function delete_activity($id_log_activity)
    {
        return $this->db->delete($this->table, ['id_log_activity' => $id_log_activity]);
    }

    /**
     * Delete activity log berdasarkan user_id
     * 
     * @param int $user_id
     * @return bool
     */
    public function delete_user_activities($user_id)
    {
        return $this->db->delete($this->table, ['user_id' => $user_id]);
    }

    /**
     * Truncate/clear semua activity log
     * 
     * @return bool
     */
    public function clear_all_activities()
    {
        return $this->db->truncate($this->table);
    }
}
