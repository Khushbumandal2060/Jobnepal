-- Run this on existing databases to add CV upload support

-- Add cv_file column to job_applications
ALTER TABLE job_applications 
ADD COLUMN cv_file VARCHAR(255) DEFAULT NULL AFTER cover_letter;

-- Change resume column in job_seekers from TEXT to VARCHAR(255) for file paths
-- First rename old column, add new one, then drop old (safe migration)
ALTER TABLE job_seekers 
ADD COLUMN resume_file VARCHAR(255) DEFAULT NULL AFTER profile_pic;

-- If you want to keep old resume text data, do NOT run the line below.
-- Otherwise, to clean up the old TEXT column after migrating:
-- ALTER TABLE job_seekers DROP COLUMN resume;
-- ALTER TABLE job_seekers RENAME COLUMN resume_file TO resume;
