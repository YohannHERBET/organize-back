<?php

namespace App\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Entity denormalizer
 */
class EntityDenormalizer implements DenormalizerInterface
{
    /** @var EntityManagerInterface **/
    protected $em;

    // Récupération de service
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        //we check if it's an entity and if the data is numeric
        return strpos($type, 'App\\Entity\\') === 0 && (is_numeric($data));
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->em->find($class, $data);
    }
}
