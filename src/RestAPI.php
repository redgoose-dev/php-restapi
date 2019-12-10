<?php
namespace redgoose;
use Exception;

/**
 * RestAPI
 * @package redgoose
 *
 * API 통신 인터페이스 클래스
 */

class RestAPI {

  /**
   * @var string $url prefix url
   */
  protected $url = '';

  /**
   * @var array $headers request headers
   */
  protected $headers = [];

  /**
   * @var int $timeout The number of seconds to wait while trying to connect.
   */
  protected $timeout = 10;

  /**
   * @var boolean $debug out request header
   */
  protected $debug = false;

  /**
   * @var string $outputType response type (json|text)
   */
  protected $outputType = 'text';


  /**
   * create instance
   *
   * @param object $pref
   * @throws Exception
   */
  public function __construct($pref=null)
  {
    try
    {
      // check support
      if (!self::checkSupportCurl())
      {
        throw new Exception('Not support `curl`');
      }
      // merge preference
      $this->update($pref);
    }
    catch (Exception $e)
    {
      throw new Exception($e);
    }
  }


  /**
   * PRIVATE AREA
   */

  /**
   * check support curl
   *
   * @return boolean
   */
  private static function checkSupportCurl()
  {
    return (function_exists('curl_init'));
  }

  /**
   * set method
   *
   * @param mixed $curl
   * @param string $method (get,post,put,patch,delete)
   */
  private static function setMethod($curl, $method)
  {
    switch ($method)
    {
      case 'get':
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        break;
      case 'post':
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        break;
      case 'put':
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        break;
      case 'patch':
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        break;
      case 'delete':
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        break;
      default:
        break;
    }
  }

  /**
   * get message
   *
   * @param int $code
   * @param mixed $curl
   * @return string
   */
  private static function getMessage($code=200, $curl=null)
  {
    switch ($code)
    {
      case 200:
      case 204:
        return 'Success';
      case 404:
        return 'API Not found';
      case 500:
        return 'Servers replied with an error.';
      case 502:
        return 'servers may be down or being upgraded.';
      case 503:
        return 'service unavailable.';
      default:
        return "Undocumented error: {$code} / ".curl_error($curl);
        break;
    }
  }


  /**
   * PUBLIC AREA
   */

  /**
   * request
   *
   * @param string $method (get|post|put|delete)
   * @param string $path url path
   * @param object $data data & params
   * @param object $options base options
   *   $options = (object)[
   *     'url' => string
   *     'timeout' => int
   *     'headers' => array
   *     'debug' => boolean
   *     'outputType' => string
   *   ]
   * @return string|object
   */
  static public function request($method='get', $path='', $data=null, $options=null)
  {
    $result = (object)[];
    $curl = curl_init();

    // set params
    $params = '';
    if ($method === 'get' && $data)
    {
      $params = http_build_query($data);
      $params = $params ? '?'.$params : '';
    }

    // setting body
    if (isset($options->timeout))
    {
      curl_setopt($curl, CURLOPT_TIMEOUT, 0);
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $options->timeout);
    }
    curl_setopt($curl, CURLOPT_URL, (isset($options->url) ? $options->url : '').$path.$params);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    if ($data && $method !== 'get')
    {
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    self::setMethod($curl, $method);
    curl_setopt($curl, CURLOPT_HTTPHEADER, isset($options->headers) ? $options->headers : []);
    curl_setopt($curl, CURLINFO_HEADER_OUT, isset($options->debug) && $options->debug);

    // exec
    $response = curl_exec($curl);

    // get info
    $result->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if (isset($options->debug) && $options->debug) $result->debug = (object)curl_getinfo($curl);

    // set message
    $result->message = self::getMessage($result->code, $curl);

    // set response
    if (isset($options->outputType) && $options->outputType === 'json')
    {
      try
      {
        $result->response = json_decode($response, JSON_PRETTY_PRINT);
      }
      catch (Exception $e)
      {
        $result->response = (string)$response;
      }
    }
    else
    {
      $result->response = (string)$response;
    }

    // close curl
    curl_close($curl);

    return $result;
  }

  /**
   * update preference
   * 인스턴스 객체의 설정값을 업데이트하는데 사용합니다.
   *
   * @param object $pref
   */
  public function update($pref=null)
  {
    if (isset($pref->url) && $pref->url) $this->url = $pref->url;
    if (isset($pref->headers) && is_array($pref->headers)) $this->headers = $pref->headers;
    if (isset($pref->timeout)) $this->timeout = $pref->timeout;
    if (isset($pref->debug)) $this->debug = $pref->debug;
    if (isset($pref->outputType)) $this->outputType = $pref->outputType;
  }

  /**
   * call request
   *
   * @param string $method (get|post|put|delete)
   * @param string $path url path
   * @param object $data data & params
   * @return string|object
   */
  public function call($method='get', $path='', $data=null)
  {
    return self::request($method, $path, $data, (object)[
      'url' => $this->url,
      'timeout' => $this->timeout,
      'headers' => $this->headers,
      'debug' => $this->debug,
      'outputType' => $this->outputType,
    ]);
  }

}
