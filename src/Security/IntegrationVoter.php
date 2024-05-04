<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IntegrationVoter extends Voter
{
    public const ROLE_INTEGRATION = 'ROLE_INTEGRATION';

    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::ROLE_INTEGRATION
            && $subject instanceof Request;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        assert(assertion: $subject instanceof Request);

        $authToken = $subject->headers->get(key: 'X-Auth-token');
        $integrationToken = $this->parameterBag->get(name: 'INTEGRATION_TOKEN');

        return $authToken === $integrationToken;
    }
}
