<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlentities($topic['title'] ?? ''); ?> - Forum</title>

    <link rel="stylesheet" href="<?php echo base_url('assets/css/inter.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/forum.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/all.min.css'); ?>">
    <script>
        const TOPIC_ID = '<?php echo $topic['id'] ?? ''; ?>';
        const USER_ROLE = '<?php echo $this->session->userdata('role'); ?>';
        const IS_ARCHIVED = <?php echo (!empty($topic['archived']) ? 'true' : 'false'); ?>;
    </script>

    <style>
        /* ===== LAYOUT ===== */
        /* body {
            background: #f4f7fb;
        } */

        * {
            box-sizing: border-box;
        }

        body {
            background: #f4f7fb;
        }

        .main {
            display: flex;
            gap: 24px;
            /* padding: 0 16px 20px 16px; */
            padding: 20px 0;
            /* HAPUS padding kiri kanan */
            /* atas kanan bawah kiri */
            align-items: flex-start;
            /* PENTING */
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            /* align-items: stretch; */
            gap: 0px;
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

        .forum-app {
            max-width: 1200px;
            /* samakan dengan my_topic */
            margin: auto;
            padding: 0 16px;
            /* INI KUNCINYA */
        }

        /* ===== HEADER ===== */
        .topbar {
            background: #fff;
            border-radius: 14px;
            margin: 0;
            /* atas kanan bawah kiri */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        /* ===== TOPIC HEADER ===== */
        .topic-header {
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 14px;
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
        }

        .topic-header .title {
            font-weight: 700;
            font-size: 18px;
        }

        /* ===== BUTTON ===== */
        .btn.primary {
            background: linear-gradient(135deg, #0d6efd, #3b82f6);
            border: none;
            color: white;
            transition: all 0.2s ease;
        }

        .btn.primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
        }

        /* ===== MESSAGE AREA ===== */
        .messages {
            background: #ffffff;
            border-radius: 14px;
            padding: 16px;
            /* allow messages to grow and use the page scrollbar instead of an inner scroll */
            min-height: 420px;
            margin: 0;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            width: 100%;
        }

        /* ===== MESSAGE ITEM ===== */
        .message {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            object-fit: cover;
        }

        /* ===== CHAT BUBBLE ===== */
        .msg-content {
            max-width: 75%;
        }

        .msg-name {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 4px;
        }

        .bubble {
            background: #f1f5f9;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.4;
            transition: 0.2s;
        }

        .bubble:hover {
            background: #e2e8f0;
        }

        .msg-meta {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }

        /* ===== COMPOSER ===== */
        .composer {
            background: #ffffff;
            padding: 16px;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            width: 100%;
        }

        .composer-row {
            display: flex;
            gap: 10px;
        }

        #message {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            outline: none;
            transition: 0.2s;
        }

        #message:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        /* .message-wrapper {
            position: relative;
            margin: 0 16px 12px 16px; */
        /* atas kanan bawah kiri */
        /* } */
        .message-wrapper {
            position: relative;
            width: 100%;
        }

        .message-wrapper,
        .composer {
            margin: 0 0 12px 0;
            /* JANGAN ada 16px lagi
            width: 100%;
            max-width: 100%; */
        }

        .messages,
        .composer {
            border-radius: 16px;
            padding: 18px;
        }

        /* tombol join di pojok kanan atas */
        .join-btn {
            position: absolute;
            top: 12px;
            right: 38px;
            z-index: 10;
            padding: 8px 14px;
            font-size: 13px;
        }

        .join-btn {
            backdrop-filter: blur(6px);
            background: rgba(13, 110, 253, 0.9);
            border-radius: 999px;
        }

        .btn.danger {
            background: #dc3545;
            color: #fff;
            box-shadow: 0 6px 12px rgba(220, 53, 69, 0.12);
            border-radius: 8px;
            padding: 8px 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn.danger:hover {
            filter: brightness(0.96);
        }

        .back-center {
            text-align: center;
            margin-top: 16px;
        }

        #notifDropdown {
            width: 340px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            /* outer container should not scroll; inner .notif-list handles scrolling */
        }

        #notifDropdown a:hover {
            background: #f8fafc;
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

        /* RESET */
        .sidebar .categories li {
            padding: 4px 6px !important;
            margin-bottom: 6px !important;
        }

        /* LINK STYLE */
        .sidebar .categories li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 8px !important;
            border-radius: 12px;
            text-decoration: none;
            color: #64748b;
            position: relative;
            transition: all 0.2s ease;
        }

        /* HOVER */
        .sidebar .categories li a:hover {
            background: #f1f5f9;
            color: #0d6efd;
        }

        /* ACTIVE STYLE (SESUAI GAMBAR KANAN) */
        .sidebar .categories li.active a {
            background: transparent;
            color: #0d6efd;
            font-weight: 600;
        }

        .categories li {
            padding: 6px 8px;
            border-radius: 8px;
        }

        .categories li.active {
            background: #e0edff;
            border-radius: 8px;
        }

        .categories li.active a {
            color: var(--accent);
            font-weight: 600;
        }

        #deleteMsgModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        #deleteMsgModal .modal-box {
            background: #fff;
            padding: 20px;
            border-radius: 14px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.2s ease;
        }

        #deleteMsgModal h3 {
            margin-bottom: 8px;
        }

        #deleteMsgModal p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 16px;
        }

        #deleteMsgModal .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        @keyframes fadeIn {
            from {
                transform: translateY(10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert-banner {
            display: none;
            margin: 16px 0;
            padding: 14px;
            border-radius: 10px;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
        }

        /* sukses (hijau seperti gambar) */
        .alert-banner.success {
            background: #d1fae5;
            color: #065f46;
        }

        /* error (merah) */
        .alert-banner.error {
            background: #fee2e2;
            color: #991b1b;
        }

        /* tampil */
        .alert-banner.show {
            display: block;
        }

        .modal-title {
            font-weight: 600;
            margin-bottom: 6px;
        }

        .modal-sub {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .modal-msg {
            background: #f1f5f9;
            padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="forum-app">

        <?php $this->load->view('layout/header', ['page_title' => htmlentities($topic['title'] ?? ''), 'show_search' => false, 'show_sort' => false]); ?>
        <div id="toastNotif" class="alert-banner"></div>

        <main class="main">
            <?php $this->load->view('layout/sidebar'); ?>

            <section class="content">

                <div class="message-wrapper">
                    <?php if (!empty($topic['archived'])): ?>
                        <div
                            style="margin-bottom:12px; padding:12px; background:#fef3c7; color:#92400e; border-radius:10px; font-weight:500; text-align:center;">
                            🔒 Diskusi ini telah ditutup. Anda tidak dapat mengirim pesan lagi.
                        </div>
                    <?php endif; ?>
                    <div class="messages" id="messages"></div>
                </div>
                <!-- <div class="messages" id="messages"></div> -->

                <div class="composer">
                    <div class="composer-row">
                        <input type="text" id="message" placeholder="Tulis pesan (maks 50 karakter)" <?php echo !empty($topic['archived']) ? 'disabled' : ''; ?>>
                        <button id="sendBtn" class="btn primary" <?php echo !empty($topic['archived']) ? 'disabled' : ''; ?>>
                            Kirim
                        </button>
                    </div>
                </div>

                <?php
                $ret = null;
                try {
                    $ret = $this->input->get('return', true);
                } catch (Exception $e) {
                    $ret = null;
                }

                $back = site_url('forum');
                if (!empty($ret)) {
                    $dec = urldecode($ret);
                    $base1 = rtrim(site_url(), '/');
                    $base2 = rtrim(base_url(), '/');
                    if (strpos($dec, $base1) === 0 || strpos($dec, $base2) === 0 || strpos($dec, '/') === 0) {
                        $back = $dec;
                    }
                }
                ?>

                <div class="back-center">
                    <a href="<?php echo htmlentities($back ?? ''); ?>" class="btn danger">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>


            </section>
        </main>
    </div>

    <!-- Confirm Delete Message Modal -->
    <div id="deleteMsgModal">
        <div class="modal-box">
            <h3>Hapus Pesan</h3>
            <p id="deleteMsgText">Yakin ingin menghapus pesan ini?</p>

            <div class="actions">
                <button id="cancelDeleteMsg" class="btn">Batal</button>
                <button id="confirmDeleteMsg" class="btn danger">Hapus</button>
            </div>
        </div>
    </div>

    <script>

        // Notifications are handled by the shared partial (application/views/layout/notifikasi.php)
        // Ensure notifications are loaded if the partial exposes the loader.
        if (typeof loadNotifications === 'function') {
            try { loadNotifications(); } catch (e) { }
        }

        function createMessageElement(m) {
            const wrap = document.createElement('div');
            wrap.className = 'message';

            // ✅ AVATAR IMAGE
            const avatar = document.createElement('img');
            avatar.className = 'avatar';
            avatar.src = m.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(m.created_by || 'User');

            // ✅ CONTENT
            const content = document.createElement('div');
            content.className = 'msg-content';

            const name = document.createElement('div');
            name.className = 'msg-name';
            name.textContent = m.created_by || 'User';

            const bubble = document.createElement('div');
            bubble.className = 'bubble';
            bubble.textContent = m.message;

            // Hapus tombol hanya untuk pesan //
            const meta = document.createElement('div');
            meta.className = 'msg-meta';

            // tombol delete
            const delBtn = document.createElement('button');
            delBtn.innerHTML = '<i class="fa-solid fa-trash"></i>';

            delBtn.style.marginLeft = '10px';
            delBtn.style.fontSize = '11px';
            delBtn.style.color = '#dc3545';
            delBtn.style.border = 'none';
            delBtn.style.background = 'transparent';
            delBtn.style.cursor = 'pointer';

            // klik hapus
            delBtn.addEventListener('click', () => {
                openDeleteMessageModal(m.id, m.message);
            });


            // const ts = new Date(m.created_at * 1000);
            // meta.textContent = ts.toLocaleDateString() + ' ' + ts.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const ts = new Date(m.created_at * 1000);

            const day = String(ts.getDate()).padStart(2, '0');
            const month = String(ts.getMonth() + 1).padStart(2, '0');
            const year = ts.getFullYear();

            meta.innerHTML = `${day}/${month}/${year} ` +
                ts.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            meta.appendChild(delBtn);
            content.appendChild(name);
            content.appendChild(bubble);
            content.appendChild(meta);

            wrap.appendChild(avatar);
            wrap.appendChild(content);

            delBtn.addEventListener('click', () => {
                openDeleteMessageModal(m.id, m.message);
            });

            return wrap;
        }

        async function loadMessages() {
            try {
                const res = await fetch('<?php echo site_url('forum/messages/' . $topic['id']); ?>');
                const data = await res.json();

                const cont = document.getElementById('messages');
                cont.innerHTML = '';

                data.forEach(m => {
                    cont.appendChild(createMessageElement(m));
                });

                scrollToBottomSmooth();

            } catch (e) { console.error(e); }
        }

        document.getElementById('sendBtn').addEventListener('click', async function () {
            const msg = document.getElementById('message').value;

            if (!msg || !msg.trim()) return;

            if (msg.length > 50) alert('Pesan dipotong ke 50 karakter');

            await fetch('<?php echo site_url('forum/post_message'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'topic_id=' + encodeURIComponent(TOPIC_ID) + '&message=' + encodeURIComponent(msg)
            });

            document.getElementById('message').value = '';
            await loadMessages();
        });

        // ✅ REALTIME WEBSOCKET
        (function () {
            var wsUrl = (location.protocol === 'https:' ? 'wss://' : 'ws://') + location.hostname + ':8080';

            try {
                var ws = new WebSocket(wsUrl);

                ws.addEventListener('message', function (ev) {
                    try {
                        var d = JSON.parse(ev.data);

                        if (d.type === 'message' && d.data && String(d.data.topic_id) === String(TOPIC_ID)) {
                            const cont = document.getElementById('messages');
                            cont.appendChild(createMessageElement(d.data.message));
                            scrollToBottomSmooth();
                        }

                    } catch (e) { }
                });

            } catch (e) { }
        })();

        // ✅ POLLING BACKUP
        setInterval(loadMessages, 2000);
        loadMessages();

        // Join topic button
        (function () {
            const btn = document.getElementById('joinTopicBtn');
            if (!btn) return;
            btn.addEventListener('click', async function () {
                try {
                    const res = await fetch('<?php echo site_url('forum/join_topic'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'topic_id=' + encodeURIComponent(TOPIC_ID)
                    });

                    if (res.status === 403) {
                        // redirect to login with return
                        window.location.href = '<?php echo site_url('auth/login'); ?>?return=' + encodeURIComponent(window.location.href);
                        return;
                    }

                    const data = await res.json();
                    if (data && data.success) {
                        showToast('Berhasil bergabung ke topik ini', 'success');
                        btn.textContent = 'Bergabung ✓';
                        btn.disabled = true;
                        try {
                            if (window.__USER_JOINED_TOPICS) window.__USER_JOINED_TOPICS.add(String(TOPIC_ID));
                            if (typeof loadNotifications === 'function') loadNotifications();
                        } catch (e) { }
                    } else {
                        alert('Gagal bergabung: ' + (data && data.error ? data.error : 'Unknown'));
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            });
        })();

        function scrollToBottomSmooth() {
            const cont = document.getElementById('messages');
            if (!cont) return;
            const last = cont.lastElementChild;
            if (last) {
                try { last.scrollIntoView({ behavior: 'smooth', block: 'end' }); } catch (e) { window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); }
            } else {
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }
        }

        // ===== GLOBAL DELETE MESSAGE =====
        let deleteMessageId = null;

        function openDeleteMessageModal(id, message) {
            deleteMessageId = id;

            const modal = document.getElementById('deleteMsgModal');
            const text = document.getElementById('deleteMsgText');

            let title = '';
            let desc = '';

            if (USER_ROLE === 'admin') {
                title = 'Hapus untuk semua';
                desc = 'Pesan ini akan dihapus untuk semua pengguna.';
            } else {
                title = 'Hapus untuk saya';
                desc = 'Pesan ini hanya akan hilang dari tampilan Anda.';
            }

            text.innerHTML = `
                <div class="modal-title">${title}</div>
                <div class="modal-sub">${desc}</div>
                <div class="modal-msg">
                    "${message}"
                </div>
            `;

            modal.style.display = 'flex';
        }


        function closeDeleteMessageModal(showMsg = false) {
            document.getElementById('deleteMsgModal').style.display = 'none';

            if (showMsg) {
                showToast('Penghapusan dibatalkan', 'error');
            }

            deleteMessageId = null;
        }

        // tombol batal
        document.getElementById('cancelDeleteMsg')
            .addEventListener('click', () => closeDeleteMessageModal(true));

        // klik background
        document.getElementById('deleteMsgModal').addEventListener('click', function (e) {
            if (e.target === this) closeDeleteMessageModal();
        });

        // tombol hapus
        document.getElementById('confirmDeleteMsg').addEventListener('click', async function () {
            if (!deleteMessageId) return;

            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Menghapus...';

            try {
                const res = await fetch('<?php echo site_url('forum/delete_message'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + encodeURIComponent(deleteMessageId)
                });

                const data = await res.json();

                if (data.success) {
                    showToast('Pesan berhasil dihapus', 'success');
                } else {
                    showToast('Pesan gagal dihapus', 'error');
                }

            } catch (e) {
                showToast('Terjadi kesalahan jaringan', 'error');
            }

            closeDeleteMessageModal();
            loadMessages();

            btn.disabled = false;
            btn.textContent = 'Hapus';
        });

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotif');

            toast.textContent = message;
            toast.className = 'alert-banner show ' + type;

            setTimeout(() => {
                toast.className = 'alert-banner';
            }, 3000);
        }

    </script>
</body>

</html>