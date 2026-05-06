<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard - Forum</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/inter.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/forum.css'); ?>">

    <style>
        .categories li a {
            display: block;
            text-decoration: none;
            color: inherit;
            width: 100%;
        }

        .categories li a:hover {
            color: var(--accent);
        }

        /* ===== DASHBOARD MODERN ===== */

        /* Title */
        .topbar h2 {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Card umum */
        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        /* Statistik */
        .stat-card {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(13, 110, 253, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--accent);
        }

        .stat-title {
            font-size: 14px;
            color: #64748b;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
        }

        /* Section title */
        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #0f172a;
        }

        /* List */
        .list-item {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item a {
            font-weight: 600;
            color: #0b1724;
            text-decoration: none;
        }

        .list-item a:hover {
            color: var(--accent);
        }

        /* Activity */
        .activity {
            display: flex;
            gap: 12px;
        }

        .activity img {
            width: 40px;
            height: 40px;
            border-radius: 999px;
        }

        .activity-content {
            flex: 1;
        }

        /* Grid dashboard */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 12px;
        }

        /* Logout Card */
        .logout-card {
            margin-top: 12px;
            text-decoration: none;
            display: block;
            transition: all 0.2s ease;
        }

        .logout-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
            background: #fff5f5;
        }

        .logout-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logout-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(220, 38, 38, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .logout-title {
            font-weight: 700;
            color: #dc2626;
        }

        .logout-sub {
            font-size: 12px;
            color: #64748b;
        }

        body {
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
        }

        /* Notification unread styles */
        #notifDropdown .notif-item.notif-unread {
            background: linear-gradient(90deg, rgba(13, 110, 253, 0.06), rgba(255, 255, 255, 0));
            border-left: 4px solid #0d6efd;
            padding-left: 12px;
        }

        #notifDropdown .unread-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #0d6efd;
            border-radius: 999px;
            margin-right: 8px;
            vertical-align: middle;
        }

        /* Scrollable notification list */
        #notifDropdown {
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            max-width: 320px;
        }

        #notifDropdown .notif-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 6px 8px 6px;
        }

        #notifDropdown .notif-list {
            max-height: 360px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 6px;
            -webkit-overflow-scrolling: touch;
        }

        #notifDropdown .notif-list::-webkit-scrollbar {
            width: 8px;
        }

        #notifDropdown .notif-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        /* Recent activity scrollable list */
        .activity-list {
            max-height: 360px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 6px;
            -webkit-overflow-scrolling: touch;
        }

        .activity-list::-webkit-scrollbar {
            width: 8px;
        }

        .activity-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        .categories li {
            padding: 10px;
            border-radius: 8px;
        }

        .categories li.active {
            background: #e0edff;
        }

        .categories li.active a {
            color: var(--accent);
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="forum-app">
        <?php
        $fullnameHeader = $this->session && $this->session->userdata('logged_in')
            ? ($this->session->userdata('nama_lengkap') ? $this->session->userdata('nama_lengkap') : $this->session->userdata('nip'))
            : '';
        $this->load->view('layout/header', [
            'page_title' => 'Dashboard',
            'subtitle' => 'Selamat Datang, ' . ($fullnameHeader ?: ''),
            'show_search' => false,
            'show_sort' => false,
            'show_new_button' => false
        ]);
        ?>

        <main class="main">
            <?php $this->load->view('layout/sidebar'); ?>

            <section class="content">

                <!-- Statistik -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div class="card stat-card">
                        <div class="stat-icon" style="background: rgba(13, 110, 253, 0.1); color: #0d6efd;">📊</div>
                        <div>
                            <div class="stat-title">Total Topik</div>
                            <div class="stat-value"><?php echo intval($total_topics); ?></div>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">⭐</div>
                        <div>
                            <div class="stat-title">Total FAQ</div>
                            <div class="stat-value"><?php echo isset($total_faq) ? intval($total_faq) : 0; ?></div>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">🔒</div>
                        <div>
                            <div class="stat-title">Total Arsip</div>
                            <div class="stat-value"><?php echo isset($total_arsip) ? intval($total_arsip) : 0; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Grid -->
                <div class="dashboard-grid">

                    <!-- Topik Terbaru -->
                    <div class="card">
                        <div class="section-title">🆕 Topik Terbaru</div>

                        <?php if (!empty($latest_topics)): ?>
                            <?php foreach ($latest_topics as $t): ?>
                                <div class="list-item">
                                    <a
                                        href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=dashboard&return=' . urlencode(site_url('dashboard')); ?>">
                                        <?php echo htmlentities($t['title'] ?? ''); ?>
                                    </a>
                                    <div class="meta">
                                        <?php echo htmlentities($t['created_by'] ?? 'Unknown'); ?> •
                                        <span class="item-ts"
                                            data-ts="<?php echo intval($t['created_at']); ?>"><?php echo date('d/m/Y H:i', $t['created_at']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="meta">Belum ada topik.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Topik Populer -->
                    <div class="card">
                        <div class="section-title">🔥 Topik Populer</div>

                        <?php if (!empty($popular_topics)): ?>
                            <?php foreach ($popular_topics as $t): ?>
                                <div class="list-item" style="display:flex;justify-content:space-between;">
                                    <a
                                        href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=dashboard&return=' . urlencode(site_url('dashboard')); ?>">
                                        <?php echo htmlentities($t['title'] ?? ''); ?>
                                    </a>
                                    <span class="meta"><?php echo intval($t['messages_count']); ?> pesan</span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="meta">Belum ada data populer.</div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Aktivitas -->
                <div class="card" style="margin-top:16px;">
                    <div class="section-title">⚡ Aktivitas Terakhir</div>

                    <?php if (!empty($recent_activity)): ?>
                        <div class="activity-list">
                            <?php foreach ($recent_activity as $act): ?>
                                <div class="activity" style="margin-bottom:12px;">
                                    <img
                                        src="<?php echo $act['avatar'] ?: 'https://ui-avatars.com/api/?name=' . rawurlencode($act['created_by'] ?? 'User'); ?>">

                                    <div class="activity-content">
                                        <a
                                            href="<?php echo site_url('forum/topic/' . $act['topic_id']) . '?from=dashboard&return=' . urlencode(site_url('dashboard')); ?>">
                                            <?php echo htmlentities($act['topic_title']); ?>
                                        </a>

                                        <div class="meta">
                                            <?php echo htmlentities($act['created_by'] ?? 'Unknown'); ?>
                                            <span class="activity-ts"
                                                data-ts="<?php echo intval($act['created_at']); ?>"><?php echo date('d/m/Y H:i', $act['created_at']); ?></span>
                                        </div>

                                        <div style="margin-top:4px;color:var(--muted);">
                                            <?php echo htmlentities(mb_substr($act['message'], 0, 100)); ?>...
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="meta">Belum ada aktivitas.</div>
                    <?php endif; ?>
                </div>

            </section>
        </main>
    </div>
    <script>
        (function () {
            const loggedIn = <?php echo json_encode((bool) ($this->session && $this->session->userdata('logged_in'))); ?>;
            window.__USER_JOINED_TOPICS = window.__USER_JOINED_TOPICS || new Set();

            // Inisialisasi WebSocket untuk notifikasi realtime
            (function initWs() {
                const wsUrl = (location.protocol === 'https:' ? 'wss://' : 'ws://') + location.hostname + ':8080';
                try {
                    const ws = new WebSocket(wsUrl);
                    ws.addEventListener('message', function (ev) {
                        try {
                            const d = JSON.parse(ev.data);
                            if (d.type === 'message' && typeof window.addNotification === 'function') {
                                window.addNotification(d.data || d);
                            }
                        } catch (e) { }
                    });
                } catch (e) { }
            })();

            if (loggedIn && typeof window.loadNotifications === 'function') {
                try { window.loadNotifications(); } catch (e) { }
            }

            document.addEventListener("DOMContentLoaded", function () {

                // 2. Pemformatan Timestamp (Sekali jalan, lebih efisien)
                const tsElements = document.querySelectorAll('.item-ts, .activity-ts');
                tsElements.forEach(el => {
                    const ts = parseInt(el.getAttribute('data-ts'), 10);
                    if (!ts) return;
                    const d = new Date(ts * 1000);
                    const day = String(d.getDate()).padStart(2, '0');
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const year = d.getFullYear();
                    const time = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    el.textContent = `${day}/${month}/${year} ${time}`;
                });
            });
        })();
    </script>
</body>

</html>