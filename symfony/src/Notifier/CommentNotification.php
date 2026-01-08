<?php
namespace App\Notifier;

use App\Entity\Recipe;
use App\Entity\Comment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Mime\Address;

final class CommentNotification extends Notification implements EmailNotificationInterface {

    public function __construct(
        private readonly Comment $comment,
        private readonly Recipe $recipe,
    )
    {
        parent::__construct('Nowy komentarz do przepisu: '.$this->recipe->getTitle());
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@przepisy-app.com', 'Przepisy Bot'))
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->htmlTemplate('emails/comment_notification.html.twig')
            ->context([
                'recipe' => $this->recipe,
                'comment' => $this->comment,
            ]);
        return new EmailMessage($email);
    }
}
