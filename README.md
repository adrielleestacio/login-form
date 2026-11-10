# Register & Login System (PHP + MySQL)

A small registration and sign-in flow: users register with name, email and
password, sign in, land on a session-protected homepage, and log out.
Plain PHP with MySQLi — no framework.

## Run it locally (XAMPP)

1. Copy this folder to `C:\xampp\htdocs\login-form`.
2. Start Apache and MySQL in the XAMPP control panel.
3. Import `database.sql` (phpMyAdmin → Import), or run
   `mysql -u root -p < database.sql`.
4. Check the credentials in `connect.php` match your MySQL setup.
5. Open <http://localhost/login-form/>.

## Security notes

This started as a tutorial-style project and was later hardened. What changed
and why:

| Before | Now | Why |
| --- | --- | --- |
| `md5($password)` | `password_hash()` / `password_verify()` (bcrypt, salted) | md5 is fast and unsalted, so leaked hashes are trivially cracked. |
| SQL built from `$_POST` strings | Prepared statements with bound parameters | Input can no longer alter the query — no SQL injection. |
| Same session ID before and after login | `session_regenerate_id(true)` on login | Prevents session fixation. |
| "Email not found" vs "wrong password" | One generic message | Doesn't reveal which emails are registered. |
| Names echoed raw | `htmlspecialchars()` on output | Blocks stored XSS through a user's name. |
| `logout.php` without `session_start()` | Starts the session, clears it, expires the cookie | The old logout never actually logged anyone out. |
| Homepage viewable by anyone | Redirects to sign-in unless `user_id` is in the session | Protected page is actually protected. |

Accounts created by the old version still work: a legacy md5 hash is accepted
once and immediately replaced with a bcrypt hash on that sign-in.

## Files

| File | Purpose |
| --- | --- |
| `index.php` | Sign-in and register forms, shows feedback messages |
| `register.php` | Handles both form submissions |
| `homepage.php` | Protected page greeting the signed-in user |
| `logout.php` | Ends the session |
| `connect.php` | Database connection |
| `database.sql` | Schema |
