<?php

namespace App\Controller;
use App\Entity\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
class MessageController extends AbstractController
{
    #[Route('/send-message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $content = $request->request->get('message');

        if (!$content) {
            $this->addFlash('error', 'Message cannot be empty.');
            return $this->redirectToRoute('app_index');
        }

        $message = new Message();
        $message->setContent($content);
        $message->setUser($this->getUser());
        $message->setCreatedAt(new \DateTimeImmutable());


        $em->persist($message);
        $em->flush();

        $this->addFlash('success', 'Message sent successfully!');

        return $this->redirectToRoute('app_index');
    }
}
