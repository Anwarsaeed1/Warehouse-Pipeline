<?php

return [
    // Common
    'created_success' => 'Created successfully',
    'updated_success' => 'Updated successfully',
    'deleted_success' => 'Deleted successfully',
    'cant_delete_record' => "Can't delete this record because related data",
    'cant_delete' => "Can't delete this record",
    'something_error' => 'Something went wrong',

    // Auth
    'not_found' => 'Not found',
    'account_not_found' => 'Account not found!',
    'invalid_reset_token' => 'Invalid password reset token!',
    'password_reset_success' => 'Password updated successfully',
    'reset_password_send_success' => 'Password reset request has been sent to your email',
    'invalid_email_and_password' => 'Email or password is incorrect',
    'user_logged_out' => 'User logged out',
    'profile_updated' => 'Profile updated',
    'invalid_otp_or_email' => 'Invalid OTP or email',
    'email_not_registered' => 'Email not registered',

    // Permissions & roles
    'no_required_permissions' => 'You do not have the required permissions',
    'record_not_found' => 'Record not found!',
    'root' => 'Root',
    'admin' => 'Admin',

    // Models (for RoleService/permissions)
    'permission' => 'Permission',
    'role' => 'Role',
    'user' => 'User',
    'warehouse' => 'Warehouse',
    'inventory_item' => 'Inventory Item',
    'stock' => 'Stock',
    'stock_transfer' => 'Stock Transfer',

    // Operations
    'create' => 'Create',
    'update' => 'Update',
    'delete' => 'Delete',
    'read' => 'Read',
    'view-all' => 'View all',
    'view all' => 'View all',
    'view-own' => 'View own',
    'view own' => 'View own',

    // General
    'yes' => 'Yes',
    'no' => 'No',
    'home' => 'Dashboard',

    // Inventory
    'quantity_exceeds_available' => 'Quantity exceeds available stock. Available: :available',
    'transfer_success' => 'Stock transferred successfully',
    'low_stock_alert' => 'Low stock alert',
    'login_success' => 'Login successful',
];
