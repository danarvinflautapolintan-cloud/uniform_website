<?php
namespace App\Controllers;

abstract class AppBase extends BaseController
{
    public const SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

    protected function me() { return session('u'); }
    protected function needAdmin() { $u = $this->me(); return ($u && $u['role'] === 'admin') ? null : redirect()->to('/admin/login'); }
    protected function needUser() { return $this->me() ? null : redirect()->to('/login'); }
    protected function msg(string $k, string $m) { session()->setFlashdata($k, $m); }

    protected function attempt(bool $admin): bool
    {
        $u = db_connect()->table('users')->where('student_no', trim((string) $this->request->getPost('student_no')))->get()->getRowArray();
        if ($u && password_verify((string) $this->request->getPost('password'), $u['password_hash']) && (($u['role'] === 'admin') === $admin)) {
            session()->regenerate(true);
            session()->set('u', ['id' => (int) $u['id'], 'name' => $u['name'], 'role' => $u['role']]);
            return true;
        }
        usleep(400000);
        return false;
    }
}
