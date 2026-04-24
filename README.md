# CV Manager PHP/WAMP

## Install on Windows with WAMP
1. Copy the `cv_manager` folder into `C:\wamp64\www\`.
2. Start WAMP. Make sure Apache and MySQL are green.
3. Open `http://localhost/phpmyadmin`.
4. Import `schema.sql`.
5. Check `config/database.php`. WAMP usually uses user `root` and blank password.
6. Open `http://localhost/cv_manager`.
7. Register a real user account. Passwords are encrypted with PHP `password_hash()` and checked with `password_verify()`.

## Features
- User registration/login/logout.
- Each logged-in user only sees their own CVs.
- Create, edit, delete, view, and print/save PDF.
- Two CV designs: `modern` and `classic`.
- Photo upload for each CV.
- MySQL storage; no hardcoded users.

## Notes
For production, use HTTPS, stronger upload storage rules, CSRF tokens, email verification, and server backups.
