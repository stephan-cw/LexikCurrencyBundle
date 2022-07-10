<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Twig\Extension;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\ToolsException;
use Lexik\Bundle\CurrencyBundle\Currency\Converter;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Currency\Formatter;
use Lexik\Bundle\CurrencyBundle\Entity\Currency;
use Lexik\Bundle\CurrencyBundle\Twig\Extension\CurrencyExtension;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;

class CurrencyExtensionTest extends BaseUnitTestCase
{
    /** @var Registry */
    protected $doctrine;

    /** @var Converter */
    protected $converter;

    /** @var Formatter */
    protected $formatter;

    /**
     * @throws ORMException
     * @throws ToolsException
     */
    protected function setUp(): void
    {
        $this->doctrine = $this->getMockDoctrine();
        $em = $this->getEntityManager();

        $this->createSchema($em);
        $this->loadFixtures($em);

        $factory = new AdapterFactory($this->doctrine, 'EUR', ['EUR', 'USD'], Currency::class);

        $this->converter = new Converter($factory->createDoctrineAdapter());
        $this->formatter = new Formatter('fr');
    }

    public function testConvert()
    {
        $extension = new CurrencyExtension($this->converter, $this->formatter);

        $this->assertEquals(11.27, $extension->convert(8.666, 'USD'));
        $this->assertEquals(8.67, $extension->convert(8.666, 'EUR'));
    }

    public function testFormat()
    {
        $extension = new CurrencyExtension($this->converter, $this->formatter);

        $this->assertEquals('8,67 €', $extension->format(8.666));
        $this->assertEquals('8,67 €', $extension->format(8.666, 'EUR'));
        $this->assertEquals('8,67 $', $extension->format(8.666, 'USD'));
        $this->assertEquals('8 $', $extension->format(8.0, 'USD', false));
        $this->assertEquals('8,67', $extension->format(8.666, 'USD', false, false));
        $this->assertEquals('8', $extension->format(8.0, 'USD', false, false));
        $this->assertEquals('8 $', $extension->format(8.0, 'USD', false, true));
    }

    public function testConvertAndFormat()
    {
        $extension = new CurrencyExtension($this->converter, $this->formatter);

        $this->assertEquals('11,27 $', $extension->convertAndFormat(8.666, 'USD'));
        $this->assertEquals('11,27 $', $extension->convertAndFormat(8.666, 'USD', false));
        $this->assertEquals('11,27', $extension->convertAndFormat(8.666, 'USD', false, false));
        $this->assertEquals('8,67', $extension->convertAndFormat(8.666, 'USD', true, false, 'USD'));
        $this->assertEquals('8,00', $extension->convertAndFormat(8.0, 'USD', true, false, 'USD'));
        $this->assertEquals('8,00 $', $extension->convertAndFormat(8.0, 'USD', true, true, 'USD'));
        $this->assertEquals('8 $', $extension->convertAndFormat(8.0, 'USD', false, true, 'USD'));
    }
}
