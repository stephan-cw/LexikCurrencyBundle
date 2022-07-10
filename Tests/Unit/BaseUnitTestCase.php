<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Lexik\Bundle\CurrencyBundle\Tests\Fixtures\CurrencyData;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use PHPUnit\Framework\TestCase;

/**
 * Base unit test class providing functions to create a mock entity manger, load schema and fixtures.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
abstract class BaseUnitTestCase extends TestCase
{
    /**
     * Create the database schema.
     *
     * @param EntityManagerInterface $em
     * @throws ToolsException
     */
    protected function createSchema(EntityManagerInterface $em)
    {
        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    /**
     * Load test fixtures.
     *
     * @param EntityManagerInterface $em
     */
    protected function loadFixtures(EntityManagerInterface $em)
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);

        $executor->execute(array(new CurrencyData()), false);
    }

    /**
     * EntityManager mock object together with annotation mapping driver and
     * pdo_sqlite database in memory
     *
     * @return EntityManagerInterface
     * @throws ORMException
     */
    protected function getMockSqliteEntityManager(): EntityManagerInterface
    {
        // xml driver
        $xmlDriver = new SimplifiedXmlDriver([
            __DIR__.'/../../Resources/config/doctrine' => 'Lexik\Bundle\CurrencyBundle\Entity',
        ]);

        // configuration mock
        $config = $this->createMock(Configuration::class);
        $config->expects($this->once())
            ->method('getProxyDir')
            ->will($this->returnValue(sys_get_temp_dir()));
        $config->expects($this->once())
            ->method('getProxyNamespace')
            ->will($this->returnValue('Proxy'));
        $config->expects($this->once())
            ->method('getAutoGenerateProxyClasses')
            ->will($this->returnValue(true));
        $config->expects($this->any())
            ->method('getMetadataDriverImpl')
            ->will($this->returnValue($xmlDriver));
        $config->expects($this->any())
            ->method('getClassMetadataFactoryName')
            ->will($this->returnValue(ClassMetadataFactory::class));
        $config->expects($this->any())
            ->method('getDefaultRepositoryClassName')
            ->will($this->returnValue(EntityRepository::class));
        $config->expects($this->any())
            ->method('getRepositoryFactory')
            ->will($this->returnValue(new DefaultRepositoryFactory()));
        $config->expects($this->any())
            ->method('getQuoteStrategy')
            ->will($this->returnValue(new DefaultQuoteStrategy()));

        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        return EntityManager::create($conn, $config);
    }

    /**
     * @throws ORMException
     */
    protected  function getMockDoctrine()
    {
        $em = $this->getMockSqliteEntityManager();

        $doctrine = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $doctrine->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($em));

        return $doctrine;
    }

    protected function getEntityManager()
    {
        return $this->doctrine->getManager();
    }
}