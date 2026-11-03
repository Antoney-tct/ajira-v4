USE ajira_v4;

ALTER TABLE contact_messages
  ADD COLUMN IF NOT EXISTS member_id INT UNSIGNED DEFAULT NULL AFTER id;

SET @message_member_index_sql = IF(
  (SELECT COUNT(*) FROM information_schema.statistics
   WHERE table_schema=DATABASE() AND table_name='contact_messages'
   AND index_name='idx_contact_messages_member')=0,
  'ALTER TABLE contact_messages ADD INDEX idx_contact_messages_member (member_id, created_at)',
  'SELECT 1'
);
PREPARE message_member_index_stmt FROM @message_member_index_sql;
EXECUTE message_member_index_stmt;
DEALLOCATE PREPARE message_member_index_stmt;

SET @message_member_fk_sql = IF(
  (SELECT COUNT(*) FROM information_schema.referential_constraints
   WHERE constraint_schema=DATABASE() AND table_name='contact_messages'
   AND constraint_name='fk_contact_messages_member')=0,
  'ALTER TABLE contact_messages ADD CONSTRAINT fk_contact_messages_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE message_member_fk_stmt FROM @message_member_fk_sql;
EXECUTE message_member_fk_stmt;
DEALLOCATE PREPARE message_member_fk_stmt;

CREATE TABLE IF NOT EXISTS contact_message_replies (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  contact_message_id INT UNSIGNED NOT NULL,
  sender_type ENUM('admin','member') NOT NULL,
  sender_member_id INT UNSIGNED DEFAULT NULL,
  sender_admin_id INT UNSIGNED DEFAULT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_contact_message_replies_thread (contact_message_id, created_at),
  CONSTRAINT fk_contact_message_replies_thread
    FOREIGN KEY (contact_message_id) REFERENCES contact_messages(id) ON DELETE CASCADE,
  CONSTRAINT fk_contact_message_replies_member
    FOREIGN KEY (sender_member_id) REFERENCES members(id) ON DELETE SET NULL,
  CONSTRAINT fk_contact_message_replies_admin
    FOREIGN KEY (sender_admin_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
