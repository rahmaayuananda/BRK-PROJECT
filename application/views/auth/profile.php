<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Profil - Forum</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/forum.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/all.min.css'); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
        }

        /* Container */
        .forum-app {
            font-family: 'Inter', sans-serif;
        }

        /* Card */
        .sidebar-card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            border: 1px solid #eef0f4;
        }

        /* Title */
        .sidebar-card h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        /* Profile Info */
        .profile-info p {
            margin: 6px 0;
            color: #374151;
            font-size: 14px;
        }

        /* Avatar */
        #profileAvatarImg {
            transition: 0.2s;
        }

        #profileAvatarImg:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* Edit button avatar */
        #editProfileBtn:hover {
            transform: scale(1.1);
            background: #1d4ed8;
        }

        /* Back button */
        a.back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            margin-top: 16px;
        }

        a.back-link:hover {
            text-decoration: underline;
        }

        /* Section titles */
        .sidebar-card h3 {
            font-size: 16px;
            margin-top: 20px;
            border-bottom: 1px solid #eef0f4;
            padding-bottom: 6px;
            < !-- back button moved below recent activity -->
        }

        .sidebar-card ul li a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar-card ul li a:hover {
            text-decoration: underline;
        }

        /* Modal */
        #editProfileModal {
            backdrop-filter: blur(4px);
        }

        /* Buttons */
        .btn {
            padding: 8px 14px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
        }

        .btn.primary {
            background: #2563eb;
            color: white;
        }

        .btn.primary:hover {
            background: #1d4ed8;
        }

        .btn.danger {
            background: #dc3545;
            color: #fff;
            box-shadow: 0 6px 12px rgba(220, 53, 69, 0.12);
        }

        .btn.danger:hover {
            filter: brightness(0.96);
        }

        /* Container scroll modern */
        .activity-scroll {
            max-height: 260px;
            overflow-y: auto;
            padding-right: 6px;
            scroll-behavior: smooth;

            /* efek modern */
            mask-image: linear-gradient(to bottom, transparent, black 10%, black 90%, transparent);
            -webkit-mask-image: linear-gradient(to bottom, transparent, black 10%, black 90%, transparent);
        }

        /* Scrollbar modern */
        .activity-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .activity-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .activity-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #c7d2fe, #6366f1);
            border-radius: 10px;
            transition: 0.3s;
        }

        .activity-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #818cf8, #4f46e5);
        }

        /* Firefox support */
        .activity-scroll {
            scrollbar-width: thin;
            scrollbar-color: #6366f1 transparent;
        }

        .activity-item {
            background: #f9fafb;
            padding: 10px;
            border-radius: 8px;
            margin-top: 6px;
            transition: all 0.2s ease;
        }

        .activity-item:hover {
            background: #eef2ff;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="forum-app" style="max-width:560px;margin:40px auto">
        <div class="sidebar-card" style="padding:20px">
            <h2 style="margin-top:0">Profil Pengguna</h2>

            <?php if ($this->session->flashdata('avatar_success')): ?>
                <div style="color:#065f46;background:#ecfdf5;padding:8px;border-radius:6px;margin-bottom:8px"
                    id="serverFlash"><?php echo htmlentities($this->session->flashdata('avatar_success')); ?></div>
            <?php elseif ($this->session->flashdata('avatar_error')): ?>
                <div style="color:#dc2626;background:#fff1f2;padding:8px;border-radius:6px;margin-bottom:8px"
                    id="serverFlash"><?php echo htmlentities($this->session->flashdata('avatar_error')); ?></div>
            <?php else: ?>
                <div id="serverFlash" style="display:none"></div>
            <?php endif; ?>

            <div id="profileAlert"
                style="display:none;color:#065f46;background:#ecfdf5;padding:12px;border-radius:6px;margin-bottom:12px;font-weight:600">
                Profile updated!</div>

            <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px">
                <div style="text-align:center;min-width:160px">
                    <div style="position:relative;display:inline-block">

                        <a href="<?php echo htmlentities(!empty($avatar_url) ? $avatar_url : '#'); ?>" target="_blank">
                            <img id="profileAvatarImg"
                                src="<?php echo htmlentities(!empty($avatar_url) ? $avatar_url : ''); ?>"
                                alt="<?php echo htmlentities(!empty($fullname) ? $fullname : 'avatar'); ?>"
                                style="width:120px;height:120px;border-radius:999px;object-fit:cover;border:1px solid #e6e9ee">
                        </a>

                        <button id="editProfileBtn" style="position:absolute;bottom:5px;right:5px;
                   background:#2563eb;color:white;
                   border:none;border-radius:50%;
                   width:36px;height:36px;
                   display:flex;align-items:center;justify-content:center;
                   cursor:pointer;box-shadow:0 4px 10px rgba(0,0,0,0.2);">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                    </div>
                </div>
                <div class="profile-info" style="flex:1">
                    <p><strong>NIP:</strong> <?php echo htmlentities($username ?? ''); ?></p>
                    <p><strong>Nama:</strong> <?php echo htmlentities($fullname ?? ''); ?></p>

                    <?php if (!empty($created_at)): ?>
                        <p><strong>Terdaftar:</strong> <?php echo htmlentities($created_at); ?></p>
                    <?php endif; ?>

                    <div id="profileBioBlock" style="margin-top:8px">
                        <?php if (!empty($bio)): ?>
                            <p id="profileBio"><strong>Bio:</strong> <?php echo nl2br(htmlentities($bio)); ?></p>
                        <?php else: ?>
                            <p id="profileBio" style="color:#6b7280"><strong>Bio:</strong> <em>Belum menambahkan bio
                                    singkat.</em></p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <?php
            // support returning to a custom page when profile was opened with ?return=...
            $ret = null;
            try {
                $ret = $this->input->get('return', true);
            } catch (Exception $e) {
                $ret = null;
            }
            $back = site_url('forum');
            if (!empty($ret)) {
                // decode and allow only internal links (starting with site_url() or base url or a relative path)
                $dec = urldecode($ret);
                $base1 = rtrim(site_url(), '/');
                $base2 = rtrim(base_url(), '/');
                if (strpos($dec, $base1) === 0 || strpos($dec, $base2) === 0 || strpos($dec, '/') === 0) {
                    $back = $dec;
                }
            }
            ?>

            <!-- User's topics -->
            <div style="margin-top:18px">
                <h3 style="margin:0 0 8px 0">Topik Oleh <?php echo htmlentities($fullname); ?></h3>
                <?php if (!empty($user_topics) && is_array($user_topics)): ?>
                    <ul style="padding-left:18px;margin-top:8px">
                        <?php foreach ($user_topics as $t): ?>
                            <li style="margin-bottom:6px">
                                <a
                                    href="<?php echo site_url('forum/topic/' . $t['id'] . '?return=' . urlencode(current_url())); ?>">>
                                    <?php echo htmlentities($t['title']); ?>
                                </a>
                                <small style="color:#6b7280"> - <?php echo date('d/m/Y H:i', $t['created_at'] ?? 0); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div style="color:#6b7280">Belum membuat topik.</div>
                <?php endif; ?>
            </div>

            <!-- Recent activity by user -->
            <div style="margin-top:18px">
                <h3 style="margin:0 0 8px 0">Aktivitas Terakhir</h3>

                <div class="activity-scroll">
                    <?php if (!empty($recent_activity) && is_array($recent_activity)): ?>
                        <ul style="padding-left:18px;margin-top:8px">
                            <?php foreach ($recent_activity as $a): ?>
                                <li style="margin-bottom:8px">
                                    <a
                                        href="<?php echo site_url('forum/topic/' . $a['topic_id'] . '?return=' . urlencode(current_url())); ?>">
                                        <?php echo htmlentities($a['topic_title']); ?>
                                    </a>
                                    <div class="activity-item" data-ts="<?php echo intval($a['created_at'] ?? 0); ?>">
                                        <?php echo htmlentities(mb_strimwidth($a['message'] ?? '', 0, 140, '...')); ?>
                                        <div class="activity-ts" style="color:#6b7280;font-size:12px;margin-top:4px">
                                            <?php echo date('d/m/Y H:i', $a['created_at'] ?? 0); ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div style="color:#6b7280">Belum ada aktivitas.</div>
                    <?php endif; ?>
                </div>

                <div class="back-center" style="margin-top:16px">
                    <a href="<?php echo htmlentities($back); ?>" class="btn danger back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>

                <!-- Edit Profile Modal -->
                <div id="editProfileModal"
                    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;padding:20px;z-index:9999">
                    <div
                        style="background:white;border-radius:10px;max-width:760px;width:100%;padding:18px;box-shadow:0 10px 30px rgba(2,6,23,0.2);">
                        <h3 style="margin-top:0;margin-bottom:10px">Edit Profil</h3>
                        <div id="editProfileMsg" style="margin-bottom:8px"></div>
                        <form id="editProfileForm" enctype="multipart/form-data">
                            <div style="display:flex;gap:24px;align-items:flex-start">
                                <div
                                    style="min-width:220px;max-width:260px;text-align:center;padding:12px;border:1px solid #e5e7eb;border-radius:6px;background:#ffffff">
                                    <img id="modalAvatarPreview" src="<?php echo htmlentities($avatar_url ?? ''); ?>"
                                        style="width:180px;height:180px;border-radius:12px;object-fit:cover;border:2px solid #e5e7eb;margin-bottom:10px">
                                    <div style="margin-bottom:8px;font-size:13px;color:#6b7280">Change Profile Photo
                                    </div>
                                    <input id="modalAvatarInput" name="avatar" type="file" accept="image/*">
                                    <div style="margin-top:8px;color:#6b7280;font-size:12px">Tipe: PNG/JPEG/GIF/WEBP •
                                        Maks
                                        2MB</div>
                                </div>
                                <div style="flex:1;padding-left:8px">
                                    <div style="margin-bottom:12px;display:flex;gap:12px;align-items:center">
                                        <label style="width:160px;text-align:right;padding-right:12px">Name</label>
                                        <input name="fullname" type="text"
                                            value="<?php echo htmlentities(!empty($fullname) ? $fullname : ''); ?>"
                                            style="flex:1;padding:10px;border-radius:6px;border:1px solid #e6e9ee">
                                    </div>
                                    <!-- email field removed per request -->
                                    <div style="margin-bottom:12px;display:flex;gap:12px;align-items:flex-start">
                                        <label style="width:160px;text-align:right;padding-right:12px">Bio</label>
                                        <textarea name="bio" rows="4"
                                            style="flex:1;padding:10px;border-radius:6px;border:1px solid #e6e9ee"><?php echo htmlentities(!empty($bio) ? $bio : ''); ?></textarea>
                                    </div>
                                    <div style="margin-bottom:12px;display:flex;gap:12px;align-items:center">
                                        <label style="width:160px;text-align:right;padding-right:12px">Old
                                            Password</label>
                                        <input name="old_password" type="password" placeholder="Old Password"
                                            style="flex:1;padding:10px;border-radius:6px;border:1px solid #e6e9ee">
                                    </div>
                                    <div style="margin-bottom:12px;display:flex;gap:12px;align-items:center">
                                        <label style="width:160px;text-align:right;padding-right:12px">New
                                            Password</label>
                                        <input name="new_password" type="password" placeholder="New Password"
                                            style="flex:1;padding:10px;border-radius:6px;border:1px solid #e6e9ee">
                                    </div>
                                    <div style="margin-bottom:12px;display:flex;gap:12px;align-items:center">
                                        <label style="width:160px;text-align:right;padding-right:12px">Confirm
                                            Password</label>
                                        <input name="confirm_password" type="password" placeholder="Confirm Password"
                                            style="flex:1;padding:10px;border-radius:6px;border:1px solid #e6e9ee">
                                    </div>
                                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
                                        <button id="cancelEditBtn" type="button" class="btn danger">Batal</button>
                                        <button type="submit" class="btn primary">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Edit profile modal logic
            (function () {
                const btn = document.getElementById('editProfileBtn');
                const modal = document.getElementById('editProfileModal');
                const cancel = document.getElementById('cancelEditBtn');
                const form = document.getElementById('editProfileForm');
                const msg = document.getElementById('editProfileMsg');
                const profileAvatar = document.getElementById('profileAvatarImg');
                const modalAvatarInput = document.getElementById('modalAvatarInput');
                const modalAvatarPreview = document.getElementById('modalAvatarPreview');

                if (!btn || !modal || !form) return;

                function open() { modal.style.display = 'flex'; msg.innerHTML = ''; }
                function close() { modal.style.display = 'none'; }

                btn.addEventListener('click', open);
                cancel.addEventListener('click', close);
                // allow clicking the profile photo on the profile page to open edit modal
                if (profileAvatar) { profileAvatar.style.cursor = 'pointer'; profileAvatar.addEventListener('click', open); }
                modal.addEventListener('click', function (e) { if (e.target === modal) close(); });

                // preview selected file in modal
                if (modalAvatarInput && modalAvatarPreview) {
                    modalAvatarInput.addEventListener('change', function (e) {
                        const f = (this.files && this.files[0]) ? this.files[0] : null;
                        if (!f) return;
                        if (f.size > 2 * 1024 * 1024) {
                            msg.style.color = '#dc2626'; msg.innerText = 'File terlalu besar (maks 2MB)';
                            this.value = '';
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = function (ev) { modalAvatarPreview.src = ev.target.result; };
                        reader.readAsDataURL(f);
                    });
                }

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    msg.style.color = ''; msg.innerText = '';
                    const fd = new FormData(form);
                    const localBio = (fd.get('bio') || '').toString();
                    try {
                        const res = await fetch('<?php echo site_url('auth/update_profile'); ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });

                        if (!res.ok) {
                            const txt = await res.text();
                            msg.style.color = '#dc2626';
                            msg.innerText = txt ? txt : 'Gagal menyimpan';
                            return;
                        }

                        // Try to parse JSON if possible, but tolerate non-JSON responses
                        let data = null;
                        const ct = (res.headers.get('content-type') || '').toLowerCase();
                        if (ct.indexOf('application/json') !== -1) {
                            try { data = await res.json(); } catch (e) { data = null; }
                        } else {
                            const txt = await res.text();
                            try { data = JSON.parse(txt); } catch (e) { data = null; }
                        }

                        if (data && data.success) {
                            if (data.avatar_url && profileAvatar) { profileAvatar.src = data.avatar_url + '?t=' + Date.now(); }
                            try { var hdrImg = document.getElementById('headerAvatarImg'); var hdrName = document.getElementById('headerFullname'); if (hdrImg && data.avatar_url) hdrImg.src = data.avatar_url + '?t=' + Date.now(); if (hdrName && data.fullname) hdrName.textContent = data.fullname; } catch (e) { }
                            msg.style.color = '#065f46'; msg.innerText = data.message || 'Profile berhasil di update';
                            try { var alert = document.getElementById('profileAlert'); if (alert) { alert.textContent = data.message || 'Profile berhasil di update'; alert.style.display = 'block'; } } catch (e) { }
                            // update bio on the page (data from server if available, otherwise use local form value)
                            try {
                                var bioBlock = document.getElementById('profileBioBlock');
                                if (bioBlock) {
                                    var esc = function (s) { var d = document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; };
                                    var txt = (typeof data !== 'undefined' && typeof data.bio !== 'undefined') ? (data.bio || '') : localBio;
                                    if (txt === '') {
                                        bioBlock.innerHTML = '<p id="profileBio" style="color:#6b7280"><strong>Bio:</strong> <em>Belum menambahkan bio singkat.</em></p>';
                                    } else {
                                        bioBlock.innerHTML = '<p id="profileBio"><strong>Bio:</strong> ' + esc(txt).replace(/\n/g, '<br>') + '</p>';
                                    }
                                }
                            } catch (e) { }
                            setTimeout(() => { close(); }, 900);
                            return;
                        }

                        // If response was OK but we couldn't parse JSON, assume update succeeded on server
                        if (!data) {
                            // refresh images to pick up server-side update
                            try { if (profileAvatar) profileAvatar.src = profileAvatar.src.split('?')[0] + '?t=' + Date.now(); } catch (e) { }
                            try { var hdrImg = document.getElementById('headerAvatarImg'); if (hdrImg) hdrImg.src = hdrImg.src.split('?')[0] + '?t=' + Date.now(); } catch (e) { }
                            msg.style.color = '#065f46'; msg.innerText = 'Profile berhasil di update';
                            try { var alert = document.getElementById('profileAlert'); if (alert) { alert.textContent = 'Profile berhasil di update'; alert.style.display = 'block'; } } catch (e) { }
                            // also update bio using the local form value so UI reflects change without refresh
                            try {
                                var bioBlock = document.getElementById('profileBioBlock');
                                if (bioBlock) {
                                    var txt = localBio || '';
                                    var esc = function (s) { var d = document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; };
                                    if (txt === '') {
                                        bioBlock.innerHTML = '<p id="profileBio" style="color:#6b7280"><strong>Bio:</strong> <em>Belum menambahkan bio singkat.</em></p>';
                                    } else {
                                        bioBlock.innerHTML = '<p id="profileBio"><strong>Bio:</strong> ' + esc(txt).replace(/\n/g, '<br>') + '</p>';
                                    }
                                }
                            } catch (e) { }
                            setTimeout(() => { close(); }, 900);
                            return;
                        }

                        // Otherwise show server-provided error
                        msg.style.color = '#dc2626'; msg.innerText = (data && data.message) ? data.message : 'Gagal menyimpan';
                    } catch (err) {
                        msg.style.color = '#dc2626'; msg.innerText = 'Terjadi kesalahan jaringan';
                    }
                });
            })();
        </script>
        <script>
            // Format recent activity timestamps to match topic page
            (function () {
                try {
                    var els = document.querySelectorAll('.activity-ts');
                    els.forEach(function (el) {
                        var parent = el.closest('.activity-item');
                        var ts = 0;
                        if (parent && parent.getAttribute('data-ts')) {
                            ts = parseInt(parent.getAttribute('data-ts'), 10) || 0;
                        } else if (el.getAttribute('data-ts')) {
                            ts = parseInt(el.getAttribute('data-ts'), 10) || 0;
                        }
                        if (!ts) return;
                        var d = new Date(ts * 1000);

                        var day = String(d.getDate()).padStart(2, '0');
                        var month = String(d.getMonth() + 1).padStart(2, '0');
                        var year = d.getFullYear();

                        var time = d.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        el.textContent = day + '/' + month + '/' + year + ' ' + time;
                    });
                } catch (e) { /* ignore */ }
            })();
        </script>
</body>

</html>