<?php
// api/src/Sate/EtablissementProcessor.php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Etablissement;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @implements ProcessorInterface<Etablissement, Etablissement|void>
 */
final class EtablissementProcessor implements ProcessorInterface
{
    private $mailer;

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private ProcessorInterface $removeProcessor,
        MailerInterface $mailer,
    )
    {
        $this->mailer = $mailer;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Etablissement
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }
    
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        $this->sendWelcomeEmail($data);

        return $result;
    }

    private function sendWelcomeEmail(Etablissement $etablissement): void
    {
        $message = (new Email())
            ->from('no-reply@pickme.fr')
            ->to('vietanhc12@gmail.com')
            ->subject('A new etablissement has been added')
            ->text(sprintf('The etablissement #%d has been added.', $etablissement->getId()));

        $this->mailer->send($message);
    }
}