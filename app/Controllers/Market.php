<?php
namespace App\Controllers;

class Market extends AppBase
{
    public const CONDS = ['Like new', 'Good', 'Fair'];

    private function student()
    {
        $u = $this->me();
        if (!$u) return redirect()->to('/login');
        if ($u['role'] === 'admin') return redirect()->to('/admin');
        return null;
    }

    public function index()
    {
        if ($r = $this->student()) return $r;
        $db = db_connect();
        $me = $this->me()['id'];
        $q = trim((string) $this->request->getGet('q'));
        $size = (string) $this->request->getGet('size');
        $b = $db->table('listings l')->select('l.*,u.name seller')->join('users u', 'u.id=l.seller_id')
            ->where('l.status', 'active')->orderBy('l.id', 'DESC')->limit(100);
        if ($q !== '') $b->like('l.name', $q);
        if (in_array($size, self::SIZES, true)) $b->where('l.size', $size);
        $mine = array_map('intval', array_column($db->table('listing_requests')->select('listing_id')->where('buyer_id', $me)->get()->getResultArray(), 'listing_id'));
        return view('market/index', ['items' => $b->get()->getResultArray(), 'q' => $q, 'size' => $size, 'all' => self::SIZES, 'me' => $me, 'mine' => $mine]);
    }

    public function sell()
    {
        if ($r = $this->student()) return $r;
        $it = ['id' => 0, 'name' => '', 'description' => '', 'size' => 'M', 'item_condition' => 'Good', 'price' => '0.00', 'photo' => ''];
        $id = (int) $this->request->getGet('id');
        if ($id) {
            $row = db_connect()->table('listings')->where('id', $id)->where('seller_id', $this->me()['id'])->get()->getRowArray();
            if (!$row) return redirect()->to('/market/my');
            $it = $row;
        }
        return view('market/form', ['it' => $it, 'sizes' => self::SIZES, 'conds' => self::CONDS]);
    }

    public function save()
    {
        if ($r = $this->student()) return $r;
        $db = db_connect();
        $me = $this->me()['id'];
        $id = (int) $this->request->getPost('id');
        $old = $id ? $db->table('listings')->where('id', $id)->where('seller_id', $me)->get()->getRowArray() : null;
        if ($id && !$old) return redirect()->to('/market/my');
        if ($old && in_array($old['status'], ['reserved', 'sold'], true)) {
            $this->msg('err', 'You cannot edit a reserved or sold listing.');
            return redirect()->to('/market/my');
        }
        $file = $this->request->getFile('photo');
        $has = $file && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $img = 'is_image[photo]|mime_in[photo,image/jpeg,image/png,image/webp]|max_size[photo,2048]';
        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'price'       => 'required|decimal|greater_than_equal_to[0]|less_than[100000]',
        ];
        if (!$id) $rules['photo'] = 'uploaded[photo]|' . $img; elseif ($has) $rules['photo'] = $img;
        $errs = $this->validate($rules) ? [] : $this->validator->getErrors();
        $size = (string) $this->request->getPost('size');
        $cond = (string) $this->request->getPost('item_condition');
        if (!in_array($size, self::SIZES, true)) $errs[] = 'Invalid size.';
        if (!in_array($cond, self::CONDS, true)) $errs[] = 'Invalid condition.';
        if ($errs) {
            $this->msg('err', implode(' ', $errs));
            return redirect()->back()->withInput();
        }
        $row = [
            'name' => trim($this->request->getPost('name')),
            'description' => trim((string) $this->request->getPost('description')),
            'size' => $size, 'item_condition' => $cond,
            'price' => round((float) $this->request->getPost('price'), 2),
            'status' => 'pending',
        ];
        if ($has) { $row['photo'] = $file->getRandomName(); $file->move(FCPATH . 'uploads', $row['photo']); }
        if ($id) {
            $db->table('listings')->where('id', $id)->update($row);
            if ($has) @unlink(FCPATH . 'uploads/' . basename($old['photo']));
        } else {
            $row['seller_id'] = $me;
            $db->table('listings')->insert($row);
        }
        $this->msg('msg', 'Listing submitted! Waiting for admin approval.');
        return redirect()->to('/market/my');
    }

    public function delete()
    {
        if ($r = $this->student()) return $r;
        $db = db_connect();
        $l = $db->table('listings')->where('id', (int) $this->request->getPost('id'))->where('seller_id', $this->me()['id'])->get()->getRowArray();
        if (!$l) return redirect()->to('/market/my');
        if ($l['status'] === 'reserved') $this->msg('err', 'Mark it sold or release it first.');
        else {
            $db->table('listings')->where('id', $l['id'])->delete();
            @unlink(FCPATH . 'uploads/' . basename($l['photo']));
            $this->msg('msg', 'Listing deleted.');
        }
        return redirect()->to('/market/my');
    }

    public function buy()
    {
        if ($r = $this->student()) return $r;
        $db = db_connect();
        $me = $this->me()['id'];
        $l = $db->table('listings')->where('id', (int) $this->request->getPost('id'))->where('status', 'active')->get()->getRowArray();
        if (!$l) $this->msg('err', 'This listing is not available.');
        elseif ((int) $l['seller_id'] === $me) $this->msg('err', 'You cannot buy your own listing.');
        else {
            $db->query('INSERT IGNORE INTO listing_requests(listing_id,buyer_id) VALUES(?,?)', [$l['id'], $me]);
            $this->msg('msg', 'Request sent! Wait for the seller to approve it.');
        }
        return redirect()->to('/market/my');
    }

    public function cancel()
    {
        if ($r = $this->student()) return $r;
        db_connect()->table('listing_requests')->where('id', (int) $this->request->getPost('id'))
            ->where('buyer_id', $this->me()['id'])->where('status', 'pending')->delete();
        $this->msg('msg', 'Request cancelled.');
        return redirect()->to('/market/my');
    }

    public function decide()
    {
        if ($r = $this->student()) return $r;
        $db = db_connect();
        $act = (string) $this->request->getPost('action');
        $req = $db->table('listing_requests r')->select('r.id,r.listing_id')->join('listings l', 'l.id=r.listing_id')
            ->where('r.id', (int) $this->request->getPost('id'))->where('l.seller_id', $this->me()['id'])
            ->where('r.status', 'pending')->where('l.status', 'active')->get()->getRowArray();
        if (!$req || !in_array($act, ['approve', 'reject'], true)) return redirect()->to('/market/my');
        $db->transStart();
        if ($act === 'approve') {
            $db->table('listing_requests')->where('id', $req['id'])->update(['status' => 'approved']);
            $db->table('listing_requests')->where('listing_id', $req['listing_id'])->where('id !=', $req['id'])->where('status', 'pending')->update(['status' => 'rejected']);
            $db->table('listings')->where('id', $req['listing_id'])->update(['status' => 'reserved']);
        } else {
            $db->table('listing_requests')->where('id', $req['id'])->update(['status' => 'rejected']);
        }
        $db->transComplete();
        $this->msg('msg', $act === 'approve' ? 'Approved. The buyer can now see your contact.' : 'Request rejected.');
        return redirect()->to('/market/my');
    }

    public function sold()
    {
        if ($r = $this->student()) return $r;
        db_connect()->table('listings')->where('id', (int) $this->request->getPost('id'))
            ->where('seller_id', $this->me()['id'])->where('status', 'reserved')->update(['status' => 'sold']);
        $this->msg('msg', 'Marked as sold.');
        return redirect()->to('/market/my');
    }

    public function release()
    {
        if ($r = $this->student()) return $r;
        $db = db_connect();
        $id = (int) $this->request->getPost('id');
        $l = $db->table('listings')->where('id', $id)->where('seller_id', $this->me()['id'])->where('status', 'reserved')->get()->getRowArray();
        if ($l) {
            $db->transStart();
            $db->table('listings')->where('id', $id)->update(['status' => 'active']);
            $db->table('listing_requests')->where('listing_id', $id)->where('status', 'approved')->update(['status' => 'rejected']);
            $db->transComplete();
            $this->msg('msg', 'Your listing is active again.');
        }
        return redirect()->to('/market/my');
    }

    public function my()
    {
        if ($r = $this->student()) return $r;
        $db = db_connect();
        $me = $this->me()['id'];
        $mine = $db->table('listings')->where('seller_id', $me)->orderBy('id', 'DESC')->get()->getResultArray();
        $in = $db->table('listing_requests r')->select('r.id,r.status,r.created_at,l.name listing,l.size,u.name buyer,u.student_no')
            ->join('listings l', 'l.id=r.listing_id')->join('users u', 'u.id=r.buyer_id')
            ->where('l.seller_id', $me)->whereIn('r.status', ['pending', 'approved'])->orderBy('r.id', 'DESC')->get()->getResultArray();
        $out = $db->table('listing_requests r')->select('r.id,r.status,r.created_at,l.name,l.size,l.price,s.name seller,s.student_no seller_no')
            ->join('listings l', 'l.id=r.listing_id')->join('users s', 's.id=l.seller_id')
            ->where('r.buyer_id', $me)->orderBy('r.id', 'DESC')->get()->getResultArray();
        return view('market/my', ['mine' => $mine, 'in' => $in, 'out' => $out]);
    }
}