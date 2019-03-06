<?php



declare(strict_types=1);

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Bridge\Elasticsearch\Serializer\ItemNormalizer as ElasticsearchItemNormalizer;
use ApiPlatform\Core\Exception\InvalidIdentifierException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizes an UUID string to an instance of Ramsey\Uuid.
 *
 * @author Antoine Bluchet <soyuka@gmail.com>
 */
final class UuidNormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (null === $data) {
            return null;
        }

        try {
            return Uuid::fromString($data);
        } catch (InvalidUuidStringException $e) {
            throw new InvalidIdentifierException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, UuidInterface::class, true) && (ElasticsearchItemNormalizer::FORMAT === $format || 'json' === $format);
    }
}
