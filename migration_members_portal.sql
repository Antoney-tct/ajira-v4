USE ajira_v4;

ALTER TABLE members
  ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) DEFAULT NULL AFTER email,
  ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) DEFAULT NULL AFTER password_hash,
  ADD COLUMN IF NOT EXISTS bio VARCHAR(500) DEFAULT NULL AFTER profile_image,
  ADD COLUMN IF NOT EXISTS payment_status ENUM('not_submitted','submitted','verified','rejected') NOT NULL DEFAULT 'not_submitted' AFTER payment_ref,
  ADD COLUMN IF NOT EXISTS payment_proof VARCHAR(255) DEFAULT NULL AFTER payment_status,
  ADD COLUMN IF NOT EXISTS payment_amount DECIMAL(10,2) DEFAULT NULL AFTER payment_proof,
  ADD COLUMN IF NOT EXISTS payment_submitted_at DATETIME DEFAULT NULL AFTER payment_amount,
  ADD COLUMN IF NOT EXISTS reviewed_at DATETIME DEFAULT NULL AFTER payment_submitted_at;
ALTER TABLE members
  ADD COLUMN IF NOT EXISTS expires_at DATETIME DEFAULT NULL AFTER activated_at,
  ADD COLUMN IF NOT EXISTS deactivated_at DATETIME DEFAULT NULL AFTER expires_at,
  ADD COLUMN IF NOT EXISTS deactivation_reason VARCHAR(255) DEFAULT NULL AFTER deactivated_at;

CREATE TABLE IF NOT EXISTS member_notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  title VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_member_notifications (member_id, is_read, created_at),
  CONSTRAINT fk_member_notifications_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  audience ENUM('public','members') NOT NULL DEFAULT 'public',
  link VARCHAR(500) DEFAULT NULL,
  expires_at DATETIME DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notifications_audience (audience, created_at, expires_at),
  CONSTRAINT fk_notifications_admin FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE notifications ADD COLUMN IF NOT EXISTS expires_at DATETIME DEFAULT NULL AFTER link;

CREATE TABLE IF NOT EXISTS notification_reads (
  notification_id INT UNSIGNED NOT NULL,
  member_id INT UNSIGNED NOT NULL,
  read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (notification_id, member_id),
  CONSTRAINT fk_notification_reads_notification FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
  CONSTRAINT fk_notification_reads_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add these values to .env for the QR payment instruction.
-- QR_PAYMENT_LABEL=AJira Digital Club payment
