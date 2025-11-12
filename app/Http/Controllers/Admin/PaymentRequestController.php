<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function index()
    {
        return view('admin.payment-requests.index');
    }

    public function data(Request $request)
    {
        try {
            $draw   = $request->input('draw', 1);
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');

            $query = PaymentRequest::with(['user', 'quote.pickupDetail', 'quote.deliveryDetail']);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('fullname', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('quote_id', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%");
                });
            }

            $total    = PaymentRequest::count();
            $filtered = (clone $query)->count();

            $requests = $query->latest()
                ->skip($start)
                ->take($length)
                ->get();

            $data = $requests->map(function ($req, $idx) use ($start) {
                $carrier = $req->carrier_data ?? [];

                return [
                    'sr_no'     => $start + $idx + 1,
                    'user'      => $this->renderUserCell($req->user),
                    'quote_id'  => '<a href="'.route('quotes.index').'#quote-'.$req->quote_id.'" target="_blank">#'.$req->quote_id.'</a>',
                    'route'     => $this->renderRouteCell($req),
                    'carrier'   => $this->renderCarrierCell($carrier),
                    'amount'    => $this->renderAmountCell($req),
                    'requested' => $req->created_at->format('d M Y').'<br><small class="text-muted">'.$req->created_at->diffForHumans().'</small>',
                    'status'    => $this->renderStatusBadge($req->status),
                    'actions'   => $this->renderActions($req),
                ];
            })->toArray();

            return response()->json([
                'draw'            => (int)$draw,
                'recordsTotal'    => $total,
                'recordsFiltered' => $filtered,
                'data'            => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('PaymentRequests DataTable Error: '.$e->getMessage());
            return response()->json([
                'draw'            => 1,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Server error'
            ], 500);
        }
    }

    public function updateStatus(Request $request, PaymentRequest $paymentRequest)
    {
        $request->validate([
            'status'      => 'required|in:approved,rejected',
            'admin_note'  => 'nullable|string|max:500'
        ]);

        if ($paymentRequest->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Already processed.'], 422);
        }

        $paymentRequest->update([
            'status'      => $request->status,
            'admin_note'  => $request->admin_note,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $msg = $request->status === 'approved'
            ? 'Booking approved! User can now proceed to payment.'
            : 'Booking rejected.';

        return response()->json(['success' => true, 'message' => $msg]);
    }

    // Render Helpers
    private function renderUserCell($user)
    {
        if (!$user) return '<span class="text-muted">—</span>';

        $avatar = $user->avatar
            ? "<img src='{$user->avatar}' class='rounded-circle w-40px h-40px' alt='avatar'>"
            : "<div class='symbol symbol-40px symbol-light-primary d-flex align-items-center justify-content-center fw-bold fs-4'>"
              . strtoupper(substr($user->fullname, 0, 1)) . "</div>";

        return "<div class='d-flex align-items-center'>
                    <div class='symbol symbol-40px me-4'>{$avatar}</div>
                    <div class='d-flex flex-column'>
                        <span class='text-gray-800 fw-bold'>{$user->fullname}</span>
                        <span class='text-muted'>{$user->email}</span>
                    </div>
                </div>";
    }

    private function renderRouteCell($req)
    {
        $pickup = $req->quote?->pickupDetail;
        $delivery = $req->quote?->deliveryDetail;
        if (!$pickup || !$delivery) return '<span class="text-muted">—</span>';

        return "<small>
                    <span class='text-success'>{$pickup->city}, {$pickup->state}</span><br>
                    <i class='ki-duotone ki-arrow-right fs-2'></i><br>
                    <span class='text-danger'>{$delivery->city}, {$delivery->state}</span>
                </small>";
    }

    private function renderCarrierCell($carrier)
    {
        $name = $carrier['carrier'] ?? 'Unknown';
        $service = $carrier['serviceLevelDescription'] ?? $carrier['serviceLevel'] ?? 'Standard';
        $days = $carrier['transitDays'] ?? '?';

        $badges = '';
        if ($carrier['isPreferred'] ?? false) $badges .= '<span class="badge badge-warning ms-1">Preferred</span>';
        if ($carrier['isCarrierOfTheYear'] ?? false) $badges .= '<span class="badge badge-info ms-1">COTY</span>';

        return "<div><strong>{$name}</strong>{$badges}<br><small class='text-muted'>{$service} • {$days} days</small></div>";
    }

    private function renderAmountCell($req)
    {
        $fee = $req->markup_percent > 0 ? " + {$req->markup_percent}% fee" : '';
        return "<strong class='text-success'>$" . number_format($req->total_amount, 2) . "</strong><br>
                <small class='text-muted'>Base: $" . number_format($req->amount, 2) . "{$fee}</small>";
    }

    private function renderStatusBadge($status)
    {
        $badges = [
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];
        $color = $badges[$status] ?? 'secondary';
        $text  = ucfirst($status);
        return "<span class='badge badge-{$color}'>{$text}</span>";
    }

    private function renderActions($req)
    {
        if ($req->status !== 'pending') {
            return '<span class="text-muted">' . ucfirst($req->status) . '</span>';
        }

        return '<div class="btn-group" role="group">
                    <button class="btn btn-sm btn-success btn-approve" data-id="'.$req->id.'">Approve</button>
                    <button class="btn btn-sm btn-danger btn-reject" data-id="'.$req->id.'">Reject</button>
                </div>';
    }
}