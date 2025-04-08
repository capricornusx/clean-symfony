<?php

declare(strict_types=1);

namespace App\Infrastructure\Logger;

use DateTimeImmutable;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/** @psalm-suppress MissingConstructor */
class MainProcessor implements ProcessorInterface
{
    public const ELASTIC_DATETIME_FORMAT = 'Y-m-d\TH:i:s.u\Z';

    /** @psalm-suppress PropertyNotSetInConstructor */
    protected array $record;

    protected LogRecord $originalRecord;

    public function __invoke(LogRecord $record): LogRecord
    {
        $this->originalRecord = $record;
        $this->record = $record->toArray();
        return $this->processRecord();
    }

    public function processRecord(): LogRecord
    {
        $this->withDateTime();
        $this->withMemoryUsage();
        $this->withRequestId();
        $this->dropUnwantedFields();

        return self::buildMessage($this->originalRecord, $this->record);
    }

    protected static function buildMessage(LogRecord $orig, array $new): LogRecord
    {
        return new LogRecord(
            datetime: $orig->datetime,
            channel: $orig->channel,
            level: $orig->level,
            message: $orig->message,
            context: $new['context'],
            extra: $orig->extra,
            formatted: $orig->formatted
        );

    }

    /**
     * Не пишем в лог пустые поля (в том числе пустые массивы)
     */
    protected function dropEmptyFields(): void
    {
        foreach ($this->record['context'] as $key => $value) {
            if ($value === '') {
                unset($this->record['context'][$key]);
            }
        }
    }

    /**
     * Бесполезные поля, нет смысла сохранять и отправлять их в Elasticsearch
     */
    protected function dropUnwantedFields(): void
    {
        unset(
            $this->record['context']['comment'],
            $this->record['context']['request']['security'],
        );
    }

    protected function withDateTime(): void
    {
        /** @var DateTimeImmutable $monologTime */
        $monologTime = $this->record['datetime'];
        $this->record['context']['@timestamp'] = $monologTime->format(self::ELASTIC_DATETIME_FORMAT);
    }

    protected function withMemoryUsage(): void
    {
        $this->record['context']['memory_usage'] = memory_get_usage(false);
    }

    /**
     * Логируются только ВХОДЯЩИЕ запросы от клиентов, не CLI и что-то внутреннее.
     * Добавляем request_id, который передаём нам nginx
     * Это позволяет связать запрос к серверу с сообщением в логах (Elasticsearch + Kibana)
     *
     * Необходимы настройки nginx /etc/nginx/fastcgi_params:
     * fastcgi_param  X_REQUEST_ID       $request_id;
     */
    protected function withRequestId(): void
    {
        if (array_key_exists('X_REQUEST_ID', $_SERVER) && $_SERVER['X_REQUEST_ID'] !== '') {
            $this->record['context']['requestId'] = $_SERVER['X_REQUEST_ID'] ?? '';
        }
    }

}