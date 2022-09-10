<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use ArrayIterator;
use InvalidArgumentException;
use Lexik\Bundle\CurrencyBundle\Entity\Currency;

/**
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
abstract class AbstractCurrencyAdapter extends ArrayIterator
{
    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * @var array
     */
    protected $managedCurrencies = [];

    /**
     * @var string
     */
    protected $currencyClass;

    /**
     * Set default currency
     *
     * @param string $defaultCurrency
     */
    public function setDefaultCurrency($defaultCurrency)
    {
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * Get default currency
     *
     * @return string
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * Set managed currencies
     *
     * @param array $currencies
     */
    public function setManagedCurrencies($currencies)
    {
        $this->managedCurrencies = $currencies;
    }

    /**
     * Get managed currencies
     *
     * @return array
     */
    public function getManagedCurrencies()
    {
        return $this->managedCurrencies;
    }

    /**
     * Set currency class
     *
     * @param string $currencyClass
     */
    public function setCurrencyClass($currencyClass)
    {
        $this->currencyClass = $currencyClass;
    }

    /**
     * Get currency class
     *
     * @return string
     */
    public function getCurrencyClass()
    {
        return $this->currencyClass;
    }

    /**
     * Set object
     *
     * @param mixed $key
     * @param Currency $value
     */
    public function offsetSet($key, $value)
    {
        if (!$value instanceof $this->currencyClass) {
            throw new InvalidArgumentException(sprintf('$value must be an instance of Currency, instance of "%s" given', get_class($value)));
        }

        parent::offsetSet($key, $value);
    }

    /**
     * Append a value
     *
     * @param Currency $value
     */
    public function append($value)
    {
        if (!$value instanceof $this->currencyClass) {
            throw new InvalidArgumentException(sprintf('$value must be an instance of Currency, instance of "%s" given', get_class($value)));
        }

        parent::append($value);
    }

    /**
     * Convert all
     *
     * @param mixed $rate
     */
    protected function convertAll($rate)
    {
        foreach ($this as $currency) {
            $currency->convert($rate);
        }
    }

    /**
     * This method is used by the constructor
     * to attach all currencies.
     */
    abstract public function attachAll();

    /**
     * Get identifier value for the adapter must be unique
     * for all the project
     *
     * @return string
     */
    abstract protected function getIdentifier();
}
