# Use CloudAMQP from PHP

A minimal example of publishing and consuming messages with
[php-amqplib](https://github.com/php-amqplib/php-amqplib), against
[CloudAMQP](https://www.cloudamqp.com) or any AMQP 0-9-1 broker such as
[LavinMQ](https://lavinmq.com) or RabbitMQ.

- [`Publisher.php`](src/Publisher.php) publishes a persistent message and waits for the broker's confirm.
- [`Worker.php`](src/Worker.php) consumes with manual acknowledgements and a prefetch of 10.
- [`Amqp.php`](src/Amqp.php) connects using the `CLOUDAMQP_URL` environment variable, with TLS for `amqps://` URLs.

## Usage

Requires PHP 8.4+ with the `sockets` and `mbstring` extensions, and
[Composer](https://getcomposer.org). The version used in development is in
`mise.toml`.

```sh
composer install
export CLOUDAMQP_URL="amqps://user:password@host/vhost"
php worker.php      # in one terminal
php publisher.php   # in another
```

Without `CLOUDAMQP_URL` it connects to `amqp://guest:guest@localhost`.

## Tests

`composer test` publishes and consumes a message through a real broker on
localhost. CI runs it against LavinMQ.
