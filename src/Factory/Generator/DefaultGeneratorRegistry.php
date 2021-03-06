<?php

/*
 * This file is part of the puli/manager package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Manager\Factory\Generator;

use Puli\Manager\Api\Factory\Generator\GeneratorRegistry;
use RuntimeException;

/**
 * Puli's default {@link GeneratorRegistry}.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class DefaultGeneratorRegistry implements GeneratorRegistry
{
    /**
     * @var string[][]
     */
    private static $classNames = array(
        self::REPOSITORY => array(
            'filesystem' => 'Puli\Manager\Factory\Generator\Repository\FilesystemRepositoryGenerator',
            'path-mapping' => 'Puli\Manager\Factory\Generator\Repository\PathMappingRepositoryGenerator',
        ),
        self::DISCOVERY => array(
            'key-value-store' => 'Puli\Manager\Factory\Generator\Discovery\KeyValueStoreDiscoveryGenerator',
        ),
        self::KEY_VALUE_STORE => array(
            null => 'Puli\Manager\Factory\Generator\KeyValueStore\NullStoreGenerator',
            'null' => 'Puli\Manager\Factory\Generator\KeyValueStore\NullStoreGenerator',
            'array' => 'Puli\Manager\Factory\Generator\KeyValueStore\ArrayStoreGenerator',
            'json-file' => 'Puli\Manager\Factory\Generator\KeyValueStore\JsonFileStoreGenerator',
            'php-redis' => 'Puli\Manager\Factory\Generator\KeyValueStore\PhpRedisStoreGenerator',
            'predis' => 'Puli\Manager\Factory\Generator\KeyValueStore\PredisStoreGenerator',
            'riak' => 'Puli\Manager\Factory\Generator\KeyValueStore\RiakStoreGenerator',
        ),
    );

    /**
     * {@inheritdoc}
     */
    public function getServiceGenerator($type, $name)
    {
        if (!isset(self::$classNames[$type])) {
            throw new RuntimeException(sprintf(
                'The service type "%s" is not supported.',
                $type
            ));
        }

        if (!isset(self::$classNames[$type][$name])) {
            throw new RuntimeException(sprintf(
                'The service "%s" of type "%s" does not exist.',
                $name,
                $type
            ));
        }

        $className = self::$classNames[$type][$name];

        return new $className();
    }
}
