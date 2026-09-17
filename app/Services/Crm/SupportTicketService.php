<?php

namespace App\Services\Crm;

use App\Models\Crm\SupportTicket;
use App\Models\Crm\SupportTicketMessage;
use App\Repositories\CarRepositoryInterface;
use App\Repositories\Crm\CustomerCrmRepositoryInterface;
use App\Repositories\Crm\SupportTicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupportTicketService
{
    public function __construct(
        protected SupportTicketRepositoryInterface $ticketRepository,
        protected CustomerCrmRepositoryInterface $customerCrmRepository,
        protected CarRepositoryInterface $carRepository
    ) {}

    public function getTickets(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->ticketRepository->getFilteredTickets($filters, $perPage);
    }

    public function getTicket(int $id): SupportTicket
    {
        return $this->ticketRepository->getTicketWithDetails($id);
    }

    public function getFormData(): array
    {
        return [
            'customers' => $this->customerCrmRepository->getCustomersForSelect(),
            'cars' => $this->carRepository->getCarsForSelect(),
        ];
    }

    public function createTicket(array $data, ?string $initialMessage = null): SupportTicket
    {
        if (empty($data['ticket_number'])) {
            $data['ticket_number'] = 'TCK-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        $ticket = $this->ticketRepository->createTicket($data);

        if ($initialMessage) {
            $this->ticketRepository->addMessage($ticket->id, [
                'sender_type' => 'admin',
                'sender_id' => auth('admin')->id(),
                'sender_name' => auth('admin')->user()->name ?? 'Support Agent',
                'message' => $initialMessage,
            ]);
        }

        return $ticket;
    }

    public function addMessage(SupportTicket|int $ticket, string $message, string $senderType = 'admin'): SupportTicketMessage
    {
        if (is_int($ticket)) {
            $ticket = $this->ticketRepository->findOrFail($ticket);
        }

        $admin = auth('admin')->user();

        $ticketMessage = $this->ticketRepository->addMessage($ticket->id, [
            'sender_type' => $senderType,
            'sender_id' => auth('admin')->id(),
            'sender_name' => $admin ? ($admin->name ?? 'Admin Staff') : 'Support Agent',
            'message' => $message,
        ]);

        $ticket->touch();

        return $ticketMessage;
    }

    public function updateTicketStatus(SupportTicket|int $ticket, string $status): SupportTicket
    {
        if (is_int($ticket)) {
            $ticket = $this->ticketRepository->findOrFail($ticket);
        }

        $update = ['status' => $status];
        if (in_array($status, ['resolved', 'closed']) && ! $ticket->resolved_at) {
            $update['resolved_at'] = now();
        }

        return $this->ticketRepository->updateTicket($ticket, $update);
    }

    public function replyTicket(SupportTicket|int $ticket, string $message, string $senderType = 'admin'): SupportTicketMessage
    {
        if (is_int($ticket)) {
            $ticket = $this->ticketRepository->findOrFail($ticket);
        }

        $msg = $this->addMessage($ticket, $message, $senderType);

        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $this->updateTicketStatus($ticket, 'in_progress');
        }

        return $msg;
    }
}

