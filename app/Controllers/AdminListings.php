<?php
namespace App\Controllers;

class AdminListings extends AppBase
{
    public function index()
    {
        if ($r = $this->needAdmin()) return $r;
        $st = (string) $this->request->getGet('status');
        $b = db_connect()->table('listings l')->select('l.*,u.name seller,u.student_no')->join('users u', 'u.id=l.seller_id')
            ->orderBy("FIELD(l.status,'pending','active','reserved','sold','rejected')", '', false)->orderBy('l.id', 'DESC');
        if (in_array($st, ['pending', 'active', 'reserved', 'sold', 'rejected'], true)) $b->where('l.status', $st);
        return view('admin/listings', ['rows' => $b->get()->getResultArray(), 'st' => $st]);
    }

    public function act()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $act = (string) $this->request->getPost('action');
        $l = $db->table('listings')->where('id', (int) $this->request->getPost('id'))->get()->getRowArray();
        if ($l && $act === 'approve' && in_array($l['status'], ['pending', 'rejected'], true)) {
            $db->table('listings')->where('id', $l['id'])->update(['status' => 'active']);
            $this->msg('msg', 'Listing approved.');
        } elseif ($l && $act === 'reject' && in_array($l['status'], ['pending', 'active'], true)) {
            $db->table('listings')->where('id', $l['id'])->update(['status' => 'rejected']);
            $this->msg('msg', 'Listing rejected.');
        }
        return redirect()->to('/admin/listings');
    }

    public function delete()
    {
        if ($r = $this->needAdmin()) return $r;
        $db = db_connect();
        $l = $db->table('listings')->where('id', (int) $this->request->getPost('id'))->get()->getRowArray();
        if ($l) {
            $db->table('listings')->where('id', $l['id'])->delete();
            @unlink(FCPATH . 'uploads/' . basename($l['photo']));
            $this->msg('msg', 'Listing deleted.');
        }
        return redirect()->to('/admin/listings');
    }
}