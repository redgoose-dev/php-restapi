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
   * request
   *
   * @param string $method request method (get|post|put|delete)
   * @param string $path request local url
   * @param array|object $data
   * @return string|object
   * @throws Exception
   */
  public function request($method='get', $path='', $data=null)
  {
    $result = (object)[];
    $curl = curl_init();

    // setting body
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
    curl_setopt($curl, CURLOPT_URL, $this->url.$path);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    self::setMethod($curl, $method);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($curl, CURLINFO_HEADER_OUT, $this->debug);

    // exec
    $response = curl_exec($curl);

    // get info
    $result->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($this->debug) $result->debug = (object)curl_getinfo($curl);

    // set message
    $result->message = self::getMessage($result->code, $curl);

    // set response
    if ($this->outputType === 'json')
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
   *
   */
  static public function basic()
  {
    //
    var_dump('asd');
  }

  static public function simple()
  {
    // file_get_contents()
  }
}
