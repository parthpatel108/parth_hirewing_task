<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_profile == 1 ? 'Update your profile' : 'Edit User' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap4/dist/css/bootstrap.min.css') ?>">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><b><?= $_profile == 1 ? 'Update your profile' : 'Edit User' ?></b></div>
                    <div class="card-body">
                        <form id="registrationForm" method="POST">
                            <input type="hidden" id="id" name="id" value="<?= encrypt($user['id']); ?>" />
                            <input type="hidden" id="_profile" name="_profile" value="<?= $_profile ?>" />
                            <div class="form-group">
                                <label for="first_name">Firstname:</label>
                                <input type="text" value="<?= $user['first_name'] ?>" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Lastname:</label>
                                <input type="text" value="<?= $user['last_name'] ?>" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" value="<?= $user['email'] ?>" class="form-control" id="email" name="email" required>
                            </div>
                            <?php
                            if ($_profile == 1) { ?>
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div id="passwordStrength"></div>
                                </div>
                            <?php }
                            ?>
                            <?php
                            if (!$_profile) { ?>
                                <div class="form-group">
                                    <label for="email">User Role:</label>
                                    <select type="role_id" class="form-control" id="role_id" name="role_id" required>
                                        <?php
                                        foreach ([1 => 'Administrator', 2 => 'Teacher', 3 => 'Student', 4 => 'Parent'] as $key => $value) {
                                            if ($key == $user['role_id']) { ?>
                                                <option value="<?= $key ?>" selected><?= $value ?></option>
                                            <?php } else { ?>
                                                <option value="<?= $key ?>"><?= $value ?></option>
                                        <?php   }
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php   }
                            ?>
                            <!-- <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div id="passwordStrength"></div>
                            </div> -->
                            <div class="form-group text-center"><a class="btn btn-danger mr-1" href="<?= base_url('users'); ?>">Cancel</a><button type="button" id="btnSubmit" class="btn btn-primary">Update</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check if jQuery is loaded and document is ready
            $('#password').keyup(function() {
                var password = $(this).val();
                $.post('<?= base_url('password_strength') ?>', {
                    password: password
                }, function(data) {
                    $('#passwordStrength').html(data);
                });
            });
            // Using event delegation to handle click event
            $(document).on('click', '#btnSubmit', function(event) {
                event.preventDefault(); // Prevent the default form submission
                var username = $('#username').val();
                var first_name = $('#first_name').val();
                var last_name = $('#last_name').val();
                var email = $('#email').val();
                var password = $('#password').val();
                // Client-side validation
                // Client-side validation
                if (first_name.trim() == '') {
                    alert('Please enter a first name.');
                    return false;
                }
                // Client-side validation
                if (last_name.trim() == '') {
                    alert('Please enter a last name.');
                    return false;
                }
                // if (username.trim() == '') {
                //     alert('Please enter a username.');
                //     return false;
                // }
                // if (email.trim() == '') {
                //     alert('Please enter an email.');
                //     return false;
                // }
                // if (password.trim() == '') {
                //     alert('Please enter a password.');
                //     return false;
                // }
                var formData = $('#registrationForm').serialize(); // Serialize form data
                // AJAX post request
                $.ajax({
                    url: '<?= base_url('update_user_details') ?>',
                    type: 'POST',
                    data: formData,
                    success: function(data) {
                        try {
                            var response = JSON.parse(data);
                            if (response.status == 'SUCCESS') {
                                alert(response.message);
                                if (response._profile == 1) {
                                    setTimeout(() => {
                                        location.href = '<?= base_url('profile') ?>';
                                    }, 300);
                                } else {
                                    setTimeout(() => {
                                        location.href = '<?= base_url('users') ?>';
                                    }, 300);
                                }
                            } else {
                                alert(response.message);
                            }
                        } catch (e) {
                            alert('Something went wrong, Please try again later.!');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>