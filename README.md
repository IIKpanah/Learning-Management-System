# ADSU Virtual Learning Platform (Sandwich Programme)

A complete Learning Management System for Adamawa State University (ADSU) Mubi, designed for the Sandwich Programme.

## Features
- Role-based access: Admin, Lecturer, Student
- Course management, enrolment, materials upload (PDF, PPT, video)
- Appointment scheduling with email reminders
- Assignment submission and grading
- Gradebook with automatic GPA calculation
- Real-time notifications
- RESTful API with JWT authentication
- Mobile-responsive UI

## Installation

1. Clone the repository into your web root.
2. Import the database schema from the SQL script provided in the documentation.
3. Run `composer install` to install dependencies.
4. Edit `includes/config.php` with your database and email settings.
5. Set correct file permissions (uploads/ folders must be writable).
6. Access via browser and login with default admin account (create manually in DB or via registration).

## API Endpoints

- `POST /api/auth.php?register` – Student registration
- `POST /api/auth.php?login` – Login (returns JWT)
- `GET /api/courses.php` – List courses based on role
- `POST /api/courses.php` – Create course (admin)
- `GET /api/materials.php?course_id=` – List materials
- `POST /api/materials.php` – Upload material (lecturer)
- `GET /api/appointments.php` – List appointments
- `POST /api/appointments.php` – Book appointment (student)
- `PUT /api/appointments.php` – Update appointment status (lecturer)
- `GET /api/assignments.php?course_id=` – List assignments
- `POST /api/assignments.php` – Create assignment (lecturer)
- `POST /api/assignments.php?grade` – Grade submission (lecturer)
- `GET /api/notifications.php` – Fetch notifications

## Security

- All passwords hashed with bcrypt.
- JWT stored in HTTP‑Only cookie.
- Prepared statements prevent SQL injection.
- XSS filtering on all outputs.
- CSRF protection (can be added).

## Support

For issues, contact iikpanah2018@gmail.com for more projects!***.