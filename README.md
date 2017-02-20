# iTop plugin for GLPI

**Please note that an [iTop module, provided by Teclib'](https://github.com/TECLIB/teclib-itop-glpi-module), is also required for the synchronization to work.**

## Features

The plugin allows you to export GLPI inventory data into iTop CMDB.
There's two export modes :
- online : the plugin pushes data into iTop synchronization tables
- offline : the plugin exports data into CSV files, to be imported into iTop

What you can configure
- Matching between object types in GLPI and iTop
- Matching between GLPI statuses and iTop statuses
- Matching between GLPI software categories and iTop software instances types

## Contributing

* Open a ticket for each bug/feature so it can be discussed
* Follow [development guidelines](http://glpi-developer-documentation.readthedocs.io/en/latest/plugins.html)
* Refer to [GitFlow](http://git-flow.readthedocs.io/) process for branching
* Work on a new branch on your own fork
* Open a PR that will be reviewed by a developer
