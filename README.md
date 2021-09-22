# HTTP Responder

A simple solution to create [PSR-7](https://www.php-fig.org/psr/psr-7/)
compatible HTTP responses.

![phpunit workflow](https://github.com/kovagoz/http-responder/actions/workflows/php.yml/badge.svg)

## Requirements

* PHP >=8.0

## Usage

#### Instantiate the class:

```php
$responder = new HttpResponder($responseFactory, $streamFactory);
```

#### Create HTML response

```php
$response = $responder->reply('hello world');
```

#### Create JSON response

```php
$response = $responder->reply(['foo' => 'bar']);
```

In this case, response body will be the following:

```json
{
  "foo": "bar"
}
```

Furthermore, `Content-Type` header will be set to `application/json`.

#### Create empty response (204 No Content)

```php
$response = $responder->reply();
```

#### Create redirection

```php
$response = $responder->redirect('https://example.com/');
```

Default status code is `302`. You can change it on the response object by the
`withStatus()` method.

You can also pass `UriInterface` object to the `redirect()` method instead of
string URL.

## Testing

This repository contains a Makefile which aids to run unit tests on your
computer using a Docker container.

Just run the command below, sit back and watch results.

```shell
make test
```
