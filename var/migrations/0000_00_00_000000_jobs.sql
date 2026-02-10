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

-- Table for failed jobs

CREATE TABLE IF NOT EXISTS failed_jobs (
    id VARCHAR(64) PRIMARY KEY,
    job VARCHAR(255) NOT NULL,
    payload JSON NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    exception JSON NULL,
    failed_at DOUBLE NOT NULL,
    created_at DOUBLE NULL
);

-- Index for performance
CREATE INDEX idx_failed_jobs_failed_at ON failed_jobs(failed_at);
