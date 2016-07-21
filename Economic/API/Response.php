<?php
namespace Lenius\Economic\API;

/**
 * @class      Economic_Response
 */
class Response
{
    /**
     * HTTP status code of request.
     *
     * @var integer
     */
    protected $status_code;

    /**
     * The headers sent during the request.
     *
     * @var string
     */
    protected $sent_headers;

    /**
     * The headers received during the request.
     *
     * @var string
     */
    protected $received_headers;

    /**
     * Response body of last request.
     *
     * @var string
     */
    protected $response_data;


    /**
     * Response constructor.
     * @param $status_code
     * @param $sent_headers
     * @param $received_headers
     * @param $response_data
     */
    public function __construct($status_code, $sent_headers, $received_headers, $response_data)
    {
        $this->status_code = $status_code;
        $this->sent_headers = $sent_headers;
        $this->received_headers = $received_headers;
        $this->response_data = $response_data;
    }


    /**
     * @param bool $keep_authorization_value
     * @return array
     */
    public function asRaw($keep_authorization_value = false)
    {
        // To avoid unintentional logging of credentials the default is to mask the value of the Authorization: header
        if ($keep_authorization_value) {
            $sent_headers = $this->sent_headers;
        } else {
            // Avoid dependency on mbstring
            $lines = explode("\n", $this->sent_headers);
            foreach ($lines as &$line) {
                if (strpos($line, 'Authorization: ') === 0) {
                    $line = 'Authorization: <hidden by default>';
                }
            }
            $sent_headers = implode("\n", $lines);
        }

        return array(
            $this->status_code,
            array(
                'sent' => $sent_headers,
                'received' => $this->received_headers,
            ),
            $this->response_data,
        );
    }

    /**
     * asArray
     *
     * Returns the response body as an array
     *
     * @return array
     */
    public function asArray()
    {
        if ($response = json_decode($this->response_data, true)) {
            return $response;
        }

        return array();
    }

    /**
     * asObject
     *
     * Returns the response body as an array
     *
     * @return \stdClass
     */
    public function asObject()
    {
        if ($response = json_decode($this->response_data)) {
            return $response;
        }

        return new \stdClass;
    }

    /**
     * httpStatus
     *
     * Returns the http_status code
     *
     * @return int
     */
    public function httpStatus()
    {
        return $this->status_code;
    }

    /**
     * isSuccess
     *
     * Checks if the http status code indicates a succesful or an error response.
     *
     * @return boolean
     */
    public function isSuccess()
    {
        if ($this->status_code > 299) {
            return false;
        }

        return true;
    }
}
