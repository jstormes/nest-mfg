# Coding Conventions

This document outlines the coding conventions and standards for our project.


## Coding enviornment 
- This project uses docker and docker compose.  
- Don't rebuild docker containers unless asked.  
- Use only the php-dev container unlessed asked to use the prod container.
- Use php composer only inside the docker php-dev container.

## General Formatting

- **Indentation**: 4 spaces
- **Maximum Line Length**: 120 characters
- **File Encoding**: UTF-8
- **Line Endings**: LF (Unix-style)
- **Trailing Whitespace**: Trimmed
- **Final Newline**: Required at end of file

## PHP Standards

### Version and Standards
- PHP Version: 8.1
- Coding Standards: PSR-12
- Strict Types: Enabled


### Type Safety
- All functions and methods must declare return types
- All parameters must have type declarations
- All class properties must have type declarations
- Use union types where appropriate (e.g., `string|int`)
- Use nullable types where appropriate (e.g., `?string`)
- Use `void` return type for functions that return nothing
- Use `never` return type for functions that never return
- Use `mixed` type when type is truly unknown
- Use intersection types where appropriate
- Use PHP 8.1+ enums where applicable

### Class Design
- Prefer readonly properties
- Prefer final classes by default
- Prefer final methods by default
- Use constructor property promotion
- Follow PSR-12 class structure
- Implement interfaces for service contracts
- Use traits for shared functionality
- Follow SOLID principles
- Use dependency injection
- Implement proper exception handling

## SQL Standards (MariaDB 10.6)

### Database Configuration
- Engine: InnoDB
- Character Set: utf8mb4
- Collation: utf8mb4_unicode_ci
- Use the database named app
- All startup SQL script should be in .docker/db-startup.development
- When chainging database startup scripts just do a docker build with cache
- Use the MYSQL_DSN enviornment varable from dcoker to setup the database conection

### Query Formatting
- SQL Keywords: UPPERCASE
- Identifiers: lowercase
- Table Aliases: 't'
- Column Aliases: 'c'
- Use explicit JOIN syntax
- Use table aliases consistently

### Features and Best Practices
- Use window functions where appropriate
- Use Common Table Expressions (CTEs)
- Use JSON functions for JSON data
- Implement fulltext search where needed
- Use spatial functions for geographic data
- Use sequences for auto-incrementing
- Implement check constraints
- Use computed columns where appropriate
- Use partitioning for large tables
- Always use foreign keys for referential integrity
- Create appropriate indexes
- Use views for complex queries
- Use stored procedures for complex operations
- Use triggers when necessary
- Use events for scheduled tasks

## HTML Standards

### Version and Framework
- HTML5
- Bootstrap 5.3

### Best Practices
- Use semantic HTML5 elements
- Prefer CSS classes over inline styles
- Follow responsive design principles

### Responsive Breakpoints
- xs: 0
- sm: 576px
- md: 768px
- lg: 992px
- xl: 1200px
- xxl: 1400px

## CSS Standards

### Naming Convention
- Use BEM (Block Element Modifier) naming convention
- Format: `block__element--modifier`

### Units
- Prefer rem units over px
- Use relative units for responsive design

### Media Queries
- Mobile-first approach
- Order:
  1. Mobile styles (default)
  2. Tablet styles
  3. Desktop styles

## Version Control

### Commit Messages
- Use present tense
- Start with a verb
- Keep first line under 72 characters
- Provide detailed description when needed

### Branch Naming
- feature/feature-name
- bugfix/bug-description
- hotfix/issue-description
- release/version-number

## Documentation

### Code Comments
- Use PHPDoc for PHP functions and classes
- Document complex SQL queries
- Explain non-obvious CSS rules
- Document JavaScript functions

### README Files
- Include setup instructions
- List dependencies
- Provide usage examples
- Document configuration options

## Testing

### PHP Unit Tests
- Follow PSR-12
- Use strict types
- Test both positive and negative cases
- Mock external dependencies

### Database Tests
- Use test database
- Clean up after tests
- Test both schema and data integrity

## Security

### General
- Never store sensitive data in code
- Use environment variables for configuration
- Implement proper input validation
- Use prepared statements for all SQL queries
- Follow OWASP security guidelines

### Authentication
- Use secure password hashing
- Implement proper session management
- Use HTTPS for all connections
- Implement rate limiting 