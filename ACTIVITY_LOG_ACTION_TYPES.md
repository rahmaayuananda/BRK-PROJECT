# 📊 Activity Log - Action Types Reference

## Daftar Lengkap Action Types

Sistem activity log mendukung 4 tipe action utama:

---

## 1. LOGIN

### Kapan tercatat?
- User berhasil login di halaman `/auth/login`
- Kredensial (username & password) valid

### Lokasi Code
```php
// File: application/controllers/Auth.php
// Method: authenticate()
```

### Data yang tercatat
| Field | Contoh |
|-------|--------|
| `user_id` | 1 |
| `action` | LOGIN |
| `target_id` | null |
| `description` | User 'John Doe' berhasil login dari IP 192.168.1.1 |
| `created_at` | 2024-01-15 10:30:45 |

### Informasi yang diambil
- User ID dari session
- Nama user lengkap
- IP address user
- Timestamp otomatis

### Contoh Query
```sql
SELECT * FROM activity_log 
WHERE action = 'LOGIN' 
ORDER BY created_at DESC 
LIMIT 10;
```

---

## 2. CREATE_TOPIC

### Kapan tercatat?
- User membuat topik/diskusi baru di halaman forum
- Topik berhasil dibuat dan disimpan ke file/database

### Lokasi Code
```php
// File: application/controllers/Forum.php
// Method: create_topic()
```

### Data yang tercatat
| Field | Contoh |
|-------|--------|
| `user_id` | 1 |
| `action` | CREATE_TOPIC |
| `target_id` | bagaimana-cara-install-laravel-12345 |
| `description` | User 'John Doe' membuat topik baru dengan judul 'Bagaimana cara install Laravel?' |
| `created_at` | 2024-01-15 10:40:00 |

### Informasi yang diambil
- User ID dari session
- Nama user
- Topic ID (slug + hash unik)
- Judul topik yang dibuat
- Timestamp otomatis

### Contoh Query
```sql
SELECT * FROM activity_log 
WHERE action = 'CREATE_TOPIC' 
AND user_id = 1
ORDER BY created_at DESC;
```

### API Query
```
GET /activitylog/api_get_by_action/CREATE_TOPIC?limit=50
```

---

## 3. ARCHIVE_TOPIC

### Kapan tercatat?
- User/Admin mengklik tombol "Tutup/Close" pada topik (di halaman topic view)
- Admin mengarsipkan topik melalui fungsi `archive_topic()`
- Topik dipindahkan ke folder arsip

### Lokasi Code
```php
// File: application/controllers/Forum.php
// Methods:
//   - archive_topic() [API endpoint]
//   - close_topic()   [Direct close from topic page]
```

### Data yang tercatat
| Field | Contoh |
|-------|--------|
| `user_id` | 1 |
| `action` | ARCHIVE_TOPIC |
| `target_id` | bagaimana-cara-install-laravel-12345 |
| `description` | User 'John Doe' mengarsipkan topik dengan ID 'bagaimana-cara-install-laravel-12345' |
| `created_at` | 2024-01-15 12:00:00 |

### Informasi yang diambil
- User ID dari session
- Nama user
- Topic ID yang diarsipkan
- Timestamp otomatis

### Contoh Query
```sql
-- Get all archived topics
SELECT * FROM activity_log 
WHERE action = 'ARCHIVE_TOPIC' 
ORDER BY created_at DESC;

-- Get archived topics by specific user
SELECT * FROM activity_log 
WHERE action = 'ARCHIVE_TOPIC' 
AND user_id = 1;

-- Get archive history untuk topic tertentu
SELECT * FROM activity_log 
WHERE action = 'ARCHIVE_TOPIC' 
AND target_id = 'bagaimana-cara-install-laravel-12345';
```

### API Query
```
GET /activitylog/api_get_by_action/ARCHIVE_TOPIC?limit=50
GET /activitylog/api_get_by_topic/bagaimana-cara-install-laravel-12345
```

---

## 4. MARK_FAQ

### Kapan tercatat?
- Admin/Moderator menjadikan topik sebagai FAQ
- Topik dipindahkan ke section FAQ
- Biasanya dilakukan oleh admin saja

### Lokasi Code
```php
// File: application/controllers/Forum.php
// Method: set_faq()
```

### Data yang tercatat
| Field | Contoh |
|-------|--------|
| `user_id` | 3 |
| `action` | MARK_FAQ |
| `target_id` | bagaimana-cara-install-laravel-12345 |
| `description` | Admin 'Admin User' menjadikan topik dengan ID 'bagaimana-cara-install-laravel-12345' sebagai FAQ |
| `created_at` | 2024-01-15 14:00:00 |

### Informasi yang diambil
- Admin/User ID dari session
- Nama admin/user
- Topic ID yang dijadikan FAQ
- Role admin (dari session)
- Timestamp otomatis

### Contoh Query
```sql
-- Get semua FAQ yang dibuat
SELECT * FROM activity_log 
WHERE action = 'MARK_FAQ' 
ORDER BY created_at DESC;

-- Get FAQ yang dibuat oleh admin tertentu
SELECT * FROM activity_log 
WHERE action = 'MARK_FAQ' 
AND user_id = 3;

-- Track all FAQ history untuk topic
SELECT * FROM activity_log 
WHERE action = 'MARK_FAQ' 
AND target_id = 'bagaimana-cara-install-laravel-12345';
```

### API Query
```
GET /activitylog/api_get_by_action/MARK_FAQ?limit=50
```

---

## Analisis & Reporting

### Statistik Activity

```sql
-- Total activity by action type
SELECT action, COUNT(*) as total
FROM activity_log
GROUP BY action
ORDER BY total DESC;

-- Output contoh:
-- action          | total
-- CREATE_TOPIC    | 28
-- ARCHIVE_TOPIC   | 15
-- MARK_FAQ        | 8
-- LOGIN           | 42
```

### User Activity Report

```sql
-- Most active users
SELECT user_id, COUNT(*) as total_activities
FROM activity_log
GROUP BY user_id
ORDER BY total_activities DESC
LIMIT 10;

-- Combined dengan user info
SELECT 
    al.user_id,
    u.username,
    u.name,
    COUNT(*) as total_activities,
    COUNT(CASE WHEN al.action = 'LOGIN' THEN 1 END) as login_count,
    COUNT(CASE WHEN al.action = 'CREATE_TOPIC' THEN 1 END) as topics_created
FROM activity_log al
LEFT JOIN users u ON al.user_id = u.id_users
GROUP BY al.user_id, u.username, u.name
ORDER BY total_activities DESC;
```

### Daily Activity Report

```sql
-- Activity trend per day
SELECT 
    DATE(created_at) as date,
    action,
    COUNT(*) as count
FROM activity_log
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at), action
ORDER BY date DESC, action;
```

### Topic Activity Tracking

```sql
-- All activities untuk topic tertentu (timeline)
SELECT 
    al.id_log_activity,
    al.user_id,
    u.username,
    al.action,
    al.description,
    al.created_at
FROM activity_log al
LEFT JOIN users u ON al.user_id = u.id_users
WHERE al.target_id = 'bagaimana-cara-install-laravel-12345'
ORDER BY al.created_at DESC;
```

---

## Query Useful untuk Dashboard

### Get Recent Activities (Last 24 hours)
```sql
SELECT al.*, u.username, u.name
FROM activity_log al
LEFT JOIN users u ON al.user_id = u.id_users
WHERE al.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY al.created_at DESC
LIMIT 50;
```

### Get Activity by Hour (Distribution)
```sql
SELECT 
    HOUR(created_at) as hour,
    COUNT(*) as count
FROM activity_log
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY HOUR(created_at)
ORDER BY hour;
```

### Get Topics with Most Activities
```sql
SELECT 
    target_id,
    COUNT(*) as activity_count
FROM activity_log
WHERE action IN ('CREATE_TOPIC', 'ARCHIVE_TOPIC', 'MARK_FAQ')
GROUP BY target_id
ORDER BY activity_count DESC
LIMIT 20;
```

---

## Integration Checklist

### Saat Implementasi

- [x] Activity_log Model dibuat
- [x] Activitylog Controller dibuat
- [x] View activity log dibuat
- [x] Auth.php diupdate → log LOGIN
- [x] Forum.php diupdate → log CREATE_TOPIC
- [x] Forum.php diupdate → log ARCHIVE_TOPIC
- [x] Forum.php diupdate → log MARK_FAQ
- [x] Database tabel activity_log terbuat otomatis

### Testing Checklist

- [ ] Test LOGIN activity
- [ ] Test CREATE_TOPIC activity
- [ ] Test ARCHIVE_TOPIC activity
- [ ] Test MARK_FAQ activity
- [ ] Test Web UI dashboard
- [ ] Test API endpoints
- [ ] Test filtering & pagination
- [ ] Test date range queries

---

## Future Enhancements

### Possible Action Types to Add:

```php
// Comment/Reply
'CREATE_MESSAGE' - Saat user post reply/komentar
'DELETE_MESSAGE' - Saat user hapus komentar

// User Management
'USER_CREATED'   - Admin buat user baru
'USER_DELETED'   - Admin hapus user
'USER_UPDATED'   - Admin update data user
'PASSWORD_CHANGED' - User ganti password

// Moderation
'TOPIC_LOCKED'   - Topik dikunci (no reply)
'TOPIC_FEATURED' - Topik ditampilkan di featured
'USER_BANNED'    - User di-ban
'USER_WARNED'    - User diberi warning

// Search & View
'SEARCH_QUERY'   - User melakukan search
'TOPIC_VIEWED'   - User lihat topik
```

---

## Notes

- ✅ Semua timestamp menggunakan `CURRENT_TIMESTAMP` dari database
- ✅ Semua user info diambil dari `$this->session->userdata()`
- ✅ IP address diambil dari `$this->input->ip_address()`
- ✅ Database sudah terotomatis index untuk performa query
- ✅ System support untuk soft-delete atau archive old logs

---

**Last Updated:** 2024-01-15  
**Version:** 1.0  
**Status:** Production Ready ✅
