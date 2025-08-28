# Condo Manager Development Guidelines

## Build/Lint/Test Commands

**Next.js Frontend**
```bash
npm run build # Next.js build
npm run lint # ESLint + TypeScript
npm test # Jest tests (add test script to package.json)
```

**Symfony API**
```bash
php bin/phpunit # Run all tests
php bin/phpunit tests/Controller/DashboardControllerTest.php # Single test
```

## Code Style

**Next.js**
- Use TypeScript with strict null checks
- Sort imports: `react` → `next` → local imports
- Prettier formatting with 2-space indentation

**Symfony**
- PSR-12 PHP standards
- PHP CS Fixer with Symfony ruleset
- Entity names use PascalCase (e.g., `User.php`)
- DTOs in `DTO/Request/` and `DTO/Response/`

## Error Handling
- PHP: Use typed exceptions and log with Monolog
- JS: Handle async errors with `try/catch` + `Promise.catch`
- All errors must have status codes and user-friendly messages