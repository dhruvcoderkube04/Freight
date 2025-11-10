<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\TQLResponse;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Total Statistics
        $totalQuotes = Quote::count();
        $totalUsers = User::where('user_type', 'user')->count();
        $totalPayments = Payment::count();
        $totalTQLResponses = TQLResponse::count();

        // Success/Failed Statistics
        $successfulQuotes = Quote::where('status', 'completed')->count();
        $failedQuotes = Quote::where('status', 'failed')->count();
        $processingQuotes = Quote::where('status', 'processing')->count();
        $pendingQuotes = Quote::where('status', 'pending')->count();

        // TQL Response Statistics
        $successfulTQLResponses = TQLResponse::where('status', 'success')->count();
        $failedTQLResponses = TQLResponse::where('status', 'failed')->count();
        $pendingTQLResponses = TQLResponse::where('status', 'pending')->count();

        // Payment Statistics
        $completedPayments = Payment::where('payment_status', 'completed')->count();
        $pendingPayments = Payment::where('payment_status', 'pending')->count();
        $requiresApprovalPayments = Payment::where('payment_status', 'requires_approval')->count();

        // Calculate Percentages
        $quoteSuccessRate = $totalQuotes > 0 ? round(($successfulQuotes / $totalQuotes) * 100, 2) : 0;
        $tqlSuccessRate = $totalTQLResponses > 0 ? round(($successfulTQLResponses / $totalTQLResponses) * 100, 2) : 0;
        $paymentCompletionRate = $totalPayments > 0 ? round(($completedPayments / $totalPayments) * 100, 2) : 0;

        // Monthly Statistics
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('M Y');
            $monthlyData[] = [
                'month' => $monthKey,
                'quotes' => Quote::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'successful_quotes' => Quote::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'completed')
                    ->count(),
                'payments' => Payment::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'revenue' => Payment::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('payment_status', 'completed')
                    ->sum('total_amount'),
            ];
        }

        // Status Distribution
        $quoteStatusDistribution = [
            'completed' => $successfulQuotes,
            'failed' => $failedQuotes,
            'processing' => $processingQuotes,
            'pending' => $pendingQuotes,
        ];

        // Recent Quotes
        $recentQuotes = Quote::with(['user', 'tqlResponses', 'pickupDetail', 'deliveryDetail'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // TQL Responses
        $tqlResponses = TQLResponse::with(['quote.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Extract carrier data
        $carrierData = [];
        foreach ($tqlResponses as $response) {
            if ($response->status === 'success' && isset($response->response['content']['carrierPrices'])) {
                foreach ($response->response['content']['carrierPrices'] as $price) {
                    $carrierData[] = [
                        'quote_id' => $response->quote_id,
                        'user_name' => $response->quote->user->fullname ?? 'N/A',
                        'user_email' => $response->quote->user->email ?? 'N/A',
                        'tql_quote_id' => $response->tql_quote_id ?? 'N/A',
                        'carrier' => $price['carrier'] ?? 'N/A',
                        'carrier_scac' => $price['scac'] ?? 'N/A',
                        'service_level' => $price['serviceLevelDescription'] ?? $price['serviceLevel'] ?? 'Standard',
                        'customer_rate' => $price['customerRate'] ?? 0,
                        'transit_days' => $price['transitDays'] ?? 'N/A',
                        'is_preferred' => $price['isPreferred'] ?? false,
                        'is_carrier_of_the_year' => $price['isCarrierOfTheYear'] ?? false,
                        'status' => $response->status,
                        'created_at' => $response->created_at,
                    ];
                }
            }
        }

        // Top Carriers (your original DB query)
        $topCarriers = DB::table('tql_responses')
            ->join('quotes', 'tql_responses.quote_id', '=', 'quotes.id')
            ->select(DB::raw('JSON_EXTRACT(response, "$.content.carrierPrices[*].carrier") as carriers'))
            ->where('tql_responses.status', 'success')
            ->get()
            ->flatMap(function ($item) {
                $carriers = json_decode($item->carriers, true);
                return is_array($carriers) ? $carriers : [];
            })
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(10);

        return view('admin.dashboard', compact(
            'totalQuotes', 'totalUsers', 'totalPayments', 'totalTQLResponses',
            'successfulQuotes', 'failedQuotes', 'processingQuotes', 'pendingQuotes',
            'successfulTQLResponses', 'failedTQLResponses', 'pendingTQLResponses',
            'completedPayments', 'pendingPayments', 'requiresApprovalPayments',
            'quoteSuccessRate', 'tqlSuccessRate', 'paymentCompletionRate',
            'monthlyData', 'quoteStatusDistribution', 'recentQuotes',
            'tqlResponses', 'carrierData', 'topCarriers'
        ));
    }

    public function quotes()
    {
        $quotes = Quote::with(['user', 'tqlResponses', 'pickupDetail', 'deliveryDetail'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.quotes', compact('quotes'));
    }

    public function tqlResponses()
    {
        $responses = TQLResponse::with(['quote.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.tql-responses', compact('responses'));
    }
}