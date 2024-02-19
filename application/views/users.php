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
            <div class="col-md-10">
                <?php
                if (!empty($this->session->flashdata('error'))) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $this->session->flashdata('error'); ?> <!-- Display error message if any -->
                    </div>
                <?php   }

                ?>

                <?php
                if (!empty($this->session->flashdata('success'))) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $this->session->flashdata('success'); ?> <!-- Display error message if any -->
                    </div>
                <?php   }

                ?>
                <div class="card">
                    <div class="card-header"><b>Users List</b><a class="btn btn-primary" href="<?= base_url('home') ?>" style="float:right;">Home</a></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user) : ?>
                                        <tr>
                                            <td><?php echo $user->first_name; ?></td>
                                            <td><?php echo $user->last_name; ?></td>
                                            <td><?php echo $user->email; ?></td>
                                            <td><?= format_role($user->role_id); ?></td>
                                            <td>
                                                <a class="btn btn-info" href="<?php echo base_url('users/edit/' . encrypt($user->id)); ?>">Edit</a> |
                                                <a class="btn btn-danger" href="<?php echo base_url('users/delete/' . encrypt($user->id)); ?>">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>