CREATE DATABASE IF NOT EXISTS ajira_v4 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ajira_v4;

-- Test credentials only. Change both passwords before production use.
INSERT INTO admin_users(username,password_hash)
VALUES('admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash);

INSERT INTO members(full_name,reg_number,phone,email,password_hash,course_year,member_type,is_active,expires_at)
VALUES('Test Member','TEST/2026/001','0712345678','member@example.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','BBIT Year 1','new',1,DATE_ADD(NOW(),INTERVAL 1 YEAR))
ON DUPLICATE KEY UPDATE
  full_name=VALUES(full_name), phone=VALUES(phone), email=VALUES(email),
  password_hash=VALUES(password_hash), is_active=VALUES(is_active), expires_at=VALUES(expires_at);

INSERT INTO member_notifications(member_id,title,message)
SELECT id,'Welcome to the test profile','This is a test member account for checking the profile and admin management workflow.'
FROM members
WHERE reg_number='TEST/2026/001'
  AND NOT EXISTS (
    SELECT 1 FROM member_notifications n
    WHERE n.member_id=members.id AND n.title='Welcome to the test profile'
  );
