<?php

namespace Juste\Facades\Mails;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;


class JusteMailer
{
    private $mailer;
    private array $config;

    private $body;

    public function __construct()
    {
        $this->config = require BASE_URL . '/config/mail.php';

        $dsn = sprintf(
            '%s://%s:%s@%s:%s',
            $this->config['mailer']['transport'],
            $this->config['mailer']['username'],
            $this->config['mailer']['password'],
            $this->config['mailer']['host'],
            $this->config['mailer']['port']
        );

        $transport = Transport::fromDsn($dsn);
        $this->mailer = new Mailer($transport);
    }

    public function view(string $path, array $data = [], bool $hasSpaces = true): JusteMailer
    {
        $body = file_get_contents(VIEW_PATH . '/' . $path . '.php');

        foreach ($data as $key => $value) {
            $searchPattern = $hasSpaces ? '/{{\s*\$' . $key . '\s*}}/' : '/{{\$' . $key . '}}/';
            $body = preg_replace($searchPattern, $value, $body);
        }

        $this->body = $body;

        return $this;
    }


    public function sendEmail(array $mail): bool
    {
        $senderName = $this->config['mailer']['from_name'];
        $from = $this->config['mailer']['from_addr'];

        $email = (new Email())
            ->from(new Address($from, $senderName))
            ->to($mail['to'])
            ->subject($mail['subject'])
            ->html($this->body);

        return (bool) $this->mailer->send($email);
    }
}
