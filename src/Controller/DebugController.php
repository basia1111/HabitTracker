<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebugController
{
    #[Route('/debug/scheme', name: 'debug_scheme')]
    public function debugScheme(Request $request): Response
    {
        return new Response('Detected Scheme: ' . $request->getScheme());
    }
}

