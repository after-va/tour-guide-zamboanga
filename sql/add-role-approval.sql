-- Add approval status to Account_Role table for guide approval workflow
-- Run this migration after the main schema

ALTER TABLE Account_Role ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1 COMMENT 'For Guide role: 0=pending, 1=approved. For Tourist role: always 1';

-- Create index for faster queries on pending guide approvals (compatible with MariaDB)
CREATE INDEX IF NOT EXISTS idx_pending_guides ON Account_Role(role_ID, is_approved);
