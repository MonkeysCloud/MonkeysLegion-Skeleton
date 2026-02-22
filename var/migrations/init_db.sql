-- Migration script to initialize the database schema for sessions and job queues

-- Table for sessions
CREATE TABLE IF NOT EXISTS sessions (
    session_id VARCHAR(255) NOT NULL,
    payload TEXT,
    flash_data TEXT,
    created_at BIGINT UNSIGNED NOT NULL,
    last_activity BIGINT UNSIGNED NOT NULL,
    expiration BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    PRIMARY KEY (session_id),
    INDEX idx_sessions_last_activity (last_activity),
    INDEX idx_sessions_expiration (expiration),
    INDEX idx_sessions_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for failed jobs
CREATE TABLE IF NOT EXISTS failed_jobs (
    id VARCHAR(64) NOT NULL,
    job VARCHAR(255) NOT NULL,
    payload JSON NOT NULL,
    original_queue VARCHAR(64) NOT NULL DEFAULT 'default',
    attempts INT NOT NULL DEFAULT 0,
    exception JSON NULL,
    failed_at BIGINT UNSIGNED NOT NULL,
    created_at BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    INDEX idx_failed_jobs_failed_at (failed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for job batches
CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(64) NOT NULL,
    name VARCHAR(255) NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids JSON NULL,
    options JSON NULL,
    cancelled_at BIGINT UNSIGNED NULL,
    created_at BIGINT UNSIGNED NOT NULL,
    finished_at BIGINT UNSIGNED NULL,
    PRIMARY KEY (id),
    INDEX idx_job_batches_created_at (created_at),
    INDEX idx_job_batches_finished_at (finished_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for normal jobs
CREATE TABLE IF NOT EXISTS jobs (
    id VARCHAR(64) NOT NULL,
    queue VARCHAR(64) NOT NULL DEFAULT 'default',
    job VARCHAR(255) NOT NULL,
    payload JSON NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    created_at BIGINT UNSIGNED NOT NULL,
    available_at BIGINT UNSIGNED NOT NULL,
    reserved_at BIGINT UNSIGNED NULL,
    failed_at BIGINT UNSIGNED NULL,
    PRIMARY KEY (id),
    INDEX idx_jobs_queue (queue),
    INDEX idx_jobs_available_at (available_at),
    INDEX idx_jobs_reserved_at (reserved_at),
    INDEX idx_jobs_failed_at (failed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;