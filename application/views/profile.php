<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap4/dist/css/bootstrap.min.css') ?>">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php
                if (!empty($this->session->flashdata('error'))) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $this->session->flashdata('error'); ?> <!-- Display error message if any -->
                    </div>
                <?php   }
                ?>
                <div class="card">
                    <div class="card-header"><b>User Profile</b><a class="btn btn-primary" href="<?=base_url('home')?>" style="float:right;">Home</a></div>
                    <div class="card-body">

                        <p>Welcome, <?php echo $user['first_name']; ?>!</p>
                        <p><strong>First Name:</strong> <?php echo $user['first_name']; ?></p>
                        <p><strong>Last Name:</strong> <?php echo $user['last_name']; ?></p>
                        <p><strong>Usename:</strong> <?php echo $user['username']; ?></p>
                        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                        <!-- Add more user details here as needed -->
                        <a class="btn btn-danger" href="<?php echo base_url('logout'); ?>">Logout</a><a class="btn btn-primary" href="<?=base_url('update-profile')?>" style="float:right;">Update Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>

</html>