<?php
namespace redgoose;
use Exception, CURLFile;

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
   * filtering files
   * `$_FILES` 값을 리얼패스 파일주소로 변환시킵니다.
   *
   * @param array $data
   * @param array $file
   * @param string $key
   * @return array
   */
  private static function filteringFiles($data, $file, $key)
  {
    if (is_array($file['tmp_name']) && $file['tmp_name'][0])
    {
      for ($i=0; $i<count($file['tmp_name']); $i++)
      {
        $data["{$key}[{$i}]"] = new CURLFile($file['tmp_name'][$i], $file['type'][$i], $file['name'][$i]);
      }
    }
    else if (is_string($file['tmp_name']) && $file['tmp_name'])
    {
      $data[$key] = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
    }
    return $data;
  }


  /**
   * PUBLIC AREA
   */

  /**
   * request
   *
   * @param string $method (get|post|put|delete)
   * @param string $path url path
   * @param object|array $data data & params
   * @param array $files files
   * @param object $options base options
   *   $options = (object)[
   *     'url' => string
   *     'outputType' => string
   *     'headers' => array
   *     'timeout' => int
   *     'debug' => boolean
   *   ]
   * @return string|object
   */
  static public function request($method='get', $path='', $data=null, $files=null, $options=null)
  {
    $result = (object)[];
    $curl = curl_init();

    // set data and params
    $params = '';
    if ($method === 'get' && $data)
    {
      $params = http_build_query($data);
      $params = $params ? '?'.$params : '';
    }
    else if ($data || $files)
    {
      // TODO: PUT, PATCH, DELETE 값 테스트 해보고 값이 안나오면 처리 개발필요함.
      // `$_POST`같은 값을 `$_FILES`와 합치기 위한 준비를 합니다.
      $data = (array)$data;
      // $_FILES 값을 `curl`을 통하여 보낼 수 있도록 값을 정리합니다.
      if ($files && is_array($files) && count($files))
      {
        foreach ($files as $key=>$file)
        {
          $data = self::filteringFiles($data, $file, $key);
        }
      }
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
        $result->response = (object)$result->response;
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
   * @param object|array $data data & params
   * @param array $files files
   * @return string|object
   */
  public function call($method='get', $path='', $data=null, $files=null)
  {
    return self::request($method, $path, $data, $files, (object)[
      'url' => $this->url,
      'timeout' => $this->timeout,
      'headers' => $this->headers,
      'debug' => $this->debug,
      'outputType' => $this->outputType,
    ]);
  }

}
