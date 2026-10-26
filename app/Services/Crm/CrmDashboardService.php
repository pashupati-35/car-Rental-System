<?php

namespace App\Services\Crm;

use App\Models\Crm\CorporateAccount;
use App\Models\Crm\CustomerInteraction;
use App\Models\Crm\Deal;
use App\Models\Crm\Lead;
use App\Models\Crm\Quotation;
use App\Models\Crm\SupportTicket;
use Illuminate\Support\Facades\DB;

class CrmDashboardService
{
    public function getDashboardMetrics(): array
    {
        $totalLeads = Lead::count();
        $newLeadsThisMonth = Lead::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $convertedLeads = Lead::where('status', 'converted')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        $totalPipelineValue = Deal::whereNotIn('stage', ['lost'])->sum('value');
        $wonDealsValue = Deal::where('stage', 'won')->sum('value');

        $openTickets = SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_customer'])->count();
        $urgentTickets = SupportTicket::whereIn('status', ['open', 'in_progress'])->where('priority', 'urgent')->count();

        $activeCorporateAccounts = CorporateAccount::where('status', 'active')->count();

        // Pipeline stage breakdowns
        $dealsByStage = Deal::select('stage', DB::raw('count(*) as count'), DB::raw('sum(value) as total_value'))
            ->groupBy('stage')
            ->get()
            ->keyBy('stage')
            ->toArray();

        // Lead sources breakdown
        $leadsBySource = Lead::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        // Recent leads
        $recentLeads = Lead::with('interestedCar')
            ->latest('id')
            ->take(5)
            ->get();

        // Recent customer interactions
        $recentInteractions = CustomerInteraction::with(['customer', 'admin'])
            ->latest('interaction_date')
            ->take(6)
            ->get();

        // Recent quotes
        $recentQuotations = Quotation::with(['customer', 'lead', 'car'])
            ->latest('id')
            ->take(5)
            ->get();

        return [
            'metrics' => [
                'total_leads' => $totalLeads,
                'new_leads_this_month' => $newLeadsThisMonth,
                'conversion_rate' => $conversionRate,
                'total_pipeline_value' => (float) $totalPipelineValue,
                'won_deals_value' => (float) $wonDealsValue,
                'open_tickets' => $openTickets,
                'urgent_tickets' => $urgentTickets,
                'active_corporate_accounts' => $activeCorporateAccounts,
            ],
            'deals_by_stage' => $dealsByStage,
            'leads_by_source' => $leadsBySource,
            'recent_leads' => $recentLeads,
            'recent_interactions' => $recentInteractions,
            'recent_quotations' => $recentQuotations,
        ];
    }
}
