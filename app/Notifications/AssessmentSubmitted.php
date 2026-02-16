<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CandidateAssessment;

class AssessmentSubmitted extends Notification
{
    use Queueable;

    public $assessment;

    /**
     * Create a new notification instance.
     */
    public function __construct(CandidateAssessment $assessment)
    {
        $this->assessment = $assessment;
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
            'department' => $this->assessment->candidate->department_id,
            'designation' => $this->assessment->candidate->designation_id,
            'candidate_id' => $this->assessment->candidate->id
        ]);

        return (new MailMessage)
                    ->subject('Assessment Submitted: ' . $this->assessment->candidate->name)
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line($this->assessment->candidate->name . ' has submitted their assessment test.')
                    ->line('**Test:** ' . $this->assessment->test->name)
                    ->line('**Submission Link:** ' . $this->assessment->submission_link)
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
        return [
            'candidate_id' => $this->assessment->candidate->id,
            'candidate_name' => $this->assessment->candidate->name,
            'test_name' => $this->assessment->test->name,
            'message' => $this->assessment->candidate->name . ' submitted assessment test',
            'submission_link' => $this->assessment->submission_link,
            'url' => route('recruitment.designation', [
                'department' => $this->assessment->candidate->department_id,
                'designation' => $this->assessment->candidate->designation_id,
                'candidate_id' => $this->assessment->candidate->id
            ])
        ];
    }
}
