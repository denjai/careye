<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Car;
use App\Repository\CarHistoryRepository;
use Evp\Component\Money\Money;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFieldEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFooterEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class CarDiscordNotificationSender
{
    private ChatterInterface $chatter;
    private SiteUrlProvider $urlProvider;
    private CarHistoryRepository $carHistoryRepository;

    public function __construct(
        ChatterInterface $chatter,
        SiteUrlProvider $urlProvider,
        CarHistoryRepository $carHistoryRepository
    ) {
        $this->chatter = $chatter;
        $this->urlProvider = $urlProvider;
        $this->carHistoryRepository = $carHistoryRepository;
    }

    public function sendPriceUpdatedNotification(Car $car)
    {
        $chatMessage = new ChatMessage('');
        $oldPrice = Money::createZero('BGN')->getAsString();
        $carHistory = $this->carHistoryRepository->getPreviousPrice($car);
        if ($carHistory !== null) {
            $oldPrice = $carHistory->getPrice()->getAsString();
        }

        // Create Discord Embed
        $discordOptions = (new DiscordOptions())
            ->username('careye')
            ->addEmbed((new DiscordEmbed())
                ->color(4832766)
                ->title('Price update!')
                ->thumbnail((new DiscordMediaEmbedObject()) //TODO save car images in DB
                    ->url('https://cdn.shopify.com/s/files/1/0570/5518/3000/articles/7042763ff404370edac35734bfcbde6a.jpg?v=1646127884'))
                ->addField((new DiscordFieldEmbedObject())
                    ->name('Name')
                    ->value(
                        sprintf(
                            '[%s](%s)',
                            $car->getTitle(),
                            $this->urlProvider->getUrl((string)$car->getRemoteId())
                        )
                    )
                    ->inline(true)
                )
                ->addField((new DiscordFieldEmbedObject())
                    ->name('Old Price')
                    ->value($oldPrice)
                    ->inline(true)
                )
                ->addField((new DiscordFieldEmbedObject())
                    ->name('New Price')
                    ->value($car->getPrice()->getAsString())
                    ->inline(true)
                )
                ->footer((new DiscordFooterEmbedObject())
                    ->text('...') //TODO set car brand name and logo
                    ->iconUrl('https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/BMW.svg/2048px-BMW.svg.png')
                )
                ->url($this->urlProvider->getUrl((string)$car->getRemoteId()))
            )
        ;

        // Add the custom options to the chat message and send the message
        $chatMessage->options($discordOptions);

        $this->chatter->send($chatMessage);
    }
}