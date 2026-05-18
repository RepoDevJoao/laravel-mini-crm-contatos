<?php

namespace App\Infrastructure\Persistence\Observers;

use App\Infrastructure\Persistence\Models\Contact;

class ContactObserver
{
    public function saving(Contact $contact): void
    {
        if ($contact->isDirty('phone') || !$contact->exists) {
            $contact->phone = preg_replace('/\D/', '', $contact->phone);
        }
    }
}