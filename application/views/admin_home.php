<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
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
                    <div class="card-header"><b>Admin Home</b></div>
                    <div class="card-body">
                        <h4>Welcome to Admin Home Page</h4>
                        <a class="btn btn-primary" href="<?php echo base_url('profile'); ?>">Go to Profile</a>
                        <a class="btn btn-warning" href="<?php echo base_url('users'); ?>">Users List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>