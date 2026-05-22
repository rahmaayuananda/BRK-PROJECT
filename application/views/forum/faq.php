<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>FAQ - Forum</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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

        /* Notification unread styles (match dashboard) */
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

        /* Pagination */
        .pagination-bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 12px 0;
        }

        .pagination-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination-buttons button {
            min-width: 36px;
            height: 36px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
        }

        .pagination-buttons button:hover:not(:disabled) {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .pagination-buttons button.active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .pagination-buttons button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .pagination-info {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <div class="forum-app">
        <?php $this->load->view('layout/header', ['page_title' => 'FAQ']); ?>

        <div id="globalAlert" style="display:none;max-width:1100px;margin:12px auto;padding:12px 14px;border-radius:6px;font-weight:600;text-align:center;box-shadow:0 6px 18px rgba(2,6,23,0.06)"></div>

        <main class="main">
            <?php $this->load->view('layout/sidebar'); ?>

            <section class="content">
                <div class="topic-list">

                    <?php if (!empty($topics)): ?>
                        <?php foreach ($topics as $t): ?>
                            <div class="topic-card"
                                data-href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=faq&return=' . urlencode(current_url()); ?>"
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
                                        Pertanyaan yang sering ditanyakan (FAQ)
                                    </p>

                                    <div class="meta">
                                        <?php echo $t['created_by']; ?> •
                                        <?php echo date('d/m/Y H:i', $t['created_at']); ?>
                                    </div>
                                </div>

                                <div class="stats">
                                    <span style="color:#10b981;font-weight:600;">
                                        ⭐ FAQ
                                    </span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding:20px;color:#6b7280">
                            Belum ada FAQ.
                        </div>
                    <?php endif; ?>

                </div>
                <div id="paginationBar" class="pagination-bar"></div>
            </section>
        </main>
    </div>
</body>

<script>
    document.querySelectorAll('.topic-card').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.closest('a') || e.target.closest('button')) return;

            const href = this.getAttribute('data-href');
            if (href) window.location.href = href;
        });

        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const href = this.getAttribute('data-href');
                if (href) window.location.href = href;
            }
        });

        card.style.cursor = 'pointer';
    });

    function handleSetFAQ(id) {
        fetch('<?php echo site_url('forum/set_faq/'); ?>' + id, {
            method: 'POST'
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    showGlobalAlert('Berhasil dijadikan FAQ', 'success');
                    refreshTopics();
                } else {
                    showGlobalAlert('Gagal', 'error');
                }
            });
    }

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
        refreshPagination(true);
    });

    document.getElementById('searchInput')?.addEventListener('input', function () {
        refreshPagination(true);
    });

    // Pagination state
    const ITEMS_PER_PAGE = 10;
    let currentPage = 1;

    function refreshPagination(resetPage) {
        if (resetPage) currentPage = 1;
        const container = document.querySelector('.topic-list');
        if (!container) return;

        const query = document.getElementById('searchInput')?.value.toLowerCase() || '';
        const cards = Array.from(container.querySelectorAll('.topic-card'));

        // Hide all first
        cards.forEach(card => card.style.display = 'none');

        // Filter cards matching query
        const visibleCards = cards.filter(card => {
            const title = card.querySelector('.topic-title').textContent.toLowerCase();
            return title.includes(query);
        });

        const totalFilteredItems = visibleCards.length;
        const totalPages = Math.max(1, Math.ceil(totalFilteredItems / ITEMS_PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;

        const startIdx = (currentPage - 1) * ITEMS_PER_PAGE;
        const pageCards = visibleCards.slice(startIdx, startIdx + ITEMS_PER_PAGE);
        pageCards.forEach(card => card.style.display = '');

        renderPaginationControls(totalPages, totalFilteredItems);
    }

    function renderPaginationControls(totalPages, totalFilteredItems) {
        const bar = document.getElementById('paginationBar');
        if (!bar) return;
        bar.innerHTML = '';

        if (totalPages <= 1) return;

        const btnContainer = document.createElement('div');
        btnContainer.className = 'pagination-buttons';

        // Prev
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '‹ Prev';
        prevBtn.disabled = currentPage <= 1;
        prevBtn.addEventListener('click', function () { if (currentPage > 1) { currentPage--; refreshPagination(); scrollToTop(); } });
        btnContainer.appendChild(prevBtn);

        // Pages
        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
        if (endPage - startPage < maxVisible - 1) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        if (startPage > 1) {
            const firstBtn = document.createElement('button');
            firstBtn.textContent = '1';
            firstBtn.addEventListener('click', function () { currentPage = 1; refreshPagination(); scrollToTop(); });
            btnContainer.appendChild(firstBtn);
            if (startPage > 2) {
                const dots = document.createElement('button');
                dots.textContent = '...';
                dots.disabled = true;
                dots.style.border = 'none';
                dots.style.background = 'none';
                btnContainer.appendChild(dots);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            if (i === currentPage) btn.classList.add('active');
            btn.addEventListener('click', (function (page) {
                return function () { currentPage = page; refreshPagination(); scrollToTop(); };
            })(i));
            btnContainer.appendChild(btn);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('button');
                dots.textContent = '...';
                dots.disabled = true;
                dots.style.border = 'none';
                dots.style.background = 'none';
                btnContainer.appendChild(dots);
            }
            const lastBtn = document.createElement('button');
            lastBtn.textContent = totalPages;
            lastBtn.addEventListener('click', function () { currentPage = totalPages; refreshPagination(); scrollToTop(); });
            btnContainer.appendChild(lastBtn);
        }

        // Next
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = 'Next ›';
        nextBtn.disabled = currentPage >= totalPages;
        nextBtn.addEventListener('click', function () { if (currentPage < totalPages) { currentPage++; refreshPagination(); scrollToTop(); } });
        btnContainer.appendChild(nextBtn);

        bar.appendChild(btnContainer);

        // Info text
        const info = document.createElement('div');
        info.className = 'pagination-info';
        const startItem = (currentPage - 1) * ITEMS_PER_PAGE + 1;
        const endItem = Math.min(currentPage * ITEMS_PER_PAGE, totalFilteredItems);
        info.textContent = startItem + '-' + endItem + ' dari ' + totalFilteredItems + ' topik';
        bar.appendChild(info);
    }

    function scrollToTop() {
        const content = document.querySelector('.content');
        if (content) content.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Initial load
    refreshPagination();

    // show a global alert
    function showGlobalAlert(msg, type) {
        const el = document.getElementById('globalAlert');
        if (!el) return;
        el.textContent = msg || '';
        if (type === 'error') {
            el.style.color = '#dc2626';
            el.style.background = '#fff1f2';
        } else {
            el.style.color = '#065f46';
            el.style.background = '#ecfdf5';
        }
        el.style.display = 'block';
        clearTimeout(window.__globalAlertTimeout);
        window.__globalAlertTimeout = setTimeout(() => { try { el.style.display = 'none'; } catch (e) { } }, 3000);
    }
</script>

</html>