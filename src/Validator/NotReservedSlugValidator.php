<?php

namespace App\Validator;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Une page admin ne doit pas prendre l'adresse d'une route existante (/tcc, /backoffice, /login...) :
 * la route du site passerait devant et la page serait inaccessible.
 */
class NotReservedSlugValidator extends ConstraintValidator
{
    // Dossiers servis directement par le serveur web
    private const RESERVED = ['assets', 'bundles', 'uploads', 'index.php'];

    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotReservedSlug) {
            throw new UnexpectedTypeException($constraint, NotReservedSlug::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (in_array($value, $this->getReservedSlugs(), true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ slug }}', $value)
                ->addViolation();
        }
    }

    /**
     * @return string[] premier segment fixe de chaque route ("/backoffice/..." => "backoffice")
     */
    private function getReservedSlugs(): array
    {
        $slugs = self::RESERVED;

        foreach ($this->router->getRouteCollection() as $name => $route) {
            if ($name === 'app_page') {
                continue;
            }

            $segment = explode('/', trim($route->getPath(), '/'))[0];
            if ($segment !== '' && !str_contains($segment, '{')) {
                $slugs[] = $segment;
            }
        }

        return array_unique($slugs);
    }
}
