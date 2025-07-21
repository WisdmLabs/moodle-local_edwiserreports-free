---
description: Instructs Cursor to always create new MDC rules in .cursor/rules as separate files.
globs: []
alwaysApply: true
---

# MDC Rules Organization

## Rule Creation Guidelines

When creating new MDC (Model Context) rules for this project:

1. **Location**: Always create new MDC rules in the `.cursor/rules/` folder
2. **File Structure**: Each rule should be a separate file with a descriptive name
3. **Naming Convention**: Use kebab-case for file names (e.g., `api-documentation.md`, `code-review-standards.md`)
4. **File Extension**: Use `.md` extension for all rule files
5. **Content Format**: Write rules in clear, actionable language with specific instructions

## File Organization

- Keep related rules together but separate
- Use descriptive file names that clearly indicate the rule's purpose
- Avoid creating monolithic rule files - prefer multiple focused files
- Consider grouping related rules in subdirectories if the rules folder grows large

## Example Rule Structure

```markdown
# Rule Name

## Purpose
Brief description of what this rule accomplishes

## Instructions
Specific, actionable instructions for the AI

## Examples
Concrete examples of how to apply the rule

## Exceptions
Any cases where this rule doesn't apply (if applicable)
```

## Maintenance

- Review and update rules regularly
- Remove obsolete rules
- Consolidate overlapping rules when appropriate
- Keep rules focused and specific to avoid conflicts 