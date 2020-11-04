<?php declare(strict_types=1);

namespace Boneng\Codex;

use Boneng\Model\Request;

interface Decoder {
    public const ACCEPTED_TYPE_HTML = 2;
    public const ACCEPTED_TYPE_JSON = 1;

    public const METHOD_POST = 'POST';
    public const METHOD_GET = 'GET';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_OTHER = 'OTHER';

    public function decode() : Request;
}