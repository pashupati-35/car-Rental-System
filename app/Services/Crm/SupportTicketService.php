<?php

namespace App\Services\Crm;

use App\Models\Crm\SupportTicket;
use App\Models\Crm\SupportTicketMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupportTicketService
{
    public function getTickets(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SupportTicket::with(['customer', 'car', 'booking', 'assignedAdmin'])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority']) && $filters['priority'] !== 'all') {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function createTicket(array $data, ?string $initialMessage = null): SupportTicket
    {
        if (empty($data['ticket_number'])) {
            $data['ticket_number'] = 'TCK-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        $ticket = SupportTicket::create($data);

        if ($initialMessage) {
            SupportTicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'admin',
                'sender_id' => auth('admin')->id(),
                'sender_name' => auth('admin')->user()->name ?? 'Support Agent',
                'message' => $initialMessage,
            ]);
        }

        return $ticket;
    }

    public function addMessage(SupportTicket $ticket, string $message, string $senderType = 'admin'): SupportTicketMessage
    {
        $admin = auth('admin')->user();

        $ticketMessage = SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => $senderType,
            'sender_id' => auth('admin')->id(),
            'sender_name' => $admin ? ($admin->name ?? 'Admin Staff') : 'Support Agent',
            'message' => $message,
        ]);

        $ticket->touch(); // updates updated_at

        return $ticketMessage;
    }

    public function updateTicketStatus(SupportTicket $ticket, string $status): SupportTicket
    {
        $update = ['status' => $status];
        if (in_array($status, ['resolved', 'closed']) && ! $ticket->resolved_at) {
            $update['resolved_at'] = now();
        }

        $ticket->update($update);

        return $ticket;
    }
}
