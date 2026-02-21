-- Migration script to initialize the database schema for sessions and job queues

-- Table for sessions
CREATE TABLE IF NOT EXISTS sessions (
    session_id VARCHAR(255) NOT NULL PRIMARY KEY,
    payload TEXT,
    flash_data TEXT,
    created_at INTEGER,
    last_activity INTEGER,
    expiration INTEGER,
    user_id VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT
);

-- Table for failed jobs
CREATE TABLE IF NOT EXISTS failed_jobs (
    id VARCHAR(64) PRIMARY KEY,
    job VARCHAR(255) NOT NULL,
    payload JSON NOT NULL,
    original_queue VARCHAR(64) NOT NULL DEFAULT 'default',
    attempts INT NOT NULL DEFAULT 0,
    exception JSON NULL,
    failed_at DOUBLE NOT NULL,
    created_at DOUBLE NULL
);
-- Index for performance
CREATE INDEX idx_failed_jobs_failed_at ON failed_jobs(failed_at);

-- Table for job batches
CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(64) PRIMARY KEY,
    name VARCHAR(255) NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids JSON NULL,
    options JSON NULL,
    cancelled_at DOUBLE NULL,
    created_at DOUBLE NOT NULL,
    finished_at DOUBLE NULL
);

-- Table for normal jobs
CREATE TABLE IF NOT EXISTS jobs (
    id VARCHAR(64) PRIMARY KEY,
    queue VARCHAR(64) NOT NULL DEFAULT 'default',
    job VARCHAR(255) NOT NULL,
    payload JSON NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    created_at DOUBLE NOT NULL,
    available_at DOUBLE NULL,
    reserved_at DOUBLE NULL,
    failed_at DOUBLE NULL
);
-- Indexes for performance
CREATE INDEX idx_jobs_queue ON jobs(queue);
CREATE INDEX idx_jobs_available_at ON jobs(available_at);
CREATE INDEX idx_jobs_reserved_at ON jobs(reserved_at);
CREATE INDEX idx_jobs_failed_at ON jobs(failed_at);