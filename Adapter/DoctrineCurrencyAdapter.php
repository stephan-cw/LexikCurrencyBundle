<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class DoctrineCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * {@inheritdoc}
     */
    public function attachAll()
    {
        // nothing here
    }

    /**
     * Return identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'doctrine';
    }

    /**
     * @param EntityManagerInterface $manager
     */
    public function setManager(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function offsetExists($key)
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return parent::offsetExists($key);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function offsetGet($key)
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return parent::offsetGet($key);
    }

    /**
     * @return bool
     */
    private function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * @throws Exception
     */
    private function initialize()
    {
        if (!isset($this->manager)) {
            throw new RuntimeException('No ObjectManager set on DoctrineCurrencyAdapter.');
        }

        $currencies = $this->manager
            ->getRepository($this->currencyClass)
            ->findAll();

        foreach ($currencies as $currency) {
            $this[$currency->getCode()] = $currency;
        }

        $this->initialized = true;
    }
}
