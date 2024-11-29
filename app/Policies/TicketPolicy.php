<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny(User $user): bool
    {
        if (! Auth()->check()) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if (! Auth()->check()) {
            return false;
        }

        return $user->isAdmin() || $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user): bool
    {
        return Auth()->check();
    }

    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if (! Auth()->check()) {
            return false;
        }

        return $user->isAdmin() || $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        if (! Auth()->check()) {
            return false;
        }

        return $user->isAdmin() || $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can change the status of the ticket.
     */
    public function changeStatus(User $user, Ticket $ticket): bool
    {
        if (! Auth()->check()) {
            return false;
        }

        return $user->isAdmin();
    }
}
