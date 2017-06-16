<?php
namespace Nmotion\NmotionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Nmotion\NmotionBundle\Entity;

/**
 * CronCommand
 */
class CronCommand extends ContainerAwareCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('nmotion:run')
            ->setDescription('NMotion cli runner')
            ->addArgument('hash', InputArgument::REQUIRED, 'hash?')
            ->addArgument('jobname', InputArgument::REQUIRED, 'job?');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input  input
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hash = $input->getArgument('hash');
        if ($hash == 'nmotion') {
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            
            $job = $input->getArgument('jobname') . 'Job';
            $output->writeln($job);
            if (method_exists($this, $job)) {
                $this->$job();
            }
        }
    }

    /**
     * get list of paid orders and send them to printer
     */
    private function printOrdersJob()
    {
        if (!$this->start(__FUNCTION__)) {
            echo 'Exit: already running ' . __FUNCTION__ . PHP_EOL;
            return;
        };
        try {
            $doctrine = $this->getContainer()->get('doctrine');
            $restaurants = $doctrine->getRepository('NmotionNmotionBundle:Restaurant')->findBy(['visible' => true]);
            $orderRepository = $doctrine->getRepository('NmotionNmotionBundle:Order');

            foreach ($restaurants as $restaurant) {
                $orders = $orderRepository->getFullOrdersWithStatus(Entity\OrderStatus::PAID, $restaurant->getId());
                $sendTo = $this->getPrinterRecipients($restaurant);

                foreach ($orders as $order) {
                    $text = $this->getContainer()->get('templating')
                        ->render('NmotionNmotionBundle:Default:receipt.txt.twig', ['order' => $order]);
                    if ($this->getContainer()->get('cpcl.translator')->sendTranslation($text, $sendTo)) {
                        $orderRepository->setOrderStatus($order, Entity\OrderStatus::SENT_TO_PRINTER);
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $this->finish(__FUNCTION__);
    }

    private function getPrinterRecipients(Entity\Restaurant $restaurant)
    {
        $sendTo = ['pop3@nmotion.pp.ciklum.com', 'nami@ciklum.com', 'yka@ciklum.com'];

        $env = $this->getContainer()->get('kernel')->getEnvironment();
        if ($env == 'prod') {
            $sendTo = ['restaurant' . $restaurant->getId() . '@printer.nmotion.dk'];
        } elseif ($env == 'demo') {
            $sendTo = ['restaurant' . $restaurant->getId() . '@printer.nmotion.dk'];
            //$sendTo = ['nmotionprinter@nmotion.pp.ciklum.com', 'pag@ciklum.com'];
        } elseif ($env == 'dev') {
            $sendTo = ['ridhish@vervesys.com'];
        }
        return $sendTo;
    }

    /**
     * check if exists file which indicates that job is running and return false if it exists
     * otherwise create file and return true
     * @param string $method
     * @return bool
     */
    private function start($method)
    {
        $fileName = $this->getContainer()->get('kernel')->getCacheDir() . '/' . $method;
        if (file_exists($fileName)) {
            return false;
        }
        touch($fileName);
        return true;
    }

    /**
     * delete file which indicates that job is running
     * @param string $method
     */
    private function finish($method)
    {
        unlink($this->getContainer()->get('kernel')->getCacheDir() . '/' . $method);
    }
}
