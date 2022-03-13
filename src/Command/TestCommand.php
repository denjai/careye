<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\CarRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFieldEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFooterEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;

class TestCommand extends Command
{
    private CarRepository $carRepository;
    private NotifierInterface $notifier;
    private ChatterInterface $chatter;
    private $start;

    public function __construct(CarRepository $carRepository, NotifierInterface $notifier, ChatterInterface $chatter)
    {
        parent::__construct();
        $this->carRepository = $carRepository;
        $this->start = microtime(true);
        $this->notifier = $notifier;
        $this->chatter = $chatter;
    }

    protected function configure(): void
    {
        $this->setName('app:test');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $car = $this->carRepository->findOneBy(['remoteId' => '11621334781951056']);
        $car = $this->carRepository->findOneBy(['remoteId' => '11647103910069075']);
        $car = $this->carRepository->findOneBy(['remoteId' => '11645979306816069']);
        var_dump($car);
        var_dump(microtime(true) - $start);
        var_dump(microtime(true) - $this->start);

        $notification = (new Notification('New Invoice rekt', ['chat/discord']));
        // Send the notification to the recipient
        $this->notifier->send($notification);

        //chat message
        $chatMessage = new ChatMessage('teta ole');

        // Create Discord Embed
        $discordOptions = (new DiscordOptions())
            ->username('connor bot')
            ->addEmbed((new DiscordEmbed())
                ->color(2021216)
                ->title('New song added!')
                ->thumbnail((new DiscordMediaEmbedObject())
                    ->url('https://i.scdn.co/image/ab67616d0000b2735eb27502aa5cb1b4c9db426b'))
                ->addField((new DiscordFieldEmbedObject())
                    ->name('Track')
                    ->value('[Common Ground](https://open.spotify.com/track/36TYfGWUhIRlVjM8TxGUK6)')
                    ->inline(true)
                )
                ->addField((new DiscordFieldEmbedObject())
                    ->name('Artist')
                    ->value('Alasdair Fraser')
                    ->inline(true)
                )
                ->addField((new DiscordFieldEmbedObject())
                    ->name('Album')
                    ->value('Dawn Dance')
                    ->inline(true)
                )
                ->footer((new DiscordFooterEmbedObject())
                    ->text('Added ...')
                    ->iconUrl('https://upload.wikimedia.org/wikipedia/commons/thumb/1/19/Spotify_logo_without_text.svg/200px-Spotify_logo_without_text.svg.png')
                )
            )
        ;

        // Add the custom options to the chat message and send the message
        $chatMessage->options($discordOptions);

        $this->chatter->send($chatMessage);

        return 0;
    }
}