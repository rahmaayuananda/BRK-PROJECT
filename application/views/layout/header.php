<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
// Session mappings to support DB fields: nip, nama_lengkap, id_users
$session_nip = $this->session->userdata('nip') ?? $this->session->userdata('username') ?? '';
$session_username = $this->session->userdata('username') ?? '';
$session_id = $this->session->userdata('id_users') ?? '';
$session_fullname = $this->session->userdata('nama_lengkap') ?? $this->session->userdata('fullname') ?? ($session_username ?: 'User');
$avatar = $this->session->userdata('avatar_url') ? $this->session->userdata('avatar_url') : null;
$exts = array('png', 'jpg', 'jpeg', 'gif', 'webp');
if (!$avatar) {
    foreach (array_filter([$session_nip, $session_username]) as $base) {
        foreach ($exts as $e) {
            $p = FCPATH . 'assets/avatars/' . $base . '.' . $e;
            if (file_exists($p)) {
                $avatar = base_url('assets/avatars/' . $base . '.' . $e);
                break 2;
            }
        }
    }
}
if (!$avatar) {
    $avatar = 'https://ui-avatars.com/api/?name=' . rawurlencode($session_fullname) . '&size=64&background=0D8ABC&color=fff';
}

// Header options
$show_search = isset($show_search) ? (bool) $show_search : true;
$show_sort = isset($show_sort) ? (bool) $show_sort : true;
$show_new_button = isset($show_new_button) ? (bool) $show_new_button : false;
$new_button_id = isset($new_button_id) ? $new_button_id : 'newDiscussion';
$new_button_label = isset($new_button_label) ? $new_button_label : '+ NEW DISCUSSION';
$subtitle = isset($subtitle) ? $subtitle : '';
?>
<header class="topbar">
    <div>
        <h2><?php echo isset($page_title) ? $page_title : 'My Topics'; ?></h2>
        <?php if (!empty($subtitle)): ?>
            <div style="font-size:14px;color:#64748b;margin-top:6px"><?php echo $subtitle; ?></div>
        <?php endif; ?>
        <?php if (!empty($show_new_button)): ?>
            <button id="<?php echo $new_button_id; ?>" class="btn primary" style="margin-left:12px">
                <?php echo $new_button_label; ?>
            </button>
        <?php endif; ?>
    </div>
    <div class="controls">
        <?php if ($show_sort): ?>
            <select id="sortSelect">
                <option value="latest">Latest</option>
                <option value="oldest">Oldest</option>
            </select>
        <?php endif; ?>

        <?php if ($show_search): ?>
            <input id="searchInput" type="text" placeholder="Search forum">
        <?php endif; ?>

        <?php if ($this->session && $this->session->userdata('logged_in')): ?>
            <div id="notifWrap" style="display:inline-block;position:relative;margin-right:8px;vertical-align:middle">
                <button id="notifBell" class="btn" title="Notifikasi" style="position:relative">🔔
                    <span id="notifCount"
                        style="display:none;position:absolute;top:-6px;right:-6px;background:#dc2626;color:#fff;border-radius:999px;padding:2px 6px;font-size:12px;line-height:1"></span>
                </button>
                <div id="notifDropdown"
                    style="display:none;position:absolute;right:0;top:40px;width:320px;background:white;border:1px solid #e6e9ee;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,0.1);z-index:9999;padding:8px">
                </div>
            </div>

            <a id="headerProfileLink" class="btn"
                href="<?php echo site_url('auth/profile?return=' . urlencode(current_url())); ?>">
                <img id="headerAvatarImg" src="<?php echo $avatar; ?>"
                    alt="<?php echo htmlentities($session_fullname ?? ''); ?>"
                    style="width:36px;height:36px;border-radius:999px;object-fit:cover">
            </a>
        <?php else: ?>
            <a class="btn" href="<?php echo site_url('auth/login'); ?>">Login</a>
        <?php endif; ?>
    </div>
</header>
<script>
    (function () {
        if (window.__NOTIF_INITED) return; // ensure single init per page
        window.__NOTIF_INITED = true;

        var __LOGGED_IN = <?php echo json_encode((bool) ($this->session && $this->session->userdata('logged_in'))); ?>;
        var __USERNAME = <?php echo json_encode($this->session->userdata('nip') ?? $this->session->userdata('username') ?? ''); ?>;
        var __FULLNAME = <?php echo json_encode($this->session->userdata('nama_lengkap') ?? $this->session->userdata('fullname') ?? ''); ?>;
        window.__USER_JOINED_TOPICS = window.__USER_JOINED_TOPICS || new Set();
        window.__NOTIFS = window.__NOTIFS || [];

        function escapeHtml(s) { return String(s || '').replace(/[&<>"]+/g, function (c) { return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[c] || c; }); }

        async function loadUserTopics() {
            try {
                const res = await fetch('<?php echo site_url('forum/user_topics'); ?>');
                if (!res.ok) return;
                const topics = await res.json();
                window.__USER_JOINED_TOPICS = new Set((topics || []).map(t => String(t.id)));
            } catch (e) { }
        }

        function renderNotifDropdown(notifs) {
            const dd = document.getElementById('notifDropdown');
            if (!dd) return;
            if (!notifs || notifs.length === 0) {
                dd.innerHTML = '<div style="padding:8px;color:#6b7280">Tidak ada notifikasi.</div>';
                return;
            }

            const lastSeen = parseInt(localStorage.getItem('forum_last_seen_' + __USERNAME) || '0', 10) || 0;
            let readList = [];
            try { readList = JSON.parse(localStorage.getItem('forum_read_notifs_' + __USERNAME) || '[]'); } catch (e) { readList = []; }
            const readSet = new Set((readList || []).map(String));

            let html = '<div class="notif-header"><strong>Notifikasi</strong><a href="#" id="markAllRead" style="font-size:12px;margin-left:auto">Tandai dibaca</a></div>';
            html += '<div class="notif-list">';
            for (let n of notifs) {
                let when = '';
                if (n.created_at) {
                    const d = new Date(n.created_at * 1000);

                    const day = String(d.getDate()).padStart(2, '0');
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const year = d.getFullYear();

                    const time = d.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    when = `${day}/${month}/${year} ${time}`;
                }
                const createdAt = n.created_at || 0;
                const topicId = n.topic_id || '';
                const readKey = String(topicId) + '::' + String(createdAt);
                const isUnread = (createdAt > lastSeen) && !readSet.has(String(readKey));
                const unreadClass = isUnread ? ' notif-unread' : '';

                html += '<a class="notif-item' + unreadClass + '" data-topic-id="' + encodeURIComponent(n.topic_id) + '" data-created-at="' + createdAt + '" data-read-key="' + encodeURIComponent(readKey) + '" href="' + '<?php echo site_url('forum/topic/'); ?>' + encodeURIComponent(n.topic_id) + '" style="display:block;padding:8px;border-radius:6px;text-decoration:none;color:inherit;border-bottom:1px solid #f5f7fa;margin-bottom:6px">';
                //menampilkan nama pengirim
                const actor = n.created_by || 'Someone';

                html += '<div style="font-weight:600">';
                html += (isUnread
                    ? '<span class="unread-dot" style="display:inline-block;width:8px;height:8px;background:#0d6efd;border-radius:999px;margin-right:8px;vertical-align:middle"></span>'
                    : '');
                let text = '';

                if (n.type === 'topic_created') {
                    text = actor + ' membuat topic baru';
                }
                else if (n.type === 'topic_deleted') {
                    text = actor + ' menghapus topic';
                }
                else if (n.type === 'user_login') {
                    text = actor + ' melakukan login';
                }
                else {
                    // 🔥 DEFAULT = NOTIF LAMA (JANGAN DIUBAH)
                    text = actor + ' sent a message';
                }

                html += escapeHtml(text);

                let link = '#';

                if (n.type === 'message' || !n.type) {
                    link = '<?php echo site_url('forum/topic/'); ?>' + encodeURIComponent(n.topic_id);
                }
                html += '</div>';

                //menampilkan nama topik
                html += '<div style="font-size:12px;color:#9ca3af">';
                html += 'in "' + escapeHtml(n.topic_title) + '"';
                html += '</div>';

                html += '<div style="font-size:13px;color:#6b7280">'
                    + escapeHtml((n.message || '').substring(0, 120))
                    + '</div>';
                html += '<div style="font-size:12px;color:#9ca3af;margin-top:6px">' + when + '</div>';
                html += '</a>';
            }
            html += '</div>';
            dd.innerHTML = html;

            // mark clicked notification as read (per-item) before navigating away
            const links = dd.querySelectorAll('a.notif-item');
            links.forEach(function (a) {
                a.addEventListener('click', function () {
                    try {
                        const readKeyAttr = a.getAttribute('data-read-key');
                        const storageKey = 'forum_read_notifs_' + __USERNAME;
                        let list = [];
                        try { list = JSON.parse(localStorage.getItem(storageKey) || '[]'); } catch (e) { list = []; }

                        if (readKeyAttr) {
                            // mark only this specific notification as read
                            const rk = decodeURIComponent(readKeyAttr);
                            if (!list.includes(rk)) {
                                list.push(rk);
                                localStorage.setItem(storageKey, JSON.stringify(list));
                            }
                        } else {
                            // fallback: try to build a key from topic + created
                            const created = parseInt(a.getAttribute('data-created-at') || '0', 10) || 0;
                            let topic = a.getAttribute('data-topic-id') || '';
                            try { topic = decodeURIComponent(topic); } catch (er) { }
                            if (created && topic) {
                                const rk = String(topic) + '::' + String(created);
                                if (!list.includes(rk)) {
                                    list.push(rk);
                                    localStorage.setItem(storageKey, JSON.stringify(list));
                                }
                            } else {
                                // no reliable per-item id — do not mark all as read here
                            }
                        }

                        // update badge and UI (recompute unread using per-item keys)
                        const ls = parseInt(localStorage.getItem('forum_last_seen_' + __USERNAME) || '0', 10) || 0;
                        let rl = [];
                        try { rl = JSON.parse(localStorage.getItem('forum_read_notifs_' + __USERNAME) || '[]'); } catch (e) { rl = []; }
                        const readSet2 = new Set((rl || []).map(String));
                        const unread = (window.__NOTIFS || []).filter(n => {
                            const rk = String(n.topic_id || '') + '::' + String(n.created_at || 0);
                            return ((n.created_at || 0) > ls) && !readSet2.has(rk);
                        }).length;
                        const badge = document.getElementById('notifCount'); if (badge) { if (unread > 0) { badge.textContent = unread; badge.style.display = 'inline-block'; } else { badge.style.display = 'none'; } }

                        // remove visual highlight for this item only
                        a.classList.remove('notif-unread');
                    } catch (e) { }
                });
            });

            const mark = document.getElementById('markAllRead');
            if (mark) {
                mark.addEventListener('click', function (e) {
                    e.preventDefault();
                    try {
                        localStorage.setItem('forum_last_seen_' + __USERNAME, String(Math.floor(Date.now() / 1000)));
                        localStorage.removeItem('forum_read_notifs_' + __USERNAME);
                    } catch (er) { }
                    const b = document.getElementById('notifCount'); if (b) b.style.display = 'none';
                    // remove highlight in DOM
                    const u = dd.querySelectorAll('.notif-unread');
                    u.forEach(function (el) { el.classList.remove('notif-unread'); });
                });
            }
        }

        async function loadNotifications() {
            try {
                const res = await fetch('<?php echo site_url('forum/notifications'); ?>');
                if (!res.ok) return;
                const notifs = await res.json();
                window.__NOTIFS = notifs || [];
                const lastSeen = parseInt(localStorage.getItem('forum_last_seen_' + __USERNAME) || '0', 10) || 0;
                let readList = [];
                try { readList = JSON.parse(localStorage.getItem('forum_read_notifs_' + __USERNAME) || '[]'); } catch (e) { readList = []; }
                const readSet = new Set((readList || []).map(String));
                const unread = (window.__NOTIFS || []).filter(n => {
                    const rk = String(n.topic_id || '') + '::' + String(n.created_at || 0);
                    return (n.created_at || 0) > lastSeen && !readSet.has(rk);
                }).length;
                const badge = document.getElementById('notifCount');
                if (badge) {
                    if (unread > 0) { badge.textContent = unread; badge.style.display = 'inline-block'; } else { badge.style.display = 'none'; }
                }
                renderNotifDropdown(window.__NOTIFS);
            } catch (e) { }
        }

        function addNotification(n) {
            console.log("ADD NOTIF:", n); // 🔥 debug
            if (!n) return;
            window.__NOTIFS = window.__NOTIFS || [];
            // avoid duplicates by created_at + topic
            const key = (n.topic_id || '') + '|' + (n.created_at || 0);
            if (window.__NOTIFS.find(x => String(x.topic_id) === String(n.topic_id) && (x.created_at || 0) === (n.created_at || 0))) return;
            window.__NOTIFS.unshift(n);
            try {
                const lastSeen = parseInt(localStorage.getItem('forum_last_seen_' + __USERNAME) || '0', 10) || 0;
                let readList = [];
                try { readList = JSON.parse(localStorage.getItem('forum_read_notifs_' + __USERNAME) || '[]'); } catch (e) { readList = []; }
                const readSet = new Set((readList || []).map(String));
                const unread = window.__NOTIFS.filter(x => {
                    const rk = String(x.topic_id || '') + '::' + String(x.created_at || 0);
                    return (x.created_at || 0) > lastSeen && !readSet.has(rk);
                }).length;
                const badge = document.getElementById('notifCount'); if (badge) { badge.textContent = unread; if (unread > 0) badge.style.display = 'inline-block'; }
            } catch (e) { }
            renderNotifDropdown(window.__NOTIFS);
        }

        function handleIncomingWsMessage(d) {
            try {
                if (!d || !d.data) return;
                var payload = d.data;
                var topic_id = String(payload.topic_id || payload.topic || '');
                var msg = payload.message || payload;
                if (!topic_id) return;
                if (!__LOGGED_IN) return;
                // if (!window.__USER_JOINED_TOPICS || !window.__USER_JOINED_TOPICS.has(String(topic_id))) return;
                if (msg.created_by && String(msg.created_by) === String(__FULLNAME)) return;
                addNotification({ topic_id: topic_id, topic_title: (payload.topic_title || ''), message: msg.message || '', created_at: msg.created_at || Math.floor(Date.now() / 1000), created_by: msg.created_by || '', avatar: msg.avatar || null });
            } catch (e) { }
        }

        var wsUrl = (location.protocol === 'https:' ? 'wss://' : 'ws://') + location.hostname + ':8080';
        if (!window.__NOTIF_WS_INITED) {
            window.__NOTIF_WS_INITED = true;
            try {
                var ws = new WebSocket(wsUrl);

                ws.addEventListener('open', function () {
                    console.log('WS connected (notif)');
                });


                ws.addEventListener('message', function (ev) {
                    console.log("RAW WS:", ev.data);
                    try {
                        var d = JSON.parse(ev.data);

                        // 🔥 TAMBAHKAN DI SINI
                        console.log("WS DATA:", d);
                        console.log("NOTIFS:", window.__NOTIFS);

                        if (d.type === 'topic') {
                            if (typeof refreshTopics === 'function') refreshTopics();
                        }

                        // notif message lama
                        else if (d.type === 'message') {
                            handleIncomingWsMessage(d);
                        }

                        // topic baru
                        else if (d.type === 'topic_created') {
                            addNotification({
                                type: 'topic_created',
                                topic_id: d.data.topic_id,
                                topic_title: d.data.topic_title,
                                created_by: d.data.created_by,
                                created_at: d.data.created_at
                            });
                        }

                        // hapus topic
                        else if (d.type === 'topic_deleted') {
                            addNotification({
                                type: 'topic_deleted',
                                topic_id: d.data.topic_id,
                                topic_title: d.data.topic_title,
                                created_by: d.data.deleted_by,
                                created_at: d.data.created_at
                            });
                        }

                        // login user
                        else if (d.type === 'user_login') {
                            addNotification({
                                type: 'user_login',
                                topic_id: 'login', // 🔥 penting
                                topic_title: 'Login System',
                                created_by: d.data.user,
                                created_at: d.data.created_at
                            });
                        }

                    } catch (e) {
                        console.error('WS parse error:', e);
                    }
                });

                ws.addEventListener('close', function () {
                    console.log('WS closed (notif)');
                });

                ws.addEventListener('close', function () { console.log('WS closed (notif)'); });
            } catch (e) { }
        }

        if (__LOGGED_IN) { loadUserTopics().then(function () { loadNotifications(); }); setInterval(loadNotifications, 10000); }

        // expose for pages that call these directly
        window.loadNotifications = loadNotifications;
        window.addNotification = addNotification;
        window.renderNotifDropdown = renderNotifDropdown;

        // storage event listener to sync tabs
        window.addEventListener('storage', function (e) {
            try {
                if (!e.key) return;
                if (e.key === 'forum_last_seen_' + __USERNAME || e.key.indexOf('forum_read_notifs_') === 0) {
                    if (typeof loadNotifications === 'function') loadNotifications();
                }
            } catch (er) { }
        });

        // bell toggle
        (function () {
            var bell = document.getElementById('notifBell');
            var dd = document.getElementById('notifDropdown');
            if (!bell) return;
            bell.addEventListener('click', function (e) { e.stopPropagation(); if (!dd) return; dd.style.display = dd.style.display === 'block' ? 'none' : 'block'; });
            document.addEventListener('click', function (e) { if (!dd) return; if (!dd.contains(e.target) && !bell.contains(e.target)) dd.style.display = 'none'; });
        })();
    })();
</script>