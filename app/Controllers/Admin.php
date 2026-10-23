<?php
namespace App\Controllers;

class Admin extends AppBase
{
    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            if ($this->attempt(true)) return redirect()->to('/admin');
            $this->msg('err', 'Invalid admin login.');
        }
        return view('auth', ['title' => 'Admin Login', 'action' => site_url('admin/login'), 'reg' => false, 'admin' => true]);
    }

    public function logout() { session()->destroy(); return redirect()->to('/admin/login'); }

    private function sizesOf(array $ids): array
    {
        $out = [];
        if ($ids) foreach (db_connect()->table('uniform_sizes')->whereIn('uniform_id', $ids)->get()->getResultArray() as $r) {
            $out[$r['uniform_id']][$r['size']] = ['stock' => (int) $r['stock'], 'price' => (float) $r['price']];
        }
        return $out;
    }

    public function index()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $items = $db->table('uniforms')->orderBy('id', 'DESC')->get()->getResultArray();
        $stats = [
            'uniforms' => count($items),
            'stock'    => (int) ($db->table('uniform_sizes')->selectSum('stock', 't')->get()->getRow()->t ?? 0),
            'students' => $db->table('users')->where('role', 'student')->countAllResults(),
            'pending'  => $db->table('requests')->where('status', 'pending')->countAllResults(),
            'claimed'  => $db->table('requests')->where('status', 'claimed')->countAllResults(),
        ];
        return view('admin/list', ['items' => $items, 'stock' => $this->sizesOf(array_column($items, 'id')), 'sizes' => self::SIZES, 'stats' => $stats]);
    }

    public function users()
    {
        if ($r = $this->needAdmin()) return $r;
        $q = trim((string) $this->request->getGet('q'));
        $b = db_connect()->table('users u')
            ->select('u.id,u.student_no,u.name,u.created_at,COUNT(r.id) AS reqs')
            ->join('requests r', 'r.user_id=u.id', 'left')
            ->where('u.role', 'student')
            ->groupBy('u.id,u.student_no,u.name,u.created_at')
            ->orderBy('u.id', 'DESC')->limit(200);
        if ($q !== '') $b->groupStart()->like('u.name', $q)->orLike('u.student_no', $q)->groupEnd();
        return view('admin/users', ['rows' => $b->get()->getResultArray(), 'q' => $q]);
    }

    public function deleteUser()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $id = (int) $this->request->getPost('id');
        $user = $db->table('users')->where('id', $id)->where('role', 'student')->get()->getRowArray();
        $q = trim((string) $this->request->getPost('q'));
        $redirect = redirect()->to('/admin/users' . ($q !== '' ? '?q=' . rawurlencode($q) : ''));

        if (!$user) {
            $this->msg('err', 'Student not found.');
            return $redirect;
        }

        $approvedRequests = $db->table('requests')->select('size_id, COUNT(*) AS quantity', false)
            ->where('user_id', $id)->where('status', 'approved')->groupBy('size_id')->get()->getResultArray();
        $reservedListings = $db->table('listing_requests r')->select('r.listing_id')
            ->join('listings l', 'l.id=r.listing_id')
            ->where('r.buyer_id', $id)->where('r.status', 'approved')->where('l.status', 'reserved')
            ->get()->getResultArray();
        $photos = array_column(
            $db->table('listings')->select('photo')->where('seller_id', $id)->get()->getResultArray(),
            'photo'
        );

        $db->transBegin();
        foreach ($approvedRequests as $request) {
            $db->query('UPDATE uniform_sizes SET stock=stock+? WHERE id=?', [(int) $request['quantity'], (int) $request['size_id']]);
        }
        foreach ($reservedListings as $listing) {
            $db->table('listings')->where('id', $listing['listing_id'])->update(['status' => 'active']);
        }
        $db->table('users')->where('id', $id)->where('role', 'student')->delete();
        if (!$db->transStatus() || $db->affectedRows() !== 1) {
            $db->transRollback();
            $this->msg('err', 'Unable to remove student.');
            return $redirect;
        }
        if (!$db->transCommit()) {
            $this->msg('err', 'Unable to remove student.');
            return $redirect;
        }

        foreach ($photos as $photo) {
            $path = FCPATH . 'uploads/' . basename($photo);
            if (is_file($path) && !unlink($path)) {
                log_message('warning', 'Unable to remove listing photo for deleted student: {path}', ['path' => $path]);
            }
        }
        $this->msg('msg', 'Student removed.');
        return $redirect;
    }

    public function form($id = null)
    {
        if ($r = $this->needAdmin()) return $r;
        $it = ['id' => 0, 'name' => '', 'description' => '', 'date_acquired' => date('Y-m-d'), 'price' => '0.00', 'photo' => ''];
        $stock = [];
        if ($id) {
            $it = db_connect()->table('uniforms')->where('id', (int) $id)->get()->getRowArray();
            if (!$it) return redirect()->to('/admin');
            $sizeData = $this->sizesOf([(int) $id])[(int) $id] ?? [];
        } else {
            $sizeData = [];
        }
        return view('admin/form', ['it' => $it, 'sizeData' => $sizeData, 'sizes' => self::SIZES]);
    }

    public function save()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $id = (int) $this->request->getPost('id');
        $old = $id ? $db->table('uniforms')->where('id', $id)->get()->getRowArray() : null;
        if ($id && !$old) return redirect()->to('/admin');
        $file = $this->request->getFile('photo');
        $has = $file && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $img = 'is_image[photo]|mime_in[photo,image/jpeg,image/png,image/webp]|max_size[photo,2048]';
        $rules = [
            'name'          => 'required|min_length[3]|max_length[100]',
            'description'   => 'permit_empty|max_length[255]',
            'date_acquired' => 'required|valid_date[Y-m-d]',
        ];
        if (!$id) $rules['photo'] = 'uploaded[photo]|' . $img; elseif ($has) $rules['photo'] = $img;
        foreach (self::SIZES as $s) {
            $rules["price.$s"] = 'required|decimal|greater_than_equal_to[0]|less_than[100000]';
            $rules["stock.$s"] = 'required|is_natural|less_than[1000]';
        }
        if (!$this->validate($rules)) {
            $this->msg('err', implode(' ', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }
        $row = [
            'name'          => trim($this->request->getPost('name')),
            'description'   => trim((string) $this->request->getPost('description')),
            'date_acquired' => trim((string) $this->request->getPost('date_acquired')),
        ];
        if ($has) { $row['photo'] = $file->getRandomName(); $file->move(FCPATH . 'uploads', $row['photo']); }
        $stock = $this->request->getPost('stock');
        $prices = $this->request->getPost('price');
        $db->transStart();
        if ($id) $db->table('uniforms')->where('id', $id)->update($row);
        else { $db->table('uniforms')->insert($row); $id = $db->insertID(); }
        foreach (self::SIZES as $s) {
            $db->query(
                'INSERT INTO uniform_sizes(uniform_id,size,stock,price) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE stock=VALUES(stock),price=VALUES(price)',
                [$id, $s, (int) $stock[$s], round((float) $prices[$s], 2)]
            );
        }
        $db->transComplete();
        if ($has && $old) @unlink(FCPATH . 'uploads/' . basename($old['photo']));
        $this->msg('msg', 'Uniform saved.');
        return redirect()->to('/admin');
    }

    public function delete()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $u = $db->table('uniforms')->where('id', (int) $this->request->getPost('id'))->get()->getRowArray();
        if ($u) { $db->table('uniforms')->where('id', $u['id'])->delete(); @unlink(FCPATH . 'uploads/' . basename($u['photo'])); $this->msg('msg', 'Uniform deleted.'); }
        return redirect()->to('/admin');
    }

    private function allRequests(string $st = ''): array
    {
        $b = db_connect()->table('requests r')->select('r.id,r.status,r.created_at,r.price,us.name student,us.student_no,u.name uniform,s.size')
            ->join('users us', 'us.id=r.user_id')->join('uniform_sizes s', 's.id=r.size_id')->join('uniforms u', 'u.id=s.uniform_id')
            ->orderBy('r.id', 'DESC');
        if (in_array($st, ['pending', 'approved', 'rejected', 'claimed'], true)) $b->where('r.status', $st);
        return $b->get()->getResultArray();
    }

    public function requests()
    {
        if ($r = $this->needAdmin()) return $r;
        $st = (string) $this->request->getGet('status');
        return view('admin/requests', ['rows' => $this->allRequests($st), 'st' => $st]);
    }

    public function act()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $act = (string) $this->request->getPost('action');
        $req = $db->table('requests')->where('id', (int) $this->request->getPost('id'))->get()->getRowArray();
        if (!$req || !in_array($act, ['approve', 'reject', 'claim'], true)) return redirect()->to('/admin/requests');
        if ($act === 'approve' && $req['status'] === 'pending') {
            $db->transStart();
            $db->query('UPDATE uniform_sizes SET stock=stock-1 WHERE id=? AND stock>0', [$req['size_id']]);
            $ok = $db->affectedRows() === 1;
            if ($ok) $db->table('requests')->where('id', $req['id'])->update(['status' => 'approved']);
            $db->transComplete();
            $ok ? $this->msg('msg', 'Approved. Stock updated.') : $this->msg('err', 'Cannot approve: out of stock.');
        } elseif ($act === 'reject' && $req['status'] === 'pending') {
            $db->table('requests')->where('id', $req['id'])->update(['status' => 'rejected']);
        } elseif ($act === 'claim' && $req['status'] === 'approved') {
            $db->table('requests')->where('id', $req['id'])->update(['status' => 'claimed']);
        }
        return redirect()->to('/admin/requests');
    }

    public function deleteRequest()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $req = $db->table('requests')->where('id', (int) $this->request->getPost('id'))->get()->getRowArray();
        if ($req) {
            $db->transStart();
            // approved pa lang (hindi pa claimed): ibalik ang stock
            if ($req['status'] === 'approved') $db->query('UPDATE uniform_sizes SET stock=stock+1 WHERE id=?', [$req['size_id']]);
            $db->table('requests')->where('id', $req['id'])->delete();
            $db->transComplete();
            $this->msg('msg', $req['status'] === 'approved' ? 'Request deleted. Stock returned.' : 'Request deleted.');
        }
        return redirect()->to($this->request->getPost('back') === 'report' ? '/admin/report' : '/admin/requests');
    }

    public function clearRequests()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $db->table('requests')->whereIn('status', ['claimed', 'rejected'])->delete();
        $this->msg('msg', 'Cleared all claimed and rejected requests (' . $db->affectedRows() . ').');
        return redirect()->to('/admin/requests');
    }

    public function report()
    {
        if ($r = $this->needAdmin()) return $r;
        $items = db_connect()->table('uniforms')->orderBy('name')->get()->getResultArray();
        return view('admin/report', ['items' => $items, 'stock' => $this->sizesOf(array_column($items, 'id')), 'sizes' => self::SIZES, 'rows' => $this->allRequests()]);
    }
}