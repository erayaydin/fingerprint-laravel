<?php

namespace ErayAydin\Fingerprint;

use DateTimeImmutable;
use ErayAydin\Fingerprint\Enums\BotDResult;
use Fingerprint\ServerAPI\Model\BotdBot;
use Fingerprint\ServerAPI\Model\BotdBotResult;
use Fingerprint\ServerAPI\Model\EventsGetResponse;
use Fingerprint\ServerAPI\Model\Identification as FingerprintIdentification;

/**
 * Class Event
 *
 * Represents an event with identification, bot detection, Tor, and VPN status.
 */
class Event
{
    /**
     * @param  Identification  $identification  The identification data of the event.
     * @param  BotDResult|null  $botD  The bot detection result.
     * @param  bool|null  $isTor  Indicates if the event is from the Tor network.
     * @param  bool|null  $isVPN  Indicates if the event is from a VPN network.
     */
    public function __construct(
        public Identification $identification,
        public ?BotDResult $botD,
        public ?bool $isTor,
        public ?bool $isVPN,
    ) {}

    /**
     * Creates an Event instance from an EventResponse model.
     *
     * @param  EventsGetResponse  $model  The event response model.
     * @return self The created Event instance.
     */
    public static function createFromEventResponse(EventsGetResponse $model): self
    {
        $products = $model->getProducts();

        return new self(
            self::createIdentification($products->getIdentification()->getData()),
            self::getBotDResult($products->getBotd()?->getData()?->getBot()),
            $products->getTor()?->getData()?->getResult(),
            $products->getVpn()->getData()->getResult(),
        );
    }

    /**
     * Creates an Identification instance from a ProductsResponseIdentificationData model.
     *
     * @param  FingerprintIdentification  $model  The identification data model.
     * @return Identification The created Identification instance.
     */
    private static function createIdentification(FingerprintIdentification $model): Identification
    {
        $timestamp = (int) ($model->getTimestamp() / 1000);

        return new Identification(
            $model->getRequestId(),
            $model->getVisitorId(),
            $model->getIncognito(),
            DateTimeImmutable::createFromMutable($model->getTime()),
            DateTimeImmutable::createFromFormat('U', "$timestamp"),
            $model->getUrl(),
            $model->getIp(),
            $model->getConfidence()->getScore(),
        );
    }

    /**
     * Gets the BotDResult from a BotdBot model.
     *
     * @param  BotdBot|null  $botdBot  The bot detection result model.
     * @return BotDResult The corresponding BotDResult or null if not exists in result model.
     */
    private static function getBotDResult(?BotdBot $botdBot): BotDResult
    {
        return match ($botdBot?->getResult()) {
            BotdBotResult::NOT_DETECTED => BotDResult::NotDetected,
            BotdBotResult::GOOD => BotDResult::Good,
            BotdBotResult::BAD => BotDResult::Bad,
            default => null,
        };
    }
}
