<?php

namespace App\Repositories\Crm;

use App\Models\Crm\SupportTicket;
use App\Models\Crm\SupportTicketMessage;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupportTicketRepository extends BaseRepository implements SupportTicketRepositoryInterface
{
    public function __construct(SupportTicket $model)
    {
        parent::__construct($model);
    }

    public function getFilteredTickets(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['customer', 'car', 'booking', 'assignedAdmin'])
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

    public function getTicketWithDetails(int $id): SupportTicket
    {
        return $this->model->with(['customer', 'car', 'booking', 'assignedAdmin', 'messages'])
            ->findOrFail($id);
    }

    public function createTicket(array $data): SupportTicket
    {
        return $this->model->create($data);
    }

    public function updateTicket(SupportTicket|int $ticket, array $data): SupportTicket
    {
        if (is_int($ticket)) {
            $ticket = $this->model->findOrFail($ticket);
        }

        $ticket->update($data);

        return $ticket;
    }

    public function deleteTicket(SupportTicket|int $ticket): bool
    {
        if (is_int($ticket)) {
            $ticket = $this->model->findOrFail($ticket);
        }

        return (bool) $ticket->delete();
    }

    public function addMessage(int $ticketId, array $messageData): SupportTicketMessage
    {
        $messageData['ticket_id'] = $ticketId;

        return SupportTicketMessage::create($messageData);
    }
}
