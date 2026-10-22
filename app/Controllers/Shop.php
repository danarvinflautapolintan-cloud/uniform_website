<?php
namespace App\Controllers;

use CodeIgniter\Database\BaseBuilder;

class Shop extends AppBase
{
       public function index()
    {
        if ($r = $this->needUser()) return $r;
        if ($this->me()['role'] === 'admin') return redirect()->to('/admin');
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));
        $size = (string) $this->request->getGet('size');
        $b = $db->table('uniforms')->orderBy('id', 'DESC')->limit(100);
        // itago ang uniform na zero stock sa lahat ng size
        $b->whereIn('id', static function (BaseBuilder $s) {
            return $s->select('uniform_id')->from('uniform_sizes')->where('stock >', 0);
        });
        if ($q !== '') $b->like('name', $q);
        if (in_array($size, self::SIZES, true)) {
            $b->whereIn('id', static function (BaseBuilder $s) use ($size) {
                return $s->select('uniform_id')->from('uniform_sizes')->where('size', $size)->where('stock >', 0);
            });
        }
        $items = $b->get()->getResultArray();
        $sizes = [];
        if ($items) {
            $ids = array_column($items, 'id');
            foreach ($db->table('uniform_sizes')->whereIn('uniform_id', $ids)->get()->getResultArray() as $r) $sizes[$r['uniform_id']][] = $r;
            foreach ($sizes as &$list) usort($list, fn($a, $c) => array_search($a['size'], self::SIZES) <=> array_search($c['size'], self::SIZES));
        }
        return view('shop/index', ['items' => $items, 'sizes' => $sizes, 'q' => $q, 'size' => $size, 'all' => self::SIZES]);
    }

    public function register()
    {
        if ($this->request->getMethod() === 'POST') {
            $ok = $this->validate([
                'student_no' => 'required|regex_match[/^[A-Za-z0-9-]{4,20}$/]|is_unique[users.student_no]',
                'name'       => 'required|max_length[100]',
                'password'   => 'required|min_length[8]',
            ]);
            if ($ok) {
                db_connect()->table('users')->insert([
                    'student_no' => trim($this->request->getPost('student_no')),
                    'name' => trim($this->request->getPost('name')),
                    'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => 'student',
                ]);
                $this->msg('msg', 'Registered! Please log in to browse uniforms.');
                return redirect()->to('/login');
            }
            $this->msg('err', implode(' ', $this->validator->getErrors()));
        }
        return view('auth', ['title' => 'Register', 'action' => site_url('register'), 'reg' => true]);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            if ($this->attempt(false)) return redirect()->to('/');
            $this->msg('err', 'Invalid student no. or password.');
        }
        return view('auth', ['title' => 'Login', 'action' => site_url('login'), 'reg' => false]);
    }

    public function logout() { session()->destroy(); return redirect()->to('/login'); }

    public function reserve()
    {
        if ($r = $this->needUser()) return $r;
        if ($this->me()['role'] === 'admin') return redirect()->to('/admin');
        $db = db_connect();
        $sid = (int) $this->request->getPost('size_id');
        $uid = $this->me()['id'];
        $s = $db->table('uniform_sizes')->where('id', $sid)->where('stock >', 0)->get()->getRowArray();
        if (!$s) $this->msg('err', 'Out of stock.');
        elseif ($db->table('requests')->where('user_id', $uid)->where('size_id', $sid)->whereIn('status', ['pending', 'approved'])->countAllResults() > 0) $this->msg('err', 'You already requested this.');
        else { $db->table('requests')->insert(['user_id' => $uid, 'size_id' => $sid]); $this->msg('msg', 'Request sent! Wait for the admin to approve it.'); }
        return redirect()->to('/mine');
    }

  
      public function mine()
    {
        if ($r = $this->needUser()) return $r;
        if ($this->me()['role'] === 'admin') return redirect()->to('/admin');
        $rows = db_connect()->table('requests r')->select('r.id,r.status,r.created_at,u.name,u.price,s.size')
            ->join('uniform_sizes s', 's.id=r.size_id')->join('uniforms u', 'u.id=s.uniform_id')
            ->where('r.user_id', $this->me()['id'])->orderBy('r.id', 'DESC')->get()->getResultArray();
        return view('shop/mine', ['rows' => $rows]);
    }
    public function cancel()
    {
        if ($r = $this->needUser()) return $r;
        db_connect()->table('requests')
            ->where('id', (int) $this->request->getPost('id'))
            ->where('user_id', $this->me()['id'])
            ->where('status', 'pending')
            ->delete();
        $this->msg('msg', 'Request cancelled.');
        return redirect()->to('/mine');
    }
}