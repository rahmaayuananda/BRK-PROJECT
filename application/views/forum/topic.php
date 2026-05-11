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
        const TOPIC_ID = <?php echo json_encode($topic['id'] ?? ''); ?>;
        const USER_ROLE = <?php echo json_encode($this->session->userdata('role') ?? ''); ?>;
        const CURRENT_USERNAME = <?php echo json_encode($this->session->userdata('username') ?? ''); ?>;
        const CURRENT_FULLNAME = <?php echo json_encode($this->session->userdata('name') ?? $this->session->userdata('fullname') ?? ''); ?>;
        const IS_ARCHIVED = <?php echo (!empty($topic['archived']) ? 'true' : 'false'); ?>;
        const IS_FAQ = <?php echo (!empty($topic['is_faq']) ? 'true' : 'false'); ?>;
    </script>

    <style>
        /* ===== LAYOUT ===== */
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
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .msg-delete {
            padding: 4px 8px;
            margin-left: 8px;
            font-size: 12px;
            color: #dc3545;
            border: 1px solid #fca5a5;
            background: #fff5f5;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            pointer-events: auto;
            flex-shrink: 0;
        }

        .msg-delete:hover {
            background: #fecaca;
            border-color: #dc2626;
            color: #dc2626;
        }

        .msg-delete:active {
            transform: scale(0.95);
        }

        .msg-delete i {
            pointer-events: none;
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

        .btn.icon-btn {
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn.icon-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

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
            padding: 10px;
        }

        /* HOVER */
        .sidebar .categories li a:hover {
            background: transparent;
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

        .sidebar .categories li.active {
            background: #e0edff;
        }

        .sidebar .categories li.active a {
            color: #0d6efd;
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

        /* Mention styles */
        .mention-dropdown {
            position: absolute;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-height: 150px;
            overflow-y: auto;
            width: 200px;
            z-index: 1000;
            display: none;
            bottom: calc(100% + 5px); /* Position above the input */
            left: 0;
        }
        
        .mention-item {
            padding: 8px 12px;
            cursor: pointer;
            font-size: 14px;
            color: #334155;
            transition: background 0.1s;
        }

        .mention-item:hover, .mention-item.active {
            background: #f1f5f9;
            color: #0d6efd;
            font-weight: 500;
        }

        .mention-highlight {
            color: #0d6efd;
            font-weight: 600;
            background: rgba(13, 110, 253, 0.1);
            padding: 0 4px;
            border-radius: 4px;
        }

        /* ===== IMAGE DOWNLOAD OVERLAY ===== */
        .bubble-img-wrap {
            position: relative;
            display: inline-block;
            width: fit-content;
        }

        .bubble-img-wrap .img-download-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: rgba(0, 0, 0, 0.55);
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity 0.2s ease, transform 0.2s ease, background 0.2s ease;
            backdrop-filter: blur(4px);
            z-index: 2;
        }

        .bubble-img-wrap:hover .img-download-btn {
            opacity: 1;
            transform: translateY(0);
        }

        .bubble-img-wrap .img-download-btn:hover {
            background: rgba(13, 110, 253, 0.85);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
        }
        /* Delete confirmation modal styling */
        #deleteMsgModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 20px;
        }

        #deleteMsgModal .modal-box {
            background: #fff;
            padding: 18px;
            border-radius: 12px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 12px 30px rgba(0,0,0,0.18);
        }

        #deleteMsgModal .modal-title { font-weight:700; margin-bottom:6px }
        #deleteMsgModal .modal-sub { color:#6b7280; font-size:13px; margin-bottom:8px }
        #deleteMsgModal .modal-msg { background:#f8fafc; padding:10px;border-radius:8px;color:#0f172a }
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
                    <?php elseif (!empty($topic['is_faq'])): ?>
                        <div
                            style="margin-bottom:12px; padding:12px; background:#e0f2fe; color:#0369a1; border-radius:10px; font-weight:500; text-align:center;">
                            ℹ️ Ini adalah halaman FAQ. Anda hanya dapat melihat riwayat pesan.
                        </div>
                    <?php endif; ?>
                    <div class="messages" id="messages"></div>
                </div>
                <!-- <div class="messages" id="messages"></div> -->

                <div class="composer">
                    <!-- Image Preview Area -->
                    <div id="imagePreviewContainer" style="display: none; position: relative; margin-bottom: 12px; width: max-content;">
                        <img id="imagePreview" src="" style="max-height: 120px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <button id="removeImageBtn" style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);">&times;</button>
                    </div>
                    <div class="composer-row" style="position: relative;">
                        <?php $is_readonly = !empty($topic['archived']) || !empty($topic['is_faq']); ?>
                        <input type="file" id="imageInput" accept="image/*" style="display: none;" <?php echo $is_readonly ? 'disabled' : ''; ?>>
                        <button id="attachBtn" class="btn icon-btn" title="Unggah Gambar" <?php echo $is_readonly ? 'disabled' : ''; ?>>
                            <i class="fa-solid fa-plus"></i>
                        </button>
                        <div style="flex: 1; position: relative; display: flex;">
                            <input type="text" id="message" style="flex: 1; width: 100%;" placeholder="Tulis pesan atau keterangan gambar (maks 50 karakter)..." <?php echo $is_readonly ? 'disabled' : ''; ?> autocomplete="off">
                            <div id="mentionDropdown" class="mention-dropdown"></div>
                        </div>
                        <button id="sendBtn" class="btn primary" <?php echo $is_readonly ? 'disabled' : ''; ?>>
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

    <!-- Image Zoom Modal -->
    <div id="imageZoomModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); align-items: center; justify-content: center; z-index: 999999; flex-direction: column; backdrop-filter: blur(4px);">
        <button id="closeZoomBtn" style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.2); color: white; border: none; font-size: 24px; cursor: pointer; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.2s;">&times;</button>
        <img id="zoomedImage" src="" style="max-width: 90%; max-height: 75vh; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); object-fit: contain;">
        <button id="downloadZoomBtn" class="btn primary" style="margin-top: 24px; padding: 12px 24px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; font-weight: 600; box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4); cursor: pointer;">
            <i class="fa-solid fa-download"></i> Unduh Gambar
        </button>
    </div>

    <script>

        // Notifications are handled by the shared partial (application/views/layout/notifikasi.php)
        // Ensure notifications are loaded if the partial exposes the loader.
        if (typeof loadNotifications === 'function') {
            try { loadNotifications(); } catch (e) { }
        }

        // --- IMAGE ZOOM LOGIC ---
        const imageZoomModal = document.getElementById('imageZoomModal');
        const zoomedImage = document.getElementById('zoomedImage');
        const downloadZoomBtn = document.getElementById('downloadZoomBtn');
        const closeZoomBtn = document.getElementById('closeZoomBtn');

        // --- UNIVERSAL DOWNLOAD HELPER ---
        function downloadImage(url, filename) {
            // Use server-side endpoint that forces Content-Disposition: attachment
            const downloadUrl = '<?php echo site_url("forum/download_image/"); ?>' + encodeURIComponent(filename);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        let currentZoomFilename = 'download_image';

        function openImageZoom(imgSrc) {
            zoomedImage.src = imgSrc;
            currentZoomFilename = imgSrc.substring(imgSrc.lastIndexOf('/') + 1) || 'download_image';
            imageZoomModal.style.setProperty('display', 'flex', 'important');
        }

        if (downloadZoomBtn) {
            downloadZoomBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                downloadImage(zoomedImage.src, currentZoomFilename);
            });
        }

        if (closeZoomBtn) {
            closeZoomBtn.addEventListener('click', () => {
                imageZoomModal.style.display = 'none';
            });
        }
        if (imageZoomModal) {
            imageZoomModal.addEventListener('click', (e) => {
                if (e.target === imageZoomModal) {
                    imageZoomModal.style.display = 'none';
                }
            });
        }
        // --- END IMAGE ZOOM LOGIC ---

        function createMessageElement(m) {
            const wrap = document.createElement('div');
            wrap.className = 'message';
            // attach message id for delegation
            if (m.id) wrap.dataset.messageId = m.id;

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
            
            if (m.image) {
                const imgSrc = '<?php echo base_url('forums_data/images/'); ?>' + m.image;

                const img = document.createElement('img');
                img.src = imgSrc;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '300px';
                img.style.borderRadius = '8px';
                img.style.marginBottom = m.message ? '8px' : '0';
                img.style.display = 'block';
                img.style.objectFit = 'contain';
                img.style.cursor = 'pointer';
                
                img.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    openImageZoom(imgSrc);
                });

                // // Download button overlay (dinonaktifkan sementara)
                // const imgWrap = document.createElement('div');
                // imgWrap.className = 'bubble-img-wrap';
                // imgWrap.style.marginBottom = m.message ? '8px' : '0';
                // const dlBtn = document.createElement('button');
                // dlBtn.className = 'img-download-btn';
                // dlBtn.title = 'Unduh Gambar';
                // dlBtn.innerHTML = '<i class="fa-solid fa-download"></i>';
                // dlBtn.addEventListener('click', (e) => {
                //     e.preventDefault();
                //     e.stopPropagation();
                //     downloadImage(imgSrc, m.image || 'download_image');
                // });
                // imgWrap.appendChild(img);
                // imgWrap.appendChild(dlBtn);
                // bubble.appendChild(imgWrap);

                bubble.appendChild(img);
            }

            if (m.message) {
                // Parse mentions in frontend: highlight @Username
                let msgHtml = m.message.replace(/@([a-zA-Z0-9_]+)/g, '<span class="mention-highlight">@$1</span>');
                bubble.innerHTML += msgHtml;
            }

            // Hapus tombol hanya untuk pesan (tampilkan hanya untuk pemilik atau admin)
            const meta = document.createElement('div');
            meta.className = 'msg-meta';

            // waktu
            const ts = new Date(m.created_at * 1000);
            const day = String(ts.getDate()).padStart(2, '0');
            const month = String(ts.getMonth() + 1).padStart(2, '0');
            const year = ts.getFullYear();

            meta.innerHTML = `${day}/${month}/${year} ` +
                ts.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            // tombol delete: hanya tambahkan jika user adalah admin atau pemilik pesan
            try {
                const ownerName = (m.created_by || '').toString().trim().toLowerCase();
                const myName = (CURRENT_FULLNAME || '').toString().trim().toLowerCase();
                const myUser = (CURRENT_USERNAME || '').toString().trim().toLowerCase();
                const isOwner = ownerName && (ownerName === myName || ownerName === myUser);
                const canDelete = (USER_ROLE === 'admin') || isOwner;

                if (canDelete) {
                    const delBtn = document.createElement('button');
                    delBtn.className = 'msg-delete';
                    delBtn.innerHTML = '<i class="fa-solid fa-trash"></i>';
                    delBtn.dataset.messageId = m.id || '';
                    delBtn.title = (USER_ROLE === 'admin') ? 'Hapus untuk semua' : 'Hapus untuk saya';
                    delBtn.type = 'button';

                    meta.appendChild(delBtn);
                }
            } catch (err) {
                console.error('Delete button error', err);
            }

            content.appendChild(name);
            content.appendChild(bubble);
            content.appendChild(meta);

            wrap.appendChild(avatar);
            wrap.appendChild(content);

            return wrap;
        }

        // --- MENTIONS LOGIC ---
        let allUsers = [];
        let mentionActive = false;
        let mentionStartIndex = -1;
        let mentionSelectedIndex = 0;
        let filteredUsers = [];
        
        const mentionDropdown = document.getElementById('mentionDropdown');
        const msgInput = document.getElementById('message');

        async function fetchUsers() {
            try {
                const res = await fetch('<?php echo site_url('forum/get_users'); ?>');
                allUsers = await res.json();
            } catch (e) { console.error('Failed to load users'); }
        }
        fetchUsers();

        function showMentionDropdown(query) {
            filteredUsers = allUsers.filter(u => u.mention_tag.toLowerCase().includes(query.toLowerCase()));
            if (filteredUsers.length === 0) {
                mentionDropdown.style.display = 'none';
                return;
            }
            
            mentionDropdown.innerHTML = '';
            filteredUsers.forEach((u, idx) => {
                const div = document.createElement('div');
                div.className = 'mention-item' + (idx === 0 ? ' active' : '');
                div.textContent = u.name;
                div.dataset.index = idx;
                div.addEventListener('click', () => {
                    insertMention(idx);
                });
                mentionDropdown.appendChild(div);
            });
            mentionSelectedIndex = 0;
            mentionDropdown.style.display = 'block';
        }

        function insertMention(index) {
            const user = filteredUsers[index];
            if (!user) return;
            
            const text = msgInput.value;
            const before = text.substring(0, mentionStartIndex);
            const after = text.substring(msgInput.selectionEnd);
            
            const insertText = '@' + user.mention_tag + ' ';
            msgInput.value = before + insertText + after;
            
            // Set cursor position
            const newPos = before.length + insertText.length;
            msgInput.setSelectionRange(newPos, newPos);
            
            closeMentionDropdown();
            msgInput.focus();
        }

        function closeMentionDropdown() {
            mentionDropdown.style.display = 'none';
            mentionActive = false;
            mentionStartIndex = -1;
        }

        if (msgInput) {
            msgInput.addEventListener('input', (e) => {
                const text = msgInput.value;
                const cursorPos = msgInput.selectionEnd;
                
                // Find if we are typing a mention
                const textBeforeCursor = text.substring(0, cursorPos);
                const lastAtMatch = textBeforeCursor.match(/(^|\s)@([a-zA-Z0-9_]*)$/);
                
                if (lastAtMatch) {
                    mentionActive = true;
                    mentionStartIndex = cursorPos - lastAtMatch[2].length - 1;
                    showMentionDropdown(lastAtMatch[2]);
                } else {
                    closeMentionDropdown();
                }
            });

            msgInput.addEventListener('keydown', (e) => {
                if (mentionActive && mentionDropdown.style.display === 'block') {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        const items = mentionDropdown.querySelectorAll('.mention-item');
                        if (items[mentionSelectedIndex]) items[mentionSelectedIndex].classList.remove('active');
                        mentionSelectedIndex = (mentionSelectedIndex + 1) % filteredUsers.length;
                        if (items[mentionSelectedIndex]) items[mentionSelectedIndex].classList.add('active');
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        const items = mentionDropdown.querySelectorAll('.mention-item');
                        if (items[mentionSelectedIndex]) items[mentionSelectedIndex].classList.remove('active');
                        mentionSelectedIndex = (mentionSelectedIndex - 1 + filteredUsers.length) % filteredUsers.length;
                        if (items[mentionSelectedIndex]) items[mentionSelectedIndex].classList.add('active');
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        e.stopImmediatePropagation(); // prevent sending message
                        insertMention(mentionSelectedIndex);
                    } else if (e.key === 'Escape') {
                        closeMentionDropdown();
                    }
                }
            });
        }
        // --- END MENTIONS LOGIC ---

        async function loadMessages() {
            try {
                const res = await fetch('<?php echo site_url('forum/messages/' . $topic['id']); ?>');
                const data = await res.json();

                const newDataString = JSON.stringify(data);
                if (window.lastMessagesString === newDataString) {
                    return; // Tidak ada perubahan, jangan redraw
                }
                window.lastMessagesString = newDataString;

                const cont = document.getElementById('messages');
                
                // Cek apakah user sedang scroll ke atas (dengan toleransi 250px dari bawah)
                let isScrolledToBottom = (window.innerHeight + window.scrollY) >= document.body.offsetHeight - 250;

                cont.innerHTML = '';

                data.forEach(m => {
                    cont.appendChild(createMessageElement(m));
                });

                if (isScrolledToBottom || window.initialLoadCompleted === undefined || window.forceScrollToBottom) {
                    scrollToBottomSmooth();
                    window.initialLoadCompleted = true;
                    window.forceScrollToBottom = false;
                }

            } catch (e) { console.error(e); }
        }

        // Image attachment handling
        const imageInput = document.getElementById('imageInput');
        const attachBtn = document.getElementById('attachBtn');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageBtn = document.getElementById('removeImageBtn');
        let selectedFile = null;

        if (attachBtn) {
            attachBtn.addEventListener('click', () => {
                imageInput.click();
            });
        }

        if (imageInput) {
            imageInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    selectedFile = this.files[0];
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.style.display = 'block';
                        
                        // Scroll composer into view so user can type caption easily
                        setTimeout(scrollToComposer, 100);
                        document.getElementById('message').focus();
                    }
                    reader.readAsDataURL(selectedFile);
                }
            });
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', () => {
                selectedFile = null;
                imageInput.value = '';
                imagePreviewContainer.style.display = 'none';
                imagePreview.src = '';
            });
        }

        document.getElementById('sendBtn').addEventListener('click', async function () {
            const msg = document.getElementById('message').value;

            if (!msg.trim() && !selectedFile) return;

            if (msg.length > 50) {
                alert('Keterangan dipotong ke 50 karakter');
            }

            const formData = new FormData();
            formData.append('topic_id', TOPIC_ID);
            formData.append('message', msg);
            if (selectedFile) {
                formData.append('image', selectedFile);
            }

            try {
                const res = await fetch('<?php echo site_url('forum/post_message'); ?>', {
                    method: 'POST',
                    body: formData
                });
                
                if(!res.ok) {
                    const data = await res.json();
                    alert(data.error || 'Gagal mengirim pesan');
                    return;
                }

                document.getElementById('message').value = '';
                if (removeImageBtn) removeImageBtn.click();
                
                window.forceScrollToBottom = true;
                await loadMessages();
            } catch (e) {
                console.error("Error sending message", e);
            }
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

        // Join topic button (opsional - hanya jika button join ada di halaman)
        (function () {
            // cari button join - bisa dengan id "joinBtn" atau ambil dari header
            let joinBtn = document.getElementById('joinBtn') || document.querySelector('[data-action="join-topic"]');
            if (!joinBtn) return; // skip jika button tidak ditemukan

            joinBtn.addEventListener('click', async function () {
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
                        joinBtn.textContent = 'Bergabung ✓';
                        joinBtn.disabled = true;
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

        // Enter key to send message handled inside mention logic partially, 
        // but we add this specifically for sending if not mentioning
        if (msgInput) {
            msgInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    if (mentionActive && mentionDropdown.style.display === 'block') return; // let mention logic handle it
                    e.preventDefault();
                    document.getElementById('sendBtn').click();
                }
            });
            // Close mention dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (e.target !== msgInput && !mentionDropdown.contains(e.target)) {
                    closeMentionDropdown();
                }
            });
        }

        function scrollToBottomSmooth() {
            const cont = document.getElementById('messages');
            if (!cont) return;
            const last = cont.lastElementChild;
            if (last) {
                try { last.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (e) { window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); }
            } else {
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }
        }

        function scrollToComposer() {
            const composer = document.querySelector('.composer');
            if (composer) {
                try { composer.scrollIntoView({ behavior: 'smooth', block: 'end' }); } catch (e) { window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); }
            } else {
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }
        }

        // ===== GLOBAL DELETE MESSAGE =====
        let deleteMessageId = null;

        function openDeleteMessageModal(id, message) {
            console.log('openDeleteMessageModal', { id: id, message: message });
            deleteMessageId = id;

            if (!deleteMessageId) {
                showToast('Tidak dapat menghapus: id pesan tidak tersedia', 'error');
                return;
            }

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
        // event delegation for delete buttons - attached to messages container
        (function () {
            const msgs = document.getElementById('messages');
            if (!msgs) return;
            msgs.addEventListener('click', function (e) {
                try {
                    // support clicking on button or icon inside
                    let btn = e.target.closest('.msg-delete');
                    if (!btn) {
                        if (e.target.tagName === 'I' && e.target.closest('.msg-delete')) {
                            btn = e.target.closest('.msg-delete');
                        }
                    }
                    if (!btn) return;
                    
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const msgWrapper = btn.closest('.message');
                    if (!msgWrapper) return;
                    
                    const id = btn.dataset.messageId;
                    const bubble = msgWrapper.querySelector('.bubble');
                    const msgText = bubble ? bubble.innerText.trim().substring(0, 200) : 'Pesan';
                    
                    console.log('Delete button clicked', { id: id, msgText: msgText, target: e.target });
                    if (id) {
                        openDeleteMessageModal(id, msgText);
                    }
                } catch (err) { console.error('Delete click error:', err); }
            }, true);
        })();

        document.getElementById('confirmDeleteMsg').addEventListener('click', async function () {
            if (!deleteMessageId) return;

            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Menghapus...';

            try {
                console.log('Confirm delete clicked, id=', deleteMessageId);
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