<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    // mail réinitialisation mot de passe 
    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void
    {
        //On crée le mail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->embedFromPath('https://res.cloudinary.com/aladdineshoping/image/upload/v1761840339/logo_yr2ueo.png', 'logo', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422924/h2qdkkms0lmucs44rc6s.png', 'facebook', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422987/jtxpgab3dhykivmpw6y5.png', 'instagram', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423044/gchpepwboglj5oyudqr8.png', 'twitter', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423097/afyzbcguyosyjfbhpn8v.png', 'Linkidin', 'image/png')
            //->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423192/irpvbr5wdjsewm0i5jgu.png', 'Tick', 'image/png')
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        // On envoie le mail
        $this->mailer->send($email);
    }


     // mail Bienvenue
     public function sendBienvenue(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void
    {
        //On crée le mail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->embedFromPath('https://res.cloudinary.com/aladdineshoping/image/upload/v1761840339/logo_yr2ueo.png', 'logo', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422924/h2qdkkms0lmucs44rc6s.png', 'facebook', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422987/jtxpgab3dhykivmpw6y5.png', 'instagram', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423044/gchpepwboglj5oyudqr8.png', 'twitter', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423097/afyzbcguyosyjfbhpn8v.png', 'Linkidin', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423192/irpvbr5wdjsewm0i5jgu.png', 'Tick', 'image/png')
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        // On envoie le mail
        $this->mailer->send($email);
    }


     // mail Bienvenue vendeur
     public function sendBienvenueVendeur(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void
    {
        //On crée le mail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->embedFromPath('https://res.cloudinary.com/aladdineshoping/image/upload/v1686661175/loggo_ea6ts0.png', 'logo10', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422924/h2qdkkms0lmucs44rc6s.png', 'facebook', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422987/jtxpgab3dhykivmpw6y5.png', 'instagram', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423044/gchpepwboglj5oyudqr8.png', 'twitter', 'image/png')
            ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423097/afyzbcguyosyjfbhpn8v.png', 'Linkidin', 'image/png')
           // ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423192/irpvbr5wdjsewm0i5jgu.png', 'Tick', 'image/png')
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        // On envoie le mail
        $this->mailer->send($email);
    }

  // mail Newsletter 
  public function sendnews(
    string $from,
    string $to,
    string $subject,
    string $template,
    array $context
): void
{
    //On crée le mail
    $email = (new TemplatedEmail())
        ->from($from)
        ->to($to)
        ->subject($subject)
        ->embedFromPath('https://res.cloudinary.com/aladdineshoping/image/upload/v1686661175/loggo_ea6ts0.png', 'logo10', 'image/png')
        ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422924/h2qdkkms0lmucs44rc6s.png', 'facebook', 'image/png')
        ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681422987/jtxpgab3dhykivmpw6y5.png', 'instagram', 'image/png')
        ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423044/gchpepwboglj5oyudqr8.png', 'twitter', 'image/png')
        ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423097/afyzbcguyosyjfbhpn8v.png', 'Linkidin', 'image/png')
       // ->embedFromPath('https://res.cloudinary.com/b-ja/image/upload/v1681423192/irpvbr5wdjsewm0i5jgu.png', 'Tick', 'image/png')
        ->htmlTemplate("emails/$template.html.twig")
        ->context($context);

    // On envoie le mail
    $this->mailer->send($email);
}
    
}