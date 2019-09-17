# Backward Compatibility Checker

A tool that can be used to verify BC breaks between two versions of a PHP library.

***Composer***

```
composer require --dev roave/backward-compatibility-check
```

***Config***

The task lives under the `BackwardCompatibilityChecker` namespace and has following configurable parameters:

```yaml
# grumphp.yml
parameters:
    tasks:
        BackwardCompatibilityChecker:
            report_file: report.md
            format: markdown
            triggered_by: [php]
```


**triggered_by**

*Default: [php]*

This option will specify which file extensions will trigger the robo task.
By default Backwards Compatibility Checker will be triggered by altering a PHP file. 
You can overwrite this option to whatever file you want to use!
