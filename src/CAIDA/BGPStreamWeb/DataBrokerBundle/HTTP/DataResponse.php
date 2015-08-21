<?php

namespace CAIDA\BGPStreamWeb\DataBrokerBundle\HTTP;


use Symfony\Component\HttpFoundation\JsonResponse;

class DataResponse extends JsonResponse
{

    const TYPE_META = 'meta';
    const TYPE_DATA = 'data';

    protected $showEmpty;
    protected $encodingOptions;

    protected $type;
    protected $time;
    protected $error = null;
    protected $data;
    protected $options;

    /**
     * Constructor.
     *
     * @param mixed $type      The response type
     * @param array $headers   An array of response headers
     * @param bool  $showEmpty Include empty fields
     */
    public
    function __construct($type = null, $showEmpty = true, $headers = array())
    {
        parent::__construct(null, 200, $headers);

        // our parent tries to set this for us
        $this->data = null;

        $this->time = time();

        // init the options object
        $this->options = array();

        $this->showEmpty = $showEmpty;

        // Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
        $this->encodingOptions =
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

        $this->setType($type);
    }

    /** Sets the time that the response was created
     *
     * @param integer $time
     */
    public
    function setTime($time)
    {
        $this->time = $time;
    }

    /** Gets the time that the response was created
     *
     * @return int
     */
    public
    function getTime()
    {
        return $this->time;
    }

    /**
     * Sets the data type of the response.
     *
     * @param mixed $type
     *
     * @return JsonResponse
     *
     * @throws \InvalidArgumentException
     */
    public
    function setType($type = null)
    {
        $this->type = $type;

        return $this->update();
    }

    /**
     * Sets an error string for the response.
     *
     * @param mixed $error
     *
     * @return JsonResponse
     *
     * @throws \InvalidArgumentException
     */
    public
    function setError($error = null)
    {
        $this->error = $error;

        return $this->update();
    }

    public
    function isError()
    {
        return $this->error != null;
    }

    /**
     * Sets the data to be included in the response.
     *
     * @param mixed $data
     *
     * @return JsonResponse
     *
     * @throws \InvalidArgumentException
     */
    public
    function setData($data = array())
    {
        $this->data = $data;

        return $this->update();
    }

    /**
     * Adds a key/value pair to the options field
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return JsonResponse
     *
     * @throws \InvalidArgumentException
     */
    public
    function addOption($key, $value = null)
    {
        if(!isset($key)) {
            throw new \InvalidArgumentException('Missing Key parameter');
        }

        $this->options[$key] = $value;

        return $this->update();
    }

    /**
     * Returns options used while encoding data to JSON.
     *
     * @return int
     */
    public
    function getEncodingOptions()
    {
        return $this->encodingOptions;
    }

    /**
     * Sets options used while encoding data to JSON.
     *
     * @param int $encodingOptions
     *
     * @return JsonResponse
     */
    public
    function setEncodingOptions($encodingOptions)
    {
        $this->encodingOptions = (int)$encodingOptions;

        return $this->setData(json_decode($this->data));
    }

    /**
     * Updates the content and headers according to the fields and callback.
     *
     * @return JsonResponse
     */
    protected
    function update()
    {
        if(null !== $this->callback) {
            // Not using application/javascript for compatibility reasons with older browsers.
            $this->headers->set('Content-Type', 'text/javascript');

            return $this->setContent(sprintf('%s(%s);', $this->callback,
                                             $this->data));
        }

        // Only set the header when there is none or when it equals 'text/javascript' (from a previous update with callback)
        // in order to not overwrite a custom definition.
        if(!$this->headers->has('Content-Type') ||
           'text/javascript' === $this->headers->get('Content-Type')
        ) {
            $this->headers->set('Content-Type', 'application/json');
        }

        return $this->setContent($this->toJson());
    }

    public
    function __toString()
    {
        return $this->toJson();
    }

    private
    function toArray()
    {
        $arr = array();

        $arr['time'] = $this->time;

        if($this->type || $this->showEmpty) {
            $arr['type'] = $this->type;
        }

        if($this->error || $this->showEmpty) {
            $arr['error'] = $this->error;
        }

        if($this->options || $this->showEmpty) {
            $arr['queryParameters'] = $this->options;
        }

        if($this->data || $this->showEmpty) {
            $arr['data'] = $this->data;
        }

        return $arr;
    }

    private
    function toJson()
    {
        $json = @json_encode($this->toArray(), $this->encodingOptions);

        if(JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException($this->transformJsonError());
        }

        return $json;
    }

    private
    function transformJsonError()
    {
        if(function_exists('json_last_error_msg')) {
            return json_last_error_msg();
        }

        switch(json_last_error()) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded.';

            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch.';

            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found.';

            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON.';

            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded.';

            default:
                return 'Unknown error.';
        }
    }

}
