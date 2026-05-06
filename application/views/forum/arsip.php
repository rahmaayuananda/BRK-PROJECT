<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Arsip - Forum</title>

    <link rel="stylesheet" href="<?php echo base_url('assets/css/inter.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/forum.css'); ?>">

    <style>
        /* ===== SAMAKAN BACKGROUND ===== */
        body {
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
        }

        /* ===== LAYOUT SAMA KAYAK MY TOPIC ===== */
        .main {
            display: flex;
            gap: 24px;
            padding: 0px 0;
            align-items: flex-start;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* ===== CONTAINER ===== */
        .forum-app {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== SIDEBAR MENU ===== */

        /* RESET */
        /* SAMAKAN DENGAN MY TOPICS */
        .categories li {
            padding: 10px;
        }

        .categories li a {
            display: block;
            text-decoration: none;
            color: inherit;
            width: 100%;
        }

        /* Hover hanya untuk item yang TIDAK active */
        .categories li a:hover {
            background: transparent;
            color: #0d6efd;
        }

        /* Pastikan active lebih kuat */
        .sidebar .categories li.active {
            background: #e0edff;
        }

        .sidebar .categories li.active a {
            color: #0d6efd;
            font-weight: 600;
        }

        .content {
            flex: 1;
            max-width: none;
            /* 🔥 penting */
        }

        /* ===== LOGOUT CARD ===== */
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

        .topic-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #eef0f4;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .topic-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .avatar {
            width: 58px;
            height: 58px;
            border-radius: 999px;
            background: #e0edff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 12px;
        }

        .sidebar {
            width: 260px;
        }

        .topic-card:hover {
            border: 1px solid #f59e0b;
        }

        .topic-list {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <div class="forum-app">
        <?php $this->load->view('layout/header', ['page_title' => 'Arsip']); ?>

        <main class="main">
            <?php $this->load->view('layout/sidebar'); ?>

            <section class="content">
                <div class="topic-list">

                    <?php if (!empty($topics)): ?>
                        <?php foreach ($topics as $t): ?>
                            <div class="topic-card"
                                data-href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=arsip&return=' . urlencode(current_url()); ?>"
                                data-time="<?php echo $t['created_at'] ?? 0; ?>"
                                data-messages="<?php echo isset($t['total_messages']) ? $t['total_messages'] : 0; ?>">
                                <div class="avatar">
                                    <?php echo strtoupper(mb_substr($t['title'], 0, 1)); ?>
                                </div>

                                <div class="info">
                                    <div class="topic-title">
                                        <?php echo htmlentities($t['title']); ?>
                                    </div>

                                    <p class="excerpt">
                                        Diskusi telah ditutup
                                    </p>

                                    <div class="meta">
                                        <?php echo $t['created_by']; ?> •
                                        <?php echo date('d/m/Y H:i', $t['created_at']); ?>
                                    </div>
                                </div>

                                <div class="stats">
                                    <span style="color:#ef4444;font-weight:600;">
                                        🔒 Closed
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding:20px;color:#6b7280">
                            Belum ada arsip.
                        </div>
                    <?php endif; ?>

                </div>
            </section>
        </main>
    </div>
</body>
<script>
    document.querySelectorAll('.topic-card').forEach(card => {
        card.addEventListener('click', function (e) {
            // biar kalau klik link/button ga double
            if (e.target.closest('a') || e.target.closest('button')) return;

            const href = this.getAttribute('data-href');
            if (href) window.location.href = href;
        });

        // biar bisa pakai keyboard juga
        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const href = this.getAttribute('data-href');
                if (href) window.location.href = href;
            }
        });

        card.style.cursor = 'pointer';
    });

    document.getElementById('sortSelect')?.addEventListener('change', function () {
        const sortVal = this.value;
        const container = document.querySelector('.topic-list');
        const cards = Array.from(container.querySelectorAll('.topic-card'));

        cards.sort((a, b) => {
            if (sortVal === 'latest') return b.dataset.time - a.dataset.time;
            if (sortVal === 'oldest') return a.dataset.time - b.dataset.time;
            if (sortVal === 'popular') return b.dataset.messages - a.dataset.messages;
            return 0;
        });

        cards.forEach(card => container.appendChild(card));
    });

    document.getElementById('searchInput')?.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.topic-card').forEach(card => {
            const title = card.querySelector('.topic-title').textContent.toLowerCase();
            if (title.includes(query)) card.style.display = '';
            else card.style.display = 'none';
        });
    });
</script>

</html>