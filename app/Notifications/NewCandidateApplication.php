<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Candidate;

class NewCandidateApplication extends Notification implements ShouldQueue
{
    use Queueable;

    public $candidate;

    /**
     * Create a new notification instance.
     */
    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // 'mail' is excluded here; email is sent separately to careers@loopsintegrated.com
        // in each controller using Notification::route('mail', ...) so only that fixed
        // address receives the email, regardless of which users exist in the database.
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     * This is used when sending via Notification::route('mail', ...) to the HR email address.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('recruitment.designation', [
            'department' => $this->candidate->department_id,
            'designation' => $this->candidate->designation_id,
            'candidate_id' => $this->candidate->id
        ]);

        return (new MailMessage)
                    ->subject('New Candidate Application: ' . $this->candidate->name)
                    ->greeting('Hello HR Team,')
                    ->line('A new candidate has applied for the position of **' . $this->candidate->designation . '**.')
                    ->line('**Name:** ' . $this->candidate->name)
                    ->line('**Email:** ' . $this->candidate->email)
                    ->action('View Application', $url)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'candidate_id' => $this->candidate->id,
            'name' => $this->candidate->name,
            'designation' => $this->candidate->designation,
            'message' => 'New candidate application: ' . $this->candidate->name,
            'url' => route('recruitment.designation', [
                'department' => $this->candidate->department_id,
                'designation' => $this->candidate->designation_id,
                'candidate_id' => $this->candidate->id
            ], false)
        ];
    }
}
