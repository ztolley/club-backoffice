<?php

namespace App\Observers;

use App\Models\Contact;

class ContactObserver
{
    // When deleting a contact, if the contact is associated with other players, don't delete it just
    // detatch it from that player. If the contact is not associated with any other players, delete it.
    public function deleting(Contact $contact)
    {
        if ($contact->players()->count() > 1) {
            // Just detach the contact from the current player
            $contact->players()->detach();
            return false; // Prevent the contact from being deleted
        }
    }
}
