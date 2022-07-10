<?php

namespace Lexik\Bundle\CurrencyBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterCollector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class ImportCurrencyCommand extends Command
{
    /** @var ManagerRegistry */
    private $managerRegistry;

    /** @var AdapterCollector */
    private $adapterCollector;

    /** @var string */
    private $currencyClass;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param AdapterCollector $adapterCollector
     * @param string $currencyClass
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        AdapterCollector $adapterCollector,
        string $currencyClass
    ) {
        parent::__construct();
        $this->managerRegistry = $managerRegistry;
        $this->adapterCollector = $adapterCollector;
        $this->currencyClass = $currencyClass;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lexik:currency:import')
            ->setDescription('Import currency rate')
            ->addArgument('adapter', InputArgument::REQUIRED, 'Adapter to import in database')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adapter = $this->adapterCollector
            ->get($input->getArgument('adapter'));
        $adapter->attachAll();

        // Persist currencies
        $entityManagerName = $input->getOption('em');
        $em = $this->managerRegistry->getManager($entityManagerName);

        $repository = $em->getRepository($this->currencyClass);

        foreach ($adapter as $value) {
            // Check if already exist
            $currency = $repository->findOneBy([
                'code' => $value->getCode(),
            ]);

            if (!$currency) {
                $currency = $value;
                $em->persist($currency);

                $output->writeln(sprintf('<comment>Add: %s = %s</comment>', $currency->getCode(), $currency->getRate()));
            } else {
                $currency->setRate($value->getRate());

                $output->writeln(sprintf('<comment>Update: %s = %s</comment>', $currency->getCode(), $currency->getRate()));
            }
        }

        $em->flush();
    }
}
