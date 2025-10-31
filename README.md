# ddns-bundle

[![Latest Version](https://img.shields.io/packagist/v/tourze/ddns-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/ddns-bundle)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/ddns-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/ddns-bundle)

[English](README.md) | [中文](README.zh-CN.md)

DDNS (Dynamic DNS) service bundle for Symfony applications.

## Features

- **Automatic DNS Updates**: Automatically updates DNS records when entities are modified
- **Attribute-Based Configuration**: Use PHP 8 attributes to mark domain and IP fields
- **Provider Agnostic**: Support for multiple DNS providers through interfaces
- **Console Commands**: Manual and scheduled DNS updates
- **Doctrine Integration**: Seamless integration with Doctrine ORM events

## Installation

```bash
composer require tourze/ddns-bundle
```

## Dependencies

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM

## Quick Start

### 1. Register the Bundle

```php
// config/bundles.php
return [
    // ...
    DDNSBundle\DDNSBundle::class => ['all' => true],
];
```

## Usage

### Using DDNS Attributes

Use `#[DDNSDomain]` and `#[DdnsIp]` attributes to mark domain and IP fields in your entities:

```php
<?php

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DdnsIp;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ServerNode
{
    #[ORM\Column(type: 'string')]
    #[DDNSDomain(provider: 'cloudflare')]
    private string $domain;

    #[ORM\Column(type: 'string')]
    #[DdnsIp(provider: 'cloudflare')]
    private string $ipAddress;

    // ... other fields and methods
}
```

## Advanced Usage

### Automatic DDNS Updates

When entities are created or updated, the system automatically:

1. Checks if the entity contains DDNS attributes
2. Extracts domain and IP address information
3. Calls the corresponding DNS provider to update DNS records

### Attribute Parameters

- `provider`: Optional parameter to specify the DNS provider (e.g., 'cloudflare', 'aliyun', etc.)

### Console Commands

#### ddns:provider:run

Runs DDNS provider update tasks. This command will:

1. Iterate through all IP resolvers to get domain and IP mappings that need updating
2. Check if each DNS provider supports the domain
3. Update the corresponding DNS records

```bash
# Manually run DDNS update
php bin/console ddns:provider:run
```

This command is configured as a cron task and runs automatically every minute.

## Configuration

The bundle automatically registers the following services:

- `DDNSAttributeProcessor`: Attribute processor
- `DDNSUpdateService`: DDNS update service
- `DDNSEntityListener`: Entity change listener

## Examples

See `examples/ExampleEntity.php` for a complete usage example.

## How It Works

1. **Attribute Marking**: Use `#[DDNSDomain]` and `#[DdnsIp]` attributes on entity fields
2. **Event Listening**: Doctrine event listeners monitor entity creation and update events
3. **Attribute Parsing**: Attribute processor extracts field values with DDNS attributes
4. **DNS Updates**: Calls DNS provider interfaces to update DNS records

## Contributing

Contributions are welcome! Please follow these guidelines:

1. **Issues**: Report bugs or suggest features via [GitHub Issues](https://github.com/tourze/php-monorepo/issues)
2. **Pull Requests**: Fork the repository and submit PRs with clear descriptions
3. **Code Style**: Follow PSR-12 coding standards
4. **Testing**: Ensure all tests pass and add tests for new features
5. **Documentation**: Update documentation for any changes

## References

- [Symfony Attributes](https://symfony.com/doc/current/components/dependency_injection/attributes.html)
- [Doctrine Event Listeners](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for details on version history and updates.

## License

This bundle is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.