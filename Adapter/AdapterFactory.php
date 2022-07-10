<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * This class is used to create DoctrineCurrencyAdapter
 *
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class AdapterFactory
{
    /**
     * @var EntityManager
     */
    protected $doctrine;

    /**
     * @var array
     */
    private $currencies;

    /**
     * @var string
     */
    private $currencyClass;

    /**
     * @param Registry $doctrine
     * @param $defaultCurrency
     * @param $availableCurrencies
     * @param $currencyClass
     */
    public function __construct(Registry $doctrine, $defaultCurrency, $availableCurrencies, $currencyClass)
    {
        $this->doctrine = $doctrine;

        $this->currencies = [];
        $this->currencies['default'] = $defaultCurrency;
        $this->currencies['managed'] = $availableCurrencies;
        $this->currencyClass = $currencyClass;
    }

    /**
     * Create an adapter from the given class.
     *
     * @param string $adapterClass
     * @return AbstractCurrencyAdapter
     */
    public function create(string $adapterClass): AbstractCurrencyAdapter
    {
        $adapter = new $adapterClass();
        $adapter->setDefaultCurrency($this->currencies['default']);
        $adapter->setManagedCurrencies($this->currencies['managed']);
        $adapter->setCurrencyClass($this->currencyClass);

        return $adapter;
    }

    /**
     * Create a DoctrineCurrencyAdapter.
     *
     * @param string|null $adapterClass
     * @param string|null $entityManagerName
     * @return DoctrineCurrencyAdapter
     */
    public function createDoctrineAdapter(string $adapterClass = null, string $entityManagerName = null): DoctrineCurrencyAdapter
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter';
        }

        /** @var DoctrineCurrencyAdapter $adapter */
        $adapter = $this->create($adapterClass);

        $em = $this->doctrine->getManager($entityManagerName);
        $adapter->setManager($em);

        return $adapter;
    }

    /**
     * Create an EcbCurrencyAdapter.
     *
     * @param string|null $adapterClass
     * @return EcbCurrencyAdapter
     */
    public function createEcbAdapter(string $adapterClass = null): EcbCurrencyAdapter
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\EcbCurrencyAdapter';
        }

        /** @var EcbCurrencyAdapter $adapter */
        $adapter = $this->create($adapterClass);

        return $adapter;
    }

    /**
     * Create an OerCurrencyAdapter.
     *
     * @param string|null $adapterClass
     * @return OerCurrencyAdapter
     */
    public function createOerAdapter(string $adapterClass = null): OerCurrencyAdapter
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\OerCurrencyAdapter';
        }

        /** @var OerCurrencyAdapter $adapter */
        $adapter = $this->create($adapterClass);

        return $adapter;
    }

    /**
     * Create an YahooCurrencyAdapter.
     *
     * @param string|null $adapterClass
     * @return YahooCurrencyAdapter
     */
    public function createYahooAdapter(string $adapterClass = null): YahooCurrencyAdapter
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\YahooCurrencyAdapter';
        }

        /** @var YahooCurrencyAdapter $adapter */
        $adapter = $this->create($adapterClass);

        return $adapter;
    }
}