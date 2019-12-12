# restapi

RestAPI interface on PHP

PHP 환경에서 `API`요청을 할 수 있도록 도와주는 인터페이스 클래스입니다.  
이 패키지는 [cURL](https://www.php.net/manual/en/book.curl.php) PHP 모듈이 사용되기 때문에 `curl`모듈이 꼭 설치되어 있어야 합니다.

```shell script
# php7.4
sudo apt-get install php7.4-curl
```

# Install

다음과 같이 `composer`를 통하여 패키지를 설치합니다.

```shell script
composer require redgoose/restapi
```

`composer`를 사용하지 않는다면 `github`에서 소스를 다운로드 후에 `/src/RestAPI.php`로 사용합니다.


## Usage

### with composer

```php
require 'vendor/autoload.php';
use redgoose\RestAPI;

$restapi = new RestAPI();
```

### without composer

소스를 다운로드하고 직접 연결할때 사용하는 방법입니다.

```php
require 'src/RestAPI';
use redgoose\RestAPI;

$restapi = new RestAPI();
```

### create instance

`new` 키워드를 통하여 인스턴스 객체를 만들어서 사용할 수 있습니다.

```php
use redgoose\RestAPI;

// example
$restapi = new RestAPI((object)[]);
```


## Options

```php
use redgoose\RestAPI;

$restapi = new RestAPI((object)[
 'url' => 'https://api.address.com',
 'outputType' => 'text',
 'headers' => [],
 'timeout' => 10,
 'debug' => false,
]);
```

인스턴스 객체를 만들때 사용하는 옵션들입니다.  
객체를 만드는 예제소스와 같이 `object` 타입의 값은 다음과 같습니다.

| Name       | Type    | Default | Description |
| ---------- | ------- | ------- | ----------- |
| url        | string  | ''      | url prefix |
| outputType | string  | 'text'  | 출력방식 `json,text` |
| headers    | array   | []      | 요청할때 `headers`값을 사용합니다. |
| timeout    | int     | 10      | 요청 대기시간(초) |
| debug      | boolean | false   | 요청할때 요청 정보값을 받아와서 어떻게 요청하고 있는지 확인할 수 있습니다. |


## Methods

### request

함수 형식으로 접근하여 요청할때 사용합니다.

```php
use redgoose\RestAPI;

$response = RestAPI::request($method, $path, $data, $files, $options);

// example
$response = RestAPI::request('get', 'https://api.domain.com', null, null, (object)[]);
```

이 메서드는 다음과 같은 인자값을 사용합니다.

| Name     | Type         | Default | Description |
| -------- | ------------ | ------- | ----------- |
| $method  | string       | 'get'   | 요청 메서드 |
| $path    | string       | ''      | 요청 URL 주소 |
| $data    | object,array | null    | `mehtod=get`: url query string 방식으로 사용하고, 그 외에는 `data`로 사용됩니다. |
| $files   | array        | null    | `$_FILES` 형식의 값 |
| $options | object       | null    | `Options` 섹션에서 사용되는 값들과 동일합니다. |

### call

요청할때 사용하는 메서드입니다.  
이 메서드 내부에서 `RestAPI::request()`함수를 사용하기 때문에 사용법은 `Methods/request`섹션과 비슷합니다.

```php
use redgoose\RestAPI;

$restapi = new RestAPI((object)[]);
$response = $restapi->call($method, $path, $data, $files);

// example
$response = $restapi->call('get', 'https://api.domain.com', null, null);
```

이 메서드는 다음과 같은 인자값을 사용합니다.

| Name     | Type         | Default | Description |
| -------- | ------------ | ------- | ----------- |
| $method  | string       | 'get'   | 요청 메서드 |
| $path    | string       | ''      | 요청 URL 주소 |
| $data    | object,array | null    | `mehtod=get`: url query string 방식으로 사용하고, 그 외에는 `data`로 사용됩니다. |
| $files   | array        | null    | `$_FILES` 형식의 값 |

### update

인스턴스의 값을 업데이트할때 사용 수 있습니다.

```php
use redgoose\RestAPI;

$restapi = new RestAPI();
$restapi->update($options);
```

이 메서드는 다음과 같은 인자값을 사용합니다.

| Name     | Type   | Default | Description |
| -------- | ------ | ------- | ----------- |
| $options | object | null    | `Options` 섹션에서 사용되는 값들과 동일합니다. |


## Examples

[/tests](https://github.com/redgoose-dev/php-restapi/tree/master/tests)에서 개발을 위하여 만든 흔적들을 확인할 수 있습니다.

### File upload

개발을 위하여 파일 업로드 기능에 관한 샘플 소스를 만들었습니다.

[page/file-upload.php](https://github.com/redgoose-dev/php-restapi/blob/master/tests/page/file-upload.php) 파일은 업로드하는 폼과 첨부된 파일로 요청하는 부분을 예제로 활용할 수 있습니다.  
그리고 [api/upload.php](https://github.com/redgoose-dev/php-restapi/blob/master/tests/api/upload.php) 파일로 그 결과를 출력하여 결과를 받아올 수 있습니다.

`curl`은 서로 같은 도메인으로 요청할 수 없기 때문에 테스트용 API 서버서를 하나 더 띄었습니다.  
그것은 `script.sh` 스크립트를 통하여 서로다른 포트로 로컬서버를 띄어서 파일 업로드를 개발하고 테스트하게 되었습니다.

API 서버에서는 `$_FILES` 형식으로 넘어가면 실질적으로 파일이 업로드가 가능하다는것을 알게되어 값을 넘겨주기 위한 갑 처리가 좀 더 필요하게 되었습니다.
