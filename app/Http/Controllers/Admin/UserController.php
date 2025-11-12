<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function data(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');
        $length = $request->input('length');
        $searchValue = $request->input('search.value');

        $query = User::where('user_type', 'user');

        // Search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('fullname', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('type', 'like', "%{$searchValue}%");
            });
        }

        $total = User::where('user_type', 'user')->count();
        $filtered = $query->count();

        $users = $query->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $users->map(function ($user, $index) use ($start) {
            return [
                'sr_no' => $start + $index + 1,
                'customer' => $this->renderCustomerCell($user),
                'email' => $user->email,
                'type' => $this->renderTypeBadge($user->type),
                'joined' => $user->created_at->format('d M Y'),
                'status' => $user->auto_approved
                    ? '<span class="badge badge-success">Approved</span>'
                    : '<span class="badge badge-warning">Pending</span>',
                'approval' => $this->renderApprovalToggle($user),
            ];
        })->toArray();

        return response()->json([
            'draw' => (int)$draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    private function renderCustomerCell($user)
    {
        $avatar = $user->avatar
            ? "<img src='{$user->avatar}' class='rounded-circle w-40px h-40px' alt='avatar'>"
            : "<div class='symbol-label bg-light-primary text-primary fw-bold fs-4 rounded-circle w-40px h-40px d-flex align-items-center justify-content-center'>"
            . strtoupper(substr($user->fullname, 0, 1)) . "</div>";

        return "<div class='d-flex align-items-center'>
                    <div class='symbol symbol-40px me-4'>{$avatar}</div>
                    <div class='d-flex flex-column'>
                        <span class='text-gray-800 fw-bold'>{$user->fullname}</span>
                    </div>
                </div>";
    }

    private function renderTypeBadge($type)
    {
        $badges = [
            'email'     => ['light-primary', 'Email'],
            'google'    => ['light-danger', 'Google'],
            'facebook'  => ['light-info', 'Facebook'],
        ];

        $badge = $badges[$type] ?? ['light-secondary', 'Unknown'];

        return "<span class='badge badge-{$badge[0]} fw-bold'>{$badge[1]}</span>";
    }

    private function renderApprovalToggle($user)
    {
        $checked = $user->auto_approved ? 'checked' : '';

        return "<div class='d-flex justify-content-center'>
                    <div class='form-check form-check-solid form-switch form-check-custom fv-row'>
                        <input type='checkbox' class='form-check-input w-45px h-25px user-approval-toggle'
                               data-id='{$user->id}' {$checked}>
                        <label class='form-check-label d-none'></label>
                    </div>
                </div>";
    }

    public function approve(User $user, Request $request)
    {
        if ($user->user_type !== 'user') {
            return response()->json(['success' => false, 'message' => 'Invalid user'], 403);
        }

        $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN);
        $user->auto_approved = $status;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $status ? 'User approved' : 'Approval revoked'
        ]);
    }
}