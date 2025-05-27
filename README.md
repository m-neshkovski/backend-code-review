# Backend Code-Challenge

This is a dummy project, which is used to demonstrate knowledge of symfony and backend development in general.
It serves as an example with some bad practices included.

## Tasks

- [x] Clone the repository or [download the code](https://github.com/cutlery42/backend-code-review/archive/refs/heads/main.zip)
  - [x] Handle all [open issues](https://github.com/cutlery42/backend-code-review/issues) in the project
  - [x] Make `vendor/bin/phpstan` pass without errors
  - [x] Make `vendor/bin/phpunit` pass without errors
  - [x] Upload the code to your own Repository (Avoid forking the repository and creating a PR, as this would make your solution visible to others)]

## Install

We prepared a dev environment with all dependencies included.
If this does not work / you're faster with your own setup, feel free to use your own environment.

1. Install [Nix](https://nixos.org/download) if you don't have it already.
2. Use `nix-shell` to enter the development environment
    - This will install all the necessary dependencies


## Development server

1. `just install` to install all dependencies
2. Run `just start` for a dev server (or `symfony serve` if you don't use `nix-shell`)

******************************************

# SOLUTION EVALUATION

# Code Analysis: SOLID Principles, Clean Code, Design Patterns, and Symfony Integration

After analyzing the codebase, I can provide the following assessment of how well it adheres to SOLID principles, clean code practices, what design patterns are used, and how well it integrates with the Symfony framework.

## SOLID Principles

### Single Responsibility Principle (SRP)
The codebase demonstrates excellent adherence to SRP:
- `Message` entity is focused solely on representing message data
- `MessageRepository` handles only data access operations
- `MessageFactory` is dedicated to creating message instances
- `SendMessage`: Acts as a DTO (Data Transfer Object) for message data
- `SendMessageHandler` Handles the business logic of processing messages
- `MessageController` handles HTTP requests and responses

### Open/Closed Principle (OCP)
The code is generally open for extension but closed for modification:
- The `MessageStatus` enum can be extended with new statuses without modifying existing code
- The repository pattern allows for extending query capabilities without changing core functionality

### Liskov Substitution Principle (LSP)
The inheritance hierarchies respect this principle:
- `MessageRepository` properly extends `ServiceEntityRepository`
- `MessageController` correctly extends `AbstractController`

### Interface Segregation Principle (ISP)
The interfaces used are focused and not bloated:
- The code uses Symfony's interfaces like `MessageBusInterface`, `NormalizerInterface`, etc., which are well-defined and focused

### Dependency Inversion Principle (DIP)
Dependencies are properly injected rather than created within classes:
- `SendMessageHandler` receives its dependencies via constructor injection
- `MessageController` methods receive dependencies as parameters
- Tests use mocks to replace real dependencies

## Clean Code Practices

The codebase demonstrates excellent clean code practices:

### Meaningful Names
- Classes, methods, and variables have clear, descriptive names
- Method names like `filterByStatus` clearly indicate their purpose

### Good Documentation
- Comments explain the reasoning behind code decisions
- PHPDoc blocks provide type information and context

### Error Handling
- Proper exception handling in `SendMessageHandler`
- Validation in controllers before processing requests

### Consistent Formatting
- Consistent code style throughout the codebase
- Proper use of PHP 8 features like attributes

### Testability
- Comprehensive unit tests for functionality
- Use of mocks and test data providers

## Design Patterns

The codebase implements several design patterns:

### Factory Pattern
- `MessageFactory` encapsulates the creation logic for `Message` entities
- Provides methods for creating both single messages and collections of fake messages

### Repository Pattern
- `MessageRepository` abstracts data access operations
- Provides methods for filtering and retrieving messages

### Command/Handler Pattern (via Symfony Messenger)
- `SendMessage` acts as a command object containing the data needed for the operation
- `SendMessageHandler` processes the command and performs the actual work
- This pattern enables decoupling and potentially asynchronous processing

### Data Transfer Object (DTO) Pattern
- `SendMessage` serves as a DTO to transfer data between the controller and handler
- Includes validation constraints to ensure data integrity

## Correlation with Symfony Framework

The code integrates well with the Symfony framework:

### Symfony Components
- Proper use of Symfony's Controller, Entity, Repository components
- Effective use of Symfony's validation system with attributes
- Good integration with Doctrine ORM for data persistence

### Symfony Messenger
- Appropriate configuration in `messenger.yaml`
- Currently using sync transport but designed to be easily switched to async
- Proper error handling and logging

### Symfony Best Practices
- Following Symfony's best practices for dependency injection
- Using attributes for routing and validation
- Proper use of serialization groups for API responses
- Appropriate use of environment variables for configuration

## Conclusion

The codebase follows modern PHP and Symfony best practices, adhering to SOLID principles and clean code practices. It uses appropriate design patterns (Command/Message, Factory, Repository, DTO) that integrate well with Symfony's architecture. The code is well-structured, maintainable, and demonstrates a good understanding of both object-oriented design principles and Symfony framework conventions.

## Areas for Improvement

While the code is generally well-designed, there are a few potential improvements:

1. As noted in the messenger configuration, the `SendMessage` handling could be moved to async processing in production
2. The controller's error response in the `send` method could return a more structured JSON error response
