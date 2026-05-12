-- =====================================================
-- Activity Log System - SQL Schema
-- =====================================================

-- Create table activity_log
CREATE TABLE IF NOT EXISTS activity_log (
    id_log_activity INT(10) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(10),
    action VARCHAR(50),
    target_id VARCHAR(100),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Sample Data (Optional - untuk testing)
-- =====================================================

-- Insert sample login activities
INSERT INTO activity_log (user_id, action, target_id, description, created_at) VALUES
(1, 'LOGIN', NULL, 'User ''John Doe'' berhasil login dari IP 192.168.1.1', '2024-01-15 10:30:45'),
(2, 'LOGIN', NULL, 'User ''Jane Smith'' berhasil login dari IP 192.168.1.5', '2024-01-15 10:35:20'),
(1, 'LOGIN', NULL, 'User ''John Doe'' berhasil login dari IP 192.168.1.1', '2024-01-15 14:20:10'),
(3, 'LOGIN', NULL, 'User ''Admin User'' berhasil login dari IP 192.168.1.10', '2024-01-15 15:00:00'),
(2, 'LOGIN', NULL, 'User ''Jane Smith'' berhasil login dari IP 192.168.1.5', '2024-01-15 16:45:30');

-- Insert sample create topic activities
INSERT INTO activity_log (user_id, action, target_id, description, created_at) VALUES
(1, 'CREATE_TOPIC', 'bagaimana-cara-install-laravel-12345', 'User ''John Doe'' membuat topik baru dengan judul ''Bagaimana cara install Laravel?''', '2024-01-15 10:40:00'),
(2, 'CREATE_TOPIC', 'error-500-internal-server-error-54321', 'User ''Jane Smith'' membuat topik baru dengan judul ''Error 500 Internal Server Error''', '2024-01-15 10:50:15'),
(1, 'CREATE_TOPIC', 'membuat-database-dengan-mysql-67890', 'User ''John Doe'' membuat topik baru dengan judul ''Membuat Database dengan MySQL''', '2024-01-15 11:00:30'),
(2, 'CREATE_TOPIC', 'upload-file-di-codeigniter-11111', 'User ''Jane Smith'' membuat topik baru dengan judul ''Upload File di CodeIgniter''', '2024-01-15 11:15:45'),
(1, 'CREATE_TOPIC', 'validasi-form-dengan-jquery-22222', 'User ''John Doe'' membuat topik baru dengan judul ''Validasi Form dengan jQuery''', '2024-01-15 11:30:00');

-- Insert sample archive topic activities
INSERT INTO activity_log (user_id, action, target_id, description, created_at) VALUES
(1, 'ARCHIVE_TOPIC', 'error-500-internal-server-error-54321', 'User ''John Doe'' mengarsipkan topik dengan ID ''error-500-internal-server-error-54321''', '2024-01-15 12:00:00'),
(2, 'ARCHIVE_TOPIC', 'upload-file-di-codeigniter-11111', 'User ''Jane Smith'' menutup/mengarsipkan topik dengan ID ''upload-file-di-codeigniter-11111''', '2024-01-15 12:30:45'),
(1, 'ARCHIVE_TOPIC', 'validasi-form-dengan-jquery-22222', 'User ''John Doe'' mengarsipkan topik dengan ID ''validasi-form-dengan-jquery-22222''', '2024-01-15 13:00:00');

-- Insert sample mark FAQ activities
INSERT INTO activity_log (user_id, action, target_id, description, created_at) VALUES
(3, 'MARK_FAQ', 'bagaimana-cara-install-laravel-12345', 'Admin ''Admin User'' menjadikan topik dengan ID ''bagaimana-cara-install-laravel-12345'' sebagai FAQ', '2024-01-15 14:00:00'),
(3, 'MARK_FAQ', 'membuat-database-dengan-mysql-67890', 'Admin ''Admin User'' menjadikan topik dengan ID ''membuat-database-dengan-mysql-67890'' sebagai FAQ', '2024-01-15 14:30:00');

-- =====================================================
-- Useful Queries
-- =====================================================

-- Get all login activities
-- SELECT * FROM activity_log WHERE action = 'LOGIN' ORDER BY created_at DESC;

-- Get all activities by user
-- SELECT * FROM activity_log WHERE user_id = 1 ORDER BY created_at DESC;

-- Get all activities for specific topic
-- SELECT * FROM activity_log WHERE target_id = 'bagaimana-cara-install-laravel-12345' ORDER BY created_at DESC;

-- Get activity count by action
-- SELECT action, COUNT(*) as total FROM activity_log GROUP BY action;

-- Get latest 10 activities
-- SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10;

-- Get activities from last 7 days
-- SELECT * FROM activity_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY created_at DESC;

-- Get activities with user details
-- SELECT al.*, u.username, u.name FROM activity_log al LEFT JOIN users u ON al.user_id = u.id_users ORDER BY al.created_at DESC;
