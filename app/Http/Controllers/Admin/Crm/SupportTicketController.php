<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Crm\SupportTicket;
use App\Models\Customer;
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
        $customers = Customer::select('id', 'name', 'email', 'phone_number')->latest('id')->take(100)->get();
        $cars = Car::select('id', 'car_name', 'car_model', 'car_number')->get();

        return Inertia::render('admin/crm/SupportTickets', [
            'tickets' => $tickets,
            'customers' => $customers,
            'cars' => $cars,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'car_id' => 'nullable|exists:cars,id',
            'booking_id' => 'nullable|exists:booking_car,id',
            'subject' => 'required|string|max:150',
            'category' => 'required|string|in:roadside_assistance,billing,extension,vehicle_complaint,general',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'initial_message' => 'required|string',
        ]);

        $initialMessage = $validated['initial_message'];
        unset($validated['initial_message']);

        $validated['status'] = 'open';
        $validated['assigned_admin_id'] = auth('admin')->id();

        $this->ticketService->createTicket($validated, $initialMessage);

        return redirect()->back()->with('success', 'Support ticket created successfully.');
    }

    public function show(int $id): Response
    {
        $ticket = SupportTicket::with(['customer', 'car', 'booking', 'assignedAdmin', 'messages'])
            ->findOrFail($id);

        return Inertia::render('admin/crm/TicketDetails', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(Request $request, int $id): RedirectResponse
    {
        $ticket = SupportTicket::findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $this->ticketService->addMessage($ticket, $validated['message'], 'admin');

        // If ticket was closed or resolved, re-open to in_progress upon staff reply if requested
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $this->ticketService->updateTicketStatus($ticket, 'in_progress');
        }

        return redirect()->back()->with('success', 'Reply posted successfully.');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $ticket = SupportTicket::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:open,in_progress,waiting_customer,resolved,closed',
        ]);

        $this->ticketService->updateTicketStatus($ticket, $validated['status']);

        return redirect()->back()->with('success', "Ticket status updated to {$validated['status']}.");
    }
}
