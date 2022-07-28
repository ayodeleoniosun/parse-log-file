# Legal One BE Coding Challenge

Thanks for going through our interview process and for taking the coding test!

## Problem description

There is an aggregated log file that contains log lines from multiple services. This file is potentially very large (think hundreds of millions of lines). A very small example of this file can be found in the provided `logs.txt`.

We would now like to analyze this file, e.g. count log lines for a specific service.

## Tasks

1. Build a console command that parses the file and inserts the data to a database (without using a parsing library). Pick any DB that you are familiar with and decide on a schema. The import should be triggered manually, and it should be able to start where it left off when interrupted.

2. Build a RESTful service that implements the provided `api.yaml` OpenAPI specs.

The service should query the database and provide a single endpoint `/count` which returns a count of rows that match the filter criteria.

   a) This endpoint accepts a list of filters via GET request and allows zero or more filter parameters. The filters are:
   - serviceNames
   - statusCode
   - startDate
   - endDate
   
   b) Endpoint result:

```
{
    "counter": 1
}
```

## Submit your solution

Please create a repository on Github, Gitlab or BitBucket with your solution.

Implement the solution in **PHP 8.1**, using the **Symfony** framework and document the solution in the README file. You may use the template app provided in the `devbox` folder (see included README file for details). 

Once you are done with the challenge, please send us the link to the repo.

Wishing you the best.

## Please note

- For testing purposes take a look at our example log file `logs.txt`. It only contains 20 entries. Your submission will be evaluated with a much larger file (~ hundreds of millions of lines)
- If you feel there are ambiguities in the requirements feel free to document them in the README file

## What we are looking at

We prefer quality over speed. It does not only matter if your solution produces correct results, we also take a closer look on the following criteria:

1. SOLID / DRY / KISS
2. Design patterns
3. Tests
4. Readability / keeping best practice
5. Documentation of the solution
