<?php
/**
 * @author: Sergey Tihonov
 */

class Response
{
    /**
     * @var string
     */
    protected $_content;

    /**
     * @var int
     */
    protected $_statusCode;

    /**
     * @var string
     */
    protected $_contentType;

    /**
     * @var array
     */
    protected $_statusTexts = array(
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' => '(Unused)',
        '307' => 'Temporary Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported'
    );

    public function __construct()
    {
        $this->_statusCode = 200;
        $this->setContent('');
        $this->setContentType('html');
    }

    /**
     * @param $content
     *
     * @return Response
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param string $type
     * @return Response
     */
    public function setContentType($type)
    {
        switch($type) {
            case 'json':
                $this->_contentType = 'application/json';
                break;
            default:
                $this->_contentType = 'text/html';
                break;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * @param int $statusCode
     * @return Response
     */
    public function setStatusCode($statusCode)
    {
        if (!in_array($statusCode, $this->_statusTexts)) {
            throw new HttpInvalidParamException(sprintf('Status code "%s" incorrect.', $statusCode));
        }

        $this->_statusCode = $statusCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * Send response.
     */
    public function send()
    {
        header('Content-type: ' . $this->_contentType . '; charset=utf-8');
        header($this->_statusTexts[$this->_statusCode]);
        echo $this->_content;
    }
}