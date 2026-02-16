<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Candidate;

class CandidateStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $candidate;
    public $newStatus;
    public $changerName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Candidate $candidate, string $newStatus, string $changerName)
    {
        $this->candidate = $candidate;
        $this->newStatus = $newStatus;
        $this->changerName = $changerName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('recruitment.designation', [
            'department' => $this->candidate->department_id,
            'designation' => $this->candidate->designation_id,
            'candidate_id' => $this->candidate->id
        ]);
        
        $statusLabel = ucwords(str_replace('_', ' ', $this->newStatus));

        return (new MailMessage)
                    ->subject('Candidate Status Update: ' . $this->candidate->name)
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('The status of candidate **' . $this->candidate->name . '** has been updated.')
                    ->line('**New Status:** ' . $statusLabel)
                    ->line('**Updated By:** ' . $this->changerName)
                    ->action('View Candidate', $url)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $statusLabel = ucwords(str_replace('_', ' ', $this->newStatus));
        
        return [
            'candidate_id' => $this->candidate->id,
            'name' => $this->candidate->name,
            'designation' => $this->candidate->designation,
            'message' => 'Status updated to ' . $statusLabel . ' by ' . $this->changerName,
            'url' => route('recruitment.designation', [
                'department' => $this->candidate->department_id,
                'designation' => $this->candidate->designation_id,
                'candidate_id' => $this->candidate->id
            ])
        ];
    }
}
