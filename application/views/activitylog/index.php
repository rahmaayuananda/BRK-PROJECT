<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/forum.css'); ?>">
    <style>
        .activity-log-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
        }

        .log-header h1 {
            margin: 0;
            color: #333;
        }

        .filter-section {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .filter-section form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-section select,
        .filter-section input {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-section button {
            padding: 8px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .filter-section button:hover {
            background: #0056b3;
        }

        .clear-filter {
            background: #6c757d !important;
        }

        .clear-filter:hover {
            background: #5a6268 !important;
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .log-table thead {
            background: #007bff;
            color: white;
        }

        .log-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .log-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .log-table tbody tr:hover {
            background: #f9f9f9;
        }

        .action-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .action-badge.login {
            background: #28a745;
            color: white;
        }

        .action-badge.create_topic {
            background: #17a2b8;
            color: white;
        }

        .action-badge.archive_topic {
            background: #ffc107;
            color: #333;
        }

        .action-badge.mark_faq {
            background: #e83e8c;
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
        }

        .timestamp {
            color: #666;
            font-size: 13px;
        }

        .description {
            color: #555;
            font-size: 13px;
            max-width: 300px;
            white-space: normal;
            word-wrap: break-word;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
        }

        .pagination a:hover {
            background: #007bff;
            color: white;
        }

        .pagination .active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="activity-log-container">
        <div class="log-header">
            <h1>📋 Activity Log</h1>
            <a href="<?php echo base_url('forum'); ?>" class="btn btn-secondary">Back to Forum</a>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="<?php echo site_url('activitylog'); ?>">
                <select name="action">
                    <option value="">-- Filter by Action --</option>
                    <option value="LOGIN" <?php echo ($filters['action'] === 'LOGIN') ? 'selected' : ''; ?>>LOGIN</option>
                    <option value="CREATE_TOPIC" <?php echo ($filters['action'] === 'CREATE_TOPIC') ? 'selected' : ''; ?>>CREATE_TOPIC</option>
                    <option value="ARCHIVE_TOPIC" <?php echo ($filters['action'] === 'ARCHIVE_TOPIC') ? 'selected' : ''; ?>>ARCHIVE_TOPIC</option>
                    <option value="MARK_FAQ" <?php echo ($filters['action'] === 'MARK_FAQ') ? 'selected' : ''; ?>>MARK_FAQ</option>
                </select>

                <input type="text" name="user_id" placeholder="Filter by User ID" value="<?php echo $filters['user_id'] ?? ''; ?>">
                <input type="text" name="target_id" placeholder="Filter by Target ID" value="<?php echo $filters['target_id'] ?? ''; ?>">

                <button type="submit">🔍 Filter</button>
                <a href="<?php echo site_url('activitylog'); ?>" class="clear-filter" style="display: inline-block; padding: 8px 15px; text-decoration: none; border-radius: 4px; cursor: pointer;">Clear</a>
            </form>
        </div>

        <!-- Activity Log Table -->
        <table class="log-table">
            <thead>
                <tr>
                    <th style="width: 15%;">User</th>
                    <th style="width: 15%;">Action</th>
                    <th style="width: 15%;">Target ID</th>
                    <th style="width: 40%;">Description</th>
                    <th style="width: 15%;">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activities)): ?>
                    <tr>
                        <td colspan="5" class="no-data">No activity logs found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar"><?php echo strtoupper(substr($activity['name'] ?? $activity['username'] ?? 'U', 0, 1)); ?></div>
                                    <div>
                                        <div><?php echo $activity['name'] ?? $activity['username']; ?></div>
                                        <div style="font-size: 12px; color: #999;">(ID: <?php echo $activity['user_id']; ?>)</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="action-badge <?php echo strtolower($activity['action']); ?>">
                                    <?php echo $activity['action']; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $activity['target_id'] ?? '-'; ?>
                            </td>
                            <td>
                                <div class="description"><?php echo $activity['description']; ?></div>
                            </td>
                            <td>
                                <div class="timestamp"><?php echo date('d M Y, H:i:s', strtotime($activity['created_at'])); ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_activities > $limit): ?>
            <div class="pagination">
                <?php
                $total_pages = ceil($total_activities / $limit);
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);

                if ($current_page > 1):
                    $query = http_build_query($filters);
                    echo '<a href="' . site_url('activitylog?page=1' . ($query ? '&' . $query : '')) . '">First</a>';
                    echo '<a href="' . site_url('activitylog?page=' . ($current_page - 1) . ($query ? '&' . $query : '')) . '">Previous</a>';
                endif;

                for ($i = $start_page; $i <= $end_page; $i++):
                    $query = http_build_query($filters);
                    if ($i == $current_page):
                        echo '<span class="active">' . $i . '</span>';
                    else:
                        echo '<a href="' . site_url('activitylog?page=' . $i . ($query ? '&' . $query : '')) . '">' . $i . '</a>';
                    endif;
                endfor;

                if ($current_page < $total_pages):
                    $query = http_build_query($filters);
                    echo '<a href="' . site_url('activitylog?page=' . ($current_page + 1) . ($query ? '&' . $query : '')) . '">Next</a>';
                    echo '<a href="' . site_url('activitylog?page=' . $total_pages . ($query ? '&' . $query : '')) . '">Last</a>';
                endif;
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
