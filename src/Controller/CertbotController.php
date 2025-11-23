<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class CertbotController
{
    public function serve(string $filename): Response
    {
        $path = __DIR__ . '/../../certbot/www/' . $filename;
        if (!file_exists($path)) {
            return new Response('Not found', 404);
        }
        return new Response(file_get_contents($path), 200, ['Content-Type' => 'text/plain']);
    }
}
