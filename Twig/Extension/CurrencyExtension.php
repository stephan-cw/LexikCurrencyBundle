<?php

namespace Lexik\Bundle\CurrencyBundle\Twig\Extension;

use Lexik\Bundle\CurrencyBundle\Currency\ConverterInterface;
use Lexik\Bundle\CurrencyBundle\Currency\FormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to format and convert currencies from templates.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class CurrencyExtension extends AbstractExtension
{
    /** @var ConverterInterface */
    protected $converter;

    /** @var FormatterInterface */
    protected $formatter;

    /**
     * @param ConverterInterface $converter
     * @param FormatterInterface $formatter
     */
    public function __construct(ConverterInterface $converter, FormatterInterface $formatter)
    {
        $this->converter = $converter;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('currency_convert', [$this, 'convert']),
            new TwigFilter('currency_format', [$this, 'format']),
            new TwigFilter('currency_convert_format', [$this, 'convertAndFormat']),
        ];
    }

    /**
     * Convert the given value.
     *
     * @param float   $value
     * @param string  $targetCurrency   target currency code
     * @param boolean $round            round converted value
     * @param string  $valueCurrency    $value currency code
     * @return float
     */
    public function convert($value, $targetCurrency, $round = true, $valueCurrency = null)
    {
        return $this->converter->convert($value, $targetCurrency, $round, $valueCurrency);
    }

    /**
     * Format the given value.
     *
     * @param mixed   $value
     * @param string  $valueCurrency  $value currency code
     * @param boolean $decimal        show decimal part
     * @param boolean $symbol         show currency symbol
     * @return string
     */
    public function format($value, $valueCurrency = null, $decimal = true, $symbol = true)
    {
        if (null === $valueCurrency) {
            $valueCurrency = $this->converter->getDefaultCurrency();
        }

        return $this->formatter->format($value, $valueCurrency, $decimal, $symbol);
    }

    /**
     * Convert and format the given value.
     *
     * @param mixed   $value
     * @param string  $targetCurrency  target currency code
     * @param boolean $decimal         show decimal part
     * @param boolean $symbol          show currency symbol
     * @param string  $valueCurrency   the $value currency code
     * @return string
     */
    public function convertAndFormat($value, $targetCurrency, $decimal = true, $symbol = true, $valueCurrency = null)
    {
        $value = $this->convert($value, $targetCurrency, $decimal, $valueCurrency);

        return $this->format($value, $targetCurrency, $decimal, $symbol);
    }

    public function getName()
    {
        return 'lexik_currency.currency_extension';
    }
}
