# Project Rules

## Database Modifying Operations
- **DO NOT** run destructive database scripts (such as `seed_new_schema.php`) that drop tables and wipe data unless the user explicitly requests a database reset.
- Always use non-destructive database migrations or `ALTER TABLE` statements to update schemas, preserving the user's existing records.
