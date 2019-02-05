# W3COM  : Hulk - Hana Utils Kit 

This bundle provides ways to generate advanced DataTables from Json file. Json give or
Compatible with Symfony 3.3.\*, 4.0.\* and 4.1.\*.

## Installation

#### Download the bundle

Use SATIS to get the bundle through composer by modifying your `composer.json` :

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://satis.w3cloud.fr"
        }
    ]
}
```

Also add the dependency :

```json
{
"require": {
        "w3com-sas/gulk": "^1.0"
    }
}
```

#### Configure the bundle

Add the directory of json files in config/package/hulk.yaml

````yaml
w3com_hulk:
  json_display:
    url_files: '/project/Display/'
````

Declare routes in config/route.yaml
 
````yaml
display_table:
  resource: '@W3comHulkBundle/Resources/config/routing/display_table_view.xml'

create_view:
  resource: '@W3comHulkBundle/Resources/config/routing/create_view.xml'

update_view:
  resource: '@W3comHulkBundle/Resources/config/routing/update_view.xml'
```` 




