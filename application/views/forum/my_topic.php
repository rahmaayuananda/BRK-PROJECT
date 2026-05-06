<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>My Topics - Forum</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/inter.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/forum.css'); ?>">

    <style>
        /* Confirm delete modal */
        #confirmDeleteModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 9999
        }

        #confirmDeleteModal .modal-box {
            background: white;
            border-radius: 10px;
            max-width: 560px;
            width: 100%;
            padding: 18px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.2)
        }

        #confirmDeleteModal h3 {
            margin: 0 0 8px;
            font-size: 18px
        }

        #confirmDeleteModal .modal-msg {
            margin-bottom: 14px;
            color: #111827
        }

        .btn.danger {
            background: linear-gradient(90deg, #ef4444, #b91c1c);
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer
        }

        #confirmDeleteModal .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end
        }

        /* Global inline alert (success / error) */
        #globalAlert {
            display: none;
            max-width: 1100px;
            margin: 12px auto;
            padding: 12px 14px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06)
        }

        /* action buttons inside topic card (Buka, Hapus) */
        .topic-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 13px;
            min-width: 72px;
            text-decoration: none
        }


        .categories li a {
            display: block;
            text-decoration: none;
            color: inherit;
            width: 100%;
        }

        .categories li a:hover {
            color: var(--accent);
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

        body {
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
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

        .main {
            display: flex;
            gap: 24px;
            padding: 0;
            /* 🔥 hapus jarak atas */
            margin-top: 0;
            /* tambahan biar nempel */
            align-items: flex-start;
        }

        /* Sidebar kiri */
        .sidebar {
            width: 260px;
            flex-shrink: 0;
            /* ✅ biar ga ketekan */
        }

        /* Content kanan */
        .content {
            flex: 1;
            max-width: none;
            /* ❗ ini penting */
        }

        .avatar {
            width: 58px;
            height: 58px;
            border-radius: 999px;
            background: #e0edff; /* 🔵 biru soft */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 12px;
        }

        .info {
            flex: 1;
        }

        .excerpt {
            font-size: 15px;
            margin-top: 6px;
        }

        .meta {
            font-size: 15px;
            color: #6b7280;
            margin-top: 6px;
        }

        .stats {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            gap: 8px;
            min-width: 120px;
        }

        .topic-title {
            font-weight: 700;
            font-size: 16px;
            display: block;
            margin-bottom: 4px;
        }

        .open-btn {
            background: #e0edff;
            color: #2563eb;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
        }

        .btn.danger {
            border: 1px solid #ef4444;
            color: #ef4444;
            background: transparent;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn.danger:hover {
            background: #fee2e2;
        }

        .categories li.active {
            background: #e0edff;
            border-radius: 8px;
        }

        .categories li {
            padding: 10px;
        }

        .sidebar-card {
            background: white;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .topic-list {
            display: flex;
            flex-direction: column;
        }

        .forum-app {
            max-width: 1200px;
            /* samakan dengan All Topic */
            margin: 0 auto;
            /* center seluruh layout */
        }
    </style>
</head>

<body>
    <div class="forum-app">
        <?php $this->load->view('layout/header', ['page_title' => 'My Topics']); ?>

        <div id="globalAlert"></div>

        <main class="main">
            <?php $this->load->view('layout/sidebar'); ?>

            <section class="content">
                <div class="topic-list">
                    <?php
                    $current_nip = $this->session->userdata('nip') ?? '';
                    $current_username = $this->session->userdata('username') ?? '';
                    $current_id = $this->session->userdata('id_users') ?? '';
                    $current_fullname = $this->session->userdata('nama_lengkap') ?? $this->session->userdata('fullname') ?? '';
                    ?>
                    <?php if (!empty($topics) && is_array($topics)): ?>
                        <?php foreach ($topics as $t): ?>
                            <div class="topic-card"
                                data-href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=my_topics&return=' . urlencode(current_url()); ?>">
                                <div class="avatar"><?php echo strtoupper(mb_substr($t['title'], 0, 1, 'UTF-8')); ?></div>
                                <div class="info">
                                    <a class="topic-title"
                                        href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=my_topics&return=' . urlencode(current_url()); ?>">
                                        <?php echo htmlentities($t['title']); ?>
                                    </a>

                                    <p class="excerpt">
                                        Diskusi terbuka — buat pesan singkat (maks 50 karakter).
                                    </p>

                                    <div class="meta">
                                        <?php echo htmlentities($t['created_by'] ?? 'Unknown'); ?> •
                                        <?php echo date('d/m/Y H:i', $t['created_at']); ?>
                                    </div>
                                </div>
                                <div class="stats">
                                    <div class="stat"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#6b7280"
                                                stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg><span><?php echo isset($t['total_messages']) ? $t['total_messages'] : 0; ?></span>
                                    </div>
                                    <!-- <div style="margin-top:6px">
                                        <a class="open-btn topic-action"
                                            href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=my_topics&return=' . urlencode(current_url()); ?>">
                                            Buka
                                        </a>
                                    </div> -->
                                    <?php if (isset($t['created_by']) && ($t['created_by'] == $current_nip || $t['created_by'] == $current_username || $t['created_by'] == $current_id || $t['created_by'] == $current_fullname)): ?>
                                        <div style="margin-top:6px">
                                            <button class="topic-action btn danger delete-btn"
                                                onclick="openDeleteModal('<?php echo $t['id']; ?>', <?php echo json_encode($t['title'] ?? ''); ?>)">
                                                Hapus
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding:20px;color:#6b7280">Anda belum bergabung ke topik manapun.</div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Confirm Delete Modal -->
    <div id="confirmDeleteModal"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;padding:20px;z-index:9999">
        <div class="modal-box"
            style="background:white;border-radius:10px;max-width:560px;width:100%;padding:18px;box-shadow:0 10px 30px rgba(2,6,23,0.2);">
            <h3>Konfirmasi Hapus</h3>
            <div id="confirmDeleteMsg" class="modal-msg">Yakin ingin menghapus topik: <strong
                    id="deleteTopicTitle"></strong>?</div>
            <div class="actions">
                <button type="button" id="cancelDeleteBtn" class="btn">Batal</button>
                <button type="button" id="confirmDeleteBtn" class="btn danger">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        // Load and render user's joined topics, provide search/sort, realtime + notifications, and modals
        (function () {
            async function refreshTopics() {
                try {
                    const res = await fetch('<?php echo site_url('forum/user_topics'); ?>');
                    let data = await res.json();
                    const sortVal = document.getElementById('sortSelect').value;
                    if (sortVal === 'latest') data.sort((a, b) => (b.created_at || 0) - (a.created_at || 0));
                    else if (sortVal === 'oldest') data.sort((a, b) => (a.created_at || 0) - (b.created_at || 0));
                    else if (sortVal === 'popular') data.sort((a, b) => (b.total_messages || 0) - (a.total_messages || 0));

                    const cont = document.querySelector('.topic-list');
                    const search = document.getElementById('searchInput').value.trim().toLowerCase();
                    cont.innerHTML = '';
                    for (const t of data) {
                        if (search && String(t.title || '').toLowerCase().indexOf(search) === -1) continue;
                        const card = document.createElement('div'); card.className = 'topic-card'; card.tabIndex = 0;
                        card.setAttribute('data-id', t.id); card.setAttribute('data-title', (t.title || '').toLowerCase());

                        card.setAttribute(
                            'data-href',
                            '<?php echo site_url('forum/topic/'); ?>' + t.id +
                            '?from=my_topics&return=' + encodeURIComponent(window.location.href)
                        );

                        const avatar = document.createElement('div'); avatar.className = 'avatar'; avatar.textContent = (t.title || '')[0] ? t.title[0].toUpperCase() : '?';
                        const info = document.createElement('div'); info.className = 'info';

                        const a = document.createElement('a');
                        a.className = 'topic-title';
                        a.href = '<?php echo site_url('forum/topic/'); ?>' + t.id + '?from=my_topics&return=' + encodeURIComponent(window.location.href);
                        a.textContent = t.title || 'Tanpa Judul'; // ✅ WAJIB ADA

                        const ex = document.createElement('p');
                        ex.className = 'excerpt';
                        ex.textContent = t.description && t.description.trim() !== ''
                            ? t.description
                            : 'Diskusi terbuka — buat pesan singkat (maks 50 karakter).';

                        const meta = document.createElement('div'); meta.className = 'meta';
                        const ts = new Date(t.created_at * 1000);

                        const day = String(ts.getDate()).padStart(2, '0');
                        const month = String(ts.getMonth() + 1).padStart(2, '0');
                        const year = ts.getFullYear();

                        const time = ts.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                        meta.textContent = (t.created_by ? t.created_by : 'Unknown') +
                            ' • ' + day + '/' + month + '/' + year + ' ' + time;
                        info.appendChild(a); info.appendChild(ex); info.appendChild(meta);

                        const stats = document.createElement('div'); stats.className = 'stats';
                        const stat = document.createElement('div'); stat.className = 'stat'; const total = t.total_messages ? t.total_messages : 0; stat.innerHTML = `<svg viewBox="0 0 24 24" fill="none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"stroke="#6b7280" stroke-width="1.2"/></svg><span>${total}</span>`;
                        stats.appendChild(stat);

                        const currentUsername = <?php echo json_encode($this->session->userdata('nip') ?? $this->session->userdata('username') ?? $this->session->userdata('id_users') ?? ''); ?>;
                        if (t.created_by === currentUsername) {
                            const delWrap = document.createElement('div');
                            delWrap.style.marginTop = '6px';
                            const delBtn = document.createElement('button');
                            delBtn.textContent = 'Hapus';
                            delBtn.className = 'topic-action btn danger delete-btn';
                            delBtn.onclick = function (e) {
                                e.stopPropagation();
                                openDeleteModal(t.id, t.title);
                            };
                            delWrap.appendChild(delBtn);
                            stats.appendChild(delWrap);
                        }

                        card.appendChild(avatar); card.appendChild(info); card.appendChild(stats);
                        cont.appendChild(card);

                        // clickable
                        card.addEventListener('click', function (e) { if (e.target.closest('a') || e.target.closest('button')) return; var href = this.getAttribute('data-href'); if (href) window.location.href = href; });
                        card.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); var href = this.getAttribute('data-href'); if (href) window.location.href = href; } });
                        card.style.cursor = 'pointer';

                    }
                } catch (e) { console.error(e); }
            }

            document.getElementById('searchInput').addEventListener('input', function () { refreshTopics(); });
            document.getElementById('sortSelect').addEventListener('change', function () { refreshTopics(); });
            var _nd = document.getElementById('newDiscussion'); if (_nd) { _nd.addEventListener('click', function () { openNewDiscussionModal(); }); }

            // Polling fallback for topic list
            // setInterval(refreshTopics, 4000);
            // initial load
            refreshTopics();

            // modal + delete/create flows
            (function () {
                const delModal = document.getElementById('confirmDeleteModal');
                const cancelBtn = document.getElementById('cancelDeleteBtn');
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                if (cancelBtn) cancelBtn.addEventListener('click', function () { if (delModal) delModal.style.display = 'none'; });
                if (confirmBtn) confirmBtn.addEventListener('click', performDelete);
                if (delModal) delModal.addEventListener('click', function (e) { if (e.target === delModal) delModal.style.display = 'none'; });

                const newModal = document.getElementById('newDiscussionModal');
                const cancelNew = document.getElementById('cancelNewTopicBtn');
                const createNew = document.getElementById('createTopicBtn');
                const newInput = document.getElementById('newTopicTitle');
                const newMsg = document.getElementById('newTopicMsg');
                if (cancelNew) cancelNew.addEventListener('click', function () { if (newModal) newModal.style.display = 'none'; });
                if (createNew) createNew.addEventListener('click', performCreateTopic);
                if (newModal) newModal.addEventListener('click', function (e) { if (e.target === newModal) newModal.style.display = 'none'; });
                if (newInput) newInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); performCreateTopic(); } });
            })();

            // Modal helpers
            window.openDeleteModal = function (id, title) { window.__deleteTopicId = id; const modal = document.getElementById('confirmDeleteModal'); const titleEl = document.getElementById('deleteTopicTitle'); if (titleEl) titleEl.textContent = title || ''; if (modal) modal.style.display = 'flex'; };
            window.closeDeleteModal = function () { const modal = document.getElementById('confirmDeleteModal'); if (modal) modal.style.display = 'none'; window.__deleteTopicId = null; };

            async function performDelete() {
                const id = window.__deleteTopicId;
                if (!id) return;
                try {
                    const res = await fetch('<?php echo site_url('forum/delete_topic/'); ?>' + id, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' } });
                    if (!res.ok) { showGlobalAlert('Gagal menghapus topik', 'error'); return; }
                    const data = await res.json();
                    if (data && data.success) { closeDeleteModal(); showGlobalAlert('Topik berhasil dihapus', 'success'); refreshTopics(); } else { showGlobalAlert(data.error || 'Gagal menghapus topik', 'error'); }
                } catch (e) { showGlobalAlert('Gagal menghapus topik', 'error'); }
            }

            function openNewDiscussionModal() { const modal = document.getElementById('newDiscussionModal'); const input = document.getElementById('newTopicTitle'); const msg = document.getElementById('newTopicMsg'); if (input) input.value = ''; if (msg) msg.textContent = ''; if (modal) modal.style.display = 'flex'; if (input) setTimeout(() => { try { input.focus(); } catch (e) { } }, 50); }

            async function performCreateTopic() {
                const input = document.getElementById('newTopicTitle'); const msg = document.getElementById('newTopicMsg'); if (!input) return; const title = (input.value || '').trim(); if (!title) { if (msg) { msg.style.color = '#b91c1c'; msg.textContent = 'Judul tidak boleh kosong'; } return; }
                const btn = document.getElementById('createTopicBtn'); if (btn) { btn.disabled = true; btn.style.opacity = '0.7'; }
                try {
                    const res = await fetch('<?php echo site_url('forum/create_topic'); ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'title=' + encodeURIComponent(title) });
                    if (!res.ok) { let err = 'Gagal membuat topik'; try { const d = await res.json(); if (d && d.error) err = d.error; } catch (e) { } if (msg) { msg.style.color = '#b91c1c'; msg.textContent = err; } return; }
                    const data = await res.json();
                    if (data && data.id) { if (document.getElementById('newDiscussionModal')) document.getElementById('newDiscussionModal').style.display = 'none'; showGlobalAlert('Topik berhasil dibuat', 'success'); await refreshTopics(); } else { if (msg) { msg.style.color = '#b91c1c'; msg.textContent = 'Gagal membuat topik'; } }
                } catch (e) { if (msg) { msg.style.color = '#b91c1c'; msg.textContent = 'Terjadi kesalahan jaringan'; } }
                finally { if (btn) { btn.disabled = false; btn.style.opacity = '1'; } }
            }

            // show a global inline alert
            function showGlobalAlert(msg, type) {
                const el = document.getElementById('globalAlert'); if (!el) return; el.textContent = msg || ''; if (type === 'error') { el.style.color = '#dc2626'; el.style.background = '#fff1f2'; } else { el.style.color = '#065f46'; el.style.background = '#ecfdf5'; } el.style.display = 'block'; clearTimeout(window.__globalAlertTimeout); window.__globalAlertTimeout = setTimeout(() => { try { el.style.display = 'none'; } catch (e) { } }, 3000);
            }

        })();
    </script>
</body>

</html>