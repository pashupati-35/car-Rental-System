<?php

namespace App\Repositories\Crm;

use App\Models\Crm\SupportTicket;
use App\Models\Crm\SupportTicketMessage;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupportTicketRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredTickets(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getTicketWithDetails(int $id): SupportTicket;

    public function createTicket(array $data): SupportTicket;

    public function updateTicket(SupportTicket|int $ticket, array $data): SupportTicket;

    public function deleteTicket(SupportTicket|int $ticket): bool;

    public function addMessage(int $ticketId, array $messageData): SupportTicketMessage;
}
