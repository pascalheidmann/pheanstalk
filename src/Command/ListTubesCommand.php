<?php

declare(strict_types=1);

namespace Pheanstalk\Command;

use Pheanstalk\Contract\CommandInterface;
use Pheanstalk\Exception\MalformedResponseException;
use Pheanstalk\Exception\UnsupportedResponseException;
use Pheanstalk\Parser\YamlListParser;
use Pheanstalk\Values\RawResponse;
use Pheanstalk\Values\ResponseType;
use Pheanstalk\Values\TubeList;
use Pheanstalk\Values\TubeName;

/**
 * The 'list-tubes' command.
 *
 * List all existing tubes.
 * @internal
 */
final class ListTubesCommand implements CommandInterface
{
    public function getCommandLine(): string
    {
        return "list-tubes";
    }

    public function interpret(RawResponse $response): TubeList
    {
        if ($response->type === ResponseType::Ok && is_string($response->data)) {
            return new TubeList(...array_map(
                fn (string $rawName): TubeName => new TubeName($rawName),
                (new YamlListParser())->parse($response->data)
            ));
        }
        throw match ($response->type) {
            ResponseType::Ok => MalformedResponseException::expectedData(),
            default => new UnsupportedResponseException($response->type)
        };
    }
}
