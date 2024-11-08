<?php
require(__DIR__ . "/../../partials/nav.php");
reset_session();
?>
<div class="container-fluid">
    <form onsubmit="return validate(this)" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" required class="form-control" />
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" required maxlength="30" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="pw" class="form-label">Password</label>
            <input type="password" id="pw" name="password" required minlength="8" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="confirm" class="form-label">Confirm</label>
            <input type="password" name="confirm" required minlength="8" class="form-control" />
        </div>
        <input type="submit" value="Register" class="btn btn-primary" />
    </form>
</div>
<script>
    //dsp82 4/2/2024
    function validate(form) {
        var email = form.email.value;
        var username = form.username.value;
        var password = form.password.value;
        var confirm = form.confirm.value;

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var usernameRegex = /^[a-zA-Z0-9_-]{3,16}$/;
        var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

        if (!emailRegex.test(email)) {
            flash("Please enter a valid email address.");
            return false;
        }

        if (!usernameRegex.test(username)) {
            flash("Username must only contain 3-16 characters a-z, 0-9, _, or -");
            return false;
        }

        if (!passwordRegex.test(password)) {
            flash("Password must be at least 8 characters long and contain at least one digit, one lowercase letter, and one uppercase letter.");
            return false;
        }

        if (password !== confirm) {
            flash("Passwords do not match.");
            return false;
        }

        return true;
    }
</script>
<?php
//dsp82 /4/2/2024
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["username"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (!is_valid_username($username)) {
        flash("Username must only contain 3-16 characters a-z, 0-9, _, or -", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!", "success");
        } catch (Exception $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>