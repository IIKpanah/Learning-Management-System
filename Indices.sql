CREATE INDEX idx_user_role ON users(role, status);
CREATE INDEX idx_course_semester ON courses(semester_id, status);
CREATE INDEX idx_enrollment_student ON enrollments(student_id);
CREATE INDEX idx_appointment_date ON appointments(meeting_date);
CREATE INDEX idx_notification_user ON notifications(user_id, is_read);