<?php

if (! function_exists('isStudentEmail')) {
    function isStudentEmail(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        return $domain === config('auth.student_domain');
    }
}

if (! function_exists('isAdminEmail')) {
    function isAdminEmail(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        return $domain === config('auth.admin_domain');
    }
}
