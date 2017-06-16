<?php
/**
 * @author seka
 */
namespace Nmotion\NmotionBundle\Util;

use Symfony\Component\DependencyInjection\Container;

/**
 * Service for translating incoming text to cpcl programming language instructions
 * and sending them into configured pop3 mailbox
 */
class CPCLTranslator
{
    /** @var \Symfony\Component\DependencyInjection\Container */
    protected $container;
    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;
    /**
     * horizontal offset of entire label
     * @var int
     */
    protected $labelOffset = 0;
    /**
     * label horizontal resolution (dpi)
     * @var int
     */
    protected $labelHR = 200;
    /**
     * label vertical resolution (dpi)
     * @var int
     */
    protected $labelVR = 200;
    /**
     * maximum label height (default in dots if another is not set by UNITS command)
     * @var int
     */
    protected $labelHeight = 200;
    /**
     * quantity of labels to be printedx
     * @var int
     */
    protected $labelQty = 1;
    /**
     * print tape width in mm
     * @var float
     */
    protected $mediaWidth = 48;
    /**
     * max print tape height in mm
     * @var float
     */
    protected $mediaMaxHeight = 8191;
    /**
     * table of font heights depending on Font# and Font Size
     * @var array
     */
    protected $fontHeightSettings = [
        0 => [9, 9, 18, 18, 18, 36, 36],
        1 => [48],
        2 => [12, 24],
        4 => [47, 94, 45, 90, 180, 270, 360, 450],
        5 => [24, 48, 46, 92],
        6 => [27],
        7 => [24, 48]
    ];
    /**
     * table of font width's depending on Font# and Font Size
     * for Proportional Width Fonts maximum values is being taken (width of "W" letter)
     * @var array
     */
    protected $fontWidthSettings = [
        0 => [8, 16, 8, 16, 32, 16, 32],
        1 => [28],
        2 => [20, 20],
        4 => [39, 39, 56, 56, 56, 56, 56, 56],
        5 => [24, 24, 40, 40],
        6 => [28],
        7 => [12, 12]
    ];

    /**
     * characters that are failed to transliterated to ascii automatically
     * @var array
     */
    protected $replaceableChars = [
        'Ø' => 'O', 'ø' => 'o'
    ];

    const POSTFEED = 10;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->entityManager = $this->container->get('doctrine')->getManager();
    }

    public function getLetterHeight($font, $size)
    {
        // 4 extra dots for space between lines
        return $this->fontHeightSettings[$font][$size] + 4;
    }

    public function getLetterWidth($font, $size)
    {
        return $this->fontWidthSettings[$font][$size];
    }

    public function setLabelOffset($offset)
    {
        $this->labelOffset = $offset;
        return $this;
    }

    public function setLabelHeight($height)
    {
        $this->labelHeight = $height;
        return $this;
    }

    public function setLabelResolution($horizontal, $vertical)
    {
        $this->labelHR = $horizontal;
        $this->labelVR = $vertical;
        return $this;
    }

    public function setLabelQty($qty)
    {
        $this->labelQty = $qty;
        return $this;
    }

    public function getLineWidth()
    {
        return (int)($this->labelHR * ($this->mediaWidth / 25.4));
    }

    public function getLineSymbolQty($font, $size)
    {
        return (int)($this->getLineWidth() / $this->getLetterWidth($font, $size));
    }

    public function getLabelStart()
    {
        return '! ' . $this->labelOffset . ' ' . $this->labelHR . ' ' . $this->labelVR . ' '
            . ( $this->labelHeight + self::POSTFEED ) . ' ' . $this->labelQty . "\r\n";
    }

    public function getLabelEnd()
    {
        return "POSTFEED " . self::POSTFEED . "\r\nPRINT\r\n";
    }

    public function textLine($line, $font = 7, $size = 0, $x = 0, $y = 0)
    {
        $indent = strlen($line) - strlen(ltrim($line));
        // convert indent whitespace into coordinates
        $x = $x + $indent * 8;
        return 'TEXT ' . $font . ' ' . $size . ' ' . $x . ' ' . $y . ' ' . trim($line) . "\r\n";
    }

    public function textMultiLine($text, $font = 7, $size = 0, $height = 28, $x = 0, $y = 0)
    {
        return 'ML ' . $height . "\r\n" . $this->textLine('', $font, $size, $x, $y) . $text . "\r\nENDML\r\n";
    }

    /**
     * @param string $line
     * @param int $font
     * @param int $size
     * @return array
     */
    public function breakLine($line, $font, $size)
    {
        $lineSymbolQty = $this->getLineSymbolQty($font, $size);
        $text = wordwrap($line, $lineSymbolQty);
        $lines = [];
        foreach (explode("\n", $text) as $line) {
            $line = rtrim($line);
            if (strlen($line) > $lineSymbolQty) {
                $lines = array_merge($lines, str_split($line, $lineSymbolQty));
            } else {
                $lines[] = $line;
            }
        }
        return $lines;
    }

    /**
     * @param string $text
     * @param int $font
     * @param int $size
     * @return string
     */
    public function translate($text, $font = 7, $size = 0)
    {
        $text = $this->convertToASCII($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);
        $lineCnt = 0;
        $cmd = '';
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            foreach ($this->breakLine($line, $font, $size) as $printLine) {
                $y = $lineCnt * $this->getLetterHeight($font, $size);
                $cmd .= $this->textLine($printLine, $font, $size, 0, $y);
                $lineCnt++;
            }
        }
        $this->setLabelHeight($lineCnt * $this->getLetterHeight($font, $size) + 12);
        $cmd = $this->getLabelStart() . $cmd . $this->getLabelEnd();
        return $cmd;
    }

    /**
     * @param string $text
     * @return string
     */
    public function convertToASCII($text)
    {
        $text = str_replace(array_keys($this->replaceableChars), $this->replaceableChars, $text);
        return iconv($this->container->get('kernel')->getCharset(), 'ASCII//TRANSLIT', $text);
    }

    public function sendTranslation($text, $to)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Receipt')
            ->setFrom('no-reply@nmotion.dk')
            ->setTo($to)
            ->setBody($this->translate($text), 'text/plain', 'ascii')
            ->setEncoder(new \Swift_Mime_ContentEncoder_PlainContentEncoder('7bit'));

        return $this->container->get('mailer')->send($message);
    }
}
