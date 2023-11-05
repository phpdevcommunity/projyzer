<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class MailerService
{
    private string $from;
    public function __construct(private readonly MailerInterface $mailer, private readonly ParameterBagInterface $bag)
    {
        $this->from = $this->bag->get('mailer_from');
    }

    public function send(Email $email): void
    {
        $email->from($this->from);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            error_log($e->getMessage());
        }
    }
}
