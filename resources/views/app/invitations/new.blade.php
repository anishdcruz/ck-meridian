@component('app.mail.layouts')
Hi!

{{ $invitation->team->owner->name }} has invited you to join their team! If you do not already have an account,
you may click the following link to get started:

@component('mail::button', ['url' => route('app.invitation.register',['token' => $invitation->token])])
Join Now!
@endcomponent


See you soon!

{{ $invitation->team->name }}
@endcomponent