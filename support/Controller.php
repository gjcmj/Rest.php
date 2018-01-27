<?php namespace Rest;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * Controller
 *
 * @package Rest
 * @author ky
 */
class Controller {

    /**
     * Request Object
     * @var Http\Request
     */
    protected $request;

    /**
     * Response Object
     * @var Http\Response
     */
    protected $response;

    /**
     * Construct
     */
    public function __construct(\Rest\Http\Request $request, \Rest\Http\Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * request / response
     *
     * @params Request/Response $key
     * @return mixed
     */
    public function __get($key) {
/*        return $key == 'request' ? $this->request = new Request :*/
            /*($key == 'response' ? $this->response = new Response(200, ['Content-type: application/json']) : null);*/
    }

    /**
     * output response
     *
     * @return void
     */
    public function output($code = 0) {
        // output format
        !$this->request->params('format') or $this->response->setJsonEncodeOptions($this->response->getJsonEncodeOptions() | JSON_PRETTY_PRINT);

        // response
        list($status, $headers, $body) = $this->response->finalize();

        // cors
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        // status
        header(sprintf('HTTP/%s %s', Response::HTTP_VERSION, $this->response->getMessageForCode($status)));

        // headers
        foreach ($headers as $header) {
            header($header[0], $header[1]);
        }

        // body
        $json = $this->response->getBodyJsonPacket() ? $this->response->json_packet($body, $code) : $this->response->getBody();

        echo ($callback = $this->request->params('callback')) && $this->request->isAjax() ? $callback . '(' . $json . ')' : $json;
    }
}
