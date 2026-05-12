# 📋 Dokumentasi Sistem Activity Log

## Daftar Isi
1. [Pengenalan](#pengenalan)
2. [Tabel Database](#tabel-database)
3. [Fitur Activity Logging](#fitur-activity-logging)
4. [Cara Menggunakan](#cara-menggunakan)
5. [API Endpoints](#api-endpoints)
6. [Contoh Implementasi](#contoh-implementasi)

---

## Pengenalan

Sistem activity log ini dirancang untuk mencatat semua aktivitas penting di forum Anda, termasuk:
- ✅ **Login User** - Ketika user berhasil login
- ✅ **Create Topic** - Ketika user membuat topik baru
- ✅ **Archive Topic** - Ketika topik ditutup/diarsipkan
- ✅ **Mark FAQ** - Ketika admin menjadikan topik sebagai FAQ

Semua log disimpan di database dan dapat diakses melalui:
- 📊 Dashboard Activity Log (Web UI)
- 🔌 API REST Endpoints
- 💾 Langsung dari database

---

## Tabel Database

### Struktur Tabel `activity_log`

```sql
CREATE TABLE activity_log (
    id_log_activity INT(10) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(10),
    action VARCHAR(50),
    target_id VARCHAR(100),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Field Penjelasan

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `id_log_activity` | INT | ID unik log activity (Primary Key) |
| `user_id` | INT | ID pengguna yang melakukan action |
| `action` | VARCHAR(50) | Jenis action: LOGIN, CREATE_TOPIC, ARCHIVE_TOPIC, MARK_FAQ |
| `target_id` | VARCHAR(100) | ID target (topic_id atau null untuk login) |
| `description` | TEXT | Deskripsi detail activity |
| `created_at` | DATETIME | Waktu activity terjadi |

---

## Fitur Activity Logging

### 1. LOGIN
**Ketika:** User berhasil login
**Action:** `LOGIN`
**Target ID:** `null`
**Description Example:** `User 'John Doe' berhasil login dari IP 192.168.1.1`

### 2. CREATE_TOPIC
**Ketika:** User membuat topik baru
**Action:** `CREATE_TOPIC`
**Target ID:** `{topic_id}` (ID topik yang dibuat)
**Description Example:** `User 'John Doe' membuat topik baru dengan judul 'Bagaimana cara install Laravel?'`

### 3. ARCHIVE_TOPIC
**Ketika:** 
- User/Admin menutup topik (klik tombol tutup pada topik)
- Admin mengarsipkan topik

**Action:** `ARCHIVE_TOPIC`
**Target ID:** `{topic_id}` (ID topik yang diarsipkan)
**Description Example:** `User 'John Doe' mengarsipkan topik dengan ID 'bagaimana-cara-install-laravel-12345'`

### 4. MARK_FAQ
**Ketika:** Admin menjadikan topik sebagai FAQ
**Action:** `MARK_FAQ`
**Target ID:** `{topic_id}` (ID topik yang dijadikan FAQ)
**Description Example:** `Admin 'Admin User' menjadikan topik dengan ID 'bagaimana-cara-install-laravel-12345' sebagai FAQ`

---

## Cara Menggunakan

### 1. Melihat Activity Log dari Web UI

Akses halaman activity log melalui browser:
```
http://localhost:8000/activitylog
```

#### Fitur Web UI:
- ✅ Tampilkan semua activity log dengan pagination
- ✅ Filter berdasarkan action (LOGIN, CREATE_TOPIC, etc)
- ✅ Filter berdasarkan user ID
- ✅ Filter berdasarkan target ID (topic ID)
- ✅ Badge warna untuk setiap action type
- ✅ Informasi user, timestamp, dan deskripsi

### 2. Menggunakan API REST

#### Endpoint Utama:

#### A. Get All Activities (Dengan Pagination)
```
GET /activitylog/api_get_all?page=1&limit=50
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id_log_activity": 1,
      "user_id": 1,
      "action": "LOGIN",
      "target_id": null,
      "description": "User 'John Doe' berhasil login dari IP 192.168.1.1",
      "created_at": "2024-01-15 10:30:45",
      "username": "john_doe",
      "name": "John Doe"
    }
  ],
  "pagination": {
    "current_page": 1,
    "limit": 50,
    "total": 156,
    "total_pages": 4
  }
}
```

#### B. Get Activities by User ID
```
GET /activitylog/api_get_by_user/{user_id}?limit=50
```

Contoh:
```
GET /activitylog/api_get_by_user/1?limit=50
```

#### C. Get Activities by Action
```
GET /activitylog/api_get_by_action/{action}?limit=50
```

Contoh:
```
GET /activitylog/api_get_by_action/LOGIN?limit=50
GET /activitylog/api_get_by_action/CREATE_TOPIC?limit=50
GET /activitylog/api_get_by_action/ARCHIVE_TOPIC?limit=50
GET /activitylog/api_get_by_action/MARK_FAQ?limit=50
```

#### D. Get Activities by Topic ID
```
GET /activitylog/api_get_by_topic/{topic_id}
```

Contoh:
```
GET /activitylog/api_get_by_topic/bagaimana-cara-install-laravel-12345
```

Response:
```json
{
  "success": true,
  "topic_id": "bagaimana-cara-install-laravel-12345",
  "data": [
    {
      "id_log_activity": 5,
      "user_id": 1,
      "action": "CREATE_TOPIC",
      "target_id": "bagaimana-cara-install-laravel-12345",
      "description": "User 'John Doe' membuat topik baru dengan judul 'Bagaimana cara install Laravel?'",
      "created_at": "2024-01-15 10:35:20",
      "username": "john_doe",
      "name": "John Doe"
    },
    {
      "id_log_activity": 12,
      "user_id": 2,
      "action": "MARK_FAQ",
      "target_id": "bagaimana-cara-install-laravel-12345",
      "description": "Admin 'Admin User' menjadikan topik dengan ID 'bagaimana-cara-install-laravel-12345' sebagai FAQ",
      "created_at": "2024-01-15 11:20:00",
      "username": "admin",
      "name": "Admin User"
    }
  ],
  "total": 2
}
```

#### E. Get Activity Statistics
```
GET /activitylog/api_get_statistics
```

Response:
```json
{
  "success": true,
  "data": {
    "total_activities": 156,
    "login_count": 42,
    "create_topic_count": 28,
    "archive_topic_count": 15,
    "mark_faq_count": 8
  }
}
```

#### F. Get Activities by Date Range
```
GET /activitylog/api_get_by_date_range?start_date=2024-01-01%2000:00:00&end_date=2024-01-31%2023:59:59&limit=100
```

Parameter:
- `start_date`: Format `Y-m-d H:i:s` (URL encoded)
- `end_date`: Format `Y-m-d H:i:s` (URL encoded)
- `limit`: (Optional) Default 100

Response:
```json
{
  "success": true,
  "date_range": {
    "start": "2024-01-01 00:00:00",
    "end": "2024-01-31 23:59:59"
  },
  "data": [ /* array of activities */ ],
  "total": 45
}
```

---

## API Endpoints

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/activitylog` | GET | Tampilkan halaman activity log |
| `/activitylog/api_get_all` | GET | Get semua activities dengan pagination |
| `/activitylog/api_get_by_user/{user_id}` | GET | Get activities by user ID |
| `/activitylog/api_get_by_action/{action}` | GET | Get activities by action type |
| `/activitylog/api_get_by_topic/{topic_id}` | GET | Get activities by topic ID |
| `/activitylog/api_get_statistics` | GET | Get statistics activity |
| `/activitylog/api_get_by_date_range` | GET | Get activities by date range |

---

## Contoh Implementasi

### 1. Query Activities di Controller/Model

```php
<?php
// Load model
$this->load->model('activity_log');

// Get semua activities
$all_activities = $this->activity_log->get_all_activities($limit = 50, $offset = 0);

// Get activities by user
$user_activities = $this->activity_log->get_user_activities($user_id = 1, $limit = 50);

// Get activities by action
$login_activities = $this->activity_log->get_activities_by_action('LOGIN', $limit = 50);

// Get activities by topic
$topic_activities = $this->activity_log->get_topic_activities('topic-id-123');

// Get dengan filter custom
$filters = [
    'action' => 'CREATE_TOPIC',
    'user_id' => 1,
    'target_id' => 'topic-id-123'
];
$filtered = $this->activity_log->get_activities_with_filters($filters, $limit = 50);

// Get activities by date range
$activities = $this->activity_log->get_activities_by_date_range(
    '2024-01-01 00:00:00',
    '2024-01-31 23:59:59',
    $limit = 100
);

// Get statistics
$total = $this->activity_log->count_all_activities();
$login_count = $this->activity_log->count_activities_by_action('LOGIN');
?>
```

### 2. Call API dari JavaScript

```javascript
// Get all activities
fetch('http://localhost:8000/activitylog/api_get_all?page=1&limit=50')
  .then(response => response.json())
  .then(data => {
    console.log('Activities:', data.data);
    console.log('Pagination:', data.pagination);
  });

// Get activities by user
fetch('http://localhost:8000/activitylog/api_get_by_user/1?limit=50')
  .then(response => response.json())
  .then(data => console.log('User activities:', data));

// Get activities by action
fetch('http://localhost:8000/activitylog/api_get_by_action/LOGIN?limit=50')
  .then(response => response.json())
  .then(data => console.log('Login activities:', data));

// Get activities by topic
fetch('http://localhost:8000/activitylog/api_get_by_topic/topic-id-123')
  .then(response => response.json())
  .then(data => console.log('Topic activities:', data));

// Get statistics
fetch('http://localhost:8000/activitylog/api_get_statistics')
  .then(response => response.json())
  .then(data => console.log('Statistics:', data.data));
```

### 3. Log Activity Manual di Controller

```php
<?php
class YourController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('activity_log');
    }
    
    public function some_action() {
        $user_id = $this->session->userdata('id_users');
        $fullname = $this->session->userdata('name');
        
        // Do something...
        
        // Log the activity
        $this->activity_log->log_activity(
            $user_id,
            'CUSTOM_ACTION',
            'target-id-123',
            "User '{$fullname}' melakukan custom action"
        );
    }
}
?>
```

---

## Tips & Best Practices

### 1. Performance
- Gunakan pagination untuk data besar
- Index database sudah dioptimalkan (user_id, action, created_at)
- Pertimbangkan archive log lama (misal > 6 bulan)

### 2. Security
- Pastikan user sudah terautentikasi sebelum akses log
- Pertimbangkan tambah role check (hanya admin bisa lihat semua log)
- Jangan expose IP user ke frontend jika tidak perlu

### 3. Customization
Anda bisa menambah action type baru:

**Di Auth.php atau controller lain:**
```php
$this->activity_log->log_activity(
    $user_id,
    'CUSTOM_ACTION_NAME',
    'custom-target-id',
    'Custom description'
);
```

**Di Activity_log.php:**
```php
// Tambah method baru
public function get_custom_actions($limit = 50) {
    $this->db->where('action', 'CUSTOM_ACTION_NAME');
    // ... query logic
}
```

---

## Troubleshooting

### 1. Tabel activity_log tidak terbuat
**Solusi:** Model Activity_log akan auto-create tabel. Jika tidak, jalankan query manual di atas.

### 2. Log tidak tercatat
**Periksa:**
- Apakah user sudah login? (check `id_users` di session)
- Apakah database connection aktif?
- Check error log di `application/logs/`

### 3. Query lambat
**Optimasi:**
- Gunakan pagination (limit & offset)
- Pastikan index sudah ada
- Archive data log lama ke tabel terpisah

---

## Summary

✅ **Sistem activity log siap digunakan!**

**File yang dibuat:**
- ✅ `/application/models/Activity_log.php` - Model untuk manage log
- ✅ `/application/controllers/Activitylog.php` - Controller untuk API & Web UI
- ✅ `/application/views/activitylog/index.php` - View untuk dashboard

**Fitur yang terintegrasi:**
- ✅ Login logging
- ✅ Create topic logging
- ✅ Archive topic logging
- ✅ Mark FAQ logging

**Akses:**
- 📊 Web UI: `http://localhost:8000/activitylog`
- 🔌 API: `http://localhost:8000/activitylog/api_*`

---

Selamat menggunakan! 🚀
