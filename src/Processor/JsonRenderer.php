<?php declare(strict_types=1);

namespace Boneng\Processor;

use Boneng\Model\Response;
use Boneng\Model\Result;

class JsonRenderer implements Renderer {
    public function render(Result $result) : Response {
        $body = $this->processBody($result->getData());
        return new Response($result->getStatus(),
            [
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($body)
            ], $body);
    }

    private function processBody(array $data) {
        if (empty($data)) {
            return '';
        } else {
            return \json_encode($data);
        }
    }
}