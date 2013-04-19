# Welcome

Dear visitor, welcome to the index page of The DataTank. You are seeing this page because a DataTank instance has been installed on this server. The DataTank is an open source tool that publishes data to the web. This page will give you some pointers that will get you started!

## Retrieve data

The DataTank publishes data to the web, this means that the data stored in files such as CSV and XLS files, are now accessible through the web. Furthermore, data in various data-structures such as databases and SHP files are accessible in the same way! This data can be retrieved via a simple GET HTTP request to the uri on which the data is published.

This brings us to our first pointer and answer to the question: "What datasources are available and what data do they contain?". In order to answer this question we'll first explain how datatank uri's are built. A datatank uri is built by a series of identifiers and just like any other uri, the identifiers are split with a forward slash. These identifiers identify either a package or a resource. These terms are very simple, a package is a collection of other packages and/or resources and a resource represents a datasource.

By example, we'll provide a usage of these new terms and provide a link to the overview of the published datasources.
Let's assume that a datatank is installed at localhost/datatank. If I were to browse to http://localhost/datatank, the page you are reading right now would be displayed. In order to get an overview of the published datasources you'll have to browse to the resource _resources_ located in the package _tdtinfo_ (e.g. http://localhost/datatank/tdtinfo/resources). This will provide you with not only an overview of the published datasources, but also in what package they are located, and some additional documentation about what information the data holds.

Note that the tdtinfo/resources-resource is a resource like any other, this means that when you see a resource in the overview that interests you, simple put the package-name and the resource-name together and put after the uri on which the datatank is installed. A piece of a possible overview is given below:

```json
{
    demography: {
        statistics: {
            documentation: "This resource provides information about a demographical outline.",
            requiredparameters: [ ],
            parameters: [ ]
        }
    }
}
```

In order to view the data that the resource _statistics_ hold, we'd have to go to http://localhost/datatank/demography/statistics.

We also support various formats in which data can be retrieved. If we would like to view the _statistics_-resource in a json format the uri would be build like this: http://localhost/datatank/demography/statistics.json. A full overview of what formatters are available can be viewed at the _formatters_-resource in the _tdtinfo_-package.

A last pointer we'd like to give away is that packages can be viewed as well, if you browse to the _tdtinfo_-package for example, you will get a list of the different resources that are located in that package.

For more information about retrieving data, visit our [website](http://thedatatank.com/help/category/consuming/).

