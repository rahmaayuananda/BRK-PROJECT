<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Forum - BRK</title>
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

        /* New Discussion modal */
        #newDiscussionModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 9999
        }

        #newDiscussionModal .modal-box {
            background: white;
            border-radius: 10px;
            max-width: 560px;
            width: 100%;
            padding: 18px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.2)
        }

        #newDiscussionModal h3 {
            margin: 0 0 8px;
            font-size: 18px
        }

        #newDiscussionModal .modal-msg {
            margin-bottom: 12px;
            color: #111827
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

            /* INI YANG DITAMBAHKAN */
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

        .topic-action.close-btn {
            background: #f59e0b;
            color: #fff;
        }

        .topic-action.faq-btn {
            background: #10b981;
            color: #fff;
        }

        .topic-action i {
            margin-right: 4px;
        }
    </style>
</head>

<body>
    <div class="forum-app">
        <?php $this->load->view('layout/header', ['page_title' => 'All Topic', 'show_new_button' => false, 'new_button_label' => '+ NEW DISCUSSION', 'new_button_id' => 'newDiscussion']); ?>

        <div id="globalAlert"></div>

        <main class="main">
            <?php $this->load->view('layout/sidebar', ['show_new_button' => true, 'new_button_label' => '+ NEW DISCUSSION', 'new_button_id' => 'newDiscussion']); ?>

            <section class="content">
                <div class="topic-list">
                    <?php
                    $current_fullname = $this->session->userdata('nama_lengkap') ?? $this->session->userdata('fullname') ?? '';
                    $current_user = $this->session->userdata('nip') ?? '';
                    $current_role = $this->session->userdata('role') ?? '';
                    ?>
                    <?php foreach ($topics as $t): ?>
                        <?php $msg_count = count($this->forum_model->get_messages($t['id'])); ?>
                        <div class="topic-card" tabindex="0" data-id="<?php echo $t['id']; ?>"
                            data-href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=all_topics'; ?>"
                            data-title="<?php echo strtolower(htmlentities($t['title'] ?? '')); ?>">
                            <div class="avatar"><?php echo strtoupper(mb_substr($t['title'] ?? '?', 0, 1, 'UTF-8')); ?>
                            </div>
                            <div class="info">
                                <a class="topic-title"
                                    href="<?php echo site_url('forum/topic/' . $t['id']) . '?from=all_topics'; ?>"><?php echo htmlentities($t['title']); ?></a>
                                <p class="excerpt">Diskusi terbuka — buat pesan singkat (maks 50 karakter).</p>
                                <div class="meta">
                                    <?php echo htmlentities($t['created_by'] ?? 'Unknown'); ?> •
                                    <?php echo date('d/m/Y H:i', $t['created_at']); ?>
                                </div>
                            </div>
                            <div class="stats">
                                <div class="stat"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#6b7280"
                                            stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg><span><?php echo $msg_count; ?></span></div>
                                <!-- <div style="margin-top:6px"><a class="open-btn topic-action"
                                        href="<?php echo site_url('forum/topic/' . $t['id']); ?>">Buka</a></div> -->

                                <!-- hapus button, hanya tampil jika user pembuat topik sama dengan user yang login -->
                                <?php if ($current_role === 'admin' || $t['created_by'] == $current_user): ?>
                                    <div style="margin-top:6px; display:flex; gap:6px; flex-wrap:wrap;">

                                        <!-- HAPUS (admin & owner) -->
                                        <button class="topic-action delete-btn"
                                            onclick="openDeleteModal('<?php echo $t['id']; ?>', <?php echo json_encode($t['title'] ?? ''); ?>)">
                                            🗑️ Hapus
                                        </button>

                                        <!-- TUTUP DISKUSI (admin & owner) -->
                                        <button class="topic-action close-btn"
                                            onclick="handleCloseTopic('<?php echo $t['id']; ?>')">
                                            🔒 Tutup
                                        </button>

                                        <!-- FAQ (KHUSUS ADMIN SAJA) -->
                                        <?php if ($current_role === 'admin'): ?>
                                            <button type="button" class="topic-action faq-btn"
                                                onclick="handleSetFAQ('<?php echo $t['id']; ?>')">
                                                ⭐ FAQ
                                            </button>
                                        <?php endif; ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

    <!-- New Discussion Modal -->
    <div id="newDiscussionModal"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;padding:20px;z-index:9999">
        <div class="modal-box"
            style="background:white;border-radius:10px;max-width:560px;width:100%;padding:18px;box-shadow:0 10px 30px rgba(2,6,23,0.2);">
            <h3>Buat Topik Baru</h3>
            <div id="newTopicMsg" class="modal-msg" style="margin-bottom:10px;color:#6b7280"></div>
            <div style="margin-bottom:12px">
                <label for="newTopicTitle">Judul Topik</label>
                <input id="newTopicTitle" type="text" placeholder="Judul topik..."
                    style="width:100%;padding:10px;border-radius:6px;border:1px solid #e6e9ee">
            </div>
            <div class="actions" style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" id="cancelNewTopicBtn" class="btn">Batal</button>
                <button type="button" id="createTopicBtn" class="btn primary">Buat</button>
            </div>
        </div>
    </div>

    <script>
        const currentRole = <?php echo json_encode($this->session->userdata('role') ?? ''); ?>;
        const currentUser = <?php echo json_encode($this->session->userdata('nip') ?? ''); ?>;

        async function refreshTopics() {
            try {
                const currentUser = <?php echo json_encode(
                    $this->session->userdata('nama_lengkap') ?? ''
                ); ?>;

                const res = await fetch('<?php echo site_url('forum/topics'); ?>');
                let data = await res.json();
                const sortVal = document.getElementById('sortSelect').value;
                if (sortVal === 'latest') data.sort((a, b) => b.created_at - a.created_at);
                else if (sortVal === 'oldest') data.sort((a, b) => a.created_at - b.created_at);

                const cont = document.querySelector('.topic-list');
                const search = document.getElementById('searchInput').value.trim().toLowerCase();
                cont.innerHTML = '';
                for (const t of data) {
                    if (search && t.title.toLowerCase().indexOf(search) === -1) continue;
                    const card = document.createElement('div'); card.className = 'topic-card'; card.setAttribute('data-id', t.id); card.setAttribute('data-title', t.title.toLowerCase()); card.setAttribute('data-href', '<?php echo site_url('forum/topic/'); ?>' + t.id + '?from=all_topics'); card.tabIndex = 0;
                    const avatar = document.createElement('div'); avatar.className = 'avatar'; avatar.textContent = (t.title || '')[0] ? t.title[0].toUpperCase() : '?';
                    const info = document.createElement('div'); info.className = 'info';
                    const a = document.createElement('a'); a.className = 'topic-title'; a.href = '<?php echo site_url('forum/topic/'); ?>' + t.id + '?from=all_topics'; a.textContent = t.title;
                    const ex = document.createElement('p'); ex.className = 'excerpt'; ex.textContent = 'Diskusi terbuka — buat pesan singkat (maks 50 karakter).';
                    const meta = document.createElement('div'); meta.className = 'meta';
                    // meta.textContent = (t.created_by ? t.created_by : 'Unknown') + ' • ' + new Date(t.created_at * 1000).toLocaleString();
                    const ts = new Date(t.created_at * 1000);

                    const day = String(ts.getDate()).padStart(2, '0');
                    const month = String(ts.getMonth() + 1).padStart(2, '0');
                    const year = ts.getFullYear();

                    const time = ts.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    meta.textContent = (t.created_by ? t.created_by : 'Unknown') +
                        ' • ' + day + '/' + month + '/' + year + ' ' + time;

                    info.appendChild(a); info.appendChild(ex); info.appendChild(meta);

                    const stats = document.createElement('div'); stats.className = 'stats';
                    const stat = document.createElement('div'); stat.className = 'stat'; stat.innerHTML = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#6b7280" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>0</span>';
                    stats.appendChild(stat);

                    // ROLE CHECK (admin / owner)
                    const isOwner = String(t.created_by).trim().toLowerCase() === String(currentUser).trim().toLowerCase();
                    const isAdmin = currentRole === 'admin';

                    if (isAdmin || isOwner) {
                        const actionWrap = document.createElement('div');
                        actionWrap.style.marginTop = '6px';
                        actionWrap.style.display = 'flex';
                        actionWrap.style.gap = '6px';
                        actionWrap.style.flexWrap = 'wrap';

                        // DELETE
                        const delBtn = document.createElement('button');
                        delBtn.innerHTML = '🗑️ Hapus';
                        delBtn.className = 'topic-action delete-btn';
                        delBtn.onclick = function (e) {
                            e.stopPropagation();
                            openDeleteModal(t.id, t.title);
                        };
                        actionWrap.appendChild(delBtn);

                        // CLOSE
                        const closeBtn = document.createElement('button');
                        closeBtn.innerHTML = '🔒 Tutup';
                        closeBtn.className = 'topic-action close-btn';
                        closeBtn.onclick = function (e) {
                            e.stopPropagation();
                            handleCloseTopic(t.id);
                        };
                        actionWrap.appendChild(closeBtn);

                        // FAQ → hanya admin
                        if (isAdmin) {
                            const faqBtn = document.createElement('button');
                            faqBtn.innerHTML = '⭐ FAQ';
                            faqBtn.className = 'topic-action faq-btn';
                            faqBtn.onclick = function (e) {
                                e.stopPropagation();
                                handleSetFAQ(t.id);
                            };
                            actionWrap.appendChild(faqBtn);
                        }

                        stats.appendChild(actionWrap);
                    }

                    card.appendChild(avatar); card.appendChild(info); card.appendChild(stats);
                    cont.appendChild(card);

                    // make card clickable (but ignore clicks on links/buttons inside)
                    card.addEventListener('click', function (e) { if (e.target.closest('a') || e.target.closest('button')) return; var href = this.getAttribute('data-href'); if (href) window.location.href = href; });
                    card.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); var href = this.getAttribute('data-href'); if (href) window.location.href = href; } });
                    card.style.cursor = 'pointer';

                    // load message count (lightweight per-topic)
                    (async function (el, id) {
                        try {
                            const r = await fetch('<?php echo site_url('forum/messages/'); ?>' + id);
                            const msgs = await r.json();
                            const span = el.querySelector('.stat span'); if (span) span.textContent = msgs.length;
                        } catch (e) { }
                    })(card, t.id);
                }
            } catch (e) { console.error(e); }
        }

        document.getElementById('searchInput').addEventListener('input', function () { refreshTopics(); });
        document.getElementById('sortSelect').addEventListener('change', function () { refreshTopics(); });

        document.getElementById('newDiscussion').addEventListener('click', function () {
            openNewDiscussionModal();
        });

        // Realtime via WebSocket (fallback to polling) — notifications handled centrally in header
        (function () {
            var __LOGGED_IN = <?php echo json_encode((bool) ($this->session && $this->session->userdata('logged_in'))); ?>;
            var __USERNAME = <?php echo json_encode($this->session->userdata('nip') ?? $this->session->userdata('username') ?? ''); ?>;
            var __FULLNAME = <?php echo json_encode($this->session->userdata('nama_lengkap') ?? $this->session->userdata('fullname') ?? ''); ?>;
            window.__USER_JOINED_TOPICS = window.__USER_JOINED_TOPICS || new Set();
            window.__NOTIFS = window.__NOTIFS || [];

            var wsUrl = (location.protocol === 'https:' ? 'wss://' : 'ws://') + location.hostname + ':8080';
            try {
                var ws = new WebSocket(wsUrl);
                ws.addEventListener('open', function () { console.log('WS connected'); });
                ws.addEventListener('message', function (ev) {
                    try {
                        var d = JSON.parse(ev.data);
                        if (d.type === 'topic') refreshTopics();
                        else if (d.type === 'message') {
                            try {
                                // delegate to centralized notification handler (defined in header)
                                if (typeof window.addNotification === 'function') {
                                    window.addNotification(d.data || d);
                                }
                            } catch (e) { }
                        }
                    } catch (e) { }
                });
                ws.addEventListener('close', function () { console.log('WS closed'); });
            } catch (e) { }

            if (__LOGGED_IN) {
                // header will initialize user topics and notifications; ensure loader is called if exposed
                try { if (typeof window.loadNotifications === 'function') window.loadNotifications(); } catch (e) { }
            }
        })();

        // Polling fallback
        setInterval(refreshTopics, 4000);
        // initial load
        refreshTopics();
        // bind click on any server-rendered cards (before refresh replaces them)
        (function bindExistingCards() {
            document.querySelectorAll('.topic-card').forEach(card => {
                if (card._clickBound) return; card._clickBound = true;
                if (!card.getAttribute('data-href')) {
                    var id = card.getAttribute('data-id'); if (id) card.setAttribute('data-href', '<?php echo site_url('forum/topic/'); ?>' + id);
                }
                card.addEventListener('click', function (e) { if (e.target.closest('a') || e.target.closest('button')) return; var href = this.getAttribute('data-href'); if (href) window.location.href = href; });
                card.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); var href = this.getAttribute('data-href'); if (href) window.location.href = href; } });
                card.style.cursor = 'pointer';
            });
        })();

        // modal buttons (delete + new topic)
        (function () {
            // delete modal
            const delModal = document.getElementById('confirmDeleteModal');
            const cancelBtn = document.getElementById('cancelDeleteBtn');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            if (cancelBtn) cancelBtn.addEventListener('click', function () { if (delModal) delModal.style.display = 'none'; });
            if (confirmBtn) confirmBtn.addEventListener('click', performDelete);
            if (delModal) delModal.addEventListener('click', function (e) { if (e.target === delModal) delModal.style.display = 'none'; });

            // new discussion modal
            const newModal = document.getElementById('newDiscussionModal');
            const cancelNew = document.getElementById('cancelNewTopicBtn');
            const createNew = document.getElementById('createTopicBtn');
            const newInput = document.getElementById('newTopicTitle');
            const newMsg = document.getElementById('newTopicMsg');
            if (cancelNew) cancelNew.addEventListener('click', function () { if (newModal) newModal.style.display = 'none'; });
            if (createNew) createNew.addEventListener('click', performCreateTopic);
            if (newModal) newModal.addEventListener('click', function (e) { if (e.target === newModal) newModal.style.display = 'none'; });
            // allow Enter to submit when inside input
            if (newInput) newInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); performCreateTopic(); } });
        })();

        // Modal-based delete flow
        function openDeleteModal(id, title) {
            window.__deleteTopicId = id;
            const modal = document.getElementById('confirmDeleteModal');
            const titleEl = document.getElementById('deleteTopicTitle');
            if (titleEl) titleEl.textContent = title || '';
            if (modal) modal.style.display = 'flex';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('confirmDeleteModal');
            if (modal) modal.style.display = 'none';
            window.__deleteTopicId = null;
        }

        async function performDelete() {
            const id = window.__deleteTopicId;
            if (!id) return;
            try {
                const res = await fetch('<?php echo site_url('forum/delete_topic/'); ?>' + id, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' } });
                if (!res.ok) {
                    let msg = 'Gagal menghapus topik';
                    try { const d = await res.json(); if (d && d.error) msg = d.error; } catch (e) { }
                    showGlobalAlert(msg, 'error');
                    return;
                }
                const data = await res.json();
                if (data && data.success) {
                    closeDeleteModal();
                    showGlobalAlert('Topik berhasil dihapus', 'success');
                    refreshTopics();
                } else {
                    showGlobalAlert(data.error || 'Gagal menghapus topik', 'error');
                }
            } catch (e) {
                showGlobalAlert('Gagal menghapus topik', 'error');
                console.error(e);
            }
        }

        // Backward-compatible wrapper (uses card title if available)
        function deleteTopic(id) {
            let title = '';
            try { const el = document.querySelector('.topic-card[data-id="' + id + '"]'); if (el) title = el.getAttribute('data-title') || ''; } catch (e) { }
            openDeleteModal(id, title);
        }

        // New Discussion modal flow
        function openNewDiscussionModal() {
            const modal = document.getElementById('newDiscussionModal');
            const input = document.getElementById('newTopicTitle');
            const msg = document.getElementById('newTopicMsg');
            if (input) input.value = '';
            if (msg) { msg.textContent = ''; }
            if (modal) modal.style.display = 'flex';
            if (input) setTimeout(() => { try { input.focus(); } catch (e) { } }, 50);
        }

        function closeNewDiscussionModal() {
            const modal = document.getElementById('newDiscussionModal');
            if (modal) modal.style.display = 'none';
        }

        async function performCreateTopic() {
            const input = document.getElementById('newTopicTitle');
            const msg = document.getElementById('newTopicMsg');
            if (!input) return;
            const title = (input.value || '').trim();
            if (!title) {
                if (msg) { msg.style.color = '#b91c1c'; msg.textContent = 'Judul tidak boleh kosong'; }
                return;
            }
            // disable button while submitting
            const btn = document.getElementById('createTopicBtn');
            if (btn) { btn.disabled = true; btn.style.opacity = '0.7'; }
            try {
                const res = await fetch('<?php echo site_url('forum/create_topic'); ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'title=' + encodeURIComponent(title) });
                if (!res.ok) {
                    let err = 'Gagal membuat topik';
                    try { const d = await res.json(); if (d && d.error) err = d.error; } catch (e) { }
                    if (msg) { msg.style.color = '#b91c1c'; msg.textContent = err; }
                    return;
                }
                const data = await res.json();
                if (data && data.id) {
                    closeNewDiscussionModal();
                    showGlobalAlert('Topik berhasil dibuat', 'success');
                    await refreshTopics();
                } else {
                    if (msg) { msg.style.color = '#b91c1c'; msg.textContent = 'Gagal membuat topik'; }
                }
            } catch (e) {
                if (msg) { msg.style.color = '#b91c1c'; msg.textContent = 'Terjadi kesalahan jaringan'; }
            } finally {
                if (btn) { btn.disabled = false; btn.style.opacity = '1'; }
            }
        }

        // show a global inline alert (similar to profile update behavior)
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

        // =====================ARSIP TOPIK JS=====================
        function handleCloseTopic(id) {
            if (!confirm('Tutup diskusi ini?')) return;

            fetch('<?php echo site_url('forum/archive_topic/'); ?>' + id, {
                method: 'POST'
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        showGlobalAlert('Topik berhasil diarsipkan', 'success');
                        refreshTopics();
                    } else {
                        showGlobalAlert(res.error || 'Gagal', 'error');
                    }
                })
                .catch(() => {
                    showGlobalAlert('Terjadi kesalahan', 'error');
                });
        }

        // =====================FAQ TOPIK JS=====================
        function handleSetFAQ(id) {
            console.log("FAQ CLICKED:", id); // 👈 tambah ini

            if (!confirm('Jadikan topik ini sebagai FAQ?')) return;

            fetch('<?php echo site_url('forum/set_faq/'); ?>' + id, {
                method: 'POST'
            })
                .then(res => res.json())
                .then(res => {
                    console.log("RESPONSE:", res); // 👈 tambah ini

                    if (res.success) {
                        showGlobalAlert('Topik berhasil dijadikan FAQ', 'success');
                        refreshTopics();
                    } else {
                        showGlobalAlert('Gagal menjadikan FAQ', 'error');
                    }
                })
                .catch(err => {
                    console.error("ERROR:", err); // 👈 penting
                    showGlobalAlert('Terjadi kesalahan', 'error');
                });
        }
    </script>
</body>

</html>