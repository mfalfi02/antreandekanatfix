-- Supabase schema for antrean-dekanat1
-- Jalankan di Supabase SQL Editor pada database kosong / schema baru.

CREATE TABLE IF NOT EXISTS users (
    kode VARCHAR(20) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'mahasiswa',
    jabatan VARCHAR(255) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'aktif',
    ruangan VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT users_role_check CHECK (role IN ('mahasiswa', 'dosen', 'pejabat', 'admin')),
    CONSTRAINT users_status_check CHECK (status IN ('aktif', 'nonaktif'))
);

CREATE TABLE IF NOT EXISTS services (
    id BIGSERIAL PRIMARY KEY,
    nama_layanan VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'aktif',
    est INTEGER NOT NULL DEFAULT 10,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT services_status_check CHECK (status IN ('aktif', 'nonaktif'))
);

CREATE TABLE IF NOT EXISTS system_settings (
    id BIGSERIAL PRIMARY KEY,
    queue_status VARCHAR(20) NOT NULL DEFAULT 'closed',
    center_latitude NUMERIC(10, 7) NULL,
    center_longitude NUMERIC(10, 7) NULL,
    radius_meters INTEGER NOT NULL DEFAULT 300,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id VARCHAR(20) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

ALTER TABLE sessions
    ADD CONSTRAINT sessions_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES users(kode) ON DELETE CASCADE;

CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions(user_id);
CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions(last_activity);

CREATE TABLE IF NOT EXISTS queues (
    id BIGSERIAL PRIMARY KEY,
    kode_user VARCHAR(20) NOT NULL,
    kode_dosen VARCHAR(20) NULL,
    service_id BIGINT NOT NULL,
    nomor_antrian INTEGER NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'menunggu',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT queues_status_check CHECK (status IN ('menunggu', 'diproses', 'selesai', 'batal'))
);

ALTER TABLE queues
    ADD CONSTRAINT queues_kode_user_foreign
    FOREIGN KEY (kode_user) REFERENCES users(kode) ON DELETE CASCADE;

ALTER TABLE queues
    ADD CONSTRAINT queues_kode_dosen_foreign
    FOREIGN KEY (kode_dosen) REFERENCES users(kode) ON DELETE SET NULL;

ALTER TABLE queues
    ADD CONSTRAINT queues_service_id_foreign
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE;

CREATE INDEX IF NOT EXISTS queues_kode_user_index ON queues(kode_user);
CREATE INDEX IF NOT EXISTS queues_kode_dosen_index ON queues(kode_dosen);
CREATE INDEX IF NOT EXISTS queues_service_id_index ON queues(service_id);
CREATE INDEX IF NOT EXISTS queues_status_index ON queues(status);

CREATE TABLE IF NOT EXISTS ruang_antri (
    id_ruang_antri BIGSERIAL PRIMARY KEY,
    kode_dosen VARCHAR(20) NOT NULL,
    service_id BIGINT NULL,
    service_ids JSONB NULL,
    status_ruang VARCHAR(20) NOT NULL DEFAULT 'closed',
    expected_jam_buka_ruang_antri TIME NULL,
    expected_jam_tutup_ruang_antri TIME NULL,
    jam_buka_ruang_antri TIME NULL,
    jam_tutup_ruang_antri TIME NULL,
    tanggal_buka_ruang_antri DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

ALTER TABLE ruang_antri
    ADD CONSTRAINT ruang_antri_kode_dosen_foreign
    FOREIGN KEY (kode_dosen) REFERENCES users(kode) ON DELETE CASCADE;

ALTER TABLE ruang_antri
    ADD CONSTRAINT ruang_antri_service_id_foreign
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL;

CREATE INDEX IF NOT EXISTS ruang_antri_kode_dosen_index ON ruang_antri(kode_dosen);
CREATE INDEX IF NOT EXISTS ruang_antri_service_id_index ON ruang_antri(service_id);
CREATE INDEX IF NOT EXISTS ruang_antri_tanggal_buka_index ON ruang_antri(tanggal_buka_ruang_antri);
