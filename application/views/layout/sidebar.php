<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
// Determine active sidebar state; respect `from` query on topic detail pages
$seg1 = $this->uri->segment(1);
$seg2 = $this->uri->segment(2);
$from = $this->input->get('from', true) ?? '';
// ✅ Dashboard active
$dashboard_active = '';
$all_active = '';
$my_active = '';

// PRIORITAS: halaman topic
if ($seg1 == 'forum' && $seg2 == 'topic') {

    if ($from == 'my_topics') {
        $my_active = 'active';
    } elseif ($from == 'dashboard') {
        $dashboard_active = 'active';
    } elseif ($from == 'arsip') {
        $arsip_active = 'active';
    } elseif ($from == 'faq') {
        $faq_active = 'active';
    } else {
        $all_active = 'active'; // default
    }

}
// HALAMAN NORMAL
else {

    if ($seg1 == 'dashboard') {
        $dashboard_active = 'active';

    } elseif ($seg1 == 'forum' && $seg2 == 'my_topics') {
        $my_active = 'active';

    } elseif ($seg1 == 'forum' && $seg2 == 'arsip') {
        $arsip_active = 'active'; // ✅ TAMBAHKAN INI

    } elseif ($seg1 == 'forum' && $seg2 == 'faq') {
        $faq_active = 'active'; // sekalian biar lengkap

    } elseif ($seg1 == 'forum') {
        $all_active = 'active';
    }
}
?>

<aside class="sidebar">
    <?php if (!empty($show_new_button)): ?>
        <button id="<?php echo isset($new_button_id) ? $new_button_id : 'newDiscussion'; ?>" class="btn primary"
            style="display:block;width:100%;padding:10px 12px;margin-bottom:12px;text-align:center;">
            <?php echo isset($new_button_label) ? $new_button_label : '+ NEW DISCUSSION'; ?>
        </button>
    <?php endif; ?>
    <div class="sidebar-card">
        <ul class="categories">
            <li class="<?= $dashboard_active ?>">
                <a href="<?= site_url('dashboard'); ?>">
                    <span class="icon">🏠</span> Dashboard
                </a>
            </li>

            <li class="<?= $all_active ?>">
                <a href="<?= site_url('forum'); ?>">
                    <span class="icon">💬</span> All Topic
                </a>
            </li>

            <?php if ($this->session->userdata('role') === 'user'): ?>
                <li class="<?= $my_active ?>">
                    <a href="<?= site_url('forum/my_topics'); ?>">
                        <span class="icon">📌</span> My Topics
                    </a>
                </li>
            <?php endif; ?>

            <li class="<?= $arsip_active ?? '' ?>">
                <a href="<?= site_url('forum/arsip'); ?>">
                    <span class="icon">🗂️</span> Arsip
                </a>
            </li>

            <li class="<?= $faq_active ?? '' ?>">
                <a href="<?= site_url('forum/faq'); ?>">
                    <span class="icon">❓</span> FAQ
                </a>
            </li>
        </ul>
    </div>

    <?php if ($this->session && $this->session->userdata('logged_in')): ?>
        <a href="<?php echo site_url('auth/logout'); ?>" class="sidebar-card logout-card">
            <div class="logout-box">
                <div class="logout-icon">🚪</div>
                <div>
                    <div class="logout-title">Logout</div>
                    <div class="logout-sub">Keluar dari akun</div>
                </div>
            </div>
        </a>
    <?php endif; ?>
</aside>