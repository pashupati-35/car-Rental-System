<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\SupportTicket\ReplySupportTicketRequest;
use App\Http\Requests\Crm\SupportTicket\StoreSupportTicketRequest;
use App\Http\Requests\Crm\SupportTicket\UpdateSupportTicketStatusRequest;
use App\Services\Crm\SupportTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupportTicketController extends Controller
{
    public function __construct(
        protected SupportTicketService $ticketService
    ) {}

    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 15);
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status', 'all'),
            'priority' => $request->input('priority', 'all'),
            'category' => $request->input('category', 'all'),
        ];

        $tickets = $this->ticketService->getTickets($filters, $perPage);
        $formData = $this->ticketService->getFormData();

        return Inertia::render('admin/crm/SupportTickets', [
            'tickets' => $tickets,
            'customers' => $formData['customers'],
            'cars' => $formData['cars'],
            'filters' => $filters,
        ]);
    }

    public function store(StoreSupportTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $initialMessage = $validated['initial_message'];
        unset($validated['initial_message']);

        $validated['status'] = 'open';
        $validated['assigned_admin_id'] = auth('admin')->id();

        $this->ticketService->createTicket($validated, $initialMessage);

        return redirect()->back()->with('success', 'Support ticket created successfully.');
    }

    public function show(int $id): Response
    {
        $ticket = $this->ticketService->getTicket($id);

        return Inertia::render('admin/crm/TicketDetails', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(ReplySupportTicketRequest $request, int $id): RedirectResponse
    {
        $this->ticketService->replyTicket($id, $request->validated('message'), 'admin');

        return redirect()->back()->with('success', 'Reply posted successfully.');
    }

    public function updateStatus(UpdateSupportTicketStatusRequest $request, int $id): RedirectResponse
    {
        $status = $request->validated('status');
        $this->ticketService->updateTicketStatus($id, $status);

        return redirect()->back()->with('success', "Ticket status updated to {$status}.");
    }
}
